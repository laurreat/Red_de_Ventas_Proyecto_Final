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

        // Estadísticas reales de MongoDB
        $stats = [
            'total_usuarios' => User::count(),
            'total_vendedores' => User::vendedores()->count(),
            'total_productos' => Producto::count(),
            'productos_stock_bajo' => Producto::stockBajo()->count(),
            'pedidos_hoy' => Pedido::whereDate('created_at', $hoy)->count(),
            'pedidos_pendientes' => Pedido::pendientes()->count(),
            'ventas_mes' => to_float(Pedido::whereBetween('created_at', [$inicioMes, $finMes])
                                 ->where('estado', '!=', 'cancelado')
                                 ->sum('total_final')),
            'comisiones_pendientes' => to_float(Comision::pendientes()->sum('monto')),
            'ventas_hoy' => to_float(Pedido::whereDate('created_at', $hoy)
                                 ->where('estado', '!=', 'cancelado')
                                 ->sum('total_final')),
            'clientes_activos' => User::where('rol', 'cliente')
                                    ->where('activo', true)->count(),
            'total_comisiones_mes' => to_float(Comision::whereBetween('created_at', [$inicioMes, $finMes])
                                            ->sum('monto')),
        ];

        // Pedidos recientes reales
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

        // Productos más vendidos del mes
        $productos_populares = $this->obtenerProductosPopulares();

        // Gráfico de ventas de los últimos 7 días
        $ventasSemanales = $this->obtenerVentasSemanales();

        // Top vendedores del mes
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

        // Vendedores bajo este líder (referidos por él)
        $equipo = User::where('referido_por', $user->_id)
                     ->where('rol', 'vendedor')
                     ->get()
                     ->map(function ($miembro) use ($inicioMes) {
                         // Calcular ventas reales del mes
                         $ventasMes = to_float(Pedido::where('vendedor_id', $miembro->_id)
                                           ->whereBetween('created_at', [$inicioMes, Carbon::now()])
                                           ->where('estado', '!=', 'cancelado')
                                           ->sum('total_final'));

                         $miembro->ventas_mes_actual = $ventasMes;
                         $miembro->meta_mensual = $miembro->meta_mensual ?? 0;
                         // Calcular referidos reales de la base de datos
                         $miembro->total_referidos = User::where('referido_por', $miembro->_id)->count();
                         return $miembro;
                     });

        // Ventas personales del líder
        $ventasPersonales = to_float(Pedido::where('vendedor_id', $user->_id)
                                 ->whereBetween('created_at', [$inicioMes, Carbon::now()])
                                 ->where('estado', '!=', 'cancelado')
                                 ->sum('total_final'));

        // Comisiones del líder este mes
        $comisionesMes = to_float(Comision::where('user_id', $user->_id)
                                ->whereBetween('created_at', [$inicioMes, Carbon::now()])
                                ->sum('monto'));

        // Nuevos miembros del equipo este mes
        $nuevosMes = User::where('referido_por', $user->_id)
                        ->whereBetween('created_at', [$inicioMes, Carbon::now()])
                        ->count();

        // Estadísticas reales del líder
        $stats = [
            'total_equipo' => $equipo->count(),
            'ventas_equipo_mes' => $equipo->sum('ventas_mes_actual'),
            'ventas_personales' => $ventasPersonales,
            'comisiones_mes' => $comisionesMes,
            'nuevos_mes' => $nuevosMes,
            'meta_mensual' => $user->meta_mensual ?? 0,
            'meta_equipo' => $equipo->sum('meta_mensual'),
            'comisiones_pendientes' => to_float(Comision::where('user_id', $user->_id)
                                              ->where('estado', 'pendiente')
                                              ->sum('monto')),
        ];

        // Redirigir al dashboard moderno del líder
        return redirect()->route('lider.dashboard');
    }

    private function vendedorDashboard()
    {
        // Redirigir al dashboard moderno del vendedor
        return redirect()->route('vendedor.dashboard');
    }

    private function generarResumenProductos($pedido)
    {
        $detalles = $pedido->detalles()->with('producto')->get();
        if ($detalles->count() === 0) return 'Sin productos';

        $resumen = $detalles->map(function ($detalle) {
            return $detalle->cantidad . 'x ' . $detalle->producto->nombre;
        })->take(3)->implode(', ');

        if ($detalles->count() > 3) {
            $resumen .= '...';
        }

        return $resumen;
    }

    private function generarResumenProductosEmbebido($pedido)
    {
        $detalles = $pedido->detalles ?? [];
        if (empty($detalles)) return 'Sin productos';

        $resumen = collect($detalles)->map(function ($detalle) {
            $nombre = $detalle['producto_data']['nombre'] ?? 'Producto sin nombre';
            $cantidad = $detalle['cantidad'] ?? 0;
            return $cantidad . 'x ' . $nombre;
        })->take(3)->implode(', ');

        if (count($detalles) > 3) {
            $resumen .= '...';
        }

        return $resumen;
    }

    private function clienteDashboard()
    {
        $user = auth()->user();
        $inicioMes = Carbon::now()->startOfMonth();

        // Pedidos reales del cliente
        $totalPedidos = Pedido::where('user_id', $user->_id)->count();
        $pedidosMes = Pedido::where('user_id', $user->_id)
                           ->whereBetween('created_at', [$inicioMes, Carbon::now()])
                           ->count();

        // Total gastado real
        $totalGastado = to_float(Pedido::where('user_id', $user->_id)
                             ->where('estado', '!=', 'cancelado')
                             ->sum('total_final'));

        // Promedio de pedido
        $pedidoPromedio = $totalPedidos > 0 ? $totalGastado / $totalPedidos : 0;

        // Referidos reales
        $totalReferidos = User::where('referido_por', $user->_id)->count();
        $referidosConPedidos = User::where('referido_por', $user->_id)
                                  ->whereHas('pedidosComoCliente')
                                  ->count();

        // Estadísticas reales del cliente
        $stats = [
            'total_pedidos' => $totalPedidos,
            'pedidos_mes' => $pedidosMes,
            'total_gastado' => $totalGastado,
            'pedido_promedio' => $pedidoPromedio,
            'total_referidos' => $totalReferidos,
            'referidos_realizados' => $referidosConPedidos,
        ];

        // Pedidos recientes reales
        $pedidos_recientes = Pedido::where('user_id', $user->_id)
                                  ->orderBy('created_at', 'desc')
                                  ->take(5)
                                  ->get()
                                  ->map(function ($pedido) {
                                      $vendedorData = $pedido->vendedor_data ?? [];

                                      return (object)[
                                          'id' => $pedido->_id,
                                          'numero_pedido' => $pedido->numero_pedido,
                                          'vendedor' => (object)[
                                              'name' => is_array($vendedorData) ? ($vendedorData['name'] ?? 'Vendedor') : 'Vendedor',
                                              'email' => is_array($vendedorData) ? ($vendedorData['email'] ?? '') : ''
                                          ],
                                          'total_final' => $pedido->total_final,
                                          'estado' => $pedido->estado,
                                          'productos_resumen' => $this->generarResumenProductosEmbebido($pedido),
                                          'created_at' => $pedido->created_at
                                      ];
                                  });

        // Productos favoritos (los más comprados por el cliente)
        $productos_favoritos = $this->obtenerProductosFavoritosCliente($user->_id);

        return view('cliente.dashboard', compact('stats', 'pedidos_recientes', 'productos_favoritos'));
    }

    // Métodos auxiliares para el dashboard admin
    private function obtenerProductosPopulares()
    {
        // Obtener productos más vendidos del mes basado en los detalles embebidos
        $inicioMes = Carbon::now()->startOfMonth();
        $pedidosMes = Pedido::whereBetween('created_at', [$inicioMes, Carbon::now()])
                           ->where('estado', '!=', 'cancelado')
                           ->get();

        $productosVendidos = [];

        foreach ($pedidosMes as $pedido) {
            foreach ($pedido->detalles as $detalle) {
                $productoId = $detalle['producto_id'] ?? null;
                $cantidad = $detalle['cantidad'] ?? 0;

                if (!$productoId) continue;

                if (!isset($productosVendidos[$productoId])) {
                    $productosVendidos[$productoId] = [
                        'producto_data' => $detalle['producto_data'] ?? [],
                        'cantidad_vendida' => 0,
                        'total_ventas' => 0
                    ];
                }

                $productosVendidos[$productoId]['cantidad_vendida'] += $cantidad;
                $productosVendidos[$productoId]['total_ventas'] += $detalle['subtotal'] ?? 0;
            }
        }

        // Ordenar por cantidad vendida y tomar los top 5
        uasort($productosVendidos, function($a, $b) {
            return $b['cantidad_vendida'] <=> $a['cantidad_vendida'];
        });

        return collect(array_slice($productosVendidos, 0, 5, true))
               ->map(function($data, $productoId) {
                   return (object)[
                       'id' => $productoId,
                       'nombre' => $data['producto_data']['nombre'] ?? 'Producto sin nombre',
                       'cantidad_vendida' => $data['cantidad_vendida'],
                       'total_ventas' => $data['total_ventas'],
                       'veces_vendido' => $data['cantidad_vendida'], // Para compatibilidad con la vista
                       'categoria' => (object)[
                           'nombre' => $data['producto_data']['categoria_data']['nombre'] ?? 'Sin categoría'
                       ]
                   ];
               });
    }

    private function obtenerVentasSemanales()
    {
        $ventas = [];

        for ($i = 6; $i >= 0; $i--) {
            $fecha = Carbon::now()->subDays($i);
            $ventasDia = to_float(Pedido::whereDate('created_at', $fecha)
                              ->where('estado', '!=', 'cancelado')
                              ->sum('total_final'));

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
                      $ventasMes = to_float(Pedido::where('vendedor_id', $vendedor->_id)
                                        ->whereBetween('created_at', [$inicioMes, Carbon::now()])
                                        ->where('estado', '!=', 'cancelado')
                                        ->sum('total_final'));

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

    private function obtenerProductosFavoritosCliente($clienteId)
    {
        // Obtener pedidos del cliente
        $pedidosCliente = Pedido::where('user_id', $clienteId)
                               ->where('estado', '!=', 'cancelado')
                               ->get();

        $productosComprados = [];

        foreach ($pedidosCliente as $pedido) {
            foreach ($pedido->detalles as $detalle) {
                $productoId = $detalle['producto_id'] ?? null;
                $cantidad = $detalle['cantidad'] ?? 0;

                if (!$productoId) continue;

                if (!isset($productosComprados[$productoId])) {
                    $productosComprados[$productoId] = [
                        'producto_data' => $detalle['producto_data'] ?? [],
                        'cantidad_comprada' => 0,
                        'veces_comprado' => 0
                    ];
                }

                $productosComprados[$productoId]['cantidad_comprada'] += $cantidad;
                $productosComprados[$productoId]['veces_comprado']++;
            }
        }

        // Ordenar por cantidad comprada y tomar los top 4
        uasort($productosComprados, function($a, $b) {
            return $b['cantidad_comprada'] <=> $a['cantidad_comprada'];
        });

        $topProductos = collect(array_slice($productosComprados, 0, 4, true))
                       ->map(function($data, $productoId) {
                           return (object)[
                               'id' => $productoId,
                               'nombre' => $data['producto_data']['nombre'] ?? 'Producto sin nombre',
                               'descripcion' => $data['producto_data']['descripcion'] ?? '',
                               'precio' => $data['producto_data']['precio'] ?? 0,
                               'imagen' => $data['producto_data']['imagen'] ?? null,
                               'cantidad_comprada' => $data['cantidad_comprada'],
                               'veces_comprado' => $data['veces_comprado'],
                               'categoria' => $data['producto_data']['categoria_data']['nombre'] ?? 'Sin categoría'
                           ];
                       });

        // Si no hay suficientes productos favoritos, completar con productos populares
        if ($topProductos->count() < 4) {
            $productosPopulares = Producto::activos()
                                        ->take(4 - $topProductos->count())
                                        ->get()
                                        ->map(function($producto) {
                                            return (object)[
                                                'id' => $producto->_id,
                                                'nombre' => $producto->nombre,
                                                'descripcion' => $producto->descripcion,
                                                'precio' => $producto->precio,
                                                'imagen' => $producto->imagen,
                                                'cantidad_comprada' => 0,
                                                'veces_comprado' => 0,
                                                'categoria' => $producto->categoria_data['nombre'] ?? 'Sin categoría'
                                            ];
                                        });

            $topProductos = $topProductos->merge($productosPopulares);
        }

        return $topProductos;
    }
}
