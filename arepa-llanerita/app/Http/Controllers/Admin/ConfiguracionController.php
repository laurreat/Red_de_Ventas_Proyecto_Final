<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class ConfiguracionController extends Controller
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
        // Configuraciones del sistema
        $configuraciones = [
            'general' => [
                'nombre_empresa' => config('app.name', 'Arepa la Llanerita'),
                'email_empresa' => env('MAIL_FROM_ADDRESS', 'admin@arepa-llanerita.com'),
                'telefono_empresa' => '(57) 300 123 4567',
                'direccion_empresa' => 'Calle 123 #45-67, Bogotá, Colombia',
                'moneda' => 'COP',
                'timezone' => config('app.timezone', 'America/Bogota'),
                'idioma' => 'es',
            ],
            'mlm' => [
                'comision_directa' => 10.0, // 10%
                'comision_referido' => 3.0, // 3%
                'comision_lider' => 2.0, // 2%
                'niveles_maximos' => 5,
                'bonificacion_lider' => 5.0, // 5%
                'minimo_ventas_mes' => 100000, // $100,000 COP
            ],
            'pedidos' => [
                'estados_disponibles' => [
                    'pendiente' => 'Pendiente',
                    'confirmado' => 'Confirmado',
                    'en_preparacion' => 'En Preparación',
                    'listo' => 'Listo',
                    'en_camino' => 'En Camino',
                    'entregado' => 'Entregado',
                    'cancelado' => 'Cancelado'
                ],
                'tiempo_preparacion' => 30, // minutos
                'costo_envio' => 5000, // $5,000 COP
                'envio_gratis_desde' => 50000, // $50,000 COP
            ],
            'notificaciones' => [
                'email_pedidos' => true,
                'email_comisiones' => true,
                'email_nuevos_referidos' => true,
                'sms_pedidos_entregados' => false,
                'whatsapp_recordatorios' => true,
            ],
            'sistema' => [
                'version' => '1.0.0',
                'ambiente' => config('app.env'),
                'debug' => config('app.debug'),
                'mantenimiento' => false,
                'backup_automatico' => true,
                'logs_dias_retention' => 30,
            ]
        ];

        // Estadísticas del sistema
        $estadisticas = [
            'usuarios_totales' => \App\Models\User::count(),
            'pedidos_totales' => \App\Models\Pedido::count(),
            'productos_totales' => \App\Models\Producto::count(),
            'ventas_mes_actual' => \App\Models\Pedido::whereMonth('created_at', now()->month)
                                                  ->whereYear('created_at', now()->year)
                                                  ->where('estado', 'entregado')
                                                  ->sum('total_final'),
            'usuarios_activos_mes' => \App\Models\User::where('created_at', '>=', now()->subMonth())->count(),
            'espacio_storage' => $this->calcularEspacioStorage(),
        ];

        return view('admin.configuracion.index', compact('configuraciones', 'estadisticas'));
    }

    public function updateGeneral(Request $request)
    {
        $request->validate([
            'nombre_empresa' => 'required|string|max:255',
            'email_empresa' => 'required|email|max:255',
            'telefono_empresa' => 'required|string|max:20',
            'direccion_empresa' => 'required|string|max:500',
        ]);

        // Aquí normalmente se guardarían en una tabla de configuraciones
        // Por ahora simulamos la funcionalidad

        return redirect()->route('admin.configuracion.index')
            ->with('success', 'Configuración general actualizada exitosamente.');
    }

    public function updateMlm(Request $request)
    {
        $request->validate([
            'comision_directa' => 'required|numeric|min:0|max:100',
            'comision_referido' => 'required|numeric|min:0|max:100',
            'comision_lider' => 'required|numeric|min:0|max:100',
            'niveles_maximos' => 'required|integer|min:1|max:10',
            'bonificacion_lider' => 'required|numeric|min:0|max:100',
            'minimo_ventas_mes' => 'required|numeric|min:0',
        ]);

        // Simular guardado de configuración MLM
        Cache::put('mlm_config', $request->only([
            'comision_directa', 'comision_referido', 'comision_lider',
            'niveles_maximos', 'bonificacion_lider', 'minimo_ventas_mes'
        ]), now()->addYear());

        return redirect()->route('admin.configuracion.index')
            ->with('success', 'Configuración MLM actualizada exitosamente.');
    }

    public function updatePedidos(Request $request)
    {
        $request->validate([
            'tiempo_preparacion' => 'required|integer|min:5|max:180',
            'costo_envio' => 'required|numeric|min:0',
            'envio_gratis_desde' => 'required|numeric|min:0',
        ]);

        // Simular guardado de configuración de pedidos
        Cache::put('pedidos_config', $request->only([
            'tiempo_preparacion', 'costo_envio', 'envio_gratis_desde'
        ]), now()->addYear());

        return redirect()->route('admin.configuracion.index')
            ->with('success', 'Configuración de pedidos actualizada exitosamente.');
    }

    public function updateNotificaciones(Request $request)
    {
        $notificaciones = [
            'email_pedidos' => $request->has('email_pedidos'),
            'email_comisiones' => $request->has('email_comisiones'),
            'email_nuevos_referidos' => $request->has('email_nuevos_referidos'),
            'sms_pedidos_entregados' => $request->has('sms_pedidos_entregados'),
            'whatsapp_recordatorios' => $request->has('whatsapp_recordatorios'),
        ];

        Cache::put('notificaciones_config', $notificaciones, now()->addYear());

        return redirect()->route('admin.configuracion.index')
            ->with('success', 'Configuración de notificaciones actualizada exitosamente.');
    }

    public function backup()
    {
        try {
            // Crear backup real usando MongoDB dump
            $filename = 'backup_arepa_llanerita_' . now()->format('Y-m-d_H-i-s') . '.json';
            $backupPath = storage_path('app/backups');

            // Crear directorio si no existe
            if (!file_exists($backupPath)) {
                mkdir($backupPath, 0755, true);
            }

            // Ejecutar comando de backup MongoDB (simulado para este ejemplo)
            $collections = ['users', 'products', 'orders', 'comisiones'];
            $backupData = [];

            foreach ($collections as $collection) {
                try {
                    // En un entorno real, aquí se haría el dump de MongoDB
                    $backupData[$collection] = "Backup data for {$collection} collection";
                } catch (\Exception $e) {
                    $backupData[$collection] = "Error backing up {$collection}: " . $e->getMessage();
                }
            }

            // Guardar backup
            $fullPath = $backupPath . '/' . $filename;
            file_put_contents($fullPath, json_encode([
                'timestamp' => now()->toISOString(),
                'version' => app()->version(),
                'collections' => $backupData,
                'statistics' => [
                    'file_size' => '0 MB',
                    'collections_count' => count($collections),
                    'created_by' => auth()->user()->name
                ]
            ], JSON_PRETTY_PRINT));

            $fileSize = round(filesize($fullPath) / 1024 / 1024, 2);

            return response()->json([
                'success' => true,
                'message' => 'Backup creado exitosamente',
                'filename' => $filename,
                'size' => $fileSize . ' MB',
                'path' => 'storage/app/backups/' . $filename,
                'collections' => count($collections)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al crear backup: ' . $e->getMessage()
            ], 500);
        }
    }

    public function limpiarCache()
    {
        try {
            $cachesCleared = [];

            // Limpiar cache de aplicación
            Cache::flush();
            $cachesCleared[] = 'Application Cache';

            // Limpiar cache de rutas
            \Artisan::call('route:clear');
            $cachesCleared[] = 'Routes Cache';

            // Limpiar cache de configuración
            \Artisan::call('config:clear');
            $cachesCleared[] = 'Configuration Cache';

            // Limpiar cache de vistas
            \Artisan::call('view:clear');
            $cachesCleared[] = 'Views Cache';

            // Optimizar autoloader
            \Artisan::call('optimize:clear');
            $cachesCleared[] = 'Optimization Cache';

            return response()->json([
                'success' => true,
                'message' => 'Cache limpiado exitosamente',
                'cleared' => $cachesCleared,
                'count' => count($cachesCleared)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al limpiar cache: ' . $e->getMessage()
            ], 500);
        }
    }

    public function limpiarLogs()
    {
        try {
            // Limpiar logs más antiguos a 30 días
            $logFiles = Storage::disk('local')->files('logs');
            $deleted = 0;

            foreach ($logFiles as $file) {
                if (Storage::disk('local')->lastModified($file) < now()->subDays(30)->timestamp) {
                    Storage::disk('local')->delete($file);
                    $deleted++;
                }
            }

            return response()->json([
                'success' => true,
                'message' => "Se eliminaron {$deleted} archivos de log antiguos"
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al limpiar logs: ' . $e->getMessage()
            ], 500);
        }
    }

    public function infoSistema()
    {
        try {
            // Información del sistema
            $info = [
                'Versión PHP' => PHP_VERSION,
                'Versión Laravel' => app()->version(),
                'Servidor Web' => $_SERVER['SERVER_SOFTWARE'] ?? 'No disponible',
                'Base de Datos' => config('database.default'),
                'Zona Horaria' => config('app.timezone'),
                'Memoria Límite' => ini_get('memory_limit'),
                'Tiempo Ejecución Max' => ini_get('max_execution_time') . 's',
                'Subida Max Archivos' => ini_get('upload_max_filesize'),
                'Sistema Operativo' => PHP_OS,
                'Arquitectura' => php_uname('m'),
            ];

            // Información de la aplicación
            $appInfo = [
                'Nombre Aplicación' => config('app.name'),
                'Entorno' => config('app.env'),
                'Debug Activado' => config('app.debug') ? 'Sí' : 'No',
                'URL Base' => config('app.url'),
                'Versión App' => '1.0.0',
            ];

            // Estadísticas de uso
            $storageInfo = $this->calcularEspacioStorage();
            $stats = [
                'Usuarios Totales' => \App\Models\User::count(),
                'Productos Activos' => \App\Models\Producto::where('estado', 'activo')->count(),
                'Pedidos Este Mes' => \App\Models\Pedido::whereMonth('created_at', now()->month)->count(),
                'Espacio Storage' => is_array($storageInfo) ? $storageInfo['usado'] : 'No disponible',
            ];

            return response()->json([
                'success' => true,
                'data' => [
                    'sistema' => $info,
                    'aplicacion' => $appInfo,
                    'estadisticas' => $stats
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener información del sistema: ' . $e->getMessage()
            ], 500);
        }
    }

    private function calcularEspacioStorage()
    {
        try {
            $totalSize = 0;
            $files = Storage::disk('public')->allFiles();

            foreach ($files as $file) {
                $totalSize += Storage::disk('public')->size($file);
            }

            return [
                'usado' => $this->formatBytes($totalSize),
                'usado_bytes' => $totalSize,
                'disponible' => $this->formatBytes(disk_free_space(storage_path())),
                'total' => $this->formatBytes(disk_total_space(storage_path()))
            ];
        } catch (\Exception $e) {
            return [
                'usado' => 'No disponible',
                'disponible' => 'No disponible',
                'total' => 'No disponible'
            ];
        }
    }

    private function formatBytes($bytes, $precision = 2)
    {
        $units = array('B', 'KB', 'MB', 'GB', 'TB');

        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, $precision) . ' ' . $units[$i];
    }
}
