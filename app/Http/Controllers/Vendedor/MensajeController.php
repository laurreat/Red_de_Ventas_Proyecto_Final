<?php

namespace App\Http\Controllers\Vendedor;

use App\Http\Controllers\Controller;
use App\Models\MensajeLider;
use Illuminate\Http\Request;

class MensajeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            if (!auth()->user()->esVendedor() && !auth()->user()->esLider() && !auth()->user()->esAdmin()) {
                abort(403, 'Acceso denegado.');
            }
            return $next($request);
        });
    }

    public function index()
    {
        $vendedor = auth()->user();

        // Obtener todos los mensajes del vendedor
        $mensajes = MensajeLider::porVendedor($vendedor->id)
            ->with('lider')
            ->orderBy('created_at', 'desc')
            ->get();

        // Contar mensajes no leídos
        $mensajesNoLeidos = $mensajes->where('leido', false)->count();

        // Estadísticas de mensajes
        $stats = [
            'total' => $mensajes->count(),
            'no_leidos' => $mensajesNoLeidos,
            'leidos' => $mensajes->where('leido', true)->count(),
            'por_tipo' => [
                'motivacion' => $mensajes->where('tipo_mensaje', 'motivacion')->count(),
                'felicitacion' => $mensajes->where('tipo_mensaje', 'felicitacion')->count(),
                'recomendacion' => $mensajes->where('tipo_mensaje', 'recomendacion')->count(),
                'alerta' => $mensajes->where('tipo_mensaje', 'alerta')->count(),
                'otro' => $mensajes->where('tipo_mensaje', 'otro')->count(),
            ]
        ];

        return view('vendedor.mensajes.index', compact('mensajes', 'stats', 'mensajesNoLeidos'));
    }

    public function marcarLeido($id)
    {
        $mensaje = MensajeLider::findOrFail($id);

        // Verificar que el mensaje pertenezca al vendedor autenticado
        if ($mensaje->vendedor_id != auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'No tienes permiso para marcar este mensaje como leído.'
            ], 403);
        }

        $mensaje->marcarComoLeido();

        return response()->json([
            'success' => true,
            'message' => 'Mensaje marcado como leído.'
        ]);
    }

    public function contarNoLeidos()
    {
        $count = MensajeLider::porVendedor(auth()->id())
            ->noLeidos()
            ->count();

        return response()->json([
            'count' => $count
        ]);
    }
}
