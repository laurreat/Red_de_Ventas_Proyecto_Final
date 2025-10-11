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
                $porcentajeCumplimiento = ($totalVentas / $miembro->meta_mensual) * 100;
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
        $equipoIds = $equipo->pluck('id');

        // Top vendedores por monto
        $topVentas = Pedido::selectRaw('vendedor_id, SUM(total_final) as total_ventas')
                           ->whereIn('vendedor_id', $equipoIds)
                           ->whereBetween('created_at', [$fechas['inicio'], $fechas['fin']])
                           ->groupBy('vendedor_id')
                           ->orderByDesc('total_ventas')
                           ->limit(5)
                           ->with('vendedor')
                           ->get();

        // Top vendedores por cantidad
        $topCantidad = Pedido::selectRaw('vendedor_id, COUNT(*) as cantidad_ventas')
                             ->whereIn('vendedor_id', $equipoIds)
                             ->whereBetween('created_at', [$fechas['inicio'], $fechas['fin']])
                             ->groupBy('vendedor_id')
                             ->orderByDesc('cantidad_ventas')
                             ->limit(5)
                             ->with('vendedor')
                             ->get();

        // Top por comisiones
        $topComisiones = Comision::selectRaw('user_id, SUM(monto_comision) as total_comisiones')
                               ->whereIn('user_id', $equipoIds)
                               ->whereBetween('created_at', [$fechas['inicio'], $fechas['fin']])
                               ->groupBy('user_id')
                               ->orderByDesc('total_comisiones')
                               ->limit(5)
                               ->with('usuario')
                               ->get();

        return [
            'top_ventas' => $topVentas,
            'top_cantidad' => $topCantidad,
            'top_comisiones' => $topComisiones
        ];
    }
}
