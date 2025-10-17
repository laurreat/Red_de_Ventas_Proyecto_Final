<?php

namespace App\Http\Controllers\Lider;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Pedido;
use App\Models\Comision;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class RendimientoController extends Controller
{
    public function index(Request $request)
    {
        $lider = Auth::user();
        $periodo = $request->get('periodo', 'mes'); // mes, trimestre, año

        // Obtener miembros del equipo del líder (usuarios referidos por él)
        $equipo = User::where('referido_por', $lider->id)
                     ->where('rol', '!=', 'administrador')
                     ->get();

        // Definir fechas según el período
        $fechas = $this->obtenerFechasPeriodo($periodo);

        // Métricas del equipo
        $stats = $this->calcularMetricasEquipo($equipo, $fechas);

        // Rendimiento individual de cada miembro
        $rendimientoIndividual = $this->calcularRendimientoIndividual($equipo, $fechas);

        // Evolución de métricas (últimos 6 meses)
        $evolucionMetricas = $this->calcularEvolucionMetricas($equipo);

        // Rankings
        $rankings = $this->calcularRankings($equipo, $fechas);

        return view('lider.rendimiento.index', compact(
            'equipo',
            'stats',
            'rendimientoIndividual',
            'evolucionMetricas',
            'rankings',
            'periodo'
        ));
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

    private function calcularMetricasEquipo($equipo, $fechas)
    {
        $equipoIds = $equipo->pluck('id');

        // Pedidos del período
        $pedidos = Pedido::whereIn('vendedor_id', $equipoIds)
                         ->whereBetween('created_at', [$fechas['inicio'], $fechas['fin']])
                         ->get();

        // Comisiones del período
        $comisiones = Comision::whereIn('user_id', $equipoIds)
                             ->whereBetween('created_at', [$fechas['inicio'], $fechas['fin']])
                             ->get();

        // Calcular estadísticas
        $totalVentas = $pedidos->sum('total_final');
        $cantidadVentas = $pedidos->count();
        $totalComisiones = $comisiones->sum('monto_comision');

        // Promedio de venta
        $promedioVenta = $cantidadVentas > 0 ? $totalVentas / $cantidadVentas : 0;

        // Miembros activos (con al menos 1 pedido)
        $miembrosActivos = $pedidos->pluck('vendedor_id')->unique()->count();

        // Meta del equipo (suma de metas individuales)
        $metaEquipo = $equipo->sum('meta_mensual');

        // Porcentaje de cumplimiento
        $porcentajeCumplimiento = $metaEquipo > 0 ? ($totalVentas / $metaEquipo) * 100 : 0;

        return [
            'total_ventas' => $totalVentas,
            'cantidad_ventas' => $cantidadVentas,
            'promedio_venta' => $promedioVenta,
            'total_comisiones' => $totalComisiones,
            'miembros_activos' => $miembrosActivos,
            'total_miembros' => $equipo->count(),
            'meta_equipo' => $metaEquipo,
            'porcentaje_cumplimiento' => $porcentajeCumplimiento
        ];
    }

    private function calcularRendimientoIndividual($equipo, $fechas)
    {
        $rendimiento = [];

        foreach ($equipo as $miembro) {
            // Pedidos del miembro
            $pedidos = Pedido::where('vendedor_id', $miembro->id)
                            ->whereBetween('created_at', [$fechas['inicio'], $fechas['fin']])
                            ->get();

            // Comisiones del miembro
            $comisiones = Comision::where('user_id', $miembro->id)
                                 ->whereBetween('created_at', [$fechas['inicio'], $fechas['fin']])
                                 ->get();

            $totalVentas = $pedidos->sum('total_final');
            $cantidadVentas = $pedidos->count();
            $totalComisiones = $comisiones->sum('monto_comision');

            // Calcular porcentaje de cumplimiento
            $porcentajeCumplimiento = 0;
            if ($miembro->meta_mensual > 0) {
                $porcentajeCumplimiento = (to_float($totalVentas) / to_float($miembro->meta_mensual)) * 100;
            }

            // Determinar estado
            $estado = 'bajo';
            if ($porcentajeCumplimiento >= 90) {
                $estado = 'excelente';
            } elseif ($porcentajeCumplimiento >= 70) {
                $estado = 'bueno';
            } elseif ($porcentajeCumplimiento >= 50) {
                $estado = 'regular';
            }

            $rendimiento[] = [
                'miembro' => $miembro,
                'total_ventas' => $totalVentas,
                'cantidad_ventas' => $cantidadVentas,
                'total_comisiones' => $totalComisiones,
                'porcentaje_cumplimiento' => $porcentajeCumplimiento,
                'estado' => $estado,
                'promedio_venta' => $cantidadVentas > 0 ? $totalVentas / $cantidadVentas : 0
            ];
        }

        // Ordenar por porcentaje de cumplimiento
        usort($rendimiento, function($a, $b) {
            return $b['porcentaje_cumplimiento'] <=> $a['porcentaje_cumplimiento'];
        });

        return $rendimiento;
    }

    private function calcularEvolucionMetricas($equipo)
    {
        $equipoIds = $equipo->pluck('id');
        $meses = [];

        // Últimos 6 meses
        for ($i = 5; $i >= 0; $i--) {
            $mes = Carbon::now()->subMonths($i);

            $ventas = Pedido::whereIn('vendedor_id', $equipoIds)
                            ->whereYear('created_at', $mes->year)
                            ->whereMonth('created_at', $mes->month)
                            ->sum('total_final');

            $cantidadVentas = Pedido::whereIn('vendedor_id', $equipoIds)
                                    ->whereYear('created_at', $mes->year)
                                    ->whereMonth('created_at', $mes->month)
                                    ->count();

            $meses[] = [
                'mes' => $mes->format('M Y'),
                'ventas' => $ventas,
                'cantidad' => $cantidadVentas
            ];
        }

        return $meses;
    }

    private function calcularRankings($equipo, $fechas)
    {
        $equipoIds = $equipo->pluck('id')->toArray();

        // Top vendedores por monto - Compatible con MongoDB
        $pedidosPorVendedor = Pedido::whereIn('vendedor_id', $equipoIds)
                                    ->whereBetween('created_at', [$fechas['inicio'], $fechas['fin']])
                                    ->get()
                                    ->groupBy('vendedor_id')
                                    ->map(function($pedidos) {
                                        return [
                                            'vendedor_id' => $pedidos->first()->vendedor_id,
                                            'total_ventas' => $pedidos->sum('total_final'),
                                            'cantidad_ventas' => $pedidos->count()
                                        ];
                                    })
                                    ->sortByDesc('total_ventas')
                                    ->take(5)
                                    ->values();

        // Cargar relaciones de vendedor
        $topVentas = $pedidosPorVendedor->map(function($item) {
            $vendedor = User::find($item['vendedor_id']);
            return (object)[
                'vendedor_id' => $item['vendedor_id'],
                'total_ventas' => $item['total_ventas'],
                'vendedor' => $vendedor
            ];
        });

        // Top vendedores por cantidad
        $topCantidad = $pedidosPorVendedor->sortByDesc('cantidad_ventas')
                                          ->take(5)
                                          ->map(function($item) {
                                              $vendedor = User::find($item['vendedor_id']);
                                              return (object)[
                                                  'vendedor_id' => $item['vendedor_id'],
                                                  'cantidad_ventas' => $item['cantidad_ventas'],
                                                  'vendedor' => $vendedor
                                              ];
                                          })
                                          ->values();

        // Top por comisiones - Compatible con MongoDB
        $comisionesPorUsuario = Comision::whereIn('user_id', $equipoIds)
                                        ->whereBetween('created_at', [$fechas['inicio'], $fechas['fin']])
                                        ->get()
                                        ->groupBy('user_id')
                                        ->map(function($comisiones) {
                                            return [
                                                'user_id' => $comisiones->first()->user_id,
                                                'total_comisiones' => $comisiones->sum('monto_comision')
                                            ];
                                        })
                                        ->sortByDesc('total_comisiones')
                                        ->take(5)
                                        ->values();

        // Cargar relaciones de usuario
        $topComisiones = $comisionesPorUsuario->map(function($item) {
            $usuario = User::find($item['user_id']);
            return (object)[
                'user_id' => $item['user_id'],
                'total_comisiones' => $item['total_comisiones'],
                'usuario' => $usuario
            ];
        });

        return [
            'top_ventas' => $topVentas,
            'top_cantidad' => $topCantidad,
            'top_comisiones' => $topComisiones
        ];
    }

    public function exportarRendimiento(Request $request)
    {
        $lider = Auth::user();
        $periodo = $request->get('periodo', 'mes');

        // Obtener datos
        $equipo = User::where('referido_por', $lider->id)
                     ->where('rol', '!=', 'administrador')
                     ->get();

        $fechas = $this->obtenerFechasPeriodo($periodo);
        $stats = $this->calcularMetricasEquipo($equipo, $fechas);
        $rendimientoIndividual = $this->calcularRendimientoIndividual($equipo, $fechas);
        $rankings = $this->calcularRankings($equipo, $fechas);

        // Crear CSV con BOM para UTF-8
        $filename = 'rendimiento_equipo_' . date('Y-m-d_His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0'
        ];

        $callback = function() use ($stats, $rendimientoIndividual, $rankings, $periodo) {
            $file = fopen('php://output', 'w');

            // BOM para UTF-8
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

            // SECCIÓN 1: INFORMACIÓN GENERAL
            fputcsv($file, ['═══════════════════════════════════════════════════════════'], ',');
            fputcsv($file, ['      REPORTE DE RENDIMIENTO DEL EQUIPO'], ',');
            fputcsv($file, ['      Arepa la Llanerita - Sistema de Ventas'], ',');
            fputcsv($file, ['═══════════════════════════════════════════════════════════'], ',');
            fputcsv($file, [''], ',');
            fputcsv($file, ['Fecha de Generación:', date('d/m/Y H:i:s')], ',');
            fputcsv($file, ['Período:', ucfirst($periodo)], ',');
            fputcsv($file, [''], ',');

            // SECCIÓN 2: RESUMEN GENERAL
            fputcsv($file, ['═══════════════════════════════════════════════════════════'], ',');
            fputcsv($file, ['      RESUMEN GENERAL DEL EQUIPO'], ',');
            fputcsv($file, ['═══════════════════════════════════════════════════════════'], ',');
            fputcsv($file, [''], ',');
            fputcsv($file, ['Métrica', 'Valor'], ',');
            fputcsv($file, ['───────────────────────────────────────────────────────────'], ',');
            fputcsv($file, ['Total de Ventas', '$' . number_format($stats['total_ventas'], 2)], ',');
            fputcsv($file, ['Cantidad de Ventas', $stats['cantidad_ventas']], ',');
            fputcsv($file, ['Promedio por Venta', '$' . number_format($stats['promedio_venta'], 2)], ',');
            fputcsv($file, ['Total Comisiones', '$' . number_format($stats['total_comisiones'], 2)], ',');
            fputcsv($file, ['Miembros Activos', $stats['miembros_activos'] . ' de ' . $stats['total_miembros']], ',');
            fputcsv($file, ['Meta del Equipo', '$' . number_format($stats['meta_equipo'], 2)], ',');
            fputcsv($file, ['Cumplimiento de Meta', number_format($stats['porcentaje_cumplimiento'], 2) . '%'], ',');
            fputcsv($file, [''], ',');
            fputcsv($file, [''], ',');

            // SECCIÓN 3: RENDIMIENTO INDIVIDUAL
            fputcsv($file, ['═══════════════════════════════════════════════════════════'], ',');
            fputcsv($file, ['      RENDIMIENTO INDIVIDUAL POR MIEMBRO'], ',');
            fputcsv($file, ['═══════════════════════════════════════════════════════════'], ',');
            fputcsv($file, [''], ',');
            fputcsv($file, ['Nombre', 'Rol', 'Total Ventas', 'Cantidad', 'Promedio', 'Comisiones', '% Meta', 'Estado'], ',');
            fputcsv($file, ['───────────────────────────────────────────────────────────'], ',');

            foreach ($rendimientoIndividual as $rendimiento) {
                fputcsv($file, [
                    $rendimiento['miembro']->name,
                    ucfirst($rendimiento['miembro']->rol),
                    '$' . number_format($rendimiento['total_ventas'], 2),
                    $rendimiento['cantidad_ventas'],
                    '$' . number_format($rendimiento['promedio_venta'], 2),
                    '$' . number_format($rendimiento['total_comisiones'], 2),
                    number_format($rendimiento['porcentaje_cumplimiento'], 2) . '%',
                    ucfirst($rendimiento['estado'])
                ], ',');
            }

            fputcsv($file, [''], ',');
            fputcsv($file, [''], ',');

            // SECCIÓN 4: TOP PERFORMERS - VENTAS
            fputcsv($file, ['═══════════════════════════════════════════════════════════'], ',');
            fputcsv($file, ['      TOP 5 - MEJORES VENTAS'], ',');
            fputcsv($file, ['═══════════════════════════════════════════════════════════'], ',');
            fputcsv($file, [''], ',');
            fputcsv($file, ['Posición', 'Nombre', 'Total Ventas'], ',');
            fputcsv($file, ['───────────────────────────────────────────────────────────'], ',');

            foreach ($rankings['top_ventas'] as $index => $item) {
                $medalla = '';
                if ($index === 0) $medalla = '🥇';
                elseif ($index === 1) $medalla = '🥈';
                elseif ($index === 2) $medalla = '🥉';

                fputcsv($file, [
                    ($index + 1) . ' ' . $medalla,
                    $item->vendedor->name,
                    '$' . number_format($item->total_ventas, 2)
                ], ',');
            }

            fputcsv($file, [''], ',');
            fputcsv($file, [''], ',');

            // SECCIÓN 5: TOP PERFORMERS - CANTIDAD
            fputcsv($file, ['═══════════════════════════════════════════════════════════'], ',');
            fputcsv($file, ['      TOP 5 - MAYOR CANTIDAD DE VENTAS'], ',');
            fputcsv($file, ['═══════════════════════════════════════════════════════════'], ',');
            fputcsv($file, [''], ',');
            fputcsv($file, ['Posición', 'Nombre', 'Cantidad de Ventas'], ',');
            fputcsv($file, ['───────────────────────────────────────────────────────────'], ',');

            foreach ($rankings['top_cantidad'] as $index => $item) {
                $medalla = '';
                if ($index === 0) $medalla = '🥇';
                elseif ($index === 1) $medalla = '🥈';
                elseif ($index === 2) $medalla = '🥉';

                fputcsv($file, [
                    ($index + 1) . ' ' . $medalla,
                    $item->vendedor->name,
                    $item->cantidad_ventas . ' ventas'
                ], ',');
            }

            fputcsv($file, [''], ',');
            fputcsv($file, [''], ',');

            // SECCIÓN 6: TOP PERFORMERS - COMISIONES
            fputcsv($file, ['═══════════════════════════════════════════════════════════'], ',');
            fputcsv($file, ['      TOP 5 - MAYORES COMISIONES'], ',');
            fputcsv($file, ['═══════════════════════════════════════════════════════════'], ',');
            fputcsv($file, [''], ',');
            fputcsv($file, ['Posición', 'Nombre', 'Total Comisiones'], ',');
            fputcsv($file, ['───────────────────────────────────────────────────────────'], ',');

            foreach ($rankings['top_comisiones'] as $index => $item) {
                $medalla = '';
                if ($index === 0) $medalla = '🥇';
                elseif ($index === 1) $medalla = '🥈';
                elseif ($index === 2) $medalla = '🥉';

                fputcsv($file, [
                    ($index + 1) . ' ' . $medalla,
                    $item->usuario->name,
                    '$' . number_format($item->total_comisiones, 2)
                ], ',');
            }

            fputcsv($file, [''], ',');
            fputcsv($file, [''], ',');
            fputcsv($file, ['═══════════════════════════════════════════════════════════'], ',');
            fputcsv($file, ['      Fin del Reporte'], ',');
            fputcsv($file, ['      Generado por Sistema Arepa la Llanerita'], ',');
            fputcsv($file, ['═══════════════════════════════════════════════════════════'], ',');

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
