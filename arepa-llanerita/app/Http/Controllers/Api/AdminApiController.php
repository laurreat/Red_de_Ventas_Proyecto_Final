<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Producto;
use App\Models\Pedido;
use App\Models\Comision;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class AdminApiController extends Controller
{
    /**
     * Get dashboard data
     */
    public function dashboard(): JsonResponse
    {
        try {
            $inicioMes = Carbon::now()->startOfMonth();
            $finMes = Carbon::now()->endOfMonth();
            $hoy = Carbon::today();

            // Estadísticas principales
            $stats = [
                'total_usuarios' => User::count(),
                'total_vendedores' => User::where('rol', 'vendedor')->where('activo', true)->count(),
                'total_productos' => Producto::count(),
                'productos_stock_bajo' => Producto::where('stock', '<', 10)->count(),
                'pedidos_hoy' => Pedido::whereDate('created_at', $hoy)->count(),
                'pedidos_pendientes' => Pedido::where('estado', 'pendiente')->count(),
                'ventas_mes' => Pedido::whereBetween('created_at', [$inicioMes, $finMes])
                                    ->where('estado', '!=', 'cancelado')
                                    ->sum('total_final'),
                'comisiones_pendientes' => Comision::where('estado', 'pendiente')->sum('monto'),
                'ventas_hoy' => Pedido::whereDate('created_at', $hoy)
                                    ->where('estado', '!=', 'cancelado')
                                    ->sum('total_final'),
                'clientes_activos' => User::where('rol', 'cliente')
                                        ->where('activo', true)->count(),
                'total_comisiones_mes' => Comision::whereBetween('created_at', [$inicioMes, $finMes])
                                                ->sum('monto'),
            ];

            // Pedidos recientes
            $pedidos_recientes = Pedido::orderBy('created_at', 'desc')
                                      ->take(10)
                                      ->get()
                                      ->map(function ($pedido) {
                                          return [
                                              'id' => $pedido->_id,
                                              'numero_pedido' => $pedido->numero_pedido,
                                              'cliente' => [
                                                  'name' => $pedido->cliente_data['name'] ?? 'Cliente',
                                                  'email' => $pedido->cliente_data['email'] ?? ''
                                              ],
                                              'vendedor' => [
                                                  'name' => $pedido->vendedor_data['name'] ?? 'Vendedor',
                                                  'email' => $pedido->vendedor_data['email'] ?? ''
                                              ],
                                              'total_final' => $pedido->total_final,
                                              'estado' => $pedido->estado,
                                              'created_at' => $pedido->created_at
                                          ];
                                      });

            // Top vendedores del mes
            $topVendedores = $this->obtenerTopVendedores();

            // Gráfico de ventas de los últimos 7 días
            $ventasSemanales = $this->obtenerVentasSemanales();

            return response()->json([
                'success' => true,
                'data' => [
                    'stats' => $stats,
                    'pedidos_recientes' => $pedidos_recientes,
                    'topVendedores' => $topVendedores,
                    'ventasSemanales' => $ventasSemanales
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al cargar datos del dashboard',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get users data
     */
    public function users(Request $request): JsonResponse
    {
        try {
            $query = User::query();

            // Aplicar filtros
            if ($request->has('search') && $request->search) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%")
                      ->orWhere('apellidos', 'like', "%{$search}%");
                });
            }

            if ($request->has('role') && $request->role) {
                $query->where('rol', $request->role);
            }

            if ($request->has('status') && $request->status !== '') {
                $query->where('activo', (bool) $request->status);
            }

            $users = $query->orderBy('created_at', 'desc')
                          ->paginate(20);

            return response()->json([
                'success' => true,
                'data' => $users->items(),
                'pagination' => [
                    'current_page' => $users->currentPage(),
                    'last_page' => $users->lastPage(),
                    'per_page' => $users->perPage(),
                    'total' => $users->total()
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al cargar usuarios',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get products data
     */
    public function products(Request $request): JsonResponse
    {
        try {
            $query = Producto::query();

            // Aplicar filtros
            if ($request->has('search') && $request->search) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('nombre', 'like', "%{$search}%")
                      ->orWhere('descripcion', 'like', "%{$search}%");
                });
            }

            if ($request->has('categoria') && $request->categoria) {
                $query->where('categoria_id', $request->categoria);
            }

            if ($request->has('status') && $request->status !== '') {
                $query->where('activo', (bool) $request->status);
            }

            $productos = $query->orderBy('created_at', 'desc')
                              ->paginate(15);

            return response()->json([
                'success' => true,
                'data' => $productos->items(),
                'pagination' => [
                    'current_page' => $productos->currentPage(),
                    'last_page' => $productos->lastPage(),
                    'per_page' => $productos->perPage(),
                    'total' => $productos->total()
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al cargar productos',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get orders data
     */
    public function orders(Request $request): JsonResponse
    {
        try {
            $query = Pedido::query();

            // Aplicar filtros
            if ($request->has('estado') && $request->estado) {
                $query->where('estado', $request->estado);
            }

            if ($request->has('fecha_desde') && $request->fecha_desde) {
                $query->whereDate('created_at', '>=', $request->fecha_desde);
            }

            if ($request->has('fecha_hasta') && $request->fecha_hasta) {
                $query->whereDate('created_at', '<=', $request->fecha_hasta);
            }

            if ($request->has('search') && $request->search) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('numero_pedido', 'like', "%{$search}%")
                      ->orWhere('cliente_data.name', 'like', "%{$search}%")
                      ->orWhere('vendedor_data.name', 'like', "%{$search}%");
                });
            }

            $pedidos = $query->orderBy('created_at', 'desc')
                            ->paginate(20);

            return response()->json([
                'success' => true,
                'data' => $pedidos->items(),
                'pagination' => [
                    'current_page' => $pedidos->currentPage(),
                    'last_page' => $pedidos->lastPage(),
                    'per_page' => $pedidos->perPage(),
                    'total' => $pedidos->total()
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al cargar pedidos',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get commissions data
     */
    public function commissions(Request $request): JsonResponse
    {
        try {
            $query = Comision::query();

            // Aplicar filtros
            if ($request->has('estado') && $request->estado) {
                $query->where('estado', $request->estado);
            }

            if ($request->has('tipo') && $request->tipo) {
                $query->where('tipo', $request->tipo);
            }

            $comisiones = $query->orderBy('created_at', 'desc')
                               ->paginate(20);

            // Estadísticas de comisiones
            $stats = [
                'pendientes' => Comision::where('estado', 'pendiente')->sum('monto'),
                'pagadas' => Comision::where('estado', 'pagado')->sum('monto'),
                'mes_actual' => Comision::whereBetween('created_at', [
                    Carbon::now()->startOfMonth(),
                    Carbon::now()->endOfMonth()
                ])->sum('monto'),
                'total' => Comision::sum('monto')
            ];

            return response()->json([
                'success' => true,
                'data' => $comisiones->items(),
                'stats' => $stats,
                'pagination' => [
                    'current_page' => $comisiones->currentPage(),
                    'last_page' => $comisiones->lastPage(),
                    'per_page' => $comisiones->perPage(),
                    'total' => $comisiones->total()
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al cargar comisiones',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get referrals data
     */
    public function referrals(): JsonResponse
    {
        try {
            // Estadísticas de referidos
            $totalReferidos = User::whereNotNull('referido_por')->count();
            $referidosActivos = User::whereNotNull('referido_por')
                                  ->where('activo', true)
                                  ->count();
            $referidosMes = User::whereNotNull('referido_por')
                              ->whereBetween('created_at', [
                                  Carbon::now()->startOfMonth(),
                                  Carbon::now()->endOfMonth()
                              ])
                              ->count();

            // Estructura de la red (simplificada)
            $redReferidos = $this->construirRedReferidos();

            return response()->json([
                'success' => true,
                'data' => [
                    'stats' => [
                        'total_referidos' => $totalReferidos,
                        'referidos_activos' => $referidosActivos,
                        'referidos_mes' => $referidosMes
                    ],
                    'red' => $redReferidos
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al cargar datos de referidos',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get reports data
     */
    public function reports(Request $request): JsonResponse
    {
        try {
            $periodo = $request->get('periodo', 'mes');
            $fechaDesde = $request->get('fecha_desde');
            $fechaHasta = $request->get('fecha_hasta');

            // Determinar rango de fechas
            [$inicioFecha, $finFecha] = $this->determinarRangoFechas($periodo, $fechaDesde, $fechaHasta);

            // Generar reporte
            $reporte = [
                'periodo' => $periodo,
                'fecha_inicio' => $inicioFecha->format('Y-m-d'),
                'fecha_fin' => $finFecha->format('Y-m-d'),
                'resumen' => $this->generarResumenVentas($inicioFecha, $finFecha),
                'ventas_por_dia' => $this->generarVentasPorDia($inicioFecha, $finFecha),
                'top_productos' => $this->generarTopProductos($inicioFecha, $finFecha),
                'ventas_por_vendedor' => $this->generarVentasPorVendedor($inicioFecha, $finFecha)
            ];

            return response()->json([
                'success' => true,
                'data' => $reporte
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al generar reporte',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Métodos auxiliares privados
    private function obtenerTopVendedores()
    {
        $inicioMes = Carbon::now()->startOfMonth();

        return User::where('rol', 'vendedor')
                  ->where('activo', true)
                  ->get()
                  ->map(function ($vendedor) use ($inicioMes) {
                      $ventasMes = Pedido::where('vendedor_id', $vendedor->_id)
                                        ->whereBetween('created_at', [$inicioMes, Carbon::now()])
                                        ->where('estado', '!=', 'cancelado')
                                        ->sum('total_final');

                      $pedidosMes = Pedido::where('vendedor_id', $vendedor->_id)
                                         ->whereBetween('created_at', [$inicioMes, Carbon::now()])
                                         ->where('estado', '!=', 'cancelado')
                                         ->count();

                      return [
                          'vendedor' => $vendedor,
                          'ventas_mes' => $ventasMes,
                          'pedidos_mes' => $pedidosMes,
                          'promedio_pedido' => $pedidosMes > 0 ? $ventasMes / $pedidosMes : 0
                      ];
                  })
                  ->sortByDesc('ventas_mes')
                  ->take(5)
                  ->values();
    }

    private function obtenerVentasSemanales()
    {
        $ventas = [];

        for ($i = 6; $i >= 0; $i--) {
            $fecha = Carbon::now()->subDays($i);
            $ventasDia = Pedido::whereDate('created_at', $fecha)
                              ->where('estado', '!=', 'cancelado')
                              ->sum('total_final');

            $ventas[] = [
                'fecha' => $fecha->format('d/m'),
                'ventas' => $ventasDia,
                'dia' => $fecha->format('l')
            ];
        }

        return $ventas;
    }

    private function construirRedReferidos()
    {
        // Implementación simplificada - en producción sería más compleja
        return User::whereNull('referido_por')
                  ->where('rol', '!=', 'cliente')
                  ->get()
                  ->map(function ($usuario) {
                      return [
                          'id' => $usuario->_id,
                          'name' => $usuario->name,
                          'rol' => $usuario->rol,
                          'referidos' => User::where('referido_por', $usuario->_id)->count()
                      ];
                  });
    }

    private function determinarRangoFechas($periodo, $fechaDesde, $fechaHasta)
    {
        switch ($periodo) {
            case 'hoy':
                return [Carbon::today(), Carbon::today()->endOfDay()];
            case 'semana':
                return [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()];
            case 'mes':
                return [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()];
            case 'trimestre':
                return [Carbon::now()->startOfQuarter(), Carbon::now()->endOfQuarter()];
            case 'ano':
                return [Carbon::now()->startOfYear(), Carbon::now()->endOfYear()];
            case 'personalizado':
                return [
                    Carbon::parse($fechaDesde)->startOfDay(),
                    Carbon::parse($fechaHasta)->endOfDay()
                ];
            default:
                return [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()];
        }
    }

    private function generarResumenVentas($inicio, $fin)
    {
        $pedidos = Pedido::whereBetween('created_at', [$inicio, $fin])
                        ->where('estado', '!=', 'cancelado');

        return [
            'total_ventas' => $pedidos->sum('total_final'),
            'total_pedidos' => $pedidos->count(),
            'promedio_pedido' => $pedidos->avg('total_final') ?? 0,
            'productos_vendidos' => $pedidos->get()->sum(function ($pedido) {
                return collect($pedido->detalles)->sum('cantidad');
            })
        ];
    }

    private function generarVentasPorDia($inicio, $fin)
    {
        $ventas = [];
        $fechaActual = $inicio->copy();

        while ($fechaActual <= $fin) {
            $ventasDia = Pedido::whereDate('created_at', $fechaActual)
                              ->where('estado', '!=', 'cancelado')
                              ->sum('total_final');

            $ventas[] = [
                'fecha' => $fechaActual->format('Y-m-d'),
                'ventas' => $ventasDia
            ];

            $fechaActual->addDay();
        }

        return $ventas;
    }

    private function generarTopProductos($inicio, $fin)
    {
        // Simplificado - en producción sería más complejo con datos embebidos
        return [];
    }

    private function generarVentasPorVendedor($inicio, $fin)
    {
        return User::where('rol', 'vendedor')
                  ->get()
                  ->map(function ($vendedor) use ($inicio, $fin) {
                      $ventas = Pedido::where('vendedor_id', $vendedor->_id)
                                     ->whereBetween('created_at', [$inicio, $fin])
                                     ->where('estado', '!=', 'cancelado')
                                     ->sum('total_final');

                      return [
                          'vendedor' => $vendedor->name,
                          'ventas' => $ventas
                      ];
                  })
                  ->sortByDesc('ventas')
                  ->values();
    }

    /**
     * Get configuration data
     */
    public function config(): JsonResponse
    {
        try {
            // Configuración general del sistema
            $config = [
                'empresa' => [
                    'nombre' => config('app.name', 'Arepa la Llanerita'),
                    'email' => config('mail.from.address', 'admin@empresa.com'),
                    'telefono' => '+57 300 123 4567'
                ],
                'comisiones' => [
                    'venta' => 5.0,
                    'referido' => 2.0,
                    'minimo_retiro' => 50000
                ],
                'sistema' => [
                    'version' => '1.0.0',
                    'laravel_version' => app()->version(),
                    'php_version' => PHP_VERSION,
                    'database' => 'MongoDB',
                    'cache_driver' => config('cache.default'),
                    'timezone' => config('app.timezone')
                ]
            ];

            return response()->json([
                'success' => true,
                'data' => $config
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al cargar configuración',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update configuration
     */
    public function updateConfig(Request $request): JsonResponse
    {
        try {
            // En un sistema real, esto actualizaría archivos de configuración
            // Por ahora solo simulamos la respuesta

            return response()->json([
                'success' => true,
                'message' => 'Configuración actualizada correctamente'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar configuración',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get backups list
     */
    public function backups(): JsonResponse
    {
        try {
            // Simulación de respaldos disponibles
            $backups = [
                [
                    'id' => 1,
                    'nombre' => 'backup_' . now()->format('Y-m-d_H-i') . '.zip',
                    'tamaño' => '15.2 MB',
                    'fecha' => now()->format('d/m/Y H:i'),
                    'tipo' => 'Completo',
                    'created_at' => now()
                ],
                [
                    'id' => 2,
                    'nombre' => 'backup_' . now()->subDays(1)->format('Y-m-d_H-i') . '.zip',
                    'tamaño' => '14.8 MB',
                    'fecha' => now()->subDays(1)->format('d/m/Y H:i'),
                    'tipo' => 'Base de Datos',
                    'created_at' => now()->subDays(1)
                ]
            ];

            return response()->json([
                'success' => true,
                'data' => $backups
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al cargar respaldos',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Create backup
     */
    public function createBackup(Request $request): JsonResponse
    {
        try {
            // Simulación de creación de respaldo

            return response()->json([
                'success' => true,
                'message' => 'Respaldo creado correctamente',
                'data' => [
                    'nombre' => 'backup_' . now()->format('Y-m-d_H-i') . '.zip',
                    'fecha' => now()->format('d/m/Y H:i')
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al crear respaldo',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get system logs
     */
    public function logs(Request $request): JsonResponse
    {
        try {
            // Simulación de logs del sistema
            $logs = [
                [
                    'level' => 'info',
                    'message' => 'Usuario admin@empresa.com inició sesión',
                    'timestamp' => now()->subMinutes(5)->format('Y-m-d H:i:s'),
                    'context' => ['ip' => '127.0.0.1', 'user_agent' => 'Mozilla/5.0...']
                ],
                [
                    'level' => 'warning',
                    'message' => 'Producto con stock bajo: Arepa de Queso (Stock: 5)',
                    'timestamp' => now()->subMinutes(15)->format('Y-m-d H:i:s'),
                    'context' => ['producto_id' => '123', 'stock_actual' => 5]
                ],
                [
                    'level' => 'info',
                    'message' => 'Pedido #PED-2024-001 creado exitosamente',
                    'timestamp' => now()->subMinutes(30)->format('Y-m-d H:i:s'),
                    'context' => ['pedido_id' => 'PED-2024-001', 'total' => 25000]
                ],
                [
                    'level' => 'error',
                    'message' => 'Error al procesar pago: Conexión con pasarela fallida',
                    'timestamp' => now()->subHour()->format('Y-m-d H:i:s'),
                    'context' => ['payment_id' => 'PAY-123', 'error_code' => 'GATEWAY_TIMEOUT']
                ]
            ];

            // Aplicar filtros si existen
            if ($request->has('level') && $request->level) {
                $logs = array_filter($logs, function($log) use ($request) {
                    return $log['level'] === $request->level;
                });
            }

            return response()->json([
                'success' => true,
                'data' => array_values($logs)
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al cargar logs',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Clear system logs
     */
    public function clearLogs(): JsonResponse
    {
        try {
            // Simulación de limpieza de logs

            return response()->json([
                'success' => true,
                'message' => 'Logs del sistema eliminados correctamente'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al limpiar logs',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get profile data
     */
    public function profile(): JsonResponse
    {
        try {
            $user = auth()->user();

            return response()->json([
                'success' => true,
                'data' => [
                    'name' => $user->name,
                    'apellidos' => $user->apellidos ?? '',
                    'email' => $user->email,
                    'telefono' => $user->telefono ?? '',
                    'rol' => $user->rol,
                    'created_at' => $user->created_at->format('d/m/Y')
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al cargar perfil',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update profile
     */
    public function updateProfile(Request $request): JsonResponse
    {
        try {
            $user = auth()->user();

            $request->validate([
                'name' => 'required|string|max:255',
                'apellidos' => 'nullable|string|max:255',
                'email' => 'required|email|unique:users,email,' . $user->_id . ',_id',
                'telefono' => 'nullable|string|max:20'
            ]);

            $user->update([
                'name' => $request->name,
                'apellidos' => $request->apellidos,
                'email' => $request->email,
                'telefono' => $request->telefono
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Perfil actualizado correctamente'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar perfil',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update password
     */
    public function updatePassword(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'current_password' => 'required',
                'new_password' => 'required|min:8|confirmed',
                'new_password_confirmation' => 'required'
            ]);

            $user = auth()->user();

            if (!Hash::check($request->current_password, $user->password)) {
                return response()->json([
                    'success' => false,
                    'message' => 'La contraseña actual es incorrecta'
                ], 400);
            }

            $user->update([
                'password' => Hash::make($request->new_password)
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Contraseña actualizada correctamente'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar contraseña',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Clear cache
     */
    public function clearCache(): JsonResponse
    {
        try {
            // Limpiar caché de la aplicación
            \Artisan::call('cache:clear');
            \Artisan::call('config:clear');
            \Artisan::call('route:clear');
            \Artisan::call('view:clear');

            return response()->json([
                'success' => true,
                'message' => 'Caché del sistema limpiado correctamente'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al limpiar caché',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}