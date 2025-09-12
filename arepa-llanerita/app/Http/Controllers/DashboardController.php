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
        // Estadísticas reales de la base de datos
        $stats = [
            'total_usuarios' => User::count(),
            'total_vendedores' => User::vendedores()->count(),
            'total_productos' => Producto::count(),
            'productos_stock_bajo' => Producto::stockBajo()->count(),
            'pedidos_hoy' => Pedido::whereDate('created_at', today())->count(),
            'pedidos_pendientes' => Pedido::where('estado', 'pendiente')->count(),
            'ventas_mes' => Pedido::whereMonth('created_at', now()->month)
                                 ->whereYear('created_at', now()->year)
                                 ->sum('total_final'),
            'comisiones_pendientes' => Comision::where('estado', 'pendiente')->sum('monto'),
        ];

        // Pedidos recientes reales
        $pedidos_recientes = Pedido::with(['cliente', 'vendedor'])
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        // Productos más vendidos (basado en detalles de pedidos)
        $productos_populares = Producto::with('categoria')
            ->join('detalle_pedidos', 'productos.id', '=', 'detalle_pedidos.producto_id')
            ->select('productos.*', \DB::raw('SUM(detalle_pedidos.cantidad) as veces_vendido'))
            ->groupBy('productos.id')
            ->orderBy('veces_vendido', 'desc')
            ->take(5)
            ->get();

        return view('dashboard.admin', compact('stats', 'pedidos_recientes', 'productos_populares'));
    }

    private function liderDashboard()
    {
        // Datos demo realistas para líder
        $stats = [
            'total_equipo' => 24,
            'ventas_equipo_mes' => 3250000, // $3,250,000
            'ventas_personales' => 580000, // $580,000
            'comisiones_mes' => 195000, // $195,000
            'nuevos_mes' => 3,
            'meta_mensual' => 4000000, // $4,000,000
            'meta_equipo' => 3500000, // $3,500,000
        ];

        // Miembros del equipo demo
        $equipo = collect([
            (object)[
                'name' => 'Carlos Ruiz',
                'rol' => 'vendedor',
                'ventas_mes_actual' => 485000,
                'meta_mensual' => 600000,
                'total_referidos' => 8,
            ],
            (object)[
                'name' => 'Ana López',
                'rol' => 'vendedor',
                'ventas_mes_actual' => 720000,
                'meta_mensual' => 800000,
                'total_referidos' => 12,
            ],
            (object)[
                'name' => 'Pedro Castro',
                'rol' => 'vendedor',
                'ventas_mes_actual' => 390000,
                'meta_mensual' => 500000,
                'total_referidos' => 5,
            ],
            (object)[
                'name' => 'Carmen Torres',
                'rol' => 'vendedor',
                'ventas_mes_actual' => 840000,
                'meta_mensual' => 900000,
                'total_referidos' => 15,
            ],
            (object)[
                'name' => 'Miguel Vargas',
                'rol' => 'vendedor',
                'ventas_mes_actual' => 320000,
                'meta_mensual' => 400000,
                'total_referidos' => 3,
            ],
            (object)[
                'name' => 'Lucía Herrera',
                'rol' => 'vendedor',
                'ventas_mes_actual' => 495000,
                'meta_mensual' => 600000,
                'total_referidos' => 7,
            ]
        ]);

        return view('dashboard.lider', compact('stats', 'equipo'));
    }

    private function vendedorDashboard()
    {
        // Datos demo realistas para vendedor
        $stats = [
            'ventas_mes' => 650000, // $650,000
            'meta_mensual' => 800000, // $800,000
            'comisiones_ganadas' => 65000, // $65,000
            'comisiones_disponibles' => 45000, // $45,000
            'total_referidos' => 8,
            'nuevos_referidos_mes' => 2,
        ];

        // Pedidos recientes demo
        $pedidos_recientes = collect([
            (object)[
                'numero_pedido' => '#ARF-2024-001287',
                'cliente' => (object)['name' => 'María González', 'email' => 'maria.gonzalez@email.com'],
                'total' => 89500,
                'estado' => 'en_preparacion',
                'created_at' => now()->subMinutes(15),
                'productos_resumen' => '4x Arepa Queso, 2x Arepa Carne'
            ],
            (object)[
                'numero_pedido' => '#ARF-2024-001285',
                'cliente' => (object)['name' => 'Laura Silva', 'email' => 'laura.silva@email.com'],
                'total' => 67800,
                'estado' => 'confirmado',
                'created_at' => now()->subHours(2),
                'productos_resumen' => '3x Arepa Mixta, 1x Chicha'
            ],
            (object)[
                'numero_pedido' => '#ARF-2024-001278',
                'cliente' => (object)['name' => 'Carlos Mendoza', 'email' => 'carlos.mendoza@email.com'],
                'total' => 134000,
                'estado' => 'entregado',
                'created_at' => now()->subHours(6),
                'productos_resumen' => '6x Arepa Pollo, 3x Arepa Queso'
            ],
            (object)[
                'numero_pedido' => '#ARF-2024-001275',
                'cliente' => (object)['name' => 'Patricia López', 'email' => 'patricia.lopez@email.com'],
                'total' => 78500,
                'estado' => 'entregado',
                'created_at' => now()->subDay(1),
                'productos_resumen' => '2x Arepa Especial, 1x Bebida'
            ],
            (object)[
                'numero_pedido' => '#ARF-2024-001271',
                'cliente' => (object)['name' => 'Andrés Castro', 'email' => 'andres.castro@email.com'],
                'total' => 156000,
                'estado' => 'entregado',
                'created_at' => now()->subDay(1),
                'productos_resumen' => '8x Arepa Variadas'
            ]
        ]);

        return view('dashboard.vendedor', compact('stats', 'pedidos_recientes'));
    }

    private function clienteDashboard()
    {
        // Datos demo realistas para cliente
        $stats = [
            'total_pedidos' => 23,
            'pedidos_mes' => 4,
            'total_gastado' => 892000, // $892,000
            'pedido_promedio' => 38800, // $38,800
            'total_referidos' => 3,
        ];

        // Pedidos recientes demo
        $pedidos_recientes = collect([
            (object)[
                'numero_pedido' => '#ARF-2024-001142',
                'total' => 67800,
                'estado' => 'en_camino',
                'created_at' => now()->subHours(3),
                'vendedor' => (object)['name' => 'Ana López'],
                'productos_resumen' => '3x Arepa Queso Llanero, 1x Chicha',
                'direccion_entrega' => 'Calle 45 #23-12, Bogotá'
            ],
            (object)[
                'numero_pedido' => '#ARF-2024-001098',
                'total' => 89500,
                'estado' => 'entregado',
                'created_at' => now()->subWeek(1),
                'vendedor' => (object)['name' => 'Carlos Ruiz'],
                'productos_resumen' => '4x Arepa Mixta, 2x Arepa Pollo',
                'direccion_entrega' => 'Calle 45 #23-12, Bogotá'
            ],
            (object)[
                'numero_pedido' => '#ARF-2024-001067',
                'total' => 134000,
                'estado' => 'entregado',
                'created_at' => now()->subWeeks(2),
                'vendedor' => (object)['name' => 'Pedro Castro'],
                'productos_resumen' => '6x Arepa Carne, 2x Bebidas',
                'direccion_entrega' => 'Calle 45 #23-12, Bogotá'
            ],
            (object)[
                'numero_pedido' => '#ARF-2024-001023',
                'total' => 78500,
                'estado' => 'entregado',
                'created_at' => now()->subWeeks(3),
                'vendedor' => (object)['name' => 'Ana López'],
                'productos_resumen' => '2x Arepa Especial, 3x Arepa Queso',
                'direccion_entrega' => 'Calle 45 #23-12, Bogotá'
            ]
        ]);

        // Productos favoritos demo
        $productos_favoritos = collect([
            (object)[
                'nombre' => 'Arepa de Queso Llanero',
                'categoria' => (object)['nombre' => 'Arepas Tradicionales'],
                'precio' => 18000,
                'veces_comprado' => 12,
                'imagen' => 'arepa-queso.jpg'
            ],
            (object)[
                'nombre' => 'Arepa de Carne Mechada',
                'categoria' => (object)['nombre' => 'Arepas con Carne'],
                'precio' => 25000,
                'veces_comprado' => 8,
                'imagen' => 'arepa-carne.jpg'
            ],
            (object)[
                'nombre' => 'Arepa Mixta (Queso + Carne)',
                'categoria' => (object)['nombre' => 'Arepas Combinadas'],
                'precio' => 28000,
                'veces_comprado' => 6,
                'imagen' => 'arepa-mixta.jpg'
            ],
            (object)[
                'nombre' => 'Chicha Llanera',
                'categoria' => (object)['nombre' => 'Bebidas Tradicionales'],
                'precio' => 12000,
                'veces_comprado' => 5,
                'imagen' => 'chicha.jpg'
            ]
        ]);

        return view('dashboard.cliente', compact('stats', 'pedidos_recientes', 'productos_favoritos'));
    }
}
