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
            $ventasQuery->whereHas('cliente', function($q) use ($cliente) {
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
        $ventasPeriodo = (clone $baseQuery)->where('estado', '!=', 'cancelado')->sum('total_final');
        $pedidosPeriodo = (clone $baseQuery)->where('estado', '!=', 'cancelado')->count();

        // Comparación con periodo anterior
        $periodoAnterior = $this->obtenerPeriodoAnterior($periodo);
        $ventasPeriodoAnterior = Pedido::whereIn('vendedor_id', $redIds)
            ->whereBetween('created_at', $periodoAnterior)
            ->where('estado', '!=', 'cancelado')
            ->sum('total_final');

        $crecimiento = 0;
        if ($ventasPeriodoAnterior > 0) {
            $crecimiento = (($ventasPeriodo - $ventasPeriodoAnterior) / $ventasPeriodoAnterior) * 100;
        }

        // Estados de pedidos
        $estadosPedidos = (clone $baseQuery)
            ->select('estado', DB::raw('count(*) as total'))
            ->groupBy('estado')
            ->pluck('total', 'estado');

        // Ticket promedio
        $ticketPromedio = $pedidosPeriodo > 0 ? $ventasPeriodo / $pedidosPeriodo : 0;

        // Ventas por vendedor activo
        $vendedoresActivos = (clone $baseQuery)
            ->where('estado', '!=', 'cancelado')
            ->distinct('vendedor_id')
            ->count('vendedor_id');

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

        $ranking = User::whereIn('id', $redIds)
            ->withCount(['pedidosComoVendedor as total_pedidos' => function($q) use ($fechaInicio) {
                $q->where('estado', '!=', 'cancelado');
                if ($fechaInicio) {
                    $q->where('created_at', '>=', $fechaInicio);
                }
            }])
            ->withSum(['pedidosComoVendedor as total_ventas' => function($q) use ($fechaInicio) {
                $q->where('estado', '!=', 'cancelado');
                if ($fechaInicio) {
                    $q->where('created_at', '>=', $fechaInicio);
                }
            }], 'total_final')
            ->having('total_ventas', '>', 0)
            ->orderByDesc('total_ventas')
            ->take(10)
            ->get()
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

        $query = DB::table('pedido_detalles')
            ->join('pedidos', 'pedido_detalles.pedido_id', '=', 'pedidos.id')
            ->join('productos', 'pedido_detalles.producto_id', '=', 'productos.id')
            ->whereIn('pedidos.vendedor_id', $redIds)
            ->where('pedidos.estado', '!=', 'cancelado');

        if ($fechaInicio) {
            $query->where('pedidos.created_at', '>=', $fechaInicio);
        }

        return $query->select(
                'productos.nombre',
                'productos.precio',
                DB::raw('SUM(pedido_detalles.cantidad) as total_vendido'),
                DB::raw('SUM(pedido_detalles.cantidad * pedido_detalles.precio_unitario) as total_ingresos')
            )
            ->groupBy('productos.id', 'productos.nombre', 'productos.precio')
            ->orderByDesc('total_vendido')
            ->take(10)
            ->get();
    }

    private function obtenerEvolucionVentas($redIds)
    {
        return collect(range(0, 11))->map(function($i) use ($redIds) {
            $fecha = Carbon::now()->subMonths($i);
            $ventas = Pedido::whereIn('vendedor_id', $redIds)
                ->whereYear('created_at', $fecha->year)
                ->whereMonth('created_at', $fecha->month)
                ->where('estado', '!=', 'cancelado')
                ->sum('total_final');

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

        $ventasPorDia = $query->select(
                DB::raw('DAYOFWEEK(created_at) as dia_semana'),
                DB::raw('SUM(total_final) as total_ventas'),
                DB::raw('COUNT(*) as total_pedidos')
            )
            ->groupBy('dia_semana')
            ->get();

        $diasSemana = [
            1 => 'Domingo',
            2 => 'Lunes',
            3 => 'Martes',
            4 => 'Miércoles',
            5 => 'Jueves',
            6 => 'Viernes',
            7 => 'Sábado'
        ];

        $resultado = [];
        for ($i = 1; $i <= 7; $i++) {
            $venta = $ventasPorDia->firstWhere('dia_semana', $i);
            $resultado[] = [
                'dia' => $diasSemana[$i],
                'ventas' => $venta ? $venta->total_ventas : 0,
                'pedidos' => $venta ? $venta->total_pedidos : 0
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

        $pedidos = $query->with('detalles.producto')->get();

        if ($pedidos->isEmpty()) {
            return 0;
        }

        $rentabilidadTotal = 0;
        $pedidosConRentabilidad = 0;

        foreach ($pedidos as $pedido) {
            $costoTotal = 0;
            $ventaTotal = $pedido->total_final;

            foreach ($pedido->detalles as $detalle) {
                $costoTotal += ($detalle->producto->costo ?? 0) * $detalle->cantidad;
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
        return collect([
            [
                'estado' => 'pendiente',
                'fecha' => $venta->created_at,
                'usuario' => $venta->vendedor->name,
                'observaciones' => 'Pedido creado'
            ],
            [
                'estado' => $venta->estado,
                'fecha' => $venta->updated_at,
                'usuario' => $venta->vendedor->name,
                'observaciones' => 'Estado actual'
            ]
        ]);
    }

    private function obtenerComisionesVenta($venta)
    {
        return $venta->vendedor->comisiones()
            ->where('pedido_id', $venta->id)
            ->with('user')
            ->get();
    }

    private function calcularRentabilidad($venta)
    {
        $costoTotal = 0;
        $ventaTotal = $venta->total_final;

        foreach ($venta->detalles as $detalle) {
            $costoTotal += ($detalle->producto->costo ?? 0) * $detalle->cantidad;
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
            'ventas_mes' => $vendedor->pedidosComoVendedor()
                ->where('created_at', '>=', $inicioMes)
                ->where('estado', '!=', 'cancelado')
                ->sum('total_final'),

            'pedidos_mes' => $vendedor->pedidosComoVendedor()
                ->where('created_at', '>=', $inicioMes)
                ->where('estado', '!=', 'cancelado')
                ->count(),

            'ticket_promedio_mes' => $vendedor->pedidosComoVendedor()
                ->where('created_at', '>=', $inicioMes)
                ->where('estado', '!=', 'cancelado')
                ->avg('total_final') ?? 0,

            'total_comisiones' => $vendedor->comisiones_ganadas ?? 0,

            'referidos_totales' => $vendedor->referidos->count(),

            'dias_activo' => $vendedor->created_at->diffInDays(now())
        ];
    }
}