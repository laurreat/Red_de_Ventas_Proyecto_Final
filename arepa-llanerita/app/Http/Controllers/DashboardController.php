<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Producto;
use App\Models\Pedido;
use App\Models\Comision;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        switch ($user->rol) {
            case 'administrador':
                return $this->adminDashboard();
            case 'lider':
                return $this->liderDashboard();
            case 'vendedor':
                return $this->vendedorDashboard();
            case 'cliente':
                return $this->clienteDashboard();
            default:
                return redirect()->route('login');
        }
    }

    private function adminDashboard()
    {
        $inicioMes = Carbon::now()->startOfMonth();
        $finMes = Carbon::now()->endOfMonth();
        $hoy = Carbon::today();

        $stats = [
            'total_usuarios' => User::count(),
            'total_vendedores' => User::vendedores()->count(),
            'total_productos' => Producto::count(),
            'productos_stock_bajo' => Producto::stockBajo()->count(),
            'pedidos_hoy' => Pedido::whereDate('created_at', $hoy)->count(),
            'pedidos_pendientes' => Pedido::pendientes()->count(),
            'ventas_mes' => (float) Pedido::whereBetween('created_at', [$inicioMes, $finMes])
                                 ->where('estado', '!=', 'cancelado')
                                 ->sum('total_final'),
            'comisiones_pendientes' => (float) Comision::pendientes()->sum('monto'),
            'ventas_hoy' => (float) Pedido::whereDate('created_at', $hoy)
                                 ->where('estado', '!=', 'cancelado')
                                 ->sum('total_final'),
            'clientes_activos' => User::where('rol', 'cliente')
                                    ->where('activo', true)->count(),
            'total_comisiones_mes' => (float) Comision::whereBetween('created_at', [$inicioMes, $finMes])
                                            ->sum('monto'),
        ];

        $pedidos_recientes = Pedido::orderBy('created_at', 'desc')
                                  ->take(10)
                                  ->get()
                                  ->map(function ($pedido) {
                                      $clienteData = $pedido->cliente_data ?? [];
                                      $vendedorData = $pedido->vendedor_data ?? [];

                                      return (object)[
                                          'id' => $pedido->_id,
                                          'numero_pedido' => $pedido->numero_pedido,
                                          'cliente' => (object)[
                                              'name' => is_array($clienteData) ? ($clienteData['name'] ?? 'Cliente') : 'Cliente',
                                              'email' => is_array($clienteData) ? ($clienteData['email'] ?? '') : ''
                                          ],
                                          'vendedor' => (object)[
                                              'name' => is_array($vendedorData) ? ($vendedorData['name'] ?? 'Vendedor') : 'Vendedor',
                                              'email' => is_array($vendedorData) ? ($vendedorData['email'] ?? '') : ''
                                          ],
                                          'total_final' => $pedido->total_final,
                                          'estado' => $pedido->estado,
                                          'created_at' => $pedido->created_at
                                      ];
                                  });

        $productos_populares = $this->obtenerProductosPopulares();
        $ventasSemanales = $this->obtenerVentasSemanales();
        $topVendedores = $this->obtenerTopVendedores();

        return view('dashboard.admin', compact(
            'stats',
            'pedidos_recientes',
            'productos_populares',
            'ventasSemanales',
            'topVendedores'
        ));
    }

    private function liderDashboard()
    {
        $user = auth()->user();
        $inicioMes = Carbon::now()->startOfMonth();

        $equipo = User::where('referido_por', $user->_id)
                     ->where('rol', 'vendedor')
                     ->get()
                     ->map(function ($miembro) use ($inicioMes) {
                         $ventasMes = (float) Pedido::where('vendedor_id', $miembro->_id)
                                           ->whereBetween('created_at', [$inicioMes, Carbon::now()])
                                           ->where('estado', '!=', 'cancelado')
                                           ->sum('total_final');

                         $miembro->ventas_mes_actual = $ventasMes;
                         $miembro->meta_mensual = $miembro->meta_mensual ?? 0;
                         $miembro->total_referidos = $miembro->total_referidos ?? 0;
                         return $miembro;
                     });

        $ventasPersonales = (float) Pedido::where('vendedor_id', $user->_id)
                                 ->whereBetween('created_at', [$inicioMes, Carbon::now()])
                                 ->where('estado', '!=', 'cancelado')
                                 ->sum('total_final');

        $comisionesMes = (float) Comision::where('user_id', $user->_id)
                                ->whereBetween('created_at', [$inicioMes, Carbon::now()])
                                ->sum('monto');

        $nuevosMes = User::where('referido_por', $user->_id)
                        ->whereBetween('created_at', [$inicioMes, Carbon::now()])
                        ->count();

        $stats = [
            'total_equipo' => $equipo->count(),
            'ventas_equipo_mes' => $equipo->sum('ventas_mes_actual'),
            'ventas_personales' => $ventasPersonales,
            'comisiones_mes' => $comisionesMes,
            'nuevos_mes' => $nuevosMes,
            'meta_mensual' => $user->meta_mensual ?? 0,
            'meta_equipo' => $equipo->sum('meta_mensual'),
            'comisiones_pendientes' => (float) Comision::where('user_id', $user->_id)
                                              ->where('estado', 'pendiente')
                                              ->sum('monto'),
        ];

        return view('dashboard.lider', compact('stats', 'equipo'));
    }

    private function vendedorDashboard()
    {
        $user = auth()->user();
        $inicioMes = Carbon::now()->startOfMonth();

        $ventasMes = (float) Pedido::where('vendedor_id', $user->_id)
                          ->whereBetween('created_at', [$inicioMes, Carbon::now()])
                          ->where('estado', '!=', 'cancelado')
                          ->sum('total_final');

        $pedidosMes = Pedido::where('vendedor_id', $user->_id)
                           ->whereBetween('created_at', [$inicioMes, Carbon::now()])
                           ->count();

        $comisionesGanadas = (float) Comision::where('user_id', $user->_id)
                                   ->whereBetween('created_at', [$inicioMes, Carbon::now()])
                                   ->sum('monto');

        $comisionesDisponibles = (float) Comision::where('user_id', $user->_id)
                                        ->where('estado', 'pendiente')
                                        ->sum('monto');

        $nuevosReferidosMes = User::where('referido_por', $user->_id)
                                 ->whereBetween('created_at', [$inicioMes, Carbon::now()])
                                 ->count();

        $referidosActivos = User::where('referido_por', $user->_id)
                               ->whereHas('pedidosComoCliente')
                               ->count();

        $stats = [
            'ventas_mes' => $ventasMes,
            'meta_mensual' => $user->meta_mensual ?? 0,
            'comisiones_ganadas' => $comisionesGanadas,
            'comisiones_disponibles' => $comisionesDisponibles,
            'total_referidos' => $user->total_referidos ?? 0,
            'nuevos_referidos_mes' => $nuevosReferidosMes,
            'pedidos_mes' => $pedidosMes,
            'referidos_activos' => $referidosActivos,
            'progreso_meta' => $user->meta_mensual > 0 ?
                             round(($ventasMes / $user->meta_mensual) * 100, 2) : 0,
        ];

        $pedidos_recientes = Pedido::where('vendedor_id', $user->_id)
                                  ->orderBy('created_at', 'desc')
                                  ->take(5)
                                  ->get()
                                  ->map(function ($pedido) {
                                      $clienteData = $pedido->cliente_data ?? [];

                                      return (object)[
                                          'id' => $pedido->_id,
                                          'numero_pedido' => $pedido->numero_pedido,
                                          'cliente' => (object)[
                                              'name' => is_array($clienteData) ? ($clienteData['name'] ?? 'Cliente') : 'Cliente',
                                              'email' => is_array($clienteData) ? ($clienteData['email'] ?? '') : ''
                                          ],
                                          'total_final' => $pedido->total_final,
                                          'estado' => $pedido->estado,
                                          'productos_resumen' => $this->generarResumenProductosEmbebido($pedido),
                                          'created_at' => $pedido->created_at
                                      ];
                                  });

        return view('dashboard.vendedor', compact('stats', 'pedidos_recientes'));
    }

    private function clienteDashboard()
    {
        // Método faltante - implementación básica
        $user = auth()->user();
        $inicioMes = Carbon::now()->startOfMonth();

        $stats = [
            'pedidos_mes' => Pedido::where('cliente_id', $user->_id)
                                  ->whereBetween('created_at', [$inicioMes, Carbon::now()])
                                  ->count(),
            'total_gastado_mes' => (float) Pedido::where('cliente_id', $user->_id)
                                        ->whereBetween('created_at', [$inicioMes, Carbon::now()])
                                        ->where('estado', '!=', 'cancelado')
                                        ->sum('total_final'),
            'pedidos_pendientes' => Pedido::where('cliente_id', $user->_id)
                                         ->where('estado', 'pendiente')
                                         ->count(),
        ];

        return view('dashboard.cliente', compact('stats'));
    }

    private function obtenerProductosPopulares()
    {
        // Implementación básica - debes ajustar según tu estructura de datos
        return collect([]);
    }

    private function obtenerVentasSemanales()
    {
        $ventas = [];

        for ($i = 6; $i >= 0; $i--) {
            $fecha = Carbon::now()->subDays($i);
            $ventasDia = (float) Pedido::whereDate('created_at', $fecha)
                              ->where('estado', '!=', 'cancelado')
                              ->sum('total_final');

            $ventas[] = [
                'fecha' => $fecha->format('d/m'),
                'ventas' => $ventasDia,
                'dia' => $fecha->format('l')
            ];
        }

        return collect($ventas);
    }

    private function obtenerTopVendedores()
    {
        $inicioMes = Carbon::now()->startOfMonth();

        return User::vendedores()
                  ->where('activo', true)
                  ->get()
                  ->map(function ($vendedor) use ($inicioMes) {
                      $ventasMes = (float) Pedido::where('vendedor_id', $vendedor->_id)
                                        ->whereBetween('created_at', [$inicioMes, Carbon::now()])
                                        ->where('estado', '!=', 'cancelado')
                                        ->sum('total_final');

                      $pedidosMes = Pedido::where('vendedor_id', $vendedor->_id)
                                         ->whereBetween('created_at', [$inicioMes, Carbon::now()])
                                         ->where('estado', '!=', 'cancelado')
                                         ->count();

                      return [
                          'vendedor' => $vendedor,
                          'ventas_mes' => $ventasMes,
                          'pedidos_mes' => $pedidosMes,
                          'promedio_pedido' => $pedidosMes > 0 ? $ventasMes / $pedidosMes : 0
                      ];
                  })
                  ->sortByDesc('ventas_mes')
                  ->take(5)
                  ->values();
    }

    private function generarResumenProductosEmbebido($pedido)
    {
        // Implementación básica - ajusta según tu estructura
        if (!isset($pedido->productos) || empty($pedido->productos)) {
            return 'Sin productos';
        }

        $totalProductos = is_array($pedido->productos) ? count($pedido->productos) : 0;
        return "{$totalProductos} producto(s)";
    }
}