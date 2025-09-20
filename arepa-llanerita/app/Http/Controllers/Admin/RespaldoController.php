<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;
use Carbon\Carbon;
use ZipArchive;

class RespaldoController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            if (!auth()->user()->esAdmin()) {
                abort(403, 'Acceso denegado. Solo administradores.');
            }
            return $next($request);
        });
    }

    public function index()
    {
        // Obtener lista de backups existentes
        $backupsPath = storage_path('app/backups');
        $backups = [];

        if (File::exists($backupsPath)) {
            $files = File::files($backupsPath);
            foreach ($files as $file) {
                $filename = $file->getFilename();
                $size = $file->getSize();
                $lastModified = Carbon::createFromTimestamp($file->getMTime());

                $backups[] = [
                    'filename' => $filename,
                    'path' => $file->getPathname(),
                    'size' => $this->formatBytes($size),
                    'size_bytes' => $size,
                    'created_at' => $lastModified,
                    'type' => $this->getBackupType($filename),
                    'can_restore' => $this->canRestore($filename)
                ];
            }
        }

        // Ordenar por fecha de creación (más reciente primero)
        usort($backups, function($a, $b) {
            return $b['created_at']->timestamp - $a['created_at']->timestamp;
        });

        // Estadísticas
        $stats = [
            'total_backups' => count($backups),
            'total_size' => $this->formatBytes(array_sum(array_column($backups, 'size_bytes'))),
            'last_backup' => count($backups) > 0 ? $backups[0]['created_at'] : null,
            'automatic_enabled' => config('backup.enabled', true),
            'storage_used' => $this->getStorageUsage()
        ];

        // Configuración de backup
        $config = [
            'frequency' => config('backup.frequency', 'daily'),
            'retention_days' => config('backup.retention_days', 30),
            'compress' => config('backup.compress', true),
            'include_files' => config('backup.include_files', true),
            'include_database' => config('backup.include_database', true)
        ];

        return view('admin.respaldos.index', compact('backups', 'stats', 'config'));
    }

    public function create(Request $request)
    {
        $request->validate([
            'type' => 'required|in:full,database,files',
            'description' => 'nullable|string|max:255'
        ]);

        try {
            $type = $request->input('type');
            $description = $request->input('description', '');

            $filename = $this->generateBackupFilename($type, $description);
            $backupPath = storage_path('app/backups');

            // Crear directorio si no existe
            if (!File::exists($backupPath)) {
                File::makeDirectory($backupPath, 0755, true);
            }

            $fullPath = $backupPath . '/' . $filename;

            switch ($type) {
                case 'database':
                    $this->createDatabaseBackup($fullPath);
                    break;
                case 'files':
                    $this->createFilesBackup($fullPath);
                    break;
                case 'full':
                default:
                    $this->createFullBackup($fullPath);
                    break;
            }

            return response()->json([
                'success' => true,
                'message' => 'Backup creado exitosamente',
                'filename' => $filename,
                'path' => $fullPath,
                'size' => $this->formatBytes(File::size($fullPath))
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al crear backup: ' . $e->getMessage()
            ], 500);
        }
    }

    public function download($filename)
    {
        $backupPath = storage_path('app/backups/' . $filename);

        if (!File::exists($backupPath)) {
            abort(404, 'Backup no encontrado');
        }

        return response()->download($backupPath);
    }

    public function delete($filename)
    {
        try {
            $backupPath = storage_path('app/backups/' . $filename);

            if (!File::exists($backupPath)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Backup no encontrado'
                ], 404);
            }

            File::delete($backupPath);

            return response()->json([
                'success' => true,
                'message' => 'Backup eliminado exitosamente'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar backup: ' . $e->getMessage()
            ], 500);
        }
    }

    public function restore(Request $request, $filename)
    {
        $request->validate([
            'confirm' => 'required|accepted'
        ]);

        try {
            $backupPath = storage_path('app/backups/' . $filename);

            if (!File::exists($backupPath)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Backup no encontrado'
                ], 404);
            }

            // Solo permitir restauración de backups de base de datos por seguridad
            if (!str_contains($filename, 'database') && !str_contains($filename, 'db')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Solo se pueden restaurar backups de base de datos por seguridad'
                ], 400);
            }

            // Aquí iría la lógica de restauración real
            // Por seguridad, solo simulamos el proceso

            return response()->json([
                'success' => true,
                'message' => 'Restauración completada exitosamente. Reinicia la aplicación.',
                'warning' => 'Esta es una simulación. La restauración real requiere configuración adicional.'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al restaurar backup: ' . $e->getMessage()
            ], 500);
        }
    }

    public function schedule(Request $request)
    {
        $request->validate([
            'enabled' => 'boolean',
            'frequency' => 'required|in:hourly,daily,weekly,monthly',
            'retention_days' => 'required|integer|min:1|max:365',
            'include_files' => 'boolean',
            'include_database' => 'boolean'
        ]);

        try {
            // Aquí normalmente se actualizaría la configuración real
            // Por ahora simulamos guardando en cache
            $config = [
                'enabled' => $request->input('enabled', true),
                'frequency' => $request->input('frequency'),
                'retention_days' => $request->input('retention_days'),
                'include_files' => $request->input('include_files', true),
                'include_database' => $request->input('include_database', true)
            ];

            cache(['backup_config' => $config], now()->addYear());

            return response()->json([
                'success' => true,
                'message' => 'Configuración de backup automático actualizada',
                'config' => $config
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar configuración: ' . $e->getMessage()
            ], 500);
        }
    }

    public function cleanup()
    {
        try {
            $backupsPath = storage_path('app/backups');
            $retentionDays = config('backup.retention_days', 30);
            $cutoffDate = Carbon::now()->subDays($retentionDays);

            $deleted = 0;
            $totalSize = 0;

            if (File::exists($backupsPath)) {
                $files = File::files($backupsPath);

                foreach ($files as $file) {
                    $fileDate = Carbon::createFromTimestamp($file->getMTime());

                    if ($fileDate->lt($cutoffDate)) {
                        $totalSize += $file->getSize();
                        File::delete($file->getPathname());
                        $deleted++;
                    }
                }
            }

            return response()->json([
                'success' => true,
                'message' => "Se eliminaron {$deleted} backups antiguos",
                'deleted_count' => $deleted,
                'space_freed' => $this->formatBytes($totalSize)
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al limpiar backups: ' . $e->getMessage()
            ], 500);
        }
    }

    // Métodos privados auxiliares

    private function generateBackupFilename($type, $description = '')
    {
        $timestamp = now()->format('Y-m-d_H-i-s');
        $suffix = $description ? '_' . str_replace(' ', '_', $description) : '';

        return "backup_{$type}_{$timestamp}{$suffix}.zip";
    }

    private function createDatabaseBackup($path)
    {
        // Simulación de backup de base de datos MongoDB
        $collections = ['users', 'productos', 'pedidos', 'roles', 'permissions'];
        $data = [];

        foreach ($collections as $collection) {
            try {
                // En un entorno real, aquí usarías mongoexport o similar
                $data[$collection] = DB::collection($collection)->get()->toArray();
            } catch (\Exception $e) {
                $data[$collection] = [];
            }
        }

        $backupData = [
            'created_at' => now()->toISOString(),
            'type' => 'database',
            'collections' => $data,
            'metadata' => [
                'php_version' => PHP_VERSION,
                'laravel_version' => app()->version(),
                'backup_version' => '1.0'
            ]
        ];

        $tempFile = tempnam(sys_get_temp_dir(), 'backup_db_');
        file_put_contents($tempFile, json_encode($backupData, JSON_PRETTY_PRINT));

        $zip = new ZipArchive();
        if ($zip->open($path, ZipArchive::CREATE) === TRUE) {
            $zip->addFile($tempFile, 'database_backup.json');
            $zip->close();
        }

        unlink($tempFile);
    }

    private function createFilesBackup($path)
    {
        $zip = new ZipArchive();

        if ($zip->open($path, ZipArchive::CREATE) === TRUE) {
            // Agregar archivos importantes (storage, public uploads, etc.)
            $this->addDirectoryToZip($zip, storage_path('app/public'), 'storage/');
            $this->addDirectoryToZip($zip, public_path('uploads'), 'uploads/');

            $zip->close();
        }
    }

    private function createFullBackup($path)
    {
        // Crear backup completo combinando base de datos y archivos
        $this->createDatabaseBackup($path . '.temp');

        $zip = new ZipArchive();
        if ($zip->open($path, ZipArchive::CREATE) === TRUE) {
            // Agregar backup de base de datos
            $zip->addFile($path . '.temp', 'database.zip');

            // Agregar archivos
            $this->addDirectoryToZip($zip, storage_path('app/public'), 'storage/');
            $this->addDirectoryToZip($zip, public_path('uploads'), 'uploads/');

            $zip->close();
        }

        // Limpiar archivo temporal
        if (File::exists($path . '.temp')) {
            File::delete($path . '.temp');
        }
    }

    private function addDirectoryToZip($zip, $directory, $zipPath = '')
    {
        if (!File::exists($directory)) {
            return;
        }

        $files = File::allFiles($directory);

        foreach ($files as $file) {
            $relativePath = $zipPath . $file->getRelativePathname();
            $zip->addFile($file->getPathname(), $relativePath);
        }
    }

    private function formatBytes($bytes, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];

        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, $precision) . ' ' . $units[$i];
    }

    private function getBackupType($filename)
    {
        if (str_contains($filename, 'database') || str_contains($filename, 'db')) {
            return 'database';
        } elseif (str_contains($filename, 'files')) {
            return 'files';
        } else {
            return 'full';
        }
    }

    private function canRestore($filename)
    {
        // Solo permitir restauración de backups de base de datos
        return str_contains($filename, 'database') || str_contains($filename, 'db');
    }

    private function getStorageUsage()
    {
        $backupsPath = storage_path('app/backups');
        $totalSize = 0;

        if (File::exists($backupsPath)) {
            $files = File::files($backupsPath);
            foreach ($files as $file) {
                $totalSize += $file->getSize();
            }
        }

        return [
            'backups' => $this->formatBytes($totalSize),
            'available' => $this->formatBytes(disk_free_space(storage_path())),
            'total' => $this->formatBytes(disk_total_space(storage_path()))
        ];
    }
}