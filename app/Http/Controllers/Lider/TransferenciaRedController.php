<?php

namespace App\Http\Controllers\Lider;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\SolicitudTransferenciaRed;
use Illuminate\Support\Facades\DB;

class TransferenciaRedController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified']);
        $this->middleware(function ($request, $next) {
            $user = Auth::user();
            if (!$user->esLider()) {
                abort(403, 'Acceso denegado. Solo líderes.');
            }
            return $next($request);
        });
    }

    /**
     * Listar solicitudes pendientes
     */
    public function index()
    {
        $user = Auth::user();

        $solicitudes = SolicitudTransferenciaRed::where('vendedor_id', $user->_id)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('lider.transferencia_red_index', compact('solicitudes'));
    }

    /**
     * Ver detalle de solicitud
     */
    public function show($id)
    {
        $user = Auth::user();

        $solicitud = SolicitudTransferenciaRed::where('_id', $id)
            ->where('vendedor_id', $user->_id)
            ->firstOrFail();

        // Obtener información de los nodos
        $nodos = User::whereIn('_id', $solicitud->nodos_ids)->get();

        return view('lider.transferencia_red_show', compact('solicitud', 'nodos'));
    }

    /**
     * Aprobar transferencia
     */
    public function aprobar(Request $request, $id)
    {
        $request->validate([
            'mensaje' => 'nullable|string|max:500'
        ]);

        $user = Auth::user();

        $solicitud = SolicitudTransferenciaRed::where('_id', $id)
            ->where('vendedor_id', $user->_id)
            ->where('estado', 'pendiente')
            ->firstOrFail();

        try {
            DB::beginTransaction();

            // Transferir toda la red
            $this->transferirRed($solicitud->nodos_ids, $user->_id);

            // Aprobar solicitud
            $solicitud->aprobar($user->_id, $request->mensaje);

            // Notificar al cliente
            \App\Models\Notificacion::create([
                'user_id' => $solicitud->cliente_id,
                'tipo' => 'transferencia_red_aprobada',
                'titulo' => 'Solicitud de transferencia aprobada',
                'mensaje' => "{$user->nombreCompleto()} ha aprobado tu solicitud. Tu red ha sido transferida exitosamente.",
                'datos' => [
                    'solicitud_id' => $solicitud->_id,
                    'total_nodos' => $solicitud->total_nodos
                ],
                'leido' => false
            ]);

            DB::commit();

            return redirect()->route('lider.transferencia-red.index')
                ->with('success', "Red de {$solicitud->total_nodos} referidos transferida exitosamente.");

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Error al transferir red: ' . $e->getMessage());
        }
    }

    /**
     * Rechazar transferencia
     */
    public function rechazar(Request $request, $id)
    {
        $request->validate([
            'motivo' => 'required|string|max:500'
        ]);

        $user = Auth::user();

        $solicitud = SolicitudTransferenciaRed::where('_id', $id)
            ->where('vendedor_id', $user->_id)
            ->where('estado', 'pendiente')
            ->firstOrFail();

        $solicitud->rechazar($user->_id, $request->motivo);

        // Notificar al cliente
        \App\Models\Notificacion::create([
            'user_id' => $solicitud->cliente_id,
            'tipo' => 'transferencia_red_rechazada',
            'titulo' => 'Solicitud de transferencia rechazada',
            'mensaje' => "{$user->nombreCompleto()} ha rechazado tu solicitud. Motivo: {$request->motivo}",
            'datos' => [
                'solicitud_id' => $solicitud->_id,
                'motivo' => $request->motivo
            ],
            'leido' => false
        ]);

        return redirect()->route('lider.transferencia-red.index')
            ->with('success', 'Solicitud rechazada correctamente.');
    }

    /**
     * Transferir toda la red recursivamente
     */
    private function transferirRed($nodosIds, $nuevoReferidorId)
    {
        foreach ($nodosIds as $nodoId) {
            $nodo = User::find($nodoId);
            if ($nodo) {
                // Cambiar el referido_por al nuevo vendedor/líder
                $nodo->referido_por = $nuevoReferidorId;
                $nodo->save();
            }
        }

        // Actualizar contador de referidos del nuevo líder
        $lider = User::find($nuevoReferidorId);
        if ($lider) {
            $lider->total_referidos = User::where('referido_por', $nuevoReferidorId)->count();
            $lider->save();
        }
    }
}
