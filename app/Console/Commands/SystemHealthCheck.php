<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use App\Services\CacheService;
use Exception;

class SystemHealthCheck extends Command
{
    protected $signature = 'system:health {--fix : Attempt to fix issues automatically}';
    protected $description = 'Check system health and report issues';

    protected $errors = [];
    protected $warnings = [];
    protected $successes = [];

    public function handle()
    {
        $this->info('🏥 Iniciando verificación de salud del sistema...');
        $this->newLine();

        $fix = $this->option('fix');

        // Verificaciones
        $this->checkDatabaseConnection();
        $this->checkCacheConnection();
        $this->checkStoragePermissions();
        $this->checkEnvironmentConfig();
        $this->checkCollectionsAndIndexes();
        $this->checkSystemResources();

        if ($fix) {
            $this->attemptFixes();
        }

        $this->displayResults();

        return empty($this->errors) ? 0 : 1;
    }

    private function checkDatabaseConnection()
    {
        try {
            DB::connection('mongodb')->getPdo();
            $this->addSuccess('✅ Conexión MongoDB: OK');

            // Test query
            $count = DB::connection('mongodb')->table('users')->count();
            $this->addSuccess("✅ Consulta de prueba: {$count} usuarios encontrados");

        } catch (Exception $e) {
            $this->addError('❌ Conexión MongoDB: FALLO - ' . $e->getMessage());
        }
    }

    private function checkCacheConnection()
    {
        try {
            Cache::put('health_check', true, 60);
            $value = Cache::get('health_check');

            if ($value) {
                $this->addSuccess('✅ Sistema de cache: OK');
                Cache::forget('health_check');

                // Check cache stats
                $cacheService = new CacheService();
                $stats = $cacheService->getCacheStats();

                if (isset($stats['error'])) {
                    $this->addWarning('⚠️ Estadísticas de cache no disponibles');
                } else {
                    $this->addSuccess("✅ Cache activo - Memoria: {$stats['memory_used']}, Keys: {$stats['total_keys']}");
                }
            } else {
                $this->addError('❌ Sistema de cache: FALLO - No se pudo leer valor');
            }
        } catch (Exception $e) {
            $this->addError('❌ Sistema de cache: FALLO - ' . $e->getMessage());
        }
    }

    private function checkStoragePermissions()
    {
        $directories = [
            storage_path('logs'),
            storage_path('framework/cache'),
            storage_path('framework/sessions'),
            storage_path('framework/views'),
            public_path('uploads')
        ];

        foreach ($directories as $dir) {
            if (!is_dir($dir)) {
                mkdir($dir, 0755, true);
            }

            if (is_writable($dir)) {
                $this->addSuccess("✅ Permisos {$dir}: OK");
            } else {
                $this->addError("❌ Permisos {$dir}: FALLO - No escribible");
            }
        }
    }

    private function checkEnvironmentConfig()
    {
        $requiredVars = [
            'APP_KEY',
            'MONGODB_HOST',
            'MONGODB_DATABASE',
            'CACHE_DRIVER',
            'MAIL_MAILER'
        ];

        foreach ($requiredVars as $var) {
            $value = env($var);
            if ($value) {
                $this->addSuccess("✅ Variable {$var}: Configurada");
            } else {
                $this->addError("❌ Variable {$var}: NO configurada");
            }
        }

        // Check app key
        if (strlen(env('APP_KEY')) < 32) {
            $this->addError('❌ APP_KEY: Demasiado corta');
        }

        // Check debug mode in production
        if (app()->environment('production') && env('APP_DEBUG', false)) {
            $this->addWarning('⚠️ DEBUG activado en producción');
        }
    }

