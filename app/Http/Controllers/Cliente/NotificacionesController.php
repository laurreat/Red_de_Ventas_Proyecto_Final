<?php

namespace App\Http\Controllers\Cliente;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Notificacion;

class NotificacionesController extends Controller
{
    /**
     * Constructor - Middleware de autenticación
     */
    public function __construct()
    {
        $this->middleware(['auth', 'verified']);
    }

    /**
     * Mostrar módulo de notificaciones
     */
    public function modulo()
    {
        $notificaciones = Notificacion::where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        $no_leidas = Notificacion::where('user_id', Auth::id())
            ->where('leida', false)
            ->count();

        return view('cliente.notificaciones', compact('notificaciones', 'no_leidas'));
    }

    /**
     * Obtener notificaciones no leídas
     */
    public function noLeidas()
    {
        $notificaciones = Notificacion::where('user_id', Auth::id())
            ->where('leida', false)
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return response()->json([
            'success' => true,
            'notificaciones' => $notificaciones,
            'count' => $notificaciones->count()
        ]);
    }

    /**
     * Obtener conteo de notificaciones no leídas
     */
    public function conteo()
    {
        $count = Notificacion::where('user_id', Auth::id())
            ->where('leida', false)
            ->count();

        return response()->json([
            'success' => true,
            'count' => $count
        ]);
    }

    /**
     * Marcar notificación como leída
     */
    public function marcarLeida($id)
    {
        $notificacion = Notificacion::where('_id', $id)
            ->where('user_id', Auth::id())
            ->first();

        if (!$notificacion) {
            return response()->json([
                'success' => false,
                'message' => 'Notificación no encontrada'
            ], 404);
        }

        $notificacion->leida = true;
        $notificacion->leida_at = now();
        $notificacion->save();

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
        $updated = Notificacion::where('user_id', Auth::id())
            ->where('leida', false)
            ->update([
                'leida' => true,
                'leida_at' => now()
            ]);

        return response()->json([
            'success' => true,
            'message' => 'Todas las notificaciones fueron marcadas como leídas',
            'updated' => $updated
        ]);
    }

    /**
     * Limpiar notificaciones leídas
     */
    public function limpiarLeidas()
    {
        $deleted = Notificacion::where('user_id', Auth::id())
            ->where('leida', true)
            ->delete();

        return response()->json([
            'success' => true,
            'message' => 'Notificaciones leídas eliminadas correctamente',
            'deleted' => $deleted
        ]);
    }

    /**
     * Eliminar notificación específica
     */
    public function eliminar($id)
    {
        $notificacion = Notificacion::where('_id', $id)
            ->where('user_id', Auth::id())
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
            'message' => 'Notificación eliminada correctamente'
        ]);
    }

    /**
     * Obtener todas las notificaciones (paginadas)
     */
    public function index(Request $request)
    {
        $query = Notificacion::where('user_id', Auth::id());

        // Filtrar por tipo si se especifica
        if ($request->has('tipo') && $request->tipo != 'all') {
            $query->where('tipo', $request->tipo);
        }

        // Filtrar por estado si se especifica
        if ($request->has('estado')) {
            if ($request->estado == 'leidas') {
                $query->where('leida', true);
            } elseif ($request->estado == 'no_leidas') {
                $query->where('leida', false);
            }
        }

        $notificaciones = $query->orderBy('created_at', 'desc')
            ->paginate($request->get('per_page', 20));

        return response()->json([
            'success' => true,
            'notificaciones' => $notificaciones
        ]);
    }
}
