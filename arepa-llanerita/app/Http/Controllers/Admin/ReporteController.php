<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pedido;
use App\Models\User;
use App\Models\Producto;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ReporteController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            if (!auth()->user()->esAdmin()) {
                abort(403, 'Acceso denegado. Solo administradores.');
            }
            return $next($request);
        });
    }

    public function ventas(Request $request)
    {
        // Fechas por defecto (último mes)
        $fechaInicio = $request->get('fecha_inicio', now()->subMonth()->format('Y-m-d'));
        $fechaFin = $request->get('fecha_fin', now()->format('Y-m-d'));
        $vendedorId = $request->get('vendedor_id');
        $tipoReporte = $request->get('tipo_reporte', 'resumen');

        // Consulta base
        $query = Pedido::with(['cliente', 'vendedor', 'detalles.producto'])
            ->whereDate('created_at', '>=', $fechaInicio)
            ->whereDate('created_at', '<=', $fechaFin)
            ->where('estado', '!=', 'cancelado');

        if ($vendedorId) {
            $query->where('vendedor_id', $vendedorId);
        }

        $pedidos = $query->get();

        // Estadísticas generales
        $stats = [
            'total_ventas' => $pedidos->count(),
            'total_ingresos' => $pedidos->sum('total_final'),
            'ticket_promedio' => $pedidos->count() > 0 ? $pedidos->avg('total_final') : 0,
            'productos_vendidos' => $pedidos->flatMap->detalles->sum('cantidad'),
        ];

        // Ventas por día
        $ventasPorDia = $pedidos->groupBy(function ($pedido) {
            return $pedido->created_at->format('Y-m-d');
        })->map(function ($pedidosDia) {
            return [
                'cantidad' => $pedidosDia->count(),
                'total' => $pedidosDia->sum('total_final')
            ];
        });

        // Ventas por vendedor
        $ventasPorVendedor = $pedidos->whereNotNull('vendedor_id')
            ->groupBy('vendedor_id')
            ->map(function ($pedidosVendedor) {
                $vendedor = $pedidosVendedor->first()->vendedor;
                return [
                    'vendedor' => $vendedor->name,
                    'email' => $vendedor->email,
                    'cantidad_pedidos' => $pedidosVendedor->count(),
                    'total_ventas' => $pedidosVendedor->sum('total_final'),
                    'comision_estimada' => $pedidosVendedor->sum('total_final') * 0.1, // 10% comisión estimada
                ];
            });

        // Productos más vendidos
        $productosMasVendidos = $pedidos->flatMap->detalles
            ->groupBy('producto_id')
            ->map(function ($detallesProducto) {
                $producto = $detallesProducto->first()->producto;
                return [
                    'producto' => $producto->nombre,
                    'categoria' => $producto->categoria->nombre,
                    'cantidad_vendida' => $detallesProducto->sum('cantidad'),
                    'total_ingresos' => $detallesProducto->sum('total'),
                ];
            })
            ->sortByDesc('cantidad_vendida')
            ->take(10);

        // Clientes más activos
        $clientesMasActivos = $pedidos->groupBy('cliente_id')
            ->map(function ($pedidosCliente) {
                $cliente = $pedidosCliente->first()->cliente;
                return [
                    'cliente' => $cliente->name,
                    'email' => $cliente->email,
                    'cantidad_pedidos' => $pedidosCliente->count(),
                    'total_gastado' => $pedidosCliente->sum('total_final'),
                ];
            })
            ->sortByDesc('total_gastado')
            ->take(10);

        // Ventas por estado
        $ventasPorEstado = $pedidos->groupBy('estado')->map(function ($pedidosEstado) {
            return [
                'cantidad' => $pedidosEstado->count(),
                'total' => $pedidosEstado->sum('total_final')
            ];
        });

        // Para filtros
        $vendedores = User::vendedores()->orderBy('name')->get();

        return view('admin.reportes.ventas', compact(
            'stats',
            'ventasPorDia',
            'ventasPorVendedor',
            'productosMasVendidos',
            'clientesMasActivos',
            'ventasPorEstado',
            'vendedores',
            'fechaInicio',
            'fechaFin',
            'vendedorId',
            'tipoReporte'
        ));
    }

    public function productos(Request $request)
    {
        // Fechas por defecto (último mes)
        $fechaInicio = $request->get('fecha_inicio', now()->subMonth()->format('Y-m-d'));
        $fechaFin = $request->get('fecha_fin', now()->format('Y-m-d'));
        $categoriaId = $request->get('categoria_id');

        // Productos con ventas en el período
        $productosConVentas = DB::table('detalles_pedido')
            ->join('pedidos', 'detalles_pedido.pedido_id', '=', 'pedidos.id')
            ->join('productos', 'detalles_pedido.producto_id', '=', 'productos.id')
            ->join('categorias', 'productos.categoria_id', '=', 'categorias.id')
            ->select(
                'productos.id',
                'productos.nombre',
                'productos.precio',
                'productos.stock',
                'categorias.nombre as categoria',
                DB::raw('SUM(detalles_pedido.cantidad) as cantidad_vendida'),
                DB::raw('SUM(detalles_pedido.total) as ingresos_totales'),
                DB::raw('COUNT(DISTINCT pedidos.id) as pedidos_count')
            )
            ->whereDate('pedidos.created_at', '>=', $fechaInicio)
            ->whereDate('pedidos.created_at', '<=', $fechaFin)
            ->where('pedidos.estado', '!=', 'cancelado')
            ->when($categoriaId, function ($query) use ($categoriaId) {
                return $query->where('productos.categoria_id', $categoriaId);
            })
            ->groupBy('productos.id', 'productos.nombre', 'productos.precio', 'productos.stock', 'categorias.nombre')
            ->orderByDesc('cantidad_vendida')
            ->get();

        // Productos sin ventas
        $productosSinVentas = Producto::with('categoria')
            ->whereNotIn('id', $productosConVentas->pluck('id'))
            ->when($categoriaId, function ($query) use ($categoriaId) {
                return $query->where('categoria_id', $categoriaId);
            })
            ->get();

        // Estadísticas
        $stats = [
            'productos_vendidos' => $productosConVentas->count(),
            'productos_sin_ventas' => $productosSinVentas->count(),
            'total_unidades' => $productosConVentas->sum('cantidad_vendida'),
            'total_ingresos' => $productosConVentas->sum('ingresos_totales'),
        ];

        // Para filtros
        $categorias = DB::table('categorias')->orderBy('nombre')->get();

        return view('admin.reportes.productos', compact(
            'productosConVentas',
            'productosSinVentas',
            'stats',
            'categorias',
            'fechaInicio',
            'fechaFin',
            'categoriaId'
        ));
    }

    public function comisiones(Request $request)
    {
        // Fechas por defecto (último mes)
        $fechaInicio = $request->get('fecha_inicio', now()->subMonth()->format('Y-m-d'));
        $fechaFin = $request->get('fecha_fin', now()->format('Y-m-d'));
        $vendedorId = $request->get('vendedor_id');

        // Comisiones por vendedor
        $comisiones = DB::table('pedidos')
            ->join('users', 'pedidos.vendedor_id', '=', 'users.id')
            ->select(
                'users.id',
                'users.name',
                'users.email',
                DB::raw('COUNT(pedidos.id) as total_pedidos'),
                DB::raw('SUM(pedidos.total_final) as total_ventas'),
                DB::raw('SUM(pedidos.total_final) * 0.1 as comision_estimada') // 10% comisión
            )
            ->whereDate('pedidos.created_at', '>=', $fechaInicio)
            ->whereDate('pedidos.created_at', '<=', $fechaFin)
            ->where('pedidos.estado', 'entregado')
            ->whereNotNull('pedidos.vendedor_id')
            ->when($vendedorId, function ($query) use ($vendedorId) {
                return $query->where('users.id', $vendedorId);
            })
            ->groupBy('users.id', 'users.name', 'users.email')
            ->orderByDesc('total_ventas')
            ->get();

        // Estadísticas
        $stats = [
            'total_comisiones' => $comisiones->sum('comision_estimada'),
            'total_ventas' => $comisiones->sum('total_ventas'),
            'vendedores_activos' => $comisiones->count(),
            'promedio_comision' => $comisiones->count() > 0 ? $comisiones->avg('comision_estimada') : 0,
        ];

        // Para filtros
        $vendedores = User::vendedores()->orderBy('name')->get();

        return view('admin.reportes.comisiones', compact(
            'comisiones',
            'stats',
            'vendedores',
            'fechaInicio',
            'fechaFin',
            'vendedorId'
        ));
    }

    public function exportarVentas(Request $request)
    {
        // TODO: Implementar exportación a Excel/PDF
        return response()->json(['message' => 'Funcionalidad de exportación en desarrollo']);
    }
}
