<?php

namespace App\Http\Controllers\Cliente;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Pedido;
use App\Models\Comision;
use Carbon\Carbon;

class ReferidosController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified']);
    }

    /**
     * Mostrar módulo de referidos
     */
    public function index()
    {
        $user = Auth::user();
        
        // Obtener referidos directos
        $referidos = User::where('referido_por', $user->_id)
            ->orderBy('created_at', 'desc')
            ->get();

        // Estadísticas de referidos
        $stats = $this->getEstadisticasReferidos($user, $referidos);

        // Link de referido
        $link_referido = $this->generarLinkReferido($user);

        return view('cliente.referidos.index', compact('user', 'referidos', 'stats', 'link_referido'));
    }

    /**
     * Obtener estadísticas de referidos
     */
    private function getEstadisticasReferidos($user, $referidos)
    {
        $inicioMes = Carbon::now()->startOfMonth();
        
        $stats = [
            'total_referidos' => $referidos->count(),
            'referidos_activos' => $referidos->where('activo', true)->count(),
            'referidos_mes' => $referidos->filter(function($ref) use ($inicioMes) {
                return $ref->created_at >= $inicioMes;
            })->count(),
            'total_comisiones' => 0,
            'comisiones_mes' => 0,
            'comisiones_pendientes' => 0,
        ];

        // Comisiones solo si el usuario tiene el campo de comisiones
        try {
            $stats['total_comisiones'] = to_float(
                Comision::where('user_id', $user->_id)
                    ->where('tipo', 'referido')
                    ->sum('monto')
            );

            $stats['comisiones_mes'] = to_float(
                Comision::where('user_id', $user->_id)
                    ->where('tipo', 'referido')
                    ->whereBetween('created_at', [$inicioMes, now()])
                    ->sum('monto')
            );

            $stats['comisiones_pendientes'] = to_float(
                Comision::where('user_id', $user->_id)
                    ->where('tipo', 'referido')
                    ->where('estado', 'pendiente')
                    ->sum('monto')
            );
        } catch (\Exception $e) {
            \Log::warning('Usuario sin acceso a comisiones: ' . $user->_id);
        }

        return $stats;
    }

    /**
     * Generar link de referido
     */
    private function generarLinkReferido($user)
    {
        $codigo = $user->codigo_referido ?? substr(md5($user->_id), 0, 8);
        
        // Si no tiene código, generarlo y guardarlo
        if (!$user->codigo_referido) {
            $user->codigo_referido = $codigo;
            $user->save();
        }

        return url('/registro?ref=' . $codigo);
    }

    /**
     * Copiar link de referido
     */
    public function copiarLink()
    {
        $user = Auth::user();
        $link = $this->generarLinkReferido($user);

        return response()->json([
            'success' => true,
            'link' => $link,
            'mensaje' => 'Link copiado al portapapeles'
        ]);
    }

    /**
     * Compartir link por WhatsApp
     */
    public function compartirWhatsApp()
    {
        $user = Auth::user();
        $link = $this->generarLinkReferido($user);
        
        $mensaje = "¡Hola! Te invito a registrarte en nuestra tienda. Usa mi link de referido: {$link}";
        $whatsappUrl = "https://wa.me/?text=" . urlencode($mensaje);

        return response()->json([
            'success' => true,
            'url' => $whatsappUrl
        ]);
    }

    /**
     * Ver detalles de un referido
     */
    public function verDetalle($id)
    {
        $user = Auth::user();
        
        $referido = User::where('_id', $id)
            ->where('referido_por', $user->_id)
            ->firstOrFail();

        // Estadísticas del referido
        $stats = [
            'total_pedidos' => Pedido::where('user_id', $referido->_id)->count(),
            'pedidos_entregados' => Pedido::where('user_id', $referido->_id)
                ->where('estado', 'entregado')
                ->count(),
            'total_comprado' => to_float(
                Pedido::where('user_id', $referido->_id)
                    ->whereIn('estado', ['confirmado', 'en_preparacion', 'enviado', 'entregado'])
                    ->sum('total_final')
            ),
        ];

        // Pedidos recientes del referido
        $pedidos_recientes = Pedido::where('user_id', $referido->_id)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return view('cliente.referidos.detalle', compact('referido', 'stats', 'pedidos_recientes'));
    }
}
