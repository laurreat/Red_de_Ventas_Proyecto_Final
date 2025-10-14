<?php

namespace App\Http\Controllers\Lider;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use App\Models\User;
use App\Models\Pedido;
use Carbon\Carbon;

class PerfilController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        return view('lider.perfil.index', compact('user'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'telefono' => 'nullable|string|max:20',
            'direccion' => 'nullable|string|max:500',
            'password' => 'nullable|confirmed|' . Password::defaults(),
        ]);

        $data = $request->only(['name', 'email', 'telefono', 'direccion']);

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return redirect()->back()->with('success', 'Perfil actualizado correctamente.');
    }

    /**
     * API: Obtener notificaciones en tiempo real
     */
    public function getNotificaciones()
    {
        $user = auth()->user();
        $notificaciones = [];

        // Notificaciones de nuevas ventas
        $ventasRecientes = Pedido::where('vendedor_id', $user->_id)
            ->where('created_at', '>=', Carbon::now()->subHours(24))
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        foreach ($ventasRecientes as $venta) {
            $notificaciones[] = [
                'id' => $venta->_id,
                'tipo' => 'success',
                'titulo' => 'Nueva Venta Registrada',
                'mensaje' => 'Venta de $' . number_format($venta->total_final, 0) . ' completada',
                'tiempo' => $venta->created_at->diffForHumans(),
                'leida' => false
            ];
        }

        // Notificaciones de nuevos referidos
        $nuevosReferidos = User::where('referido_por', $user->_id)
            ->where('created_at', '>=', Carbon::now()->subHours(24))
            ->orderBy('created_at', 'desc')
            ->limit(3)
            ->get();

        foreach ($nuevosReferidos as $referido) {
            $notificaciones[] = [
                'id' => $referido->_id,
                'tipo' => 'info',
                'titulo' => 'Nuevo Referido',
                'mensaje' => $referido->name . ' se ha unido a tu red',
                'tiempo' => $referido->created_at->diffForHumans(),
                'leida' => false
            ];
        }

        return response()->json([
            'success' => true,
            'notificaciones' => $notificaciones
        ]);
    }

    /**
     * API: Obtener actividad en tiempo real
     */
    public function getActividad()
    {
        $user = auth()->user();
        $actividad = [];

        // Actividad de ventas
        $ventas = Pedido::where('vendedor_id', $user->_id)
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        foreach ($ventas as $venta) {
            $actividad[] = [
                'id' => $venta->_id,
                'tipo' => 'venta',
                'descripcion' => 'Venta completada por $' . number_format($venta->total_final, 0),
                'tiempo' => $venta->created_at->diffForHumans()
            ];
        }

        // Actividad de referidos
        $referidos = User::where('referido_por', $user->_id)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        foreach ($referidos as $referido) {
            $actividad[] = [
                'id' => $referido->_id,
                'tipo' => 'referido',
                'descripcion' => 'Nuevo referido: ' . $referido->name,
                'tiempo' => $referido->created_at->diffForHumans()
            ];
        }

        // Ordenar por fecha más reciente
        usort($actividad, function($a, $b) {
            return strcmp($b['tiempo'], $a['tiempo']);
        });

        return response()->json([
            'success' => true,
            'actividad' => array_slice($actividad, 0, 15)
        ]);
    }

    /**
     * API: Obtener estadísticas del perfil
     */
    public function getEstadisticas()
    {
        $user = auth()->user();

        // Total de ventas
        $totalVentas = to_float(Pedido::where('vendedor_id', $user->_id)
            ->where('estado', '!=', 'cancelado')
            ->sum('total_final'));

        // Total de comisiones
        $totalComisiones = to_float($user->comisiones()->sum('monto'));

        // Total de referidos
        $totalReferidos = User::where('referido_por', $user->_id)->count();

        return response()->json([
            'success' => true,
            'estadisticas' => [
                'total_ventas' => $totalVentas,
                'total_comisiones' => $totalComisiones,
                'total_referidos' => $totalReferidos
            ]
        ]);
    }

    /**
     * API: Marcar notificación como leída
     */
    public function marcarNotificacionLeida($id)
    {
        // Implementar lógica para marcar como leída
        // Por ahora solo retornamos éxito

        return response()->json([
            'success' => true,
            'message' => 'Notificación marcada como leída'
        ]);
    }
}
