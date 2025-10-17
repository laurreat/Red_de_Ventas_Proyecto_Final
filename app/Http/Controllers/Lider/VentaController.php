<?php

namespace App\Http\Controllers\Lider;

use App\Http\Controllers\Controller;
use App\Models\Pedido;
use App\Models\User;
use App\Models\Producto;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class VentaController extends Controller
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

    public function index(Request $request)
    {
        $lider = auth()->user();

        // Filtros
        $periodo = $request->get('periodo', 'mes_actual');
        $vendedor = $request->get('vendedor');
        $estado = $request->get('estado');
        $cliente = $request->get('cliente');

        // Obtener IDs de la red del líder
        $redIds = $this->obtenerRedCompleta($lider);
        $redIds->push($lider->id); // Incluir al líder

        // Consulta base de ventas del equipo
        $ventasQuery = Pedido::with(['vendedor', 'cliente', 'detalles.producto'])
            ->whereIn('vendedor_id', $redIds);

        // Aplicar filtros de periodo
        $fechaInicio = $this->obtenerFechaInicioPeriodo($periodo);
        if ($fechaInicio) {
            $ventasQuery->where('created_at', '>=', $fechaInicio);
        }

        // Filtro por vendedor específico
        if ($vendedor) {
            $ventasQuery->where('vendedor_id', $vendedor);
        }

        // Filtro por estado
        if ($estado) {
            $ventasQuery->where('estado', $estado);
        }

        // Filtro por cliente
        if ($cliente) {
            $ventasQuery->where('cliente_data.name', function($q) use ($cliente) {
                $q->where('name', 'like', "%{$cliente}%")
                  ->orWhere('email', 'like', "%{$cliente}%");
            });
        }

        $ventas = $ventasQuery->orderBy('created_at', 'desc')
                            ->paginate(20);

        // Estadísticas de ventas
        $stats = $this->calcularEstadisticasVentas($redIds, $periodo);

        // Ranking de vendedores del equipo
        $rankingVendedores = $this->obtenerRankingVendedores($redIds, $periodo);

        // Productos más vendidos
        $topProductos = $this->obtenerTopProductos($redIds, $periodo);

        // Evolución de ventas
        $evolucionVentas = $this->obtenerEvolucionVentas($redIds);

        // Análisis por días de la semana
        $ventasPorDia = $this->obtenerVentasPorDia($redIds, $periodo);

        // Lista de vendedores para filtro
        $vendedoresEquipo = User::whereIn('id', $redIds)
            ->select('id', 'name')
            ->orderBy('name')
            ->get();

        return view('lider.ventas.index', compact(
            'ventas',
            'stats',
            'rankingVendedores',
            'topProductos',
            'evolucionVentas',
            'ventasPorDia',
            'vendedoresEquipo',
            'periodo',
            'vendedor',
            'estado',
            'cliente'
        ));
    }

    public function show($id)
    {
        $lider = auth()->user();
        $redIds = $this->obtenerRedCompleta($lider);
        $redIds->push($lider->id);

        $venta = Pedido::with(['vendedor', 'cliente', 'detalles.producto'])
            ->whereIn('vendedor_id', $redIds)
            ->findOrFail($id);

        // Historial de cambios de estado
        $historialEstados = $this->obtenerHistorialEstados($venta);

        // Comisiones generadas por esta venta
        $comisionesGeneradas = $this->obtenerComisionesVenta($venta);

        // Rentabilidad de la venta
        $rentabilidad = $this->calcularRentabilidad($venta);

        // Métricas del vendedor
        $metricsVendedor = $this->obtenerMetricsVendedor($venta->vendedor);

        return view('lider.ventas.show', compact(
            'venta',
            'historialEstados',
            'comisionesGeneradas',
            'rentabilidad',
            'metricsVendedor'
        ));
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

    private function obtenerFechaInicioPeriodo($periodo)
    {
        switch ($periodo) {
            case 'hoy':
                return Carbon::today();
            case 'semana_actual':
                return Carbon::now()->startOfWeek();
            case 'mes_actual':
                return Carbon::now()->startOfMonth();
            case 'mes_anterior':
                return Carbon::now()->subMonth()->startOfMonth();
            case 'trimestre_actual':
                return Carbon::now()->startOfQuarter();
            case 'ano_actual':
                return Carbon::now()->startOfYear();
            case 'ultimos_30_dias':
                return Carbon::now()->subDays(30);
            case 'ultimos_90_dias':
                return Carbon::now()->subDays(90);
            default:
                return Carbon::now()->startOfMonth();
        }
    }

    private function calcularEstadisticasVentas($redIds, $periodo)
    {
        $fechaInicio = $this->obtenerFechaInicioPeriodo($periodo);

        $baseQuery = Pedido::whereIn('vendedor_id', $redIds);

        if ($fechaInicio) {
            $baseQuery->where('created_at', '>=', $fechaInicio);
        }

        // Ventas del periodo
        $ventasPeriodo = to_float((clone $baseQuery)->where('estado', '!=', 'cancelado')->sum('total_final'));
        $pedidosPeriodo = (clone $baseQuery)->where('estado', '!=', 'cancelado')->count();

        // Comparación con periodo anterior
        $periodoAnterior = $this->obtenerPeriodoAnterior($periodo);
        $ventasPeriodoAnterior = to_float(Pedido::whereIn('vendedor_id', $redIds)
            ->whereBetween('created_at', $periodoAnterior)
            ->where('estado', '!=', 'cancelado')
            ->sum('total_final'));

        $crecimiento = 0;
        if ($ventasPeriodoAnterior > 0) {
            $crecimiento = (($ventasPeriodo - $ventasPeriodoAnterior) / $ventasPeriodoAnterior) * 100;
        }

        // Estados de pedidos - Compatible con MongoDB
        $pedidosPorEstado = (clone $baseQuery)->get()->groupBy('estado');
        $estadosPedidos = $pedidosPorEstado->map(function($pedidos) {
            return $pedidos->count();
        });

        // Ticket promedio
        $ticketPromedio = $pedidosPeriodo > 0 ? $ventasPeriodo / $pedidosPeriodo : 0;

        // Ventas por vendedor activo - Compatible con MongoDB
        $vendedoresActivos = (clone $baseQuery)
            ->where('estado', '!=', 'cancelado')
            ->get()
            ->pluck('vendedor_id')
            ->unique()
            ->count();

        return [
            'ventas_periodo' => $ventasPeriodo,
            'pedidos_periodo' => $pedidosPeriodo,
            'crecimiento' => round($crecimiento, 2),
            'ticket_promedio' => $ticketPromedio,
            'vendedores_activos' => $vendedoresActivos,
            'estados_pedidos' => $estadosPedidos,
            'conversion' => $this->calcularTasaConversion($redIds, $periodo),
            'rentabilidad_promedio' => $this->calcularRentabilidadPromedio($redIds, $periodo)
        ];
    }

    private function obtenerPeriodoAnterior($periodo)
    {
        switch ($periodo) {
            case 'hoy':
                return [Carbon::yesterday(), Carbon::today()];
            case 'semana_actual':
                $inicio = Carbon::now()->subWeek()->startOfWeek();
                return [$inicio, $inicio->copy()->endOfWeek()];
            case 'mes_actual':
                $inicio = Carbon::now()->subMonth()->startOfMonth();
                return [$inicio, $inicio->copy()->endOfMonth()];
            case 'trimestre_actual':
                $inicio = Carbon::now()->subQuarter()->startOfQuarter();
                return [$inicio, $inicio->copy()->endOfQuarter()];
            case 'ano_actual':
                $inicio = Carbon::now()->subYear()->startOfYear();
                return [$inicio, $inicio->copy()->endOfYear()];
            default:
                $inicio = Carbon::now()->subMonth()->startOfMonth();
                return [$inicio, $inicio->copy()->endOfMonth()];
        }
    }

    private function obtenerRankingVendedores($redIds, $periodo)
    {
        $fechaInicio = $this->obtenerFechaInicioPeriodo($periodo);

        // Obtener vendedores
        $vendedores = User::whereIn('id', $redIds)->get();

        // Calcular estadísticas para cada vendedor
        $ranking = $vendedores->map(function($vendedor) use ($fechaInicio) {
            $query = $vendedor->pedidosComoVendedor()
                ->where('estado', '!=', 'cancelado');

            if ($fechaInicio) {
                $query->where('created_at', '>=', $fechaInicio);
            }

            $pedidos = $query->get();

            $vendedor->total_pedidos = $pedidos->count();
            $vendedor->total_ventas = to_float($pedidos->sum('total_final'));

            return $vendedor;
        })
        ->filter(function($vendedor) {
            return $vendedor->total_ventas > 0;
        })
        ->sortByDesc('total_ventas')
        ->take(10)
        ->values()
        ->map(function($vendedor, $index) {
            $vendedor->posicion = $index + 1;
            $vendedor->ticket_promedio = $vendedor->total_pedidos > 0
                ? $vendedor->total_ventas / $vendedor->total_pedidos
                : 0;
            return $vendedor;
        });

        return $ranking;
    }

    private function obtenerTopProductos($redIds, $periodo)
    {
        $fechaInicio = $this->obtenerFechaInicioPeriodo($periodo);

        // Obtener pedidos del periodo
        $query = Pedido::whereIn('vendedor_id', $redIds)
            ->where('estado', '!=', 'cancelado');

        if ($fechaInicio) {
            $query->where('created_at', '>=', $fechaInicio);
        }

        $pedidos = $query->get();

        // Agrupar productos manualmente
        $productosVendidos = [];

        foreach ($pedidos as $pedido) {
            if (isset($pedido->detalles) && is_array($pedido->detalles)) {
                foreach ($pedido->detalles as $detalle) {
                    $productoId = $detalle['producto_id'] ?? null;
                    $cantidad = $detalle['cantidad'] ?? 0;
                    $precioUnitario = $detalle['precio_unitario'] ?? 0;

                    if (!$productoId) continue;

                    if (!isset($productosVendidos[$productoId])) {
                        $productosVendidos[$productoId] = (object)[
                            'nombre' => $detalle['producto_data']['nombre'] ?? 'Producto sin nombre',
                            'precio' => $detalle['producto_data']['precio'] ?? $precioUnitario,
                            'total_vendido' => 0,
                            'total_ingresos' => 0
                        ];
                    }

                    $productosVendidos[$productoId]->total_vendido += $cantidad;
                    $productosVendidos[$productoId]->total_ingresos += ($cantidad * $precioUnitario);
                }
            }
        }

        // Ordenar por cantidad vendida
        $productosArray = array_values($productosVendidos);
        usort($productosArray, function($a, $b) {
            return $b->total_vendido <=> $a->total_vendido;
        });

        return collect(array_slice($productosArray, 0, 10));
    }

    private function obtenerEvolucionVentas($redIds)
    {
        return collect(range(0, 11))->map(function($i) use ($redIds) {
            $fecha = Carbon::now()->subMonths($i);
            $ventas = to_float(Pedido::whereIn('vendedor_id', $redIds)
                ->whereYear('created_at', $fecha->year)
                ->whereMonth('created_at', $fecha->month)
                ->where('estado', '!=', 'cancelado')
                ->sum('total_final'));

            $pedidos = Pedido::whereIn('vendedor_id', $redIds)
                ->whereYear('created_at', $fecha->year)
                ->whereMonth('created_at', $fecha->month)
                ->where('estado', '!=', 'cancelado')
                ->count();

            return [
                'mes' => $fecha->format('M Y'),
                'ventas' => $ventas,
                'pedidos' => $pedidos,
                'ticket_promedio' => $pedidos > 0 ? $ventas / $pedidos : 0
            ];
        })->reverse()->values();
    }

    private function obtenerVentasPorDia($redIds, $periodo)
    {
        $fechaInicio = $this->obtenerFechaInicioPeriodo($periodo);

        $query = Pedido::whereIn('vendedor_id', $redIds)
            ->where('estado', '!=', 'cancelado');

        if ($fechaInicio) {
            $query->where('created_at', '>=', $fechaInicio);
        }

        $pedidos = $query->get();

        // Agrupar por día de la semana
        $ventasPorDia = [];

        foreach ($pedidos as $pedido) {
            $diaSemana = $pedido->created_at->dayOfWeek; // 0=Domingo, 6=Sábado

            if (!isset($ventasPorDia[$diaSemana])) {
                $ventasPorDia[$diaSemana] = [
                    'total_ventas' => 0,
                    'total_pedidos' => 0
                ];
            }

            $ventasPorDia[$diaSemana]['total_ventas'] += to_float($pedido->total_final ?? 0);
            $ventasPorDia[$diaSemana]['total_pedidos']++;
        }

        $diasSemana = [
            0 => 'Domingo',
            1 => 'Lunes',
            2 => 'Martes',
            3 => 'Miércoles',
            4 => 'Jueves',
            5 => 'Viernes',
            6 => 'Sábado'
        ];

        $resultado = [];
        for ($i = 0; $i <= 6; $i++) {
            $resultado[] = [
                'dia' => $diasSemana[$i],
                'ventas' => $ventasPorDia[$i]['total_ventas'] ?? 0,
                'pedidos' => $ventasPorDia[$i]['total_pedidos'] ?? 0
            ];
        }

        return collect($resultado);
    }

    private function calcularTasaConversion($redIds, $periodo)
    {
        // Aquí puedes implementar lógica de conversión basada en leads/clientes potenciales
        // Por ahora retornamos un cálculo básico
        $fechaInicio = $this->obtenerFechaInicioPeriodo($periodo);

        $pedidosCompletados = Pedido::whereIn('vendedor_id', $redIds)
            ->where('estado', 'completado');

        $pedidosTotal = Pedido::whereIn('vendedor_id', $redIds)
            ->where('estado', '!=', 'cancelado');

        if ($fechaInicio) {
            $pedidosCompletados->where('created_at', '>=', $fechaInicio);
            $pedidosTotal->where('created_at', '>=', $fechaInicio);
        }

        $completados = $pedidosCompletados->count();
        $total = $pedidosTotal->count();

        return $total > 0 ? round(($completados / $total) * 100, 2) : 0;
    }

    private function calcularRentabilidadPromedio($redIds, $periodo)
    {
        $fechaInicio = $this->obtenerFechaInicioPeriodo($periodo);

        $query = Pedido::whereIn('vendedor_id', $redIds)
            ->where('estado', '!=', 'cancelado');

        if ($fechaInicio) {
            $query->where('created_at', '>=', $fechaInicio);
        }

        $pedidos = $query->get();

        if ($pedidos->isEmpty()) {
            return 0;
        }

        $rentabilidadTotal = 0;
        $pedidosConRentabilidad = 0;

        foreach ($pedidos as $pedido) {
            $costoTotal = 0;
            $ventaTotal = to_float($pedido->total_final ?? 0);

            // Calcular costo desde los detalles embebidos
            if (isset($pedido->detalles) && is_array($pedido->detalles)) {
                foreach ($pedido->detalles as $detalle) {
                    $costo = $detalle['producto_data']['costo'] ?? 0;
                    $cantidad = $detalle['cantidad'] ?? 0;
                    $costoTotal += $costo * $cantidad;
                }
            }

            if ($ventaTotal > 0) {
                $rentabilidad = (($ventaTotal - $costoTotal) / $ventaTotal) * 100;
                $rentabilidadTotal += $rentabilidad;
                $pedidosConRentabilidad++;
            }
        }

        return $pedidosConRentabilidad > 0
            ? round($rentabilidadTotal / $pedidosConRentabilidad, 2)
            : 0;
    }

    private function obtenerHistorialEstados($venta)
    {
        // Simular historial de estados - en una implementación real esto vendría de una tabla de auditoría
        $historial = [];

        // Estado inicial
        $historial[] = [
            'estado' => 'pendiente',
            'fecha' => $venta->created_at,
            'usuario' => $venta->vendedor->name ?? 'Sistema',
            'observaciones' => 'Pedido creado'
        ];

        // Estado actual (si es diferente)
        if ($venta->estado !== 'pendiente') {
            $historial[] = [
                'estado' => $venta->estado,
                'fecha' => $venta->updated_at,
                'usuario' => $venta->vendedor->name ?? 'Sistema',
                'observaciones' => 'Estado actual: ' . ucfirst($venta->estado)
            ];
        }

        return collect($historial);
    }

    private function obtenerComisionesVenta($venta)
    {
        // Obtener comisiones relacionadas con este pedido usando el ID correcto
        $pedidoId = $venta->_id ?? $venta->id;

        return \App\Models\Comision::where('pedido_id', $pedidoId)->get();
    }

    private function calcularRentabilidad($venta)
    {
        $costoTotal = 0;
        $ventaTotal = to_float($venta->total_final ?? 0);

        // Calcular costo desde los detalles embebidos
        if (isset($venta->detalles) && is_array($venta->detalles)) {
            foreach ($venta->detalles as $detalle) {
                $costo = $detalle['producto_data']['costo'] ?? 0;
                $cantidad = $detalle['cantidad'] ?? 0;
                $costoTotal += $costo * $cantidad;
            }
        }

        $utilidad = $ventaTotal - $costoTotal;
        $margen = $ventaTotal > 0 ? ($utilidad / $ventaTotal) * 100 : 0;

        return [
            'costo_total' => $costoTotal,
            'venta_total' => $ventaTotal,
            'utilidad' => $utilidad,
            'margen' => round($margen, 2)
        ];
    }

    private function obtenerMetricsVendedor($vendedor)
    {
        $inicioMes = Carbon::now()->startOfMonth();

        return [
            'ventas_mes' => to_float($vendedor->pedidosComoVendedor()
                ->where('created_at', '>=', $inicioMes)
                ->where('estado', '!=', 'cancelado')
                ->sum('total_final')),

            'pedidos_mes' => $vendedor->pedidosComoVendedor()
                ->where('created_at', '>=', $inicioMes)
                ->where('estado', '!=', 'cancelado')
                ->count(),

            'ticket_promedio_mes' => to_float($vendedor->pedidosComoVendedor()
                ->where('created_at', '>=', $inicioMes)
                ->where('estado', '!=', 'cancelado')
                ->avg('total_final')) ?? 0,

            'total_comisiones' => to_float($vendedor->comisiones_ganadas ?? 0),

            'referidos_totales' => $vendedor->referidos->count(),

            'dias_activo' => $vendedor->created_at->diffInDays(now())
        ];
    }

    public function exportar(Request $request)
    {
        $lider = auth()->user();
        $formato = $request->get('formato', 'csv'); // csv o pdf

        // Filtros
        $periodo = $request->get('periodo', 'mes_actual');
        $vendedor = $request->get('vendedor');
        $estado = $request->get('estado');

        // Obtener IDs de la red del líder
        $redIds = $this->obtenerRedCompleta($lider);
        $redIds->push($lider->id);

        // Consulta de ventas con filtros
        $ventasQuery = Pedido::whereIn('vendedor_id', $redIds);

        $fechaInicio = $this->obtenerFechaInicioPeriodo($periodo);
        if ($fechaInicio) {
            $ventasQuery->where('created_at', '>=', $fechaInicio);
        }

        if ($vendedor) {
            $ventasQuery->where('vendedor_id', $vendedor);
        }

        if ($estado) {
            $ventasQuery->where('estado', $estado);
        }

        $ventas = $ventasQuery->orderBy('created_at', 'desc')->get();

        // Estadísticas
        $stats = $this->calcularEstadisticasVentas($redIds, $periodo);

        if ($formato === 'pdf') {
            return $this->exportarPDF($ventas, $stats, $periodo);
        }

        return $this->exportarCSV($ventas, $stats, $periodo);
    }

    private function exportarCSV($ventas, $stats, $periodo)
    {
        $filename = 'ventas_equipo_' . $periodo . '_' . date('Y-m-d_His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0'
        ];

        $callback = function() use ($ventas, $stats, $periodo) {
            $file = fopen('php://output', 'w');

            // BOM para UTF-8
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

            // ENCABEZADO PRINCIPAL
            fputcsv($file, ['═══════════════════════════════════════════════════════════════════════════'], ',');
            fputcsv($file, ['                    REPORTE DE VENTAS DEL EQUIPO'], ',');
            fputcsv($file, ['                    Arepa la Llanerita - Sistema de Ventas'], ',');
            fputcsv($file, ['═══════════════════════════════════════════════════════════════════════════'], ',');
            fputcsv($file, [''], ',');
            fputcsv($file, ['Fecha de Generación:', date('d/m/Y H:i:s')], ',');
            fputcsv($file, ['Período:', ucfirst(str_replace('_', ' ', $periodo))], ',');
            fputcsv($file, ['Líder:', auth()->user()->name], ',');
            fputcsv($file, [''], ',');

            // RESUMEN ESTADÍSTICO
            fputcsv($file, ['═══════════════════════════════════════════════════════════════════════════'], ',');
            fputcsv($file, ['                    RESUMEN ESTADÍSTICO'], ',');
            fputcsv($file, ['═══════════════════════════════════════════════════════════════════════════'], ',');
            fputcsv($file, [''], ',');
            fputcsv($file, ['Métrica', 'Valor'], ',');
            fputcsv($file, ['───────────────────────────────────────────────────────────────────────────'], ',');
            fputcsv($file, ['Total de Ventas', '$' . number_format($stats['ventas_periodo'], 2)], ',');
            fputcsv($file, ['Total de Pedidos', $stats['pedidos_periodo']], ',');
            fputcsv($file, ['Ticket Promedio', '$' . number_format($stats['ticket_promedio'], 2)], ',');
            fputcsv($file, ['Vendedores Activos', $stats['vendedores_activos']], ',');
            fputcsv($file, ['Crecimiento vs Período Anterior', $stats['crecimiento'] . '%'], ',');
            fputcsv($file, ['Tasa de Conversión', $stats['conversion'] . '%'], ',');
            fputcsv($file, ['Rentabilidad Promedio', $stats['rentabilidad_promedio'] . '%'], ',');
            fputcsv($file, [''], ',');
            fputcsv($file, [''], ',');

            // DETALLE DE VENTAS
            fputcsv($file, ['═══════════════════════════════════════════════════════════════════════════'], ',');
            fputcsv($file, ['                    DETALLE DE VENTAS'], ',');
            fputcsv($file, ['═══════════════════════════════════════════════════════════════════════════'], ',');
            fputcsv($file, [''], ',');
            fputcsv($file, [
                'Fecha',
                'Pedido #',
                'Vendedor',
                'Cliente',
                'Email Cliente',
                'Estado',
                'Subtotal',
                'Descuento',
                'Impuestos',
                'Total',
                'Productos',
                'Cant. Items'
            ], ',');
            fputcsv($file, ['───────────────────────────────────────────────────────────────────────────'], ',');

            foreach ($ventas as $venta) {
                $cantidadItems = 0;
                $productos = [];

                if (isset($venta->detalles) && is_array($venta->detalles)) {
                    foreach ($venta->detalles as $detalle) {
                        $cantidadItems += $detalle['cantidad'] ?? 0;
                        $productos[] = $detalle['producto_data']['nombre'] ?? 'Producto';
                    }
                }

                fputcsv($file, [
                    $venta->created_at->format('d/m/Y H:i'),
                    '#' . str_pad($venta->id, 6, '0', STR_PAD_LEFT),
                    $venta->vendedor->name ?? 'N/A',
                    $venta->cliente->name ?? 'Cliente',
                    $venta->cliente->email ?? 'Sin email',
                    ucfirst($venta->estado),
                    '$' . number_format($venta->subtotal ?? 0, 2),
                    '$' . number_format($venta->descuento ?? 0, 2),
                    '$' . number_format($venta->impuestos ?? 0, 2),
                    '$' . number_format($venta->total_final, 2),
                    implode(', ', $productos),
                    $cantidadItems
                ], ',');
            }

            fputcsv($file, [''], ',');
            fputcsv($file, ['───────────────────────────────────────────────────────────────────────────'], ',');
            fputcsv($file, [
                'TOTAL',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '$' . number_format($ventas->sum('total_final'), 2),
                '',
                ''
            ], ',');
            fputcsv($file, [''], ',');
            fputcsv($file, [''], ',');

            // PIE DE PÁGINA
            fputcsv($file, ['═══════════════════════════════════════════════════════════════════════════'], ',');
            fputcsv($file, ['                    Fin del Reporte'], ',');
            fputcsv($file, ['                    Generado por Sistema Arepa la Llanerita'], ',');
            fputcsv($file, ['═══════════════════════════════════════════════════════════════════════════'], ',');

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    private function exportarPDF($ventas, $stats, $periodo)
    {
        // TODO: Implementar exportación PDF con librería DOMPDF o similar
        // Por ahora retornamos el CSV
        return $this->exportarCSV($ventas, $stats, $periodo);
    }
}