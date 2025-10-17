<?php

namespace App\Http\Controllers\Vendedor;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Pedido;
use App\Models\Comision;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class PerfilController extends Controller
{
    public function index()
    {
        $vendedor = Auth::user();

        // Estadísticas del perfil
        $stats = $this->calcularEstadisticasPerfil($vendedor);

        // Actividad reciente
        $actividadReciente = $this->obtenerActividadReciente($vendedor);

        return view('vendedor.perfil.index', compact('vendedor', 'stats', 'actividadReciente'));
    }

    public function edit()
    {
        $vendedor = Auth::user();
        return view('vendedor.perfil.edit', compact('vendedor'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . Auth::id(),
            'telefono' => 'nullable|string|max:20',
            'direccion' => 'nullable|string|max:500',
            'ciudad' => 'nullable|string|max:100',
            'fecha_nacimiento' => 'nullable|date',
            'biografia' => 'nullable|string|max:1000',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $vendedor = Auth::user();

        $data = $request->only([
            'name', 'email', 'telefono', 'direccion',
            'ciudad', 'fecha_nacimiento', 'biografia'
        ]);

        // Manejar avatar
        if ($request->hasFile('avatar')) {
            // Eliminar avatar anterior si existe
            if ($vendedor->avatar) {
                Storage::disk('public')->delete($vendedor->avatar);
            }

            $avatarPath = $request->file('avatar')->store('avatars', 'public');
            $data['avatar'] = $avatarPath;
        }

        $vendedor->update($data);

        return redirect()->route('vendedor.perfil.index')
                        ->with('success', 'Perfil actualizado exitosamente.');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $vendedor = Auth::user();

        if (!Hash::check($request->current_password, $vendedor->password)) {
            return redirect()->back()
                           ->with('error', 'La contraseña actual no es correcta.');
        }

        $vendedor->update([
            'password' => Hash::make($request->password)
        ]);

        return redirect()->back()
                        ->with('success', 'Contraseña actualizada exitosamente.');
    }

    public function configuracion()
    {
        $vendedor = Auth::user();

        // Configuraciones del vendedor (por ahora simuladas)
        $configuraciones = [
            'notificaciones_email' => true,
            'notificaciones_push' => true,
            'mostrar_telefono_publico' => false,
            'recibir_ofertas' => true,
            'tema_dashboard' => 'claro',
            'zona_horaria' => 'America/Bogota'
        ];

        return view('vendedor.perfil.configuracion', compact('vendedor', 'configuraciones'));
    }

    public function updateConfiguracion(Request $request)
    {
        $request->validate([
            'notificaciones_email' => 'boolean',
            'notificaciones_push' => 'boolean',
            'mostrar_telefono_publico' => 'boolean',
            'recibir_ofertas' => 'boolean',
            'tema_dashboard' => 'in:claro,oscuro',
            'zona_horaria' => 'string'
        ]);

        // Aquí guardarías las configuraciones en la base de datos
        // Por ahora solo mostramos mensaje de éxito

        return redirect()->back()
                        ->with('success', 'Configuración actualizada exitosamente.');
    }

    private function calcularEstadisticasPerfil($vendedor)
    {
        $inicioMes = Carbon::now()->startOfMonth();
        $finMes = Carbon::now()->endOfMonth();

        return [
            // Estadísticas generales
            'total_ventas' => Pedido::where('vendedor_id', $vendedor->id)->sum('total_final'),
            'total_pedidos' => Pedido::where('vendedor_id', $vendedor->id)->count(),
            'total_comisiones' => Comision::where('user_id', $vendedor->id)->sum('monto_comision'),
            'total_referidos' => User::where('referido_por', $vendedor->id)->count(),

            // Este mes
            'ventas_mes' => Pedido::where('vendedor_id', $vendedor->id)
                                 ->whereBetween('created_at', [$inicioMes, $finMes])
                                 ->sum('total_final'),
            'pedidos_mes' => Pedido::where('vendedor_id', $vendedor->id)
                                  ->whereBetween('created_at', [$inicioMes, $finMes])
                                  ->count(),

            // Métricas de rendimiento
            'promedio_venta' => Pedido::where('vendedor_id', $vendedor->id)->avg('total_final') ?? 0,
            'mejor_mes' => $this->obtenerMejorMes($vendedor),
            'dias_activo' => Carbon::parse($vendedor->created_at)->diffInDays(Carbon::now()),

            // Meta mensual
            'meta_mensual' => $vendedor->meta_mensual ?? 0,
            'porcentaje_meta' => $vendedor->meta_mensual > 0 ?
                (to_float(Pedido::where('vendedor_id', $vendedor->id)
                       ->whereBetween('created_at', [$inicioMes, $finMes])
                       ->sum('total_final')) / to_float($vendedor->meta_mensual)) * 100 : 0
        ];
    }

    private function obtenerMejorMes($vendedor)
    {
        $mejorMes = Pedido::where('vendedor_id', $vendedor->id)
                         ->selectRaw('YEAR(created_at) as year, MONTH(created_at) as month, SUM(total_final) as total')
                         ->groupBy('year', 'month')
                         ->orderBy('total', 'desc')
                         ->first();

        if ($mejorMes) {
            return [
                'fecha' => Carbon::createFromDate($mejorMes->year, $mejorMes->month, 1)->format('F Y'),
                'total' => $mejorMes->total
            ];
        }

        return null;
    }

    private function obtenerActividadReciente($vendedor)
    {
        $actividades = [];

        // Últimos pedidos
        $ultimosPedidos = Pedido::where('vendedor_id', $vendedor->id)
                               ->with('cliente')
                               ->orderBy('created_at', 'desc')
                               ->limit(5)
                               ->get();

        foreach ($ultimosPedidos as $pedido) {
            $actividades[] = [
                'tipo' => 'pedido',
                'descripcion' => "Nuevo pedido de {$pedido->cliente->name}",
                'monto' => $pedido->total_final,
                'fecha' => $pedido->created_at,
                'icono' => 'bi-cart-check',
                'color' => 'success'
            ];
        }

        // Últimas comisiones
        $ultimasComisiones = Comision::where('user_id', $vendedor->id)
                                   ->orderBy('created_at', 'desc')
                                   ->limit(5)
                                   ->get();

        foreach ($ultimasComisiones as $comision) {
            $actividades[] = [
                'tipo' => 'comision',
                'descripcion' => "Comisión por {$comision->tipo_comision}",
                'monto' => $comision->monto_comision,
                'fecha' => $comision->created_at,
                'icono' => 'bi-cash-coin',
                'color' => 'info'
            ];
        }

        // Nuevos referidos
        $nuevosReferidos = User::where('referido_por', $vendedor->id)
                              ->orderBy('created_at', 'desc')
                              ->limit(3)
                              ->get();

        foreach ($nuevosReferidos as $referido) {
            $actividades[] = [
                'tipo' => 'referido',
                'descripcion' => "Nuevo referido: {$referido->name}",
                'monto' => null,
                'fecha' => $referido->created_at,
                'icono' => 'bi-people',
                'color' => 'primary'
            ];
        }

        // Ordenar por fecha
        usort($actividades, function($a, $b) {
            return $b['fecha'] <=> $a['fecha'];
        });

        return array_slice($actividades, 0, 10); // Solo las 10 más recientes
    }

    public function eliminarAvatar()
    {
        $vendedor = Auth::user();

        if ($vendedor->avatar) {
            Storage::disk('public')->delete($vendedor->avatar);
            $vendedor->update(['avatar' => null]);
        }

        return redirect()->back()
                        ->with('success', 'Avatar eliminado exitosamente.');
    }

    public function exportarDatos()
    {
        $vendedor = Auth::user();

        $datos = [
            'perfil' => $vendedor->toArray(),
            'pedidos' => Pedido::where('vendedor_id', $vendedor->id)->with('cliente')->get(),
            'comisiones' => Comision::where('user_id', $vendedor->id)->get(),
            'referidos' => User::where('referido_por', $vendedor->id)->get()
        ];

        return response()->json($datos)
                        ->header('Content-Disposition', 'attachment; filename="mis_datos_vendedor.json"');
    }
}