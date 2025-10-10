<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pedido;
use App\Models\User;
use App\Models\Producto;
// use App\Exports\VentasExport; // Removed - Excel export functionality removed
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
// use Maatwebsite\Excel\Facades\Excel; // Removed - Excel export functionality removed
use Barryvdh\DomPDF\Facade\Pdf;
use MongoDB\BSON\Decimal128;

class ReporteController extends Controller
{
    /**
     * Convert MongoDB Decimal128 or any value to float safely
     */
    private function toFloat($value)
    {
        if ($value instanceof Decimal128) {
            return (float) $value->__toString();
        }

        if (is_numeric($value)) {
            return (float) $value;
        }

        return 0.0;
    }

    /**
     * Recursively convert Decimal128 values in arrays
     */
    private function convertDecimal128InArray($data)
    {
        if (!is_array($data)) {
            return $data;
        }

        foreach ($data as $key => $value) {
            if ($value instanceof Decimal128) {
                $data[$key] = (float) $value->__toString();
            } elseif (is_array($value)) {
                $data[$key] = $this->convertDecimal128InArray($value);
            }
        }

        return $data;
    }
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
        $query = Pedido::with(['cliente', 'vendedor'])
            ->whereDate('created_at', '>=', $fechaInicio)
            ->whereDate('created_at', '<=', $fechaFin)
            ->where('estado', '!=', 'cancelado');

        if ($vendedorId) {
            $query->where('vendedor_id', $vendedorId);
        }

        $pedidos = $query->get();

        // Estadísticas generales
        $totalIngresos = 0;
        $productosVendidos = 0;

        foreach ($pedidos as $pedido) {
            $totalIngresos += $this->toFloat($pedido->total_final);
            $detalles = $this->convertDecimal128InArray($pedido->detalles_embebidos ?? []);
            foreach ($detalles as $detalle) {
                $productosVendidos += (int)($detalle['cantidad'] ?? 0);
            }
        }

        $totalVentas = $pedidos->count();
        $stats = [
            'total_ventas' => (int)$totalVentas,
            'total_ingresos' => (float)$totalIngresos,
            'ticket_promedio' => $totalVentas > 0 ? (float)((float)$totalIngresos / (float)$totalVentas) : 0.0,
            'productos_vendidos' => (int)$productosVendidos,
        ];

        // Ventas por día
        $ventasPorDia = $pedidos->groupBy(function ($pedido) {
            return $pedido->created_at->format('Y-m-d');
        })->map(function ($pedidosDia) {
            $total = 0;
            foreach ($pedidosDia as $pedido) {
                $total += $this->toFloat($pedido->total_final);
            }
            return [
                'cantidad' => (int)$pedidosDia->count(),
                'total' => (float)$total
            ];
        });

        // Ventas por vendedor
        $ventasPorVendedor = $pedidos->whereNotNull('vendedor_id')
            ->groupBy('vendedor_id')
            ->map(function ($pedidosVendedor) {
                $vendedor = $pedidosVendedor->first()->vendedor;
                if (!$vendedor) {
                    return null;
                }
                $totalVentas = 0.0;
                foreach ($pedidosVendedor as $pedido) {
                    $totalVentas += $this->toFloat($pedido->total_final);
                }
                return [
                    'vendedor' => $vendedor->name ?? 'Sin nombre',
                    'email' => $vendedor->email ?? '',
                    'cantidad_pedidos' => (int)$pedidosVendedor->count(),
                    'total_ventas' => (float)$totalVentas,
                    'comision_estimada' => (float)($totalVentas * 0.1),
                ];
            })
            ->filter(); // Eliminar elementos null

        // Productos más vendidos
        $productosData = [];

        foreach ($pedidos as $pedido) {
            $detalles = $this->convertDecimal128InArray($pedido->detalles_embebidos ?? []);
            foreach ($detalles as $detalle) {
                $productoId = $detalle['producto_id'] ?? null;
                if (!$productoId) continue;

                if (!isset($productosData[$productoId])) {
                    $productosData[$productoId] = [
                        'producto' => $detalle['producto_nombre'] ?? 'Producto sin nombre',
                        'categoria' => 'General',
                        'cantidad_vendida' => 0,
                        'total_ingresos' => 0.0,
                    ];
                }

                $cantidadActual = (int)($productosData[$productoId]['cantidad_vendida'] ?? 0);
                $ingresosActual = (float)($productosData[$productoId]['total_ingresos'] ?? 0);

                $productosData[$productoId]['cantidad_vendida'] = $cantidadActual + (int)($detalle['cantidad'] ?? 0);
                $productosData[$productoId]['total_ingresos'] = $ingresosActual + (float)($detalle['subtotal'] ?? 0);
            }
        }

        $productosMasVendidos = collect($productosData)
            ->sortByDesc('cantidad_vendida')
            ->take(10);

        // Clientes más activos
        $clientesMasActivos = $pedidos->groupBy('cliente_id')
            ->map(function ($pedidosCliente) {
                $cliente = $pedidosCliente->first()->cliente;
                if (!$cliente) {
                    return null;
                }
                $totalGastado = 0.0;
                foreach ($pedidosCliente as $pedido) {
                    $totalGastado += $this->toFloat($pedido->total_final);
                }
                return [
                    'cliente' => $cliente->name ?? 'Sin nombre',
                    'email' => $cliente->email ?? '',
                    'cantidad_pedidos' => (int)$pedidosCliente->count(),
                    'total_gastado' => (float)$totalGastado,
                ];
            })
            ->filter() // Eliminar elementos null
            ->sortByDesc('total_gastado')
            ->take(10);

        // Ventas por estado
        $ventasPorEstado = $pedidos->groupBy('estado')->map(function ($pedidosEstado) {
            $total = 0;
            foreach ($pedidosEstado as $pedido) {
                $total += $this->toFloat($pedido->total_final);
            }
            return [
                'cantidad' => (int)$pedidosEstado->count(),
                'total' => (float)$total
            ];
        });

        // Para filtros
        $vendedores = User::where('rol', 'vendedor')->orderBy('name')->get();

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
        $totalUnidades = 0;
        $totalIngresos = 0;
        foreach ($productosConVentas as $producto) {
            $totalUnidades += (int)($producto->cantidad_vendida ?? 0);
            $totalIngresos += (float)($producto->ingresos_totales ?? 0);
        }

        $stats = [
            'productos_vendidos' => (int)$productosConVentas->count(),
            'productos_sin_ventas' => (int)$productosSinVentas->count(),
            'total_unidades' => (int)$totalUnidades,
            'total_ingresos' => (float)$totalIngresos,
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
        $totalComisiones = 0;
        $totalVentas = 0;
        foreach ($comisiones as $comision) {
            $totalComisiones += (float)($comision->comision_estimada ?? 0);
            $totalVentas += (float)($comision->total_ventas ?? 0);
        }

        $vendedoresActivos = $comisiones->count();
        $stats = [
            'total_comisiones' => (float)$totalComisiones,
            'total_ventas' => (float)$totalVentas,
            'vendedores_activos' => (int)$vendedoresActivos,
            'promedio_comision' => $vendedoresActivos > 0 ? (float)((float)$totalComisiones / (float)$vendedoresActivos) : 0.0,
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
        $fechaInicio = $request->get('fecha_inicio', now()->subMonth()->format('Y-m-d'));
        $fechaFin = $request->get('fecha_fin', now()->format('Y-m-d'));
        $vendedorId = $request->get('vendedor_id');

        // Obtener los mismos datos que en el método ventas
        $data = $this->getDatosVentas($fechaInicio, $fechaFin, $vendedorId);

        return $this->exportarVentasPDF($data, $fechaInicio, $fechaFin);
    }

    private function getDatosVentas($fechaInicio, $fechaFin, $vendedorId = null)
    {
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
        $totalIngresos = 0;
        $productosVendidos = 0;

        foreach ($pedidos as $pedido) {
            $totalIngresos += $this->toFloat($pedido->total_final);
            $detalles = $this->convertDecimal128InArray($pedido->detalles_embebidos ?? []);
            foreach ($detalles as $detalle) {
                $productosVendidos += (int)($detalle['cantidad'] ?? 0);
            }
        }

        $totalVentasCount = $pedidos->count();
        $stats = [
            'total_ventas' => (int)$totalVentasCount,
            'total_ingresos' => (float)$totalIngresos,
            'ticket_promedio' => $totalVentasCount > 0 ? (float)((float)$totalIngresos / (float)$totalVentasCount) : 0.0,
            'productos_vendidos' => (int)$productosVendidos,
        ];

        // Ventas por día
        $ventasPorDia = $pedidos->groupBy(function ($pedido) {
            return $pedido->created_at->format('Y-m-d');
        })->map(function ($pedidosDia) {
            $total = 0;
            foreach ($pedidosDia as $pedido) {
                $total += $this->toFloat($pedido->total_final);
            }
            return [
                'cantidad' => (int)$pedidosDia->count(),
                'total' => (float)$total
            ];
        });

        // Ventas por vendedor
        $ventasPorVendedor = $pedidos->whereNotNull('vendedor_id')
            ->groupBy('vendedor_id')
            ->map(function ($pedidosVendedor) {
                $vendedor = $pedidosVendedor->first()->vendedor;
                if (!$vendedor) {
                    return null;
                }
                $totalVentas = 0.0;
                foreach ($pedidosVendedor as $pedido) {
                    $totalVentas += $this->toFloat($pedido->total_final);
                }
                return [
                    'vendedor' => $vendedor->name ?? 'Sin nombre',
                    'email' => $vendedor->email ?? '',
                    'cantidad_pedidos' => (int)$pedidosVendedor->count(),
                    'total_ventas' => (float)$totalVentas,
                    'comision_estimada' => (float)($totalVentas * 0.1),
                ];
            })
            ->filter(); // Eliminar elementos null

        // Productos más vendidos
        $productosData = [];
        foreach ($pedidos as $pedido) {
            $detalles = $this->convertDecimal128InArray($pedido->detalles_embebidos ?? []);
            foreach ($detalles as $detalle) {
                $productoId = $detalle['producto_id'] ?? null;
                if (!$productoId) continue;

                if (!isset($productosData[$productoId])) {
                    $productosData[$productoId] = [
                        'producto' => $detalle['producto_nombre'] ?? 'Producto sin nombre',
                        'categoria' => 'General',
                        'cantidad_vendida' => 0,
                        'total_ingresos' => 0.0,
                    ];
                }

                $cantidadActual = (int)($productosData[$productoId]['cantidad_vendida'] ?? 0);
                $ingresosActual = (float)($productosData[$productoId]['total_ingresos'] ?? 0);

                $productosData[$productoId]['cantidad_vendida'] = $cantidadActual + (int)($detalle['cantidad'] ?? 0);
                $productosData[$productoId]['total_ingresos'] = $ingresosActual + (float)($detalle['subtotal'] ?? 0);
            }
        }

        $productosMasVendidos = collect($productosData)
            ->sortByDesc('cantidad_vendida')
            ->take(10);

        // Clientes más activos
        $clientesMasActivos = $pedidos->groupBy('cliente_id')
            ->map(function ($pedidosCliente) {
                $cliente = $pedidosCliente->first()->cliente;
                if (!$cliente) {
                    return null;
                }
                $totalGastado = 0.0;
                foreach ($pedidosCliente as $pedido) {
                    $totalGastado += $this->toFloat($pedido->total_final);
                }
                return [
                    'cliente' => $cliente->name ?? 'Sin nombre',
                    'email' => $cliente->email ?? '',
                    'cantidad_pedidos' => (int)$pedidosCliente->count(),
                    'total_gastado' => (float)$totalGastado,
                ];
            })
            ->filter() // Eliminar elementos null
            ->sortByDesc('total_gastado')
            ->take(10);

        // Ventas por estado
        $ventasPorEstado = $pedidos->groupBy('estado')->map(function ($pedidosEstado) {
            $total = 0;
            foreach ($pedidosEstado as $pedido) {
                $total += $this->toFloat($pedido->total_final);
            }
            return [
                'cantidad' => (int)$pedidosEstado->count(),
                'total' => (float)$total
            ];
        });

        return [
            'stats' => $stats,
            'ventasPorDia' => $ventasPorDia,
            'ventasPorVendedor' => $ventasPorVendedor,
            'productosMasVendidos' => $productosMasVendidos,
            'clientesMasActivos' => $clientesMasActivos,
            'ventasPorEstado' => $ventasPorEstado,
        ];
    }

    private function exportarVentasPDF($data, $fechaInicio, $fechaFin)
    {
        try {
            $viewData = array_merge($data, [
                'fechaInicio' => $fechaInicio,
                'fechaFin' => $fechaFin
            ]);

            $pdf = Pdf::loadView('admin.reportes.pdf.ventas', $viewData);
            $pdf->setPaper('A4', 'portrait');
            $pdf->setOptions([
                'defaultFont' => 'Arial',
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => false
            ]);

            $nombreArchivo = 'reporte_ventas_' . str_replace('-', '_', $fechaInicio) . '_al_' . str_replace('-', '_', $fechaFin) . '.pdf';

            return $pdf->download($nombreArchivo);
        } catch (\Exception $e) {
            \Log::error('Error generando PDF: ' . $e->getMessage());
            return response()->json(['error' => 'Error generando PDF: ' . $e->getMessage()], 500);
        }
    }

}
