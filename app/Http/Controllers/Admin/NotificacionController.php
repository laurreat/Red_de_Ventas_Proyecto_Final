<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Notificacion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificacionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Mostrar todas las notificaciones del usuario
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $userId = (string) ($user->_id ?? $user->id);
        $filter = $request->get('filter', 'all'); // all, unread, read
        $tipo = $request->get('tipo', '');

        $query = Notificacion::where('user_id', $userId);

        // Filtrar por estado
        if ($filter === 'unread') {
            $query->noLeidas();
        } elseif ($filter === 'read') {
            $query->leidas();
        }

        // Filtrar por tipo
        if ($tipo) {
            $query->porTipo($tipo);
        }

        $notificaciones = $query->orderBy('created_at', 'desc')->paginate(20);

        // Estadísticas
        $stats = [
            'total' => Notificacion::where('user_id', $userId)->count(),
            'no_leidas' => Notificacion::where('user_id', $userId)->noLeidas()->count(),
            'leidas' => Notificacion::where('user_id', $userId)->leidas()->count(),
        ];

        // Tipos de notificaciones disponibles
        $tipos = Notificacion::where('user_id', $userId)
            ->select('tipo')
            ->distinct()
            ->pluck('tipo');

        return view('admin.notificaciones.index', compact(
            'notificaciones',
            'stats',
            'tipos',
            'filter',
            'tipo'
        ));
    }

    /**
     * Mostrar detalles de una notificación
     */
    public function show($id)
    {
        $user = Auth::user();
        $userId = (string) ($user->_id ?? $user->id);

        $notificacion = Notificacion::where('user_id', $userId)
            ->where('_id', $id)
            ->first();

        if (!$notificacion) {
            abort(404, 'Notificación no encontrada');
        }

        return view('admin.notificaciones.show', compact('notificacion'));
    }

    /**
     * Obtener notificaciones para el dropdown del navbar (AJAX)
     */
    public function dropdown()
    {
        $user = Auth::user();
        $userId = (string) ($user->_id ?? $user->id);

        $notificaciones = Notificacion::where('user_id', $userId)
            ->noLeidas()
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        $total_no_leidas = Notificacion::where('user_id', $userId)->noLeidas()->count();

        return response()->json([
            'success' => true,
            'notificaciones' => $notificaciones,
            'total_no_leidas' => $total_no_leidas
        ]);
    }

    /**
     * Marcar una notificación como leída
     */
    public function marcarLeida($id)
    {
        $user = Auth::user();
        $userId = (string) ($user->_id ?? $user->id);

        $notificacion = Notificacion::where('user_id', $userId)
            ->where('_id', $id)
            ->first();

        if (!$notificacion) {
            return response()->json([
                'success' => false,
                'message' => 'Notificación no encontrada'
            ], 404);
        }

        $notificacion->marcarComoLeida();

        return response()->json([
            'success' => true,
            'message' => 'Notificación marcada como leída'
        ]);
    }

    /**
     * Marcar todas las notificaciones como leídas
     */
    public function marcarTodasLeidas()
    {
        $user = Auth::user();
        $userId = (string) ($user->_id ?? $user->id);

        Notificacion::where('user_id', $userId)
            ->noLeidas()
            ->update([
                'leida' => true,
                'fecha_lectura' => now()
            ]);

        return response()->json([
            'success' => true,
            'message' => 'Todas las notificaciones han sido marcadas como leídas'
        ]);
    }

    /**
     * Eliminar una notificación
     */
    public function eliminar($id)
    {
        $user = Auth::user();
        $userId = (string) ($user->_id ?? $user->id);

        $notificacion = Notificacion::where('user_id', $userId)
            ->where('_id', $id)
            ->first();

        if (!$notificacion) {
            return response()->json([
                'success' => false,
                'message' => 'Notificación no encontrada'
            ], 404);
        }

        $notificacion->delete();

        return response()->json([
            'success' => true,
            'message' => 'Notificación eliminada exitosamente'
        ]);
    }

    /**
     * Eliminar todas las notificaciones leídas
     */
    public function limpiarLeidas()
    {
        $user = Auth::user();
        $userId = (string) ($user->_id ?? $user->id);

        $count = Notificacion::where('user_id', $userId)
            ->leidas()
            ->count();

        Notificacion::where('user_id', $userId)
            ->leidas()
            ->delete();

        return response()->json([
            'success' => true,
            'message' => "Se eliminaron {$count} notificaciones leídas"
        ]);
    }

    /**
     * Limpiar notificaciones expiradas (para usar en cron)
     */
    public function limpiarExpiradas()
    {
        $count = Notificacion::where('fecha_expiracion', '<', now())
            ->whereNotNull('fecha_expiracion')
            ->count();

        Notificacion::where('fecha_expiracion', '<', now())
            ->whereNotNull('fecha_expiracion')
            ->delete();

        return response()->json([
            'success' => true,
            'message' => "Se eliminaron {$count} notificaciones expiradas"
        ]);
    }

    /**
     * Obtener el conteo de notificaciones no leídas (para AJAX)
     */
    public function contarNoLeidas()
    {
        $user = Auth::user();
        $userId = (string) ($user->_id ?? $user->id);

        $count = Notificacion::where('user_id', $userId)->noLeidas()->count();

        return response()->json([
            'success' => true,
            'count' => $count
        ]);
    }

    /**
     * Crear una nueva notificación (método estático también disponible)
     */
    public static function crear($user_id, $tipo, $titulo, $mensaje, $data = [], $prioridad = 'normal', $fecha_expiracion = null)
    {
        return Notificacion::create([
            'user_id' => $user_id,
            'tipo' => $tipo,
            'titulo' => $titulo,
            'mensaje' => $mensaje,
            'datos_adicionales' => $data,
            'prioridad' => $prioridad,
            'fecha_expiracion' => $fecha_expiracion,
            'leida' => false
        ]);
    }

    /**
     * Enviar notificación a múltiples usuarios por rol
     */
    public static function enviarPorRol($rol, $tipo, $titulo, $mensaje, $data = [], $prioridad = 'normal')
    {
        $usuarios = \App\Models\User::where('rol', $rol)->where('activo', true)->get();

        foreach ($usuarios as $usuario) {
            self::crear($usuario->id, $tipo, $titulo, $mensaje, $data, $prioridad);
        }

        return $usuarios->count();
    }

    /**
     * Enviar notificación a administradores
     */
    public static function enviarAAdministradores($tipo, $titulo, $mensaje, $data = [], $prioridad = 'normal')
    {
        return self::enviarPorRol('administrador', $tipo, $titulo, $mensaje, $data, $prioridad);
    }

    /**
     * Crear notificaciones de prueba para testing
     */
    public function crearPruebas()
    {
        $user = Auth::user();

        $count = \App\Services\NotificationService::crearNotificacionesPrueba($user->id);

        return response()->json([
            'success' => true,
            'message' => "Se crearon {$count} notificaciones de prueba exitosamente"
        ]);
    }
}
