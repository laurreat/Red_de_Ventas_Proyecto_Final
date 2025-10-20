<?php

namespace App\Http\Controllers;

use App\Models\Notificacion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificacionController extends Controller
{
    /**
     * Vista del módulo de notificaciones
     */
    public function modulo()
    {
        $user = Auth::user();
        
        $notificaciones = Notificacion::where('user_id', $user->_id)
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        $no_leidas = Notificacion::where('user_id', $user->_id)
            ->where('leida', false)
            ->count();

        return view('cliente.notificaciones', compact('notificaciones', 'no_leidas'));
    }

    /**
     * Obtener todas las notificaciones del usuario autenticado
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        
        $query = Notificacion::where('user_id', $user->_id)
            ->orderBy('created_at', 'desc');

        // Filtrar por tipo si se especifica
        if ($request->has('tipo')) {
            $query->where('tipo', $request->tipo);
        }

        // Filtrar por leídas/no leídas
        if ($request->has('leida')) {
            $query->where('leida', (bool)$request->leida);
        }

        $notificaciones = $query->paginate($request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'notificaciones' => $notificaciones->items(),
            'pagination' => [
                'total' => $notificaciones->total(),
                'current_page' => $notificaciones->currentPage(),
                'last_page' => $notificaciones->lastPage(),
                'per_page' => $notificaciones->perPage(),
            ]
        ]);
    }

    /**
     * Obtener notificaciones no leídas (para polling)
     */
    public function noLeidas()
    {
        $user = Auth::user();
        
        $notificaciones = Notificacion::where('user_id', $user->_id)
            ->where('leida', false)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'count' => $notificaciones->count(),
            'notificaciones' => $notificaciones
        ]);
    }

    /**
     * Obtener el conteo de notificaciones no leídas
     */
    public function conteoNoLeidas()
    {
        $user = Auth::user();
        
        $count = Notificacion::where('user_id', $user->_id)
            ->where('leida', false)
            ->count();

        return response()->json([
            'success' => true,
            'count' => $count
        ]);
    }

    /**
     * Marcar una notificación como leída
     */
    public function marcarComoLeida($id)
    {
        $user = Auth::user();
        
        $notificacion = Notificacion::where('_id', $id)
            ->where('user_id', $user->_id)
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
    public function marcarTodasComoLeidas()
    {
        $user = Auth::user();
        
        Notificacion::where('user_id', $user->_id)
            ->where('leida', false)
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
        
        $notificacion = Notificacion::where('_id', $id)
            ->where('user_id', $user->_id)
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
     * Eliminar notificaciones antiguas (más de 30 días)
     */
    public function limpiarAntiguas()
    {
        $user = Auth::user();
        
        $count = Notificacion::where('user_id', $user->_id)
            ->where('created_at', '<', now()->subDays(30))
            ->delete();

        return response()->json([
            'success' => true,
            'message' => "Se eliminaron {$count} notificaciones antiguas"
        ]);
    }

    /**
     * Eliminar todas las notificaciones leídas
     */
    public function limpiarLeidas()
    {
        $user = Auth::user();
        
        $count = Notificacion::where('user_id', $user->_id)
            ->where('leida', true)
            ->delete();

        return response()->json([
            'success' => true,
            'message' => "Se eliminaron {$count} notificaciones leídas"
        ]);
    }
}
