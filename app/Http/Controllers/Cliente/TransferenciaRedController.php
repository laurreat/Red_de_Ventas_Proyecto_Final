<?php

namespace App\Http\Controllers\Cliente;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\SolicitudTransferenciaRed;

class TransferenciaRedController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified']);
    }

    /**
     * Mostrar formulario para solicitar transferencia
     */
    public function index()
    {
        $user = Auth::user();

        // Solo clientes pueden solicitar transferencia
        if (!$user->esCliente()) {
            return redirect()->back()->with('error', 'Solo los clientes pueden solicitar transferencia de red.');
        }

        // Verificar si el usuario ya tiene un referidor que sea vendedor/líder
        if ($user->referido_por) {
            $referidor = User::find($user->referido_por);
            if ($referidor && in_array($referidor->rol, ['vendedor', 'lider'])) {
                return redirect()->route('cliente.referidos.index')
                    ->with('error', 'Tu red ya está gestionada por ' . $referidor->nombreCompleto() . '. No puedes transferirla nuevamente.');
            }
        }

        // Obtener red del cliente
        $referidos = $this->obtenerRedCompleta($user->_id);
        $totalNodos = count($referidos);

        // Solicitudes previas
        $solicitudes = SolicitudTransferenciaRed::where('cliente_id', $user->_id)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('cliente.transferencia_red_index', compact('user', 'referidos', 'totalNodos', 'solicitudes'));
    }

    /**
     * Crear solicitud de transferencia
     */
    public function store(Request $request)
    {
        $request->validate([
            'codigo_vendedor' => 'required|string',
            'mensaje' => 'nullable|string|max:500'
        ]);

        $user = Auth::user();

        // Verificar que sea cliente
        if (!$user->esCliente()) {
            return redirect()->back()->with('error', 'Solo los clientes pueden solicitar transferencia de red.');
        }

        // Verificar si el usuario ya tiene un referidor que sea vendedor/líder
        if ($user->referido_por) {
            $referidor = User::find($user->referido_por);
            if ($referidor && in_array($referidor->rol, ['vendedor', 'lider'])) {
                return redirect()->back()->with('error', 'Tu red ya está gestionada por ' . $referidor->nombreCompleto() . '. No puedes transferirla nuevamente.');
            }
        }

        // Buscar vendedor/líder por código
        $vendedor = User::where('codigo_referido', $request->codigo_vendedor)
            ->whereIn('rol', ['vendedor', 'lider'])
            ->first();

        if (!$vendedor) {
            return redirect()->back()->with('error', 'Código de vendedor/líder no válido.');
        }

        // Obtener red completa
        $referidos = $this->obtenerRedCompleta($user->_id);
        $nodosIds = collect($referidos)->pluck('_id')->toArray();

        // Verificar si ya existe una solicitud pendiente
        $solicitudExistente = SolicitudTransferenciaRed::where('cliente_id', $user->_id)
            ->where('estado', 'pendiente')
            ->first();

        if ($solicitudExistente) {
            return redirect()->back()->with('error', 'Ya tienes una solicitud pendiente.');
        }

        // Crear solicitud
        $solicitud = SolicitudTransferenciaRed::create([
            'cliente_id' => $user->_id,
            'cliente_nombre' => $user->nombreCompleto(),
            'cliente_email' => $user->email,
            'cliente_codigo_referido' => $user->codigo_referido,
            'vendedor_id' => $vendedor->_id,
            'vendedor_nombre' => $vendedor->nombreCompleto(),
            'vendedor_email' => $vendedor->email,
            'total_nodos' => count($referidos),
            'nodos_ids' => $nodosIds,
            'estado' => 'pendiente',
            'mensaje_cliente' => $request->mensaje,
            'fecha_solicitud' => now()
        ]);

        // Notificar al vendedor
        \App\Models\Notificacion::create([
            'user_id' => $vendedor->_id,
            'tipo' => 'solicitud_transferencia_red',
            'titulo' => 'Nueva solicitud de transferencia de red',
            'mensaje' => "{$user->nombreCompleto()} solicita transferir su red de {$solicitud->total_nodos} referidos a tu cuenta.",
            'datos' => [
                'solicitud_id' => $solicitud->_id,
                'cliente_id' => $user->_id,
                'total_nodos' => $solicitud->total_nodos
            ],
            'leido' => false
        ]);

        return redirect()->back()->with('success', 'Solicitud enviada correctamente. El vendedor/líder será notificado.');
    }

    /**
     * Cancelar solicitud
     */
    public function cancelar($id)
    {
        $user = Auth::user();
        
        $solicitud = SolicitudTransferenciaRed::where('_id', $id)
            ->where('cliente_id', $user->_id)
            ->where('estado', 'pendiente')
            ->firstOrFail();

        $solicitud->cancelar();

        return redirect()->back()->with('success', 'Solicitud cancelada correctamente.');
    }

    /**
     * Obtener red completa recursivamente
     */
    private function obtenerRedCompleta($userId, &$referidos = [])
    {
        $directos = User::where('referido_por', $userId)->get();

        foreach ($directos as $referido) {
            $referidos[] = [
                '_id' => $referido->_id,
                'nombre' => $referido->nombreCompleto(),
                'email' => $referido->email,
                'nivel' => $this->calcularNivel($referido->_id)
            ];

            // Recursión para obtener sub-referidos
            $this->obtenerRedCompleta($referido->_id, $referidos);
        }

        return $referidos;
    }

    /**
     * Calcular nivel en la red
     */
    private function calcularNivel($userId, $nivel = 1)
    {
        $user = User::find($userId);
        if (!$user || !$user->referido_por) {
            return $nivel;
        }
        return $this->calcularNivel($user->referido_por, $nivel + 1);
    }
}