    private function checkCollectionsAndIndexes()
    {
        try {
            $collections = [
                'users' => ['email', 'rol'],
                'pedidos' => ['cliente_id', 'vendedor_id', 'estado'],
                'productos' => ['nombre', 'activo'],
                'comisiones' => ['vendedor_id', 'estado'],
                'referidos' => ['referidor_id', 'codigo_referido']
            ];

            foreach ($collections as $collection => $expectedIndexes) {
                // Check if collection exists
                $exists = DB::connection('mongodb')
                    ->getCollection($collection)
                    ->countDocuments([]) >= 0;

                if ($exists) {
                    $this->addSuccess("✅ Colección {$collection}: Existe");

                    // Check indexes
                    $indexes = DB::connection('mongodb')
                        ->getCollection($collection)
                        ->listIndexes();

                    $indexNames = [];
                    foreach ($indexes as $index) {
                        $indexNames[] = array_keys($index['key'])[0] ?? 'unknown';
                    }

                    foreach ($expectedIndexes as $expectedIndex) {
                        if (in_array($expectedIndex, $indexNames)) {
                            $this->addSuccess("  ✅ Índice {$expectedIndex}: OK");
                        } else {
                            $this->addWarning("  ⚠️ Índice {$expectedIndex}: FALTANTE");
                        }
                    }
                } else {
                    $this->addError("❌ Colección {$collection}: NO existe");
                }
            }
        } catch (Exception $e) {
            $this->addError('❌ Verificación de colecciones: FALLO - ' . $e->getMessage());
        }
    }

    private function checkSystemResources()
    {
        // Memory usage
        $memoryUsage = memory_get_usage(true);
        $memoryLimit = ini_get('memory_limit');

        if ($memoryUsage > 0) {
            $memoryMB = round($memoryUsage / 1024 / 1024, 2);
            $this->addSuccess("✅ Uso de memoria: {$memoryMB}MB (Límite: {$memoryLimit})");
        }

        // Disk space
        $freeSpace = disk_free_space(storage_path());
        if ($freeSpace !== false) {
            $freeSpaceGB = round($freeSpace / 1024 / 1024 / 1024, 2);
            if ($freeSpaceGB > 1) {
                $this->addSuccess("✅ Espacio en disco: {$freeSpaceGB}GB libres");
            } else {
                $this->addWarning("⚠️ Espacio en disco: Solo {$freeSpaceGB}GB libres");
            }
        }

        // PHP version
        $phpVersion = PHP_VERSION;
        if (version_compare($phpVersion, '8.2.0', '>=')) {
            $this->addSuccess("✅ PHP Version: {$phpVersion}");
        } else {
            $this->addWarning("⚠️ PHP Version: {$phpVersion} (Recomendado: 8.2+)");
        }
    }

    private function attemptFixes()
    {
        $this->info('🔧 Intentando reparaciones automáticas...');

        // Clear caches
        try {
            Cache::flush();
            $this->addSuccess('✅ Cache limpiado');
        } catch (Exception $e) {
            $this->addError('❌ No se pudo limpiar cache: ' . $e->getMessage());
        }

        // Create missing directories
        $directories = [
            storage_path('logs'),
            storage_path('framework/cache'),
            storage_path('framework/sessions'),
            storage_path('framework/views'),
            public_path('uploads')
        ];

        foreach ($directories as $dir) {
            if (!is_dir($dir)) {
                if (mkdir($dir, 0755, true)) {
                    $this->addSuccess("✅ Directorio creado: {$dir}");
                } else {
                    $this->addError("❌ No se pudo crear directorio: {$dir}");
                }
            }
        }
    }

    private function addSuccess($message)
    {
        $this->successes[] = $message;
        $this->line($message);
    }

    private function addWarning($message)
    {
        $this->warnings[] = $message;
        $this->warn($message);
    }

    private function addError($message)
    {
        $this->errors[] = $message;
        $this->error($message);
    }

    private function displayResults()
    {
        $this->newLine();
        $this->line('📊 <fg=cyan>RESUMEN DE VERIFICACIÓN</fg=cyan>');
        $this->line(str_repeat('=', 50));

        $this->line('<fg=green>✅ Éxitos: ' . count($this->successes) . '</fg=green>');
        $this->line('<fg=yellow>⚠️  Advertencias: ' . count($this->warnings) . '</fg=yellow>');
        $this->line('<fg=red>❌ Errores: ' . count($this->errors) . '</fg=red>');

        $this->newLine();

        if (empty($this->errors) && empty($this->warnings)) {
            $this->info('🎉 ¡Sistema completamente saludable!');
        } elseif (empty($this->errors)) {
            $this->warn('⚠️ Sistema funcional con advertencias menores');
        } else {
            $this->error('🚨 Sistema con errores críticos que requieren atención');

            $this->newLine();
            $this->line('<fg=cyan>Recomendaciones:</fg=cyan>');
            $this->line('• Ejecuta: php artisan system:health --fix');
            $this->line('• Revisa la configuración del .env');
            $this->line('• Verifica permisos de directorios');
            $this->line('• Contacta al administrador del sistema');
        }
    }
}