<?php

namespace App\Http\Controllers\Lider;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Pedido;
use App\Models\Comision;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;

class ReporteController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            if (!auth()->user()->esLider() && !auth()->user()->esAdmin()) {
                abort(403, 'Acceso denegado. Solo líderes.');
            }
            return $next($request);
        });
    }

    // MÉTODO ELIMINADO - USAR lider/ventas EN SU LUGAR
    // El módulo lider/ventas ya proporciona toda la funcionalidad de ventas con analytics avanzados

    public function equipo(Request $request)
    {
        $lider = auth()->user();

        // Filtros
        $periodo = $request->get('periodo', 'mes_actual');
        $metrica = $request->get('metrica', 'ventas');

        // Obtener red del líder
        $redIds = $this->obtenerRedCompleta($lider);

        // Reporte de rendimiento del equipo
        $rendimientoEquipo = $this->generarRendimientoEquipo($redIds, $periodo);

        // Análisis de crecimiento de la red
        $crecimientoRed = $this->generarCrecimientoRed($lider);

        // Distribución por niveles
        $distribucionNiveles = $this->generarDistribucionNiveles($lider);

        // Análisis de actividad
        $analisisActividad = $this->generarAnalisisActividad($redIds, $periodo);

        // Top performers
        $topPerformers = $this->generarTopPerformers($redIds, $periodo, $metrica);

        // Metas vs Resultados
        $metasVsResultados = $this->generarMetasVsResultados($redIds, $periodo);

        return view('lider.reportes.equipo', compact(
            'rendimientoEquipo',
            'crecimientoRed',
            'distribucionNiveles',
            'analisisActividad',
            'topPerformers',
            'metasVsResultados',
            'periodo',
            'metrica'
        ));
    }

    public function exportar(Request $request)
    {
        $lider = auth()->user();
        $tipo = $request->get('tipo', 'ventas');
        $formato = $request->get('formato', 'excel');

        switch ($tipo) {
            case 'ventas':
                return $this->exportarReporteVentas($lider, $request, $formato);
            case 'equipo':
                return $this->exportarReporteEquipo($lider, $request, $formato);
            case 'comisiones':
                return $this->exportarReporteComisiones($lider, $request, $formato);
            default:
                return redirect()->back()->with('error', 'Tipo de reporte no válido.');
        }
    }

    private function obtenerRedCompleta($lider, $nivel = 1, $maxNivel = 10)
    {
        $idsRed = collect();

        if ($nivel > $maxNivel) {
            return $idsRed;
        }

        $referidosDirectos = User::where('referido_por', $lider->id)->pluck('id');
        $idsRed = $idsRed->merge($referidosDirectos);

        foreach ($referidosDirectos as $referidoId) {
            $referido = User::find($referidoId);
            if ($referido) {
                $subRed = $this->obtenerRedCompleta($referido, $nivel + 1, $maxNivel);
                $idsRed = $idsRed->merge($subRed);
            }
        }

        return $idsRed->unique();
    }

    // MÉTODOS ELIMINADOS - Ya no se necesitan
    private function metodoEliminado_generarReporteVentas($redIds, $fechaInicio, $fechaFin, $vendedor = null, $producto = null, $agrupacion = 'dia')
    {
        $query = Pedido::with(['vendedor', 'cliente', 'detalles.producto'])
            ->whereIn('vendedor_id', $redIds)
            ->whereBetween('created_at', [$fechaInicio, $fechaFin])
            ->where('estado', '!=', 'cancelado');

        if ($vendedor) {
            $query->where('vendedor_id', $vendedor);
        }

        if ($producto) {
            $query->whereHas('detalles', function($q) use ($producto) {
                $q->where('producto_id', $producto);
            });
        }

        $ventas = $query->get();

        // Agrupar según criterio
        $ventasAgrupadas = $this->agruparVentas($ventas, $agrupacion);

        return [
            'ventas_detalladas' => $ventas,
            'ventas_agrupadas' => $ventasAgrupadas,
            'total_ventas' => $ventas->sum('total_final'),
            'total_pedidos' => $ventas->count(),
            'ticket_promedio' => $ventas->count() > 0 ? $ventas->avg('total_final') : 0
        ];
    }

    private function agruparVentas($ventas, $agrupacion)
    {
        switch ($agrupacion) {
            case 'dia':
                return $ventas->groupBy(function($venta) {
                    return $venta->created_at->format('Y-m-d');
                })->map(function($grupo) {
                    return [
                        'total' => $grupo->sum('total_final'),
                        'cantidad' => $grupo->count(),
                        'promedio' => $grupo->avg('total_final')
                    ];
                });

            case 'semana':
                return $ventas->groupBy(function($venta) {
                    return $venta->created_at->format('Y-W');
                })->map(function($grupo) {
                    return [
                        'total' => $grupo->sum('total_final'),
                        'cantidad' => $grupo->count(),
                        'promedio' => $grupo->avg('total_final')
                    ];
                });

            case 'mes':
                return $ventas->groupBy(function($venta) {
                    return $venta->created_at->format('Y-m');
                })->map(function($grupo) {
                    return [
                        'total' => $grupo->sum('total_final'),
                        'cantidad' => $grupo->count(),
                        'promedio' => $grupo->avg('total_final')
                    ];
                });

            case 'vendedor':
                return $ventas->groupBy('vendedor_id')->map(function($grupo) {
                    return [
                        'vendedor' => $grupo->first()->vendedor->name,
                        'total' => $grupo->sum('total_final'),
                        'cantidad' => $grupo->count(),
                        'promedio' => $grupo->avg('total_final')
                    ];
                });

            default:
                return collect();
        }
    }

    private function generarResumenEjecutivo($redIds, $fechaInicio, $fechaFin)
    {
        $baseQuery = Pedido::whereIn('vendedor_id', $redIds)
            ->whereBetween('created_at', [$fechaInicio, $fechaFin])
            ->where('estado', '!=', 'cancelado');

        $totalVentas = to_float((clone $baseQuery)->sum('total_final'));
        $totalPedidos = (clone $baseQuery)->count();

        // Contar vendedores únicos
        $vendedoresActivos = (clone $baseQuery)->get()->pluck('vendedor_id')->unique()->count();

        // Producto más vendido - usando datos embebidos en detalles
        $pedidos = (clone $baseQuery)->get();
        $productosContador = [];

        foreach ($pedidos as $pedido) {
            $detalles = $pedido->getRawDetalles();
            foreach ($detalles as $detalle) {
                $productoNombre = $detalle['producto_data']['nombre'] ?? 'Producto sin nombre';
                $cantidad = $detalle['cantidad'] ?? 0;

                if (!isset($productosContador[$productoNombre])) {
                    $productosContador[$productoNombre] = 0;
                }
                $productosContador[$productoNombre] += $cantidad;
            }
        }

        arsort($productosContador);
        $productoMasVendido = null;
        if (!empty($productosContador)) {
            $nombreProducto = array_key_first($productosContador);
            $productoMasVendido = (object)[
                'nombre' => $nombreProducto,
                'total_vendido' => $productosContador[$nombreProducto]
            ];
        }

        // Cliente más valioso - usando datos embebidos
        $clientesCompras = [];
        foreach ($pedidos as $pedido) {
            $clienteNombre = $pedido->cliente_data['name'] ?? 'Cliente sin nombre';
            $totalPedido = to_float($pedido->total_final);

            if (!isset($clientesCompras[$clienteNombre])) {
                $clientesCompras[$clienteNombre] = 0;
            }
            $clientesCompras[$clienteNombre] += $totalPedido;
        }

        arsort($clientesCompras);
        $clienteMasValioso = null;
        if (!empty($clientesCompras)) {
            $nombreCliente = array_key_first($clientesCompras);
            $clienteMasValioso = (object)[
                'name' => $nombreCliente,
                'total_compras' => $clientesCompras[$nombreCliente]
            ];
        }

        return [
            'total_ventas' => $totalVentas,
            'total_pedidos' => $totalPedidos,
            'ticket_promedio' => $totalPedidos > 0 ? $totalVentas / $totalPedidos : 0,
            'vendedores_activos' => $vendedoresActivos,
            'producto_mas_vendido' => $productoMasVendido,
            'cliente_mas_valioso' => $clienteMasValioso
        ];
    }

    private function generarAnalisisTendencias($redIds, $fechaInicio, $fechaFin)
    {
        $fechaInicioCarbon = Carbon::parse($fechaInicio);
        $fechaFinCarbon = Carbon::parse($fechaFin);
        $dias = $fechaInicioCarbon->diffInDays($fechaFinCarbon);

        if ($dias <= 31) {
            // Tendencia diaria
            return $this->generarTendenciaDiaria($redIds, $fechaInicio, $fechaFin);
        } elseif ($dias <= 365) {
            // Tendencia semanal
            return $this->generarTendenciaSemanal($redIds, $fechaInicio, $fechaFin);
        } else {
            // Tendencia mensual
            return $this->generarTendenciaMensual($redIds, $fechaInicio, $fechaFin);
        }
    }

    private function generarTendenciaDiaria($redIds, $fechaInicio, $fechaFin)
    {
        $pedidos = Pedido::whereIn('vendedor_id', $redIds)
            ->whereBetween('created_at', [$fechaInicio, $fechaFin])
            ->where('estado', '!=', 'cancelado')
            ->get();

        return $pedidos->groupBy(function($pedido) {
            return $pedido->created_at->format('Y-m-d');
        })->map(function($grupo, $fecha) {
            return (object)[
                'fecha' => $fecha,
                'ventas' => to_float($grupo->sum('total_final')),
                'pedidos' => $grupo->count()
            ];
        })->sortBy('fecha')->values();
    }

    private function generarTendenciaSemanal($redIds, $fechaInicio, $fechaFin)
    {
        $pedidos = Pedido::whereIn('vendedor_id', $redIds)
            ->whereBetween('created_at', [$fechaInicio, $fechaFin])
            ->where('estado', '!=', 'cancelado')
            ->get();

        return $pedidos->groupBy(function($pedido) {
            return $pedido->created_at->format('Y-W'); // Año-Semana
        })->map(function($grupo, $semana) {
            return (object)[
                'semana' => $semana,
                'ventas' => to_float($grupo->sum('total_final')),
                'pedidos' => $grupo->count()
            ];
        })->sortBy('semana')->values();
    }

    private function generarTendenciaMensual($redIds, $fechaInicio, $fechaFin)
    {
        $pedidos = Pedido::whereIn('vendedor_id', $redIds)
            ->whereBetween('created_at', [$fechaInicio, $fechaFin])
            ->where('estado', '!=', 'cancelado')
            ->get();

        return $pedidos->groupBy(function($pedido) {
            return $pedido->created_at->format('Y-m'); // Año-Mes
        })->map(function($grupo, $mes) {
            return (object)[
                'mes' => $mes,
                'ventas' => to_float($grupo->sum('total_final')),
                'pedidos' => $grupo->count()
            ];
        })->sortBy('mes')->values();
    }

    private function generarComparacionPeriodos($redIds, $fechaInicio, $fechaFin)
    {
        $fechaInicioCarbon = Carbon::parse($fechaInicio);
        $fechaFinCarbon = Carbon::parse($fechaFin);
        $diasPeriodo = $fechaInicioCarbon->diffInDays($fechaFinCarbon) + 1;

        // Periodo anterior
        $fechaInicioAnterior = $fechaInicioCarbon->copy()->subDays($diasPeriodo);
        $fechaFinAnterior = $fechaInicioCarbon->copy()->subDay();

        $ventasActual = to_float(Pedido::whereIn('vendedor_id', $redIds)
            ->whereBetween('created_at', [$fechaInicio, $fechaFin])
            ->where('estado', '!=', 'cancelado')
            ->sum('total_final'));

        $ventasAnterior = to_float(Pedido::whereIn('vendedor_id', $redIds)
            ->whereBetween('created_at', [$fechaInicioAnterior, $fechaFinAnterior])
            ->where('estado', '!=', 'cancelado')
            ->sum('total_final'));

        $crecimiento = $ventasAnterior > 0 ? (($ventasActual - $ventasAnterior) / $ventasAnterior) * 100 : 0;

        return [
            'ventas_actual' => $ventasActual,
            'ventas_anterior' => $ventasAnterior,
            'crecimiento' => round($crecimiento, 2)
        ];
    }

    private function generarRendimientoEquipo($redIds, $periodo)
    {
        $fechaInicio = $this->obtenerFechaInicioPeriodo($periodo);

        return User::whereIn('id', $redIds)
            ->with(['pedidosComoVendedor' => function($q) use ($fechaInicio) {
                $q->where('created_at', '>=', $fechaInicio)
                  ->where('estado', '!=', 'cancelado');
            }])
            ->get()
            ->map(function($usuario) use ($fechaInicio) {
                $ventas = $usuario->pedidosComoVendedor->sum('total_final');
                $pedidos = $usuario->pedidosComoVendedor->count();

                return [
                    'usuario' => $usuario,
                    'ventas' => $ventas,
                    'pedidos' => $pedidos,
                    'ticket_promedio' => $pedidos > 0 ? $ventas / $pedidos : 0,
                    'referidos_nuevos' => $usuario->referidos()
                        ->where('created_at', '>=', $fechaInicio)
                        ->count(),
                    'rendimiento' => $this->calcularRendimiento($usuario, $fechaInicio)
                ];
            })
            ->sortByDesc('ventas');
    }

    private function generarCrecimientoRed($lider)
    {
        return collect(range(0, 11))->map(function($i) use ($lider) {
            $fecha = Carbon::now()->subMonths($i);
            $redIds = $this->obtenerRedCompleta($lider);

            $nuevosReferidos = User::whereIn('id', $redIds)
                ->whereYear('created_at', $fecha->year)
                ->whereMonth('created_at', $fecha->month)
                ->count();

            $totalRed = User::whereIn('id', $redIds)
                ->where('created_at', '<=', $fecha->endOfMonth())
                ->count();

            return [
                'mes' => $fecha->format('M Y'),
                'nuevos' => $nuevosReferidos,
                'total' => $totalRed
            ];
        })->reverse()->values();
    }

    private function generarDistribucionNiveles($lider)
    {
        $distribucion = [];
        for ($i = 1; $i <= 5; $i++) {
            $distribucion["nivel_$i"] = $this->contarReferidosNivel($lider, $i);
        }
        return $distribucion;
    }

    private function contarReferidosNivel($lider, $nivel)
    {
        if ($nivel === 1) {
            return $lider->referidos->count();
        }

        $count = 0;
        $this->contarReferidosRecursivo($lider, 1, $nivel, $count);
        return $count;
    }

    private function contarReferidosRecursivo($usuario, $nivelActual, $nivelObjetivo, &$count)
    {
        if ($nivelActual === $nivelObjetivo) {
            $count += $usuario->referidos->count();
        } elseif ($nivelActual < $nivelObjetivo) {
            foreach ($usuario->referidos as $referido) {
                $this->contarReferidosRecursivo($referido, $nivelActual + 1, $nivelObjetivo, $count);
            }
        }
    }

    private function generarAnalisisActividad($redIds, $periodo)
    {
        $fechaInicio = $this->obtenerFechaInicioPeriodo($periodo);

        $activosVentas = User::whereIn('id', $redIds)
            ->whereHas('pedidosComoVendedor', function($q) use ($fechaInicio) {
                $q->where('created_at', '>=', $fechaInicio)
                  ->where('estado', '!=', 'cancelado');
            })
            ->count();

        $activosReferidos = User::whereIn('id', $redIds)
            ->whereHas('referidos', function($q) use ($fechaInicio) {
                $q->where('created_at', '>=', $fechaInicio);
            })
            ->count();

        $totalRed = count($redIds);

        return [
            'activos_ventas' => $activosVentas,
            'activos_referidos' => $activosReferidos,
            'total_red' => $totalRed,
            'tasa_actividad_ventas' => $totalRed > 0 ? round(($activosVentas / $totalRed) * 100, 2) : 0,
            'tasa_actividad_referidos' => $totalRed > 0 ? round(($activosReferidos / $totalRed) * 100, 2) : 0
        ];
    }

    private function generarTopPerformers($redIds, $periodo, $metrica)
    {
        $fechaInicio = $this->obtenerFechaInicioPeriodo($periodo);

        $query = User::whereIn('id', $redIds);

        switch ($metrica) {
            case 'ventas':
                return $query->withSum(['pedidosComoVendedor as total_ventas' => function($q) use ($fechaInicio) {
                        $q->where('created_at', '>=', $fechaInicio)
                          ->where('estado', '!=', 'cancelado');
                    }], 'total_final')
                    ->having('total_ventas', '>', 0)
                    ->orderByDesc('total_ventas')
                    ->take(10)
                    ->get();

            case 'referidos':
                return $query->withCount(['referidos as nuevos_referidos' => function($q) use ($fechaInicio) {
                        $q->where('created_at', '>=', $fechaInicio);
                    }])
                    ->having('nuevos_referidos', '>', 0)
                    ->orderByDesc('nuevos_referidos')
                    ->take(10)
                    ->get();

            default:
                return collect();
        }
    }

    private function generarMetasVsResultados($redIds, $periodo)
    {
        $fechaInicio = $this->obtenerFechaInicioPeriodo($periodo);

        return User::whereIn('id', $redIds)
            ->whereNotNull('meta_mensual')
            ->where('meta_mensual', '>', 0)
            ->get()
            ->map(function($usuario) use ($fechaInicio) {
                $ventas = $usuario->pedidosComoVendedor()
                    ->where('created_at', '>=', $fechaInicio)
                    ->where('estado', '!=', 'cancelado')
                    ->sum('total_final');

                $progreso = $usuario->meta_mensual > 0 ? (to_float($ventas) / to_float($usuario->meta_mensual)) * 100 : 0;

                return [
                    'usuario' => $usuario,
                    'meta' => $usuario->meta_mensual,
                    'ventas' => $ventas,
                    'progreso' => round($progreso, 2),
                    'cumplida' => $progreso >= 100
                ];
            })
            ->sortByDesc('progreso');
    }

    private function obtenerFechaInicioPeriodo($periodo)
    {
        switch ($periodo) {
            case 'mes_actual':
                return Carbon::now()->startOfMonth();
            case 'trimestre_actual':
                return Carbon::now()->startOfQuarter();
            case 'ano_actual':
                return Carbon::now()->startOfYear();
            default:
                return Carbon::now()->startOfMonth();
        }
    }

    private function calcularRendimiento($usuario, $fechaInicio)
    {
        $ventas = $usuario->pedidosComoVendedor()
            ->where('created_at', '>=', $fechaInicio)
            ->where('estado', '!=', 'cancelado')
            ->sum('total_final');

        $referidos = $usuario->referidos()
            ->where('created_at', '>=', $fechaInicio)
            ->count();

        // Fórmula simple de rendimiento
        $scoreVentas = min(($ventas / 1000000) * 70, 70); // Máximo 70 puntos por ventas
        $scoreReferidos = min($referidos * 15, 30); // Máximo 30 puntos por referidos

        return min($scoreVentas + $scoreReferidos, 100);
    }

    private function exportarReporteVentas($lider, $request, $formato)
    {
        $fechaInicio = $request->get('fecha_inicio', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $fechaFin = $request->get('fecha_fin', Carbon::now()->format('Y-m-d'));

        $redIds = $this->obtenerRedCompleta($lider);
        $redIds->push($lider->id);

        $datos = Pedido::with(['vendedor', 'cliente'])
            ->whereIn('vendedor_id', $redIds)
            ->whereBetween('created_at', [$fechaInicio, $fechaFin])
            ->where('estado', '!=', 'cancelado')
            ->get();

        if ($formato === 'csv') {
            return $this->exportarCSV($datos, 'reporte_ventas_' . date('Y-m-d') . '.csv');
        }

        // Por defecto Excel
        return $this->exportarExcel($datos, 'reporte_ventas_' . date('Y-m-d') . '.xlsx');
    }

    private function exportarReporteEquipo($lider, $request, $formato)
    {
        $periodo = $request->get('periodo', 'mes_actual');
        $redIds = $this->obtenerRedCompleta($lider);

        $datos = $this->generarRendimientoEquipo($redIds, $periodo);

        if ($formato === 'csv') {
            return $this->exportarCSV($datos, 'reporte_equipo_' . date('Y-m-d') . '.csv');
        }

        return $this->exportarExcel($datos, 'reporte_equipo_' . date('Y-m-d') . '.xlsx');
    }

    private function exportarReporteComisiones($lider, $request, $formato)
    {
        $fechaInicio = $request->get('fecha_inicio', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $fechaFin = $request->get('fecha_fin', Carbon::now()->format('Y-m-d'));

        $datos = $lider->comisiones()
            ->with(['pedido', 'referido'])
            ->whereBetween('created_at', [$fechaInicio, $fechaFin])
            ->get();

        if ($formato === 'csv') {
            return $this->exportarCSV($datos, 'reporte_comisiones_' . date('Y-m-d') . '.csv');
        }

        return $this->exportarExcel($datos, 'reporte_comisiones_' . date('Y-m-d') . '.xlsx');
    }

    private function exportarCSV($datos, $nombreArchivo)
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $nombreArchivo . '"',
        ];

        $callback = function() use ($datos) {
            $file = fopen('php://output', 'w');

            // Escribir headers basados en el primer elemento
            if ($datos->isNotEmpty()) {
                $primer = $datos->first();
                if (is_array($primer)) {
                    fputcsv($file, array_keys($primer));
                } else {
                    fputcsv($file, array_keys($primer->toArray()));
                }
            }

            // Escribir datos
            foreach ($datos as $fila) {
                if (is_array($fila)) {
                    fputcsv($file, $fila);
                } else {
                    fputcsv($file, $fila->toArray());
                }
            }

            fclose($file);
        };

        return Response::stream($callback, 200, $headers);
    }

    private function exportarExcel($datos, $nombreArchivo)
    {
        // Implementación básica - en producción usar Laravel Excel
        return $this->exportarCSV($datos, str_replace('.xlsx', '.csv', $nombreArchivo));
    }
}