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
        // Estadísticas básicas que funcionan
        $stats = [
            'total_usuarios' => User::count() ?? 0,
            'total_vendedores' => User::vendedores()->count() ?? 0,
            'total_productos' => Producto::count() ?? 0,
            'productos_stock_bajo' => 2, // Dato fijo por ahora
            'pedidos_hoy' => 0, // Dato fijo por ahora
            'pedidos_pendientes' => 0, // Dato fijo por ahora
            'ventas_mes' => 450000, // Dato fijo por ahora
            'comisiones_pendientes' => 125000, // Dato fijo por ahora
        ];

        // Pedidos recientes (datos de ejemplo por ahora)
        $pedidos_recientes = collect([
            (object)[
                'id' => 1,
                'numero_pedido' => 'PED-001',
                'cliente' => (object)[
                    'name' => 'Maria Gonzalez',
                    'email' => 'maria.gonzalez@email.com'
                ],
                'vendedor' => (object)[
                    'name' => 'Ana Lopez',
                    'email' => 'ana.lopez@arepallanerita.com'
                ],
                'total_final' => 35000,
                'estado' => 'entregado',
                'created_at' => now()->subDays(1)
            ],
            (object)[
                'id' => 2,
                'numero_pedido' => 'PED-002',
                'cliente' => (object)[
                    'name' => 'Pedro Ramirez',
                    'email' => 'pedro.ramirez@email.com'
                ],
                'vendedor' => (object)[
                    'name' => 'Miguel Torres',
                    'email' => 'miguel.torres@arepallanerita.com'
                ],
                'total_final' => 28000,
                'estado' => 'pendiente',
                'created_at' => now()->subDays(2)
            ],
        ]);

        // Productos populares (datos de ejemplo)
        $productos_populares = Producto::with('categoria')->take(5)->get();

        return view('dashboard.admin', compact('stats', 'pedidos_recientes', 'productos_populares'));
    }

    private function liderDashboard()
    {
        $user = auth()->user();

        // Vendedores bajo este líder (referidos por él) - con valores por defecto
        $equipo = User::where('referido_por', $user->id)
                     ->where('rol', 'vendedor')
                     ->get()
                     ->map(function ($miembro) {
                         // Asegurar que todos los campos necesarios existen
                         $miembro->ventas_mes_actual = $miembro->ventas_mes_actual ?? 0;
                         $miembro->meta_mensual = $miembro->meta_mensual ?? 0;
                         $miembro->total_referidos = $miembro->total_referidos ?? 0;
                         return $miembro;
                     });

        // Estadísticas reales del líder
        $stats = [
            'total_equipo' => $equipo->count(),
            'ventas_equipo_mes' => $equipo->sum('ventas_mes_actual'),
            'ventas_personales' => $user->ventas_mes_actual ?? 0,
            'comisiones_mes' => 150000,
            'nuevos_mes' => 2,
            'meta_mensual' => $user->meta_mensual ?? 0,
            'meta_equipo' => $equipo->sum('meta_mensual'),
        ];

        return view('dashboard.lider', compact('stats', 'equipo'));
    }

    private function vendedorDashboard()
    {
        $user = auth()->user();

        // Estadísticas del vendedor
        $stats = [
            'ventas_mes' => $user->ventas_mes_actual ?? 0,
            'meta_mensual' => $user->meta_mensual ?? 0,
            'comisiones_ganadas' => $user->comisiones_ganadas ?? 0,
            'comisiones_disponibles' => $user->comisiones_disponibles ?? 0,
            'total_referidos' => $user->total_referidos ?? 0,
            'nuevos_referidos_mes' => 1,
            'pedidos_mes' => 3,
            'referidos_activos' => 4,
        ];

        // Pedidos recientes (datos de ejemplo por ahora)
        $pedidos_recientes = collect([
            (object)[
                'id' => 1,
                'numero_pedido' => 'PED-001',
                'cliente' => (object)[
                    'name' => 'Maria Gonzalez',
                    'email' => 'maria.gonzalez@email.com'
                ],
                'total_final' => 25000,
                'estado' => 'entregado',
                'productos_resumen' => '2x Arepa Reina Pepiada, 1x Arepa con Carne',
                'created_at' => now()->subDays(2)
            ],
        ]);

        return view('dashboard.vendedor', compact('stats', 'pedidos_recientes'));
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

    private function clienteDashboard()
    {
        $user = auth()->user();

        // Estadísticas del cliente
        $stats = [
            'total_pedidos' => 3,
            'pedidos_mes' => 1,
            'total_gastado' => 85000,
            'pedido_promedio' => 28333,
            'total_referidos' => $user->total_referidos ?? 0,
            'referidos_realizados' => $user->total_referidos ?? 0,
        ];

        // Pedidos recientes (datos de ejemplo)
        $pedidos_recientes = collect([
            (object)[
                'id' => 1,
                'numero_pedido' => 'PED-001',
                'vendedor' => (object)[
                    'name' => 'Ana Lopez',
                    'email' => 'ana.lopez@arepallanerita.com'
                ],
                'total_final' => 25000,
                'estado' => 'entregado',
                'productos_resumen' => '2x Arepa Reina Pepiada',
                'created_at' => now()->subDays(3)
            ],
        ]);

        // Productos favoritos (datos de ejemplo)
        $productos_favoritos = Producto::with('categoria')->take(4)->get();

        return view('dashboard.cliente', compact('stats', 'pedidos_recientes', 'productos_favoritos'));
    }
}
