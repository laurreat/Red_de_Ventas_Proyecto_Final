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
        $stats = [
            'total_usuarios' => User::count(),
            'total_vendedores' => User::vendedores()->count(),
            'total_productos' => Producto::count(),
            'productos_stock_bajo' => Producto::stockBajo()->count(),
            'pedidos_hoy' => Pedido::hoy()->count(),
            'pedidos_pendientes' => Pedido::pendientes()->count(),
            'ventas_mes' => Pedido::whereMonth('created_at', now()->month)->sum('total'),
            'comisiones_pendientes' => Comision::pendientes()->sum('monto_comision'),
        ];

        $pedidos_recientes = Pedido::with(['cliente', 'vendedor'])
            ->latest()
            ->limit(5)
            ->get();

        $productos_populares = Producto::with('categoria')
            ->orderBy('veces_vendido', 'desc')
            ->limit(5)
            ->get();

        return view('dashboard.admin', compact('stats', 'pedidos_recientes', 'productos_populares'));
    }

    private function liderDashboard()
    {
        $user = auth()->user();
        
        $stats = [
            'total_referidos' => $user->total_referidos,
            'referidos_activos' => $user->referidos()->count(),
            'comisiones_mes' => $user->comisiones()
                ->whereMonth('fecha_calculo', now()->month)
                ->sum('monto_comision'),
            'comisiones_disponibles' => $user->comisiones_disponibles,
            'ventas_equipo_mes' => Pedido::whereIn('vendedor_id', $user->referidos()->pluck('id'))
                ->whereMonth('created_at', now()->month)
                ->sum('total'),
            'meta_alcanzada' => $user->porcentajeMetaAlcanzada(),
        ];

        $referidos_recientes = $user->referidos()
            ->with(['pedidosComoVendedor' => function($query) {
                $query->whereMonth('created_at', now()->month);
            }])
            ->latest()
            ->limit(5)
            ->get();

        return view('dashboard.lider', compact('stats', 'referidos_recientes'));
    }

    private function vendedorDashboard()
    {
        $user = auth()->user();
        
        $stats = [
            'pedidos_hoy' => $user->pedidosComoVendedor()->hoy()->count(),
            'pedidos_mes' => $user->pedidosComoVendedor()
                ->whereMonth('created_at', now()->month)
                ->count(),
            'ventas_mes' => $user->pedidosComoVendedor()
                ->whereMonth('created_at', now()->month)
                ->sum('total'),
            'comisiones_mes' => $user->comisiones()
                ->whereMonth('fecha_calculo', now()->month)
                ->sum('monto_comision'),
            'comisiones_disponibles' => $user->comisiones_disponibles,
            'total_referidos' => $user->total_referidos,
            'meta_alcanzada' => $user->porcentajeMetaAlcanzada(),
        ];

        $pedidos_recientes = $user->pedidosComoVendedor()
            ->with('cliente')
            ->latest()
            ->limit(5)
            ->get();

        return view('dashboard.vendedor', compact('stats', 'pedidos_recientes'));
    }

    private function clienteDashboard()
    {
        $user = auth()->user();
        
        $stats = [
            'total_pedidos' => $user->pedidosComoCliente()->count(),
            'pedidos_mes' => $user->pedidosComoCliente()
                ->whereMonth('created_at', now()->month)
                ->count(),
            'total_gastado' => $user->pedidosComoCliente()->sum('total'),
            'pedido_promedio' => $user->pedidosComoCliente()->avg('total') ?? 0,
        ];

        $pedidos_recientes = $user->pedidosComoCliente()
            ->with(['detalles.producto', 'vendedor'])
            ->latest()
            ->limit(5)
            ->get();

        $productos_favoritos = Producto::whereHas('detallesPedidos.pedido', function($query) use ($user) {
            $query->where('user_id', $user->id);
        })
        ->withCount(['detallesPedidos as veces_comprado' => function($query) use ($user) {
            $query->whereHas('pedido', function($q) use ($user) {
                $q->where('user_id', $user->id);
            });
        }])
        ->orderBy('veces_comprado', 'desc')
        ->limit(5)
        ->get();

        return view('dashboard.cliente', compact('stats', 'pedidos_recientes', 'productos_favoritos'));
    }
}
