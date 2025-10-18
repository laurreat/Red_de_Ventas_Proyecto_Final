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

        return view('vendedor.perfil-index', compact('vendedor', 'stats', 'actividadReciente'));
    }

    public function edit()
    {
        $vendedor = Auth::user();
        return view('vendedor.perfil-edit', compact('vendedor'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'name' => 'nullable|string|max:255',
            'email' => 'nullable|string|email|max:255|unique:users,email,' . Auth::id(),
            'telefono' => 'nullable|string|max:20',
            'direccion' => 'nullable|string|max:500',
            'ciudad' => 'nullable|string|max:100',
            'fecha_nacimiento' => 'nullable|date',
            'biografia' => 'nullable|string|max:1000',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'perfil_publico' => 'nullable|boolean',
            'mostrar_telefono' => 'nullable|boolean',
            'mostrar_stats' => 'nullable|boolean'
        ]);

        $vendedor = Auth::user();

        // Si es JSON (desde configuración)
        if ($request->isJson()) {
            $data = [];
            if ($request->has('perfil_publico')) {
                $data['perfil_publico'] = $request->boolean('perfil_publico');
            }
            if ($request->has('mostrar_telefono')) {
                $data['mostrar_telefono'] = $request->boolean('mostrar_telefono');
            }
            if ($request->has('mostrar_stats')) {
                $data['mostrar_stats'] = $request->boolean('mostrar_stats');
            }

            $vendedor->update($data);

            return response()->json([
                'success' => true,
                'message' => 'Configuración actualizada exitosamente.'
            ]);
        }

        // Si es form normal (desde editar perfil)
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


    private function calcularEstadisticasPerfil($vendedor)
    {
        $inicioMes = Carbon::now()->startOfMonth();
        $finMes = Carbon::now()->endOfMonth();

        // Calcular totales convirtiendo Decimal128 a float
        $totalVentas = 0;
        $totalComisiones = 0;
        $ventasMes = 0;
        $promedioVenta = 0;

        $pedidos = Pedido::where('vendedor_id', $vendedor->id)->get();
        foreach ($pedidos as $pedido) {
            $totalVentas += to_float($pedido->total_final ?? 0);
        }

        $comisiones = Comision::where('user_id', $vendedor->id)->get();
        foreach ($comisiones as $comision) {
            $totalComisiones += to_float($comision->monto_comision ?? 0);
        }

        $pedidosMes = Pedido::where('vendedor_id', $vendedor->id)
                            ->whereBetween('created_at', [$inicioMes, $finMes])
                            ->get();
        foreach ($pedidosMes as $pedido) {
            $ventasMes += to_float($pedido->total_final ?? 0);
        }

        $totalPedidos = $pedidos->count();
        if ($totalPedidos > 0) {
            $promedioVenta = $totalVentas / $totalPedidos;
        }

        $metaMensual = to_float($vendedor->meta_mensual ?? 0);
        $porcentajeMeta = 0;
        if ($metaMensual > 0) {
            $porcentajeMeta = ($ventasMes / $metaMensual) * 100;
        }

        return [
            // Estadísticas generales
            'total_ventas' => $totalVentas,
            'total_pedidos' => $totalPedidos,
            'total_comisiones' => $totalComisiones,
            'total_referidos' => User::where('referido_por', $vendedor->id)->count(),

            // Este mes
            'ventas_mes' => $ventasMes,
            'pedidos_mes' => $pedidosMes->count(),

            // Métricas de rendimiento
            'promedio_venta' => $promedioVenta,
            'mejor_mes' => $this->obtenerMejorMes($vendedor),
            'dias_activo' => Carbon::parse($vendedor->created_at)->diffInDays(Carbon::now()),

            // Meta mensual
            'meta_mensual' => $metaMensual,
            'porcentaje_meta' => $porcentajeMeta
        ];
    }

    private function obtenerMejorMes($vendedor)
    {
        // Para MongoDB, procesamos los pedidos manualmente
        $pedidos = Pedido::where('vendedor_id', $vendedor->id)->get();
        
        if ($pedidos->isEmpty()) {
            return null;
        }

        $ventasPorMes = [];
        foreach ($pedidos as $pedido) {
            $createdAt = $pedido->created_at;
            if (!$createdAt) continue;
            
            $year = Carbon::parse($createdAt)->format('Y');
            $month = Carbon::parse($createdAt)->format('m');
            $key = $year . '-' . $month;
            
            if (!isset($ventasPorMes[$key])) {
                $ventasPorMes[$key] = [
                    'year' => $year,
                    'month' => $month,
                    'total' => 0
                ];
            }
            
            $ventasPorMes[$key]['total'] += to_float($pedido->total_final ?? 0);
        }

        if (empty($ventasPorMes)) {
            return null;
        }

        // Ordenar por total descendente
        uasort($ventasPorMes, function($a, $b) {
            return $b['total'] <=> $a['total'];
        });

        $mejorMes = reset($ventasPorMes);

        return [
            'fecha' => Carbon::createFromDate($mejorMes['year'], $mejorMes['month'], 1)->format('F Y'),
            'total' => $mejorMes['total']
        ];
    }

    private function obtenerActividadReciente($vendedor)
    {
        $actividades = [];

        // Últimos pedidos
        $ultimosPedidos = Pedido::where('vendedor_id', $vendedor->id)
                               ->orderBy('created_at', 'desc')
                               ->limit(5)
                               ->get();

        foreach ($ultimosPedidos as $pedido) {
            $clienteNombre = $pedido->cliente_nombre ?? 'Cliente';
            $actividades[] = [
                'tipo' => 'pedido',
                'descripcion' => "Nuevo pedido de {$clienteNombre}",
                'monto' => to_float($pedido->total_final ?? 0),
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
                'monto' => to_float($comision->monto_comision ?? 0),
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