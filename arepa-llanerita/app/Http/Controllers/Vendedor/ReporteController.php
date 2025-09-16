<?php

namespace App\Http\Controllers\Vendedor;

use App\Http\Controllers\Controller;
use App\Models\Pedido;
use App\Models\Comision;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class ReporteController extends Controller
{
    public function ventas(Request $request)
    {
        $vendedor = Auth::user();

        // Filtros de fecha
        $fechaInicio = $request->get('fecha_inicio', Carbon::now()->subMonths(3)->format('Y-m-d'));
        $fechaFin = $request->get('fecha_fin', Carbon::now()->format('Y-m-d'));

        // Consulta base de pedidos
        $query = Pedido::where('vendedor_id', $vendedor->id)
                      ->whereBetween('created_at', [$fechaInicio, $fechaFin])
                      ->with('cliente');

        // Filtros adicionales
        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        $pedidos = $query->orderBy('created_at', 'desc')->get();

        // Estadísticas del período
        $stats = [
            'total_ventas' => $pedidos->sum('total_final'),
            'cantidad_pedidos' => $pedidos->count(),
            'promedio_venta' => $pedidos->count() > 0 ? $pedidos->sum('total_final') / $pedidos->count() : 0,
            'mejor_venta' => $pedidos->max('total_final'),
            'pedidos_completados' => $pedidos->where('estado', 'completado')->count(),
            'pedidos_pendientes' => $pedidos->where('estado', 'pendiente')->count(),
            'pedidos_cancelados' => $pedidos->where('estado', 'cancelado')->count()
        ];

        // Ventas por mes
        $ventasPorMes = $this->getVentasPorMes($vendedor, $fechaInicio, $fechaFin);

        // Top clientes
        $topClientes = $pedidos->groupBy('cliente_id')
                              ->map(function ($group) {
                                  return [
                                      'cliente' => $group->first()->cliente,
                                      'total_compras' => $group->sum('total_final'),
                                      'cantidad_pedidos' => $group->count()
                                  ];
                              })
                              ->sortByDesc('total_compras')
                              ->take(10);

        return view('vendedor.reportes.ventas', compact(
            'pedidos', 'stats', 'ventasPorMes', 'topClientes', 'fechaInicio', 'fechaFin'
        ));
    }

    public function rendimiento(Request $request)
    {
        $vendedor = Auth::user();

        // Período de análisis
        $periodo = $request->get('periodo', 'mes'); // mes, trimestre, año
        $fechas = $this->obtenerFechasPeriodo($periodo);

        // Métricas de rendimiento
        $metricas = $this->calcularMetricasRendimiento($vendedor, $fechas);

        // Evolución de métricas (últimos 6 períodos)
        $evolucion = $this->calcularEvolucionRendimiento($vendedor, $periodo);

        // Comparación con metas
        $comparacionMetas = $this->compararConMetas($vendedor, $fechas);

        // Ranking entre vendedores
        $ranking = $this->calcularRankingVendedores($vendedor, $fechas);

        return view('vendedor.reportes.rendimiento', compact(
            'metricas', 'evolucion', 'comparacionMetas', 'ranking', 'periodo'
        ));
    }

    public function comisiones(Request $request)
    {
        $vendedor = Auth::user();

        // Filtros de fecha
        $fechaInicio = $request->get('fecha_inicio', Carbon::now()->subMonths(6)->format('Y-m-d'));
        $fechaFin = $request->get('fecha_fin', Carbon::now()->format('Y-m-d'));

        // Comisiones del período
        $comisiones = Comision::where('user_id', $vendedor->id)
                             ->whereBetween('created_at', [$fechaInicio, $fechaFin])
                             ->with(['pedido', 'referido'])
                             ->orderBy('created_at', 'desc')
                             ->get();

        // Estadísticas de comisiones
        $stats = [
            'total_comisiones' => $comisiones->sum('monto_comision'),
            'comisiones_directas' => $comisiones->where('tipo_comision', 'venta_directa')->sum('monto_comision'),
            'comisiones_referidos' => $comisiones->where('tipo_comision', 'referido')->sum('monto_comision'),
            'comisiones_pendientes' => $comisiones->where('estado', 'pendiente')->sum('monto_comision'),
            'comisiones_pagadas' => $comisiones->where('estado', 'pagado')->sum('monto_comision'),
            'promedio_comision' => $comisiones->count() > 0 ? $comisiones->sum('monto_comision') / $comisiones->count() : 0
        ];

        // Comisiones por mes
        $comisionesPorMes = $this->getComisionesPorMes($vendedor, $fechaInicio, $fechaFin);

        // Comisiones por tipo
        $comisionesPorTipo = $comisiones->groupBy('tipo_comision')
                                      ->map(function ($group) {
                                          return $group->sum('monto_comision');
                                      });

        return view('vendedor.reportes.comisiones', compact(
            'comisiones', 'stats', 'comisionesPorMes', 'comisionesPorTipo', 'fechaInicio', 'fechaFin'
        ));
    }

    private function getVentasPorMes($vendedor, $fechaInicio, $fechaFin)
    {
        $inicio = Carbon::parse($fechaInicio);
        $fin = Carbon::parse($fechaFin);
        $meses = [];

        while ($inicio->lte($fin)) {
            $ventasMes = Pedido::where('vendedor_id', $vendedor->id)
                              ->whereYear('created_at', $inicio->year)
                              ->whereMonth('created_at', $inicio->month)
                              ->sum('total_final');

            $cantidadMes = Pedido::where('vendedor_id', $vendedor->id)
                                ->whereYear('created_at', $inicio->year)
                                ->whereMonth('created_at', $inicio->month)
                                ->count();

            $meses[] = [
                'mes' => $inicio->format('M Y'),
                'ventas' => $ventasMes,
                'cantidad' => $cantidadMes
            ];

            $inicio->addMonth();
        }

        return $meses;
    }

    private function getComisionesPorMes($vendedor, $fechaInicio, $fechaFin)
    {
        $inicio = Carbon::parse($fechaInicio);
        $fin = Carbon::parse($fechaFin);
        $meses = [];

        while ($inicio->lte($fin)) {
            $comisionesMes = Comision::where('user_id', $vendedor->id)
                                   ->whereYear('created_at', $inicio->year)
                                   ->whereMonth('created_at', $inicio->month)
                                   ->sum('monto_comision');

            $meses[] = [
                'mes' => $inicio->format('M Y'),
                'comisiones' => $comisionesMes
            ];

            $inicio->addMonth();
        }

        return $meses;
    }

    private function obtenerFechasPeriodo($periodo)
    {
        $now = Carbon::now();

        switch ($periodo) {
            case 'trimestre':
                return [
                    'inicio' => $now->copy()->startOfQuarter(),
                    'fin' => $now->copy()->endOfQuarter()
                ];
            case 'año':
                return [
                    'inicio' => $now->copy()->startOfYear(),
                    'fin' => $now->copy()->endOfYear()
                ];
            default: // mes
                return [
                    'inicio' => $now->copy()->startOfMonth(),
                    'fin' => $now->copy()->endOfMonth()
                ];
        }
    }

    private function calcularMetricasRendimiento($vendedor, $fechas)
    {
        $pedidos = Pedido::where('vendedor_id', $vendedor->id)
                        ->whereBetween('created_at', [$fechas['inicio'], $fechas['fin']])
                        ->get();

        $comisiones = Comision::where('user_id', $vendedor->id)
                             ->whereBetween('created_at', [$fechas['inicio'], $fechas['fin']])
                             ->sum('monto_comision');

        return [
            'total_ventas' => $pedidos->sum('total_final'),
            'cantidad_pedidos' => $pedidos->count(),
            'promedio_venta' => $pedidos->count() > 0 ? $pedidos->sum('total_final') / $pedidos->count() : 0,
            'total_comisiones' => $comisiones,
            'tasa_conversion' => $pedidos->where('estado', 'completado')->count() / max($pedidos->count(), 1) * 100,
            'clientes_unicos' => $pedidos->pluck('cliente_id')->unique()->count()
        ];
    }

    private function calcularEvolucionRendimiento($vendedor, $periodo)
    {
        $periodos = [];
        $cantidadPeriodos = 6;

        for ($i = $cantidadPeriodos - 1; $i >= 0; $i--) {
            switch ($periodo) {
                case 'trimestre':
                    $fecha = Carbon::now()->subQuarters($i);
                    $inicio = $fecha->copy()->startOfQuarter();
                    $fin = $fecha->copy()->endOfQuarter();
                    $label = 'Q' . $fecha->quarter . ' ' . $fecha->year;
                    break;
                case 'año':
                    $fecha = Carbon::now()->subYears($i);
                    $inicio = $fecha->copy()->startOfYear();
                    $fin = $fecha->copy()->endOfYear();
                    $label = $fecha->year;
                    break;
                default: // mes
                    $fecha = Carbon::now()->subMonths($i);
                    $inicio = $fecha->copy()->startOfMonth();
                    $fin = $fecha->copy()->endOfMonth();
                    $label = $fecha->format('M Y');
                    break;
            }

            $ventas = Pedido::where('vendedor_id', $vendedor->id)
                           ->whereBetween('created_at', [$inicio, $fin])
                           ->sum('total_final');

            $cantidad = Pedido::where('vendedor_id', $vendedor->id)
                             ->whereBetween('created_at', [$inicio, $fin])
                             ->count();

            $periodos[] = [
                'periodo' => $label,
                'ventas' => $ventas,
                'cantidad' => $cantidad
            ];
        }

        return $periodos;
    }

    private function compararConMetas($vendedor, $fechas)
    {
        $metaMensual = $vendedor->meta_mensual ?? 0;
        $ventasActuales = Pedido::where('vendedor_id', $vendedor->id)
                               ->whereBetween('created_at', [$fechas['inicio'], $fechas['fin']])
                               ->sum('total_final');

        return [
            'meta' => $metaMensual,
            'ventas' => $ventasActuales,
            'cumplimiento' => $metaMensual > 0 ? ($ventasActuales / $metaMensual) * 100 : 0,
            'diferencia' => $ventasActuales - $metaMensual
        ];
    }

    private function calcularRankingVendedores($vendedor, $fechas)
    {
        $vendedores = User::where('rol', 'vendedor')
                         ->withSum(['pedidosVendedor as ventas_periodo' => function($q) use ($fechas) {
                             $q->whereBetween('created_at', [$fechas['inicio'], $fechas['fin']]);
                         }], 'total_final')
                         ->orderBy('ventas_periodo', 'desc')
                         ->get();

        $miPosicion = $vendedores->search(function ($v) use ($vendedor) {
            return $v->id === $vendedor->id;
        }) + 1;

        return [
            'mi_posicion' => $miPosicion,
            'total_vendedores' => $vendedores->count(),
            'top_vendedores' => $vendedores->take(5),
            'mis_ventas' => $vendedores->where('id', $vendedor->id)->first()->ventas_periodo ?? 0
        ];
    }
}