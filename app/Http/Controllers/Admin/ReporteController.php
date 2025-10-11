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
            // Si no es un array, intentar convertir si es Decimal128
            if ($data instanceof Decimal128) {
                return (float) $data->__toString();
            }
            return $data;
        }

        foreach ($data as $key => $value) {
            if ($value instanceof Decimal128) {
                $data[$key] = (float) $value->__toString();
            } elseif (is_array($value)) {
                $data[$key] = $this->convertDecimal128InArray($value);
            } elseif (is_object($value) && $value instanceof Decimal128) {
                $data[$key] = (float) $value->__toString();
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
        // Validar entradas para prevenir inyección MongoDB
        $validated = $request->validate([
            'fecha_inicio' => 'nullable|date|before_or_equal:today',
            'fecha_fin' => 'nullable|date|after_or_equal:fecha_inicio|before_or_equal:today',
            'vendedor_id' => 'nullable|string|max:24', // ObjectId MongoDB tiene 24 caracteres
            'tipo_reporte' => 'nullable|string|in:resumen,detallado'
        ]);

        // Fechas por defecto (último mes)
        $fechaInicio = $validated['fecha_inicio'] ?? now()->subMonth()->format('Y-m-d');
        $fechaFin = $validated['fecha_fin'] ?? now()->format('Y-m-d');
        $vendedorId = $validated['vendedor_id'] ?? null;
        $tipoReporte = $validated['tipo_reporte'] ?? 'resumen';

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
            try {
                $totalIngresos += $this->toFloat($pedido->total_final ?? 0);
                $detalles = $this->convertDecimal128InArray($pedido->detalles_embebidos ?? []);
                foreach ($detalles as $detalle) {
                    $productosVendidos += (int)($detalle['cantidad'] ?? 0);
                }
            } catch (\Exception $e) {
                \Log::warning('Error procesando pedido en reportes', ['pedido_id' => $pedido->_id ?? 'unknown']);
                continue;
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
            try {
                if ($pedido->created_at instanceof \Carbon\Carbon) {
                    return $pedido->created_at->format('Y-m-d');
                } elseif ($pedido->created_at instanceof \MongoDB\BSON\UTCDateTime) {
                    return Carbon::createFromTimestamp($pedido->created_at->toDateTime()->getTimestamp())->format('Y-m-d');
                }
                return now()->format('Y-m-d');
            } catch (\Exception $e) {
                return now()->format('Y-m-d');
            }
        })->map(function ($pedidosDia) {
            $total = 0.0;
            foreach ($pedidosDia as $pedido) {
                $total += $this->toFloat($pedido->total_final ?? 0);
            }
            return ['cantidad' => (int)$pedidosDia->count(), 'total' => (float)$total];
        });

        // Ventas por vendedor
        $ventasPorVendedor = $pedidos->whereNotNull('vendedor_id')
            ->groupBy('vendedor_id')
            ->map(function ($pedidosVendedor) {
                try {
                    $vendedor = $pedidosVendedor->first()->vendedor;
                    if (!$vendedor) {
                        return null;
                    }
                    $totalVentas = 0.0;
                    foreach ($pedidosVendedor as $pedido) {
                        $totalVentas += $this->toFloat($pedido->total_final ?? 0);
                    }
                    return [
                        'vendedor' => $vendedor->name ?? 'Sin nombre',
                        'email' => $vendedor->email ?? '',
                        'cantidad_pedidos' => (int)$pedidosVendedor->count(),
                        'total_ventas' => (float)$totalVentas,
                        'comision_estimada' => (float)($totalVentas * 0.1),
                    ];
                } catch (\Exception $e) {
                    \Log::warning('Error procesando ventas por vendedor en reportes', ['error' => $e->getMessage()]);
                    return null;
                }
            })
            ->filter() // Eliminar elementos null
            ->values(); // Reindexar numéricamente

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
                $productosData[$productoId]['total_ingresos'] = $ingresosActual + $this->toFloat($detalle['subtotal'] ?? 0);
            }
        }

        $productosMasVendidos = collect($productosData)
            ->sortByDesc('cantidad_vendida')
            ->take(10)
            ->values(); // Reindexar numéricamente para evitar error en vista

        // Clientes más activos
        $clientesMasActivos = $pedidos->groupBy('cliente_id')
            ->map(function ($pedidosCliente) {
                try {
                    $cliente = $pedidosCliente->first()->cliente;
                    if (!$cliente) {
                        return null;
                    }
                    $totalGastado = 0.0;
                    foreach ($pedidosCliente as $pedido) {
                        $totalGastado += $this->toFloat($pedido->total_final ?? 0);
                    }
                    return [
                        'cliente' => $cliente->name ?? 'Sin nombre',
                        'email' => $cliente->email ?? '',
                        'cantidad_pedidos' => (int)$pedidosCliente->count(),
                        'total_gastado' => (float)$totalGastado,
                    ];
                } catch (\Exception $e) {
                    \Log::warning('Error procesando clientes más activos en reportes', ['error' => $e->getMessage()]);
                    return null;
                }
            })
            ->filter() // Eliminar elementos null
            ->sortByDesc('total_gastado')
            ->take(10)
            ->values(); // Reindexar numéricamente para evitar error en vista

        // Ventas por estado
        $ventasPorEstado = $pedidos->groupBy('estado')->map(function ($pedidosEstado) {
            try {
                $total = 0.0;
                foreach ($pedidosEstado as $pedido) {
                    $total += $this->toFloat($pedido->total_final ?? 0);
                }
                return [
                    'cantidad' => (int)$pedidosEstado->count(),
                    'total' => (float)$total
                ];
            } catch (\Exception $e) {
                \Log::warning('Error procesando ventas por estado en reportes', ['error' => $e->getMessage()]);
                return ['cantidad' => 0, 'total' => 0.0];
            }
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
            $totalIngresos += $this->toFloat($producto->ingresos_totales ?? 0);
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
            $totalComisiones += $this->toFloat($comision->comision_estimada ?? 0);
            $totalVentas += $this->toFloat($comision->total_ventas ?? 0);
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
        // Validar entradas para prevenir inyección MongoDB
        $validated = $request->validate([
            'fecha_inicio' => 'nullable|date|before_or_equal:today',
            'fecha_fin' => 'nullable|date|after_or_equal:fecha_inicio|before_or_equal:today',
            'vendedor_id' => 'nullable|string|max:24'
        ]);

        $fechaInicio = $validated['fecha_inicio'] ?? now()->subMonth()->format('Y-m-d');
        $fechaFin = $validated['fecha_fin'] ?? now()->format('Y-m-d');
        $vendedorId = $validated['vendedor_id'] ?? null;

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
                $productosData[$productoId]['total_ingresos'] = $ingresosActual + $this->toFloat($detalle['subtotal'] ?? 0);
            }
        }

        $productosMasVendidos = collect($productosData)
            ->sortByDesc('cantidad_vendida')
            ->take(10)
            ->values(); // Reindexar numéricamente para evitar error en vista

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
            ->take(10)
            ->values(); // Reindexar numéricamente para evitar error en vista

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
