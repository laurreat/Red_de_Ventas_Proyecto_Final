<?php

namespace App\Services;

use App\Models\User;
use App\Models\Pedido;
use App\Models\Comision;
use App\Models\Referido;
use Illuminate\Support\Facades\Cache;
use MongoDB\Laravel\Collection;

class OptimizedQueryService
{
    const CACHE_TTL = 300; // 5 minutes

    /**
     * Get vendedores with optimized query using indexes
     */
    public function getVendedoresOptimized($withStats = false)
    {
        $cacheKey = 'vendedores_optimized_' . ($withStats ? 'with_stats' : 'simple');

        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($withStats) {
            $query = User::where('rol', 'vendedor')
                        ->where('activo', true)
                        ->orderBy('name');

            if ($withStats) {
                // Use aggregation pipeline for better performance
                return $this->getVendedoresWithStatsAggregation();
            }

            return $query->get(['_id', 'name', 'email', 'telefono', 'created_at']);
        });
    }

    /**
     * Get vendedores with sales stats using MongoDB aggregation
     */
    private function getVendedoresWithStatsAggregation()
    {
        $vendedores = User::raw(function(Collection $collection) {
            return $collection->aggregate([
                [
                    '$match' => [
                        'rol' => 'vendedor',
                        'activo' => true
                    ]
                ],
                [
                    '$lookup' => [
                        'from' => 'pedidos',
                        'localField' => '_id',
                        'foreignField' => 'vendedor_id',
                        'as' => 'pedidos'
                    ]
                ],
                [
                    '$addFields' => [
                        'total_pedidos' => ['$size' => '$pedidos'],
                        'pedidos_entregados' => [
                            '$size' => [
                                '$filter' => [
                                    'input' => '$pedidos',
                                    'cond' => ['$eq' => ['$$this.estado', 'entregado']]
                                ]
                            ]
                        ],
                        'ventas_totales' => [
                            '$sum' => [
                                '$map' => [
                                    'input' => [
                                        '$filter' => [
                                            'input' => '$pedidos',
                                            'cond' => ['$eq' => ['$$this.estado', 'entregado']]
                                        ]
                                    ],
                                    'as' => 'pedido',
                                    'in' => '$$pedido.total_final'
                                ]
                            ]
                        ]
                    ]
                ],
                [
                    '$project' => [
                        'name' => 1,
                        'email' => 1,
                        'telefono' => 1,
                        'created_at' => 1,
                        'total_pedidos' => 1,
                        'pedidos_entregados' => 1,
                        'ventas_totales' => 1,
                        'comision_estimada' => ['$multiply' => ['$ventas_totales', 0.1]]
                    ]
                ],
                [
                    '$sort' => ['ventas_totales' => -1]
                ]
            ]);
        });

        return collect($vendedores);
    }

    /**
     * Get commission data for a period with optimization
     */
    public function getComisionesOptimized($fechaInicio, $fechaFin, $vendedorId = null)
    {
        $cacheKey = "comisiones_{$fechaInicio}_{$fechaFin}_" . ($vendedorId ?? 'all');

        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($fechaInicio, $fechaFin, $vendedorId) {
            $matchStage = [
                'created_at' => [
                    '$gte' => new \MongoDB\BSON\UTCDateTime(strtotime($fechaInicio) * 1000),
                    '$lte' => new \MongoDB\BSON\UTCDateTime(strtotime($fechaFin . ' 23:59:59') * 1000)
                ],
                'vendedor_id' => ['$ne' => null]
            ];

            if ($vendedorId) {
                $matchStage['vendedor_id'] = new \MongoDB\BSON\ObjectId($vendedorId);
            }

            $result = Pedido::raw(function(Collection $collection) use ($matchStage) {
                return $collection->aggregate([
                    ['$match' => $matchStage],
                    [
                        '$lookup' => [
                            'from' => 'users',
                            'localField' => 'vendedor_id',
                            'foreignField' => '_id',
                            'as' => 'vendedor'
                        ]
                    ],
                    ['$unwind' => '$vendedor'],
                    [
                        '$group' => [
                            '_id' => '$vendedor_id',
                            'vendedor_nombre' => ['$first' => '$vendedor.name'],
                            'vendedor_email' => ['$first' => '$vendedor.email'],
                            'total_pedidos' => ['$sum' => 1],
                            'pedidos_entregados' => [
                                '$sum' => [
                                    '$cond' => [['$eq' => ['$estado', 'entregado']], 1, 0]
                                ]
                            ],
                            'total_ventas' => [
                                '$sum' => [
                                    '$cond' => [
                                        ['$eq' => ['$estado', 'entregado']],
                                        '$total_final',
                                        0
                                    ]
                                ]
                            ],
                            'ventas_pendientes' => [
                                '$sum' => [
                                    '$cond' => [
                                        ['$nin' => ['$estado', ['entregado', 'cancelado']]],
                                        '$total_final',
                                        0
                                    ]
                                ]
                            ]
                        ]
                    ],
                    [
                        '$addFields' => [
                            'comision_ganada' => ['$multiply' => ['$total_ventas', 0.1]],
                            'comision_pendiente' => ['$multiply' => ['$ventas_pendientes', 0.1]]
                        ]
                    ],
                    ['$sort' => ['total_ventas' => -1]]
                ]);
            });

            return collect($result);
        });
    }

    /**
     * Get referral network data optimized
     */
    public function getReferidosNetworkOptimized($userId = null)
    {
        $cacheKey = 'referidos_network_' . ($userId ?? 'all');

        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($userId) {
            $matchStage = ['estado' => 'activo'];

            if ($userId) {
                $matchStage['referidor_id'] = new \MongoDB\BSON\ObjectId($userId);
            }

            $result = Referido::raw(function(Collection $collection) use ($matchStage) {
                return $collection->aggregate([
                    ['$match' => $matchStage],
                    [
                        '$lookup' => [
                            'from' => 'users',
                            'localField' => 'referidor_id',
                            'foreignField' => '_id',
                            'as' => 'referidor'
                        ]
                    ],
                    [
                        '$lookup' => [
                            'from' => 'users',
                            'localField' => 'referido_id',
                            'foreignField' => '_id',
                            'as' => 'referido'
                        ]
                    ],
                    ['$unwind' => '$referidor'],
                    ['$unwind' => '$referido'],
                    [
                        '$lookup' => [
                            'from' => 'pedidos',
                            'localField' => 'referido_id',
                            'foreignField' => 'vendedor_id',
                            'as' => 'pedidos_referido'
                        ]
                    ],
                    [
                        '$addFields' => [
                            'ventas_referido' => [
                                '$sum' => [
                                    '$map' => [
                                        'input' => [
                                            '$filter' => [
                                                'input' => '$pedidos_referido',
                                                'cond' => ['$eq' => ['$$this.estado', 'entregado']]
                                            ]
                                        ],
                                        'as' => 'pedido',
                                        'in' => '$$pedido.total_final'
                                    ]
                                ]
                            ]
                        ]
                    ],
                    [
                        '$project' => [
                            'referidor' => {
                                '_id' => '$referidor._id',
                                'name' => '$referidor.name',
                                'email' => '$referidor.email'
                            },
                            'referido' => {
                                '_id' => '$referido._id',
                                'name' => '$referido.name',
                                'email' => '$referido.email',
                                'rol' => '$referido.rol'
                            },
                            'fecha_referencia' => '$created_at',
                            'ventas_referido' => 1,
                            'comision_generada' => ['$multiply' => ['$ventas_referido', 0.05]]
                        ]
                    ]
                ]);
            });

            return collect($result);
        });
    }

    /**
     * Get top performers with caching
     */
    public function getTopPerformers($limit = 10, $periodo = 'mes')
    {
        $cacheKey = "top_performers_{$periodo}_{$limit}";

        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($limit, $periodo) {
            $fechaInicio = match($periodo) {
                'semana' => now()->startOfWeek(),
                'mes' => now()->startOfMonth(),
                'año' => now()->startOfYear(),
                default => now()->startOfMonth()
            };

            return $this->getComisionesOptimized(
                $fechaInicio->format('Y-m-d'),
                now()->format('Y-m-d')
            )->take($limit);
        });
    }

    /**
     * Clear related caches
     */
    public function clearCache($pattern = null)
    {
        if ($pattern) {
            $keys = Cache::getRedis()->keys("*{$pattern}*");
            if (!empty($keys)) {
                Cache::getRedis()->del($keys);
            }
        } else {
            Cache::flush();
        }
    }

    /**
     * Warm up caches for common queries
     */
    public function warmUpCaches()
    {
        // Warm up vendedores cache
        $this->getVendedoresOptimized(true);

        // Warm up comisiones for current month
        $this->getComisionesOptimized(
            now()->startOfMonth()->format('Y-m-d'),
            now()->format('Y-m-d')
        );

        // Warm up top performers
        $this->getTopPerformers(10, 'mes');

        // Warm up referidos network
        $this->getReferidosNetworkOptimized();
    }
}