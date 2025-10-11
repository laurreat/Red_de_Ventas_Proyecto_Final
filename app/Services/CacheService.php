<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;
use Carbon\Carbon;

class CacheService
{
    const DEFAULT_TTL = 3600; // 1 hour
    const SHORT_TTL = 300;    // 5 minutes
    const LONG_TTL = 86400;   // 24 hours

    const CACHE_TAGS = [
        'users' => 'users',
        'pedidos' => 'pedidos',
        'comisiones' => 'comisiones',
        'productos' => 'productos',
        'referidos' => 'referidos',
        'reportes' => 'reportes',
        'configuracion' => 'configuracion'
    ];

    /**
     * Get cached data with automatic key generation
     */
    public function remember(string $key, callable $callback, int $ttl = self::DEFAULT_TTL, array $tags = [])
    {
        $fullKey = $this->generateKey($key);

        return Cache::remember($fullKey, $ttl, $callback);
    }

    /**
     * Cache dashboard data
     */
    public function cacheDashboardData(string $userRole, string $userId, callable $callback)
    {
        $key = "dashboard.{$userRole}.{$userId}";
        return $this->remember($key, $callback, self::SHORT_TTL, ['dashboard', $userRole]);
    }

    /**
     * Cache user statistics
     */
    public function cacheUserStats(string $userId, callable $callback)
    {
        $key = "user.stats.{$userId}";
        return $this->remember($key, $callback, self::DEFAULT_TTL, ['users', 'stats']);
    }

    /**
     * Cache commission calculations
     */
    public function cacheComisionCalculation(string $fechaInicio, string $fechaFin, ?string $vendedorId, callable $callback)
    {
        $key = "comisiones.{$fechaInicio}.{$fechaFin}." . ($vendedorId ?? 'all');
        return $this->remember($key, $callback, self::DEFAULT_TTL, ['comisiones']);
    }

    /**
     * Cache referral network
     */
    public function cacheReferralNetwork(string $userId, callable $callback)
    {
        $key = "referidos.network.{$userId}";
        return $this->remember($key, $callback, self::DEFAULT_TTL, ['referidos']);
    }

    /**
     * Cache product catalog
     */
    public function cacheProductCatalog(callable $callback)
    {
        $key = "productos.catalog.active";
        return $this->remember($key, $callback, self::LONG_TTL, ['productos']);
    }

    /**
     * Cache reports
     */
    public function cacheReport(string $reportType, array $params, callable $callback)
    {
        $key = "reportes.{$reportType}." . md5(serialize($params));
        return $this->remember($key, $callback, self::DEFAULT_TTL, ['reportes']);
    }

    /**
     * Cache system configuration
     */
    public function cacheSystemConfig(callable $callback)
    {
        $key = "system.config";
        return $this->remember($key, $callback, self::LONG_TTL, ['configuracion']);
    }

    /**
     * Invalidate cache by pattern
     */
    public function invalidateByPattern(string $pattern)
    {
        $keys = $this->getKeysByPattern($pattern);
        if (!empty($keys)) {
            Cache::forget($keys);
        }
    }

    /**
     * Invalidate user-related caches
     */
    public function invalidateUserCaches(string $userId)
    {
        $this->invalidateByPattern("*user*{$userId}*");
        $this->invalidateByPattern("*dashboard*{$userId}*");
        $this->invalidateByPattern("*stats*{$userId}*");
    }

    /**
     * Invalidate commission caches
     */
    public function invalidateComisionCaches()
    {
        $this->invalidateByPattern("*comisiones*");
        $this->invalidateByPattern("*dashboard*");
    }

    /**
     * Invalidate order-related caches
     */
    public function invalidatePedidoCaches()
    {
        $this->invalidateByPattern("*pedidos*");
        $this->invalidateByPattern("*comisiones*");
        $this->invalidateByPattern("*dashboard*");
        $this->invalidateByPattern("*stats*");
    }

    /**
     * Invalidate product caches
     */
    public function invalidateProductCaches()
    {
        $this->invalidateByPattern("*productos*");
    }

    /**
     * Invalidate referral caches
     */
    public function invalidateReferralCaches(string $userId = null)
    {
        if ($userId) {
            $this->invalidateByPattern("*referidos*{$userId}*");
        } else {
            $this->invalidateByPattern("*referidos*");
        }
        $this->invalidateByPattern("*dashboard*");
    }

    /**
     * Get cache statistics
     */
    public function getCacheStats(): array
    {
        try {
            $redis = Redis::connection();
            $info = $redis->info('memory');

            return [
                'memory_used' => $info['used_memory_human'] ?? 'N/A',
                'memory_peak' => $info['used_memory_peak_human'] ?? 'N/A',
                'total_keys' => $redis->dbsize(),
                'hit_rate' => $this->calculateHitRate(),
                'expires_count' => $this->getExpiresCount()
            ];
        } catch (\Exception $e) {
            return [
                'error' => 'No se pudo obtener estadísticas de cache',
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * Warm up critical caches
     */
    public function warmUpCriticalCaches()
    {
        // System configuration
        $this->cacheSystemConfig(function() {
            return \App\Models\Configuracion::all()->pluck('valor', 'clave');
        });

        // Active products
        $this->cacheProductCatalog(function() {
            return \App\Models\Producto::where('activo', true)->get();
        });

        // Current month stats
        $fechaInicio = now()->startOfMonth()->format('Y-m-d');
        $fechaFin = now()->format('Y-m-d');

        $this->cacheComisionCalculation($fechaInicio, $fechaFin, null, function() use ($fechaInicio, $fechaFin) {
            $queryService = new OptimizedQueryService();
            return $queryService->getComisionesOptimized($fechaInicio, $fechaFin);
        });
    }

    /**
     * Clear all application caches
     */
    public function clearAllCaches(): bool
    {
        try {
            Cache::flush();
            return true;
        } catch (\Exception $e) {
            \Log::error('Error clearing caches: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Generate cache key with prefix
     */
    private function generateKey(string $key): string
    {
        $prefix = config('cache.prefix', 'arepa_llanerita');
        return "{$prefix}:{$key}";
    }

    /**
     * Get keys by pattern
     */
    private function getKeysByPattern(string $pattern): array
    {
        try {
            $redis = Redis::connection();
            return $redis->keys($this->generateKey($pattern));
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * Calculate cache hit rate
     */
    private function calculateHitRate(): string
    {
        try {
            $redis = Redis::connection();
            $info = $redis->info('stats');

            $hits = $info['keyspace_hits'] ?? 0;
            $misses = $info['keyspace_misses'] ?? 0;
            $total = $hits + $misses;

            if ($total === 0) {
                return '0%';
            }

            $hitRate = ($hits / $total) * 100;
            return number_format($hitRate, 2) . '%';
        } catch (\Exception $e) {
            return 'N/A';
        }
    }

    /**
     * Get count of keys with expiration
     */
    private function getExpiresCount(): int
    {
        try {
            $redis = Redis::connection();
            $keys = $redis->keys($this->generateKey('*'));
            $expiresCount = 0;

            foreach ($keys as $key) {
                if ($redis->ttl($key) > 0) {
                    $expiresCount++;
                }
            }

            return $expiresCount;
        } catch (\Exception $e) {
            return 0;
        }
    }
}