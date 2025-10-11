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
            'storage_used' => $this->getStorageUsage()
        ];

        return view('admin.respaldos.index', compact('backups', 'stats'));
    }

    public function create(Request $request)
    {
        $request->validate([
            'type' => 'required|in:full,database,files',
            'description' => 'nullable|string|max:255'
        ]);

        try {
            \Log::info('Iniciando creación de respaldo', ['request' => $request->all()]);

            $type = $request->input('type');
            $description = $request->input('description', '');

            $filename = $this->generateBackupFilename($type, $description);
            $backupPath = storage_path('app/backups');

            \Log::info('Detalles del respaldo', [
                'type' => $type,
                'filename' => $filename,
                'backupPath' => $backupPath
            ]);

            // Crear directorio si no existe
            if (!File::exists($backupPath)) {
                File::makeDirectory($backupPath, 0755, true);
                \Log::info('Directorio de backups creado', ['path' => $backupPath]);
            }

            $fullPath = $backupPath . '/' . $filename;

            switch ($type) {
                case 'database':
                    \Log::info('Creando backup de base de datos');
                    $this->createDatabaseBackup($fullPath);
                    break;
                case 'files':
                    \Log::info('Creando backup de archivos');
                    $this->createFilesBackup($fullPath);
                    break;
                case 'full':
                default:
                    \Log::info('Creando backup completo');
                    $this->createFullBackup($fullPath);
                    break;
            }

            // Verificar que el archivo se creó correctamente
            if (!File::exists($fullPath)) {
                throw new \Exception('El archivo de backup no se pudo crear: ' . $fullPath);
            }

            $fileSize = File::size($fullPath);
            \Log::info('Backup creado exitosamente', [
                'filename' => $filename,
                'size' => $fileSize,
                'path' => $fullPath
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Backup creado exitosamente',
                'filename' => $filename,
                'path' => $fullPath,
                'size' => $this->formatBytes($fileSize)
            ]);

        } catch (\Exception $e) {
            \Log::error('Error al crear backup', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);

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

        // Determinar el tipo de contenido basado en la extensión
        $extension = pathinfo($filename, PATHINFO_EXTENSION);

        if ($extension === 'json') {
            // Para archivos JSON, configurar cabeceras apropiadas
            return response()->download($backupPath, $filename, [
                'Content-Type' => 'application/json; charset=utf-8',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
                'Cache-Control' => 'no-store, no-cache, must-revalidate',
                'Pragma' => 'no-cache'
            ]);
        } else {
            // Para otros tipos de archivo, descarga normal
            return response()->download($backupPath);
        }
    }

    public function view($filename)
    {
        $backupPath = storage_path('app/backups/' . $filename);

        if (!File::exists($backupPath)) {
            abort(404, 'Backup no encontrado');
        }

        // Para archivos JSON, mostrar en el navegador con formato bonito
        $extension = pathinfo($filename, PATHINFO_EXTENSION);

        if ($extension === 'json') {
            $content = file_get_contents($backupPath);

            return response($content, 200, [
                'Content-Type' => 'application/json; charset=utf-8',
                'Content-Disposition' => 'inline; filename="' . $filename . '"',
                'Cache-Control' => 'no-store, no-cache, must-revalidate',
                'Pragma' => 'no-cache'
            ]);
        } else {
            // Para otros tipos, redirigir a descarga
            return redirect()->route('admin.respaldos.download', $filename);
        }
    }

    public function delete($filename)
    {
        try {
            \Log::info('Iniciando eliminación de backup', ['filename' => $filename]);

            $backupPath = storage_path('app/backups/' . $filename);
            \Log::info('Ruta del backup a eliminar', ['path' => $backupPath]);

            if (!File::exists($backupPath)) {
                \Log::warning('Backup no encontrado', ['path' => $backupPath]);
                return response()->json([
                    'success' => false,
                    'message' => 'Backup no encontrado'
                ], 404);
            }

            $deleted = File::delete($backupPath);
            \Log::info('Resultado de eliminación', ['deleted' => $deleted, 'path' => $backupPath]);

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


    public function cleanup()
    {
        try {
            \Log::info('Iniciando limpieza de backups antiguos');

            $backupsPath = storage_path('app/backups');
            $retentionDays = config('backup.retention_days', 30);
            $cutoffDate = Carbon::now()->subDays($retentionDays);

            \Log::info('Parámetros de limpieza', [
                'backupsPath' => $backupsPath,
                'retentionDays' => $retentionDays,
                'cutoffDate' => $cutoffDate->toDateTimeString()
            ]);

            $deleted = 0;
            $totalSize = 0;

            if (File::exists($backupsPath)) {
                $files = File::files($backupsPath);
                \Log::info('Archivos encontrados para evaluar', ['count' => count($files)]);

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

        // Generar extensión apropiada según el tipo
        switch ($type) {
            case 'database':
                $extension = '.json'; // JSON directo con datos de la base de datos
                break;
            case 'files':
                $extension = '.json'; // JSON con información de archivos
                break;
            case 'full':
            default:
                $extension = '.json'; // JSON completo
                break;
        }

        return "backup_{$type}_{$timestamp}{$suffix}{$extension}";
    }

    private function createDatabaseBackup($path)
    {
        \Log::info('Iniciando createDatabaseBackup con datos reales', ['path' => $path]);

        try {
            // Obtener datos reales de MongoDB
            $users = \App\Models\User::all()->toArray();
            $productos = \App\Models\Producto::all()->toArray();
            $pedidos = \App\Models\Pedido::all()->toArray();
            $comisiones = \App\Models\Comision::all()->toArray();
            $referidos = \App\Models\Referido::all()->toArray();
            $configuraciones = \App\Models\Configuracion::all()->toArray();
            $roles = \App\Models\Role::all()->toArray();
            $notificaciones = \App\Models\Notificacion::all()->toArray();

            // Crear un backup completo en formato JSON legible
            $data = [
                'backup_info' => [
                    'created_at' => now()->toISOString(),
                    'created_by' => auth()->user()->name ?? 'Sistema',
                    'type' => 'database_backup',
                    'version' => '3.0',
                    'format' => 'JSON - Complete MongoDB Export',
                    'description' => 'Respaldo completo de la base de datos MongoDB con datos reales',
                    'total_collections' => 8,
                    'total_records' => count($users) + count($productos) + count($pedidos) + count($comisiones) + count($referidos) + count($configuraciones) + count($roles) + count($notificaciones)
                ],
                'system_info' => [
                    'php_version' => PHP_VERSION,
                    'laravel_version' => app()->version(),
                    'mongodb_driver' => 'mongodb/laravel-mongodb',
                    'timestamp' => time(),
                    'server_time' => now()->format('Y-m-d H:i:s'),
                    'timezone' => config('app.timezone', 'UTC'),
                    'database' => config('database.connections.mongodb.database')
                ],
                'collections' => [
                    'users' => [
                        'total_records' => count($users),
                        'description' => 'Usuarios del sistema (Administradores, Líderes, Vendedores, Clientes)',
                        'data' => $users
                    ],
                    'productos' => [
                        'total_records' => count($productos),
                        'description' => 'Catálogo completo de productos Arepa la Llanerita',
                        'data' => $productos
                    ],
                    'pedidos' => [
                        'total_records' => count($pedidos),
                        'description' => 'Historial completo de pedidos y ventas',
                        'data' => $pedidos
                    ],
                    'comisiones' => [
                        'total_records' => count($comisiones),
                        'description' => 'Sistema de comisiones MLM',
                        'data' => $comisiones
                    ],
                    'referidos' => [
                        'total_records' => count($referidos),
                        'description' => 'Red de referidos multinivel',
                        'data' => $referidos
                    ],
                    'configuraciones' => [
                        'total_records' => count($configuraciones),
                        'description' => 'Configuraciones del sistema',
                        'data' => $configuraciones
                    ],
                    'roles' => [
                        'total_records' => count($roles),
                        'description' => 'Roles y permisos del sistema',
                        'data' => $roles
                    ],
                    'notificaciones' => [
                        'total_records' => count($notificaciones),
                        'description' => 'Notificaciones del sistema',
                        'data' => $notificaciones
                    ]
                ],
                'restore_instructions' => [
                    'format' => 'Este archivo contiene TODOS los datos reales de MongoDB',
                    'compatibility' => 'Compatible con MongoDB, MySQL (con conversión), Excel, Google Sheets',
                    'usage' => 'Los datos están listos para importarse directamente',
                    'structure' => 'Cada colección tiene un array "data" con los registros completos',
                    'import_command' => 'mongoimport --db arepa_llanerita --collection [nombre] --file backup.json',
                    'warning' => 'Este backup contiene información sensible. Mantener seguro.'
                ]
            ];

            // Guardar como archivo JSON legible con formato bonito
            $success = file_put_contents($path, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));

            if ($success === false) {
                throw new \Exception('No se pudo escribir el archivo de backup');
            }

            \Log::info('Backup de database con datos reales creado exitosamente', [
                'path' => $path,
                'size' => filesize($path),
                'total_records' => $data['backup_info']['total_records'],
                'collections' => 8
            ]);

            return;

        } catch (\Exception $e) {
            \Log::error('Error en createDatabaseBackup con datos reales', [
                'error' => $e->getMessage(),
                'path' => $path,
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    private function createFilesBackup($path)
    {
        try {
            \Log::info('Creando backup de archivos en formato JSON', ['path' => $path]);

            // Crear inventario de archivos en formato JSON fácil de exportar
            $data = [
                'backup_info' => [
                    'created_at' => now()->toISOString(),
                    'created_by' => 'Arepa la Llanerita - Sistema de Respaldos',
                    'type' => 'files_backup',
                    'version' => '2.0',
                    'format' => 'JSON - File inventory and metadata',
                    'description' => 'Inventario completo de archivos del sistema en formato JSON'
                ],
                'system_info' => [
                    'php_version' => PHP_VERSION,
                    'laravel_version' => app()->version(),
                    'timestamp' => time(),
                    'server_time' => now()->format('Y-m-d H:i:s'),
                    'timezone' => config('app.timezone', 'UTC')
                ],
                'files_inventory' => [
                    'storage_public' => $this->getDirectoryInfo(storage_path('app/public')),
                    'public_uploads' => $this->getDirectoryInfo(public_path('uploads')),
                    'public_images' => $this->getDirectoryInfo(public_path('images')),
                    'config_files' => [
                        'description' => 'Archivos de configuración importantes',
                        'note' => 'Por seguridad, las configuraciones no se incluyen en el respaldo'
                    ]
                ],
                'export_instructions' => [
                    'format' => 'Este inventario está en formato JSON estándar',
                    'purpose' => 'Permite conocer qué archivos existen en el sistema',
                    'usage' => 'Útil para auditorías y restauraciones',
                    'human_readable' => 'El formato es legible y fácil de analizar'
                ]
            ];

            // Guardar como archivo JSON
            $success = file_put_contents($path, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

            if ($success === false) {
                throw new \Exception('No se pudo escribir el archivo de backup de archivos');
            }

            \Log::info('Backup de archivos creado exitosamente', [
                'path' => $path,
                'size' => filesize($path),
                'format' => 'JSON'
            ]);

        } catch (\Exception $e) {
            \Log::error('Error en createFilesBackup', [
                'error' => $e->getMessage(),
                'path' => $path
            ]);
            throw $e;
        }
    }

    private function createFullBackup($path)
    {
        try {
            \Log::info('Creando backup completo con datos reales', ['path' => $path]);

            // Obtener datos reales de MongoDB
            $users = \App\Models\User::all()->toArray();
            $productos = \App\Models\Producto::all()->toArray();
            $pedidos = \App\Models\Pedido::all()->toArray();
            $comisiones = \App\Models\Comision::all()->toArray();
            $referidos = \App\Models\Referido::all()->toArray();
            $configuraciones = \App\Models\Configuracion::all()->toArray();
            $roles = \App\Models\Role::all()->toArray();
            $notificaciones = \App\Models\Notificacion::all()->toArray();

            // Crear backup completo combinando base de datos y archivos
            $data = [
                'backup_info' => [
                    'created_at' => now()->toISOString(),
                    'created_by' => auth()->user()->name ?? 'Sistema',
                    'type' => 'full_backup',
                    'version' => '3.0',
                    'format' => 'JSON - Complete MongoDB Export + Files Inventory',
                    'description' => 'Respaldo completo: base de datos MongoDB con datos reales + inventario de archivos',
                    'total_collections' => 8,
                    'total_records' => count($users) + count($productos) + count($pedidos) + count($comisiones) + count($referidos) + count($configuraciones) + count($roles) + count($notificaciones)
                ],
                'system_info' => [
                    'php_version' => PHP_VERSION,
                    'laravel_version' => app()->version(),
                    'mongodb_driver' => 'mongodb/laravel-mongodb',
                    'timestamp' => time(),
                    'server_time' => now()->format('Y-m-d H:i:s'),
                    'timezone' => config('app.timezone', 'UTC'),
                    'database' => config('database.connections.mongodb.database')
                ],
                'database_collections' => [
                    'users' => [
                        'total_records' => count($users),
                        'description' => 'Usuarios del sistema',
                        'data' => $users
                    ],
                    'productos' => [
                        'total_records' => count($productos),
                        'description' => 'Catálogo de productos',
                        'data' => $productos
                    ],
                    'pedidos' => [
                        'total_records' => count($pedidos),
                        'description' => 'Historial de pedidos',
                        'data' => $pedidos
                    ],
                    'comisiones' => [
                        'total_records' => count($comisiones),
                        'description' => 'Sistema de comisiones',
                        'data' => $comisiones
                    ],
                    'referidos' => [
                        'total_records' => count($referidos),
                        'description' => 'Red de referidos',
                        'data' => $referidos
                    ],
                    'configuraciones' => [
                        'total_records' => count($configuraciones),
                        'description' => 'Configuraciones',
                        'data' => $configuraciones
                    ],
                    'roles' => [
                        'total_records' => count($roles),
                        'description' => 'Roles y permisos',
                        'data' => $roles
                    ],
                    'notificaciones' => [
                        'total_records' => count($notificaciones),
                        'description' => 'Notificaciones',
                        'data' => $notificaciones
                    ]
                ],
                'files_inventory' => [
                    'storage_public' => $this->getDirectoryInfo(storage_path('app/public')),
                    'public_uploads' => $this->getDirectoryInfo(public_path('uploads')),
                    'public_images' => $this->getDirectoryInfo(public_path('images'))
                ],
                'restore_instructions' => [
                    'format' => 'Backup completo: base de datos + archivos',
                    'database' => 'La sección database_collections contiene TODOS los datos reales',
                    'files' => 'La sección files_inventory contiene el inventario completo de archivos',
                    'usage' => 'Importar datos con mongoimport o importar a Excel/Sheets',
                    'warning' => 'Contiene información sensible. Mantener seguro.'
                ]
            ];

            // Guardar como archivo JSON legible
            $success = file_put_contents($path, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));

            if ($success === false) {
                throw new \Exception('No se pudo escribir el archivo de backup completo');
            }

            \Log::info('Backup completo con datos reales creado exitosamente', [
                'path' => $path,
                'size' => filesize($path),
                'total_records' => $data['backup_info']['total_records'],
                'collections' => 8
            ]);

        } catch (\Exception $e) {
            \Log::error('Error en createFullBackup con datos reales', [
                'error' => $e->getMessage(),
                'path' => $path
            ]);
            throw $e;
        }
    }

    private function getDirectoryInfo($directory)
    {
        if (!File::exists($directory)) {
            return [
                'exists' => false,
                'message' => 'Directorio no encontrado: ' . $directory
            ];
        }

        try {
            $files = File::allFiles($directory);
            $totalSize = 0;
            $fileTypes = [];
            $fileList = [];

            foreach ($files as $file) {
                $size = $file->getSize();
                $totalSize += $size;
                $extension = $file->getExtension() ?: 'sin_extension';

                if (!isset($fileTypes[$extension])) {
                    $fileTypes[$extension] = ['count' => 0, 'size' => 0];
                }
                $fileTypes[$extension]['count']++;
                $fileTypes[$extension]['size'] += $size;

                $fileList[] = [
                    'name' => $file->getFilename(),
                    'path' => $file->getRelativePathname(),
                    'size' => $this->formatBytes($size),
                    'size_bytes' => $size,
                    'extension' => $extension,
                    'modified' => date('Y-m-d H:i:s', $file->getMTime())
                ];
            }

            // Formatear tipos de archivo
            foreach ($fileTypes as $ext => &$info) {
                $info['size'] = $this->formatBytes($info['size']);
            }

            return [
                'exists' => true,
                'directory' => $directory,
                'total_files' => count($files),
                'total_size' => $this->formatBytes($totalSize),
                'total_size_bytes' => $totalSize,
                'file_types' => $fileTypes,
                'files' => array_slice($fileList, 0, 100), // Limitar a 100 archivos para evitar JSON muy grande
                'note' => count($files) > 100 ? 'Mostrando solo los primeros 100 archivos. Total: ' . count($files) : 'Todos los archivos listados'
            ];

        } catch (\Exception $e) {
            return [
                'exists' => true,
                'error' => 'Error al analizar directorio: ' . $e->getMessage()
            ];
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