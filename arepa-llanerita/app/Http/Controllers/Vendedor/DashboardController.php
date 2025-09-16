<?php

namespace App\Http\Controllers\Vendedor;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Pedido;
use App\Models\Comision;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $vendedor = Auth::user();
        $mesActual = Carbon::now();
        $inicioMes = $mesActual->copy()->startOfMonth();
        $finMes = $mesActual->copy()->endOfMonth();

        // Métricas principales del vendedor
        $stats = $this->calcularEstadisticas($vendedor, $inicioMes, $finMes);

        // Pedidos recientes (últimos 5)
        $pedidos_recientes = Pedido::where('vendedor_id', $vendedor->id)
                                   ->orderBy('created_at', 'desc')
                                   ->with('cliente')
                                   ->limit(5)
                                   ->get();

        // Evolución de ventas (últimos 6 meses)
        $evolucionVentas = $this->calcularEvolucionVentas($vendedor);

        // Metas y progreso
        $progresoMetas = $this->calcularProgresoMetas($vendedor, $inicioMes, $finMes);

        return view('vendedor.dashboard.index', compact(
            'stats',
            'pedidos_recientes',
            'evolucionVentas',
            'progresoMetas'
        ));
    }

    private function calcularEstadisticas($vendedor, $inicioMes, $finMes)
    {
        // Ventas del mes
        $ventasMes = Pedido::where('vendedor_id', $vendedor->id)
                          ->whereBetween('created_at', [$inicioMes, $finMes])
                          ->sum('total_final');

        // Pedidos del mes
        $pedidosMes = Pedido::where('vendedor_id', $vendedor->id)
                           ->whereBetween('created_at', [$inicioMes, $finMes])
                           ->count();

        // Comisiones ganadas este mes
        $comisionesGanadas = Comision::where('user_id', $vendedor->id)
                                   ->whereBetween('created_at', [$inicioMes, $finMes])
                                   ->sum('monto_comision');

        // Comisiones disponibles para retiro
        $comisionesDisponibles = Comision::where('user_id', $vendedor->id)
                                        ->where('estado', 'pendiente')
                                        ->sum('monto_comision');

        // Referidos totales
        $totalReferidos = User::where('referido_por', $vendedor->id)->count();

        // Nuevos referidos este mes
        $nuevosReferidosMes = User::where('referido_por', $vendedor->id)
                                 ->whereBetween('created_at', [$inicioMes, $finMes])
                                 ->count();

        // Referidos activos (con al menos 1 pedido)
        $referidosActivos = User::where('referido_por', $vendedor->id)
                               ->whereHas('pedidosVendedor')
                               ->count();

        // Meta mensual
        $metaMensual = $vendedor->meta_mensual ?? 0;

        return [
            'ventas_mes' => $ventasMes,
            'pedidos_mes' => $pedidosMes,
            'comisiones_ganadas' => $comisionesGanadas,
            'comisiones_disponibles' => $comisionesDisponibles,
            'total_referidos' => $totalReferidos,
            'nuevos_referidos_mes' => $nuevosReferidosMes,
            'referidos_activos' => $referidosActivos,
            'meta_mensual' => $metaMensual
        ];
    }

    private function calcularEvolucionVentas($vendedor)
    {
        $meses = [];

        // Últimos 6 meses
        for ($i = 5; $i >= 0; $i--) {
            $mes = Carbon::now()->subMonths($i);

            $ventas = Pedido::where('vendedor_id', $vendedor->id)
                           ->whereYear('created_at', $mes->year)
                           ->whereMonth('created_at', $mes->month)
                           ->sum('total_final');

            $cantidad = Pedido::where('vendedor_id', $vendedor->id)
                             ->whereYear('created_at', $mes->year)
                             ->whereMonth('created_at', $mes->month)
                             ->count();

            $meses[] = [
                'mes' => $mes->format('M Y'),
                'ventas' => $ventas,
                'cantidad' => $cantidad
            ];
        }

        return $meses;
    }

    private function calcularProgresoMetas($vendedor, $inicioMes, $finMes)
    {
        $metaMensual = $vendedor->meta_mensual ?? 0;

        if ($metaMensual <= 0) {
            return [
                'meta_mensual' => 0,
                'ventas_actuales' => 0,
                'porcentaje_cumplimiento' => 0,
                'dias_restantes' => 0,
                'promedio_diario_necesario' => 0
            ];
        }

        $ventasActuales = Pedido::where('vendedor_id', $vendedor->id)
                               ->whereBetween('created_at', [$inicioMes, $finMes])
                               ->sum('total_final');

        $porcentajeCumplimiento = ($ventasActuales / $metaMensual) * 100;

        $diasRestantes = Carbon::now()->diffInDays($finMes);
        $ventasFaltantes = max($metaMensual - $ventasActuales, 0);
        $promedioDiarioNecesario = $diasRestantes > 0 ? $ventasFaltantes / $diasRestantes : 0;

        return [
            'meta_mensual' => $metaMensual,
            'ventas_actuales' => $ventasActuales,
            'porcentaje_cumplimiento' => $porcentajeCumplimiento,
            'dias_restantes' => $diasRestantes,
            'promedio_diario_necesario' => $promedioDiarioNecesario
        ];
    }

    public function getVentasChart()
    {
        $vendedor = Auth::user();
        $evolucionVentas = $this->calcularEvolucionVentas($vendedor);

        return response()->json($evolucionVentas);
    }

    public function getMetricasRapidas()
    {
        $vendedor = Auth::user();
        $mesActual = Carbon::now();
        $inicioMes = $mesActual->copy()->startOfMonth();
        $finMes = $mesActual->copy()->endOfMonth();

        $stats = $this->calcularEstadisticas($vendedor, $inicioMes, $finMes);

        return response()->json($stats);
    }
}