<?php

namespace App\Http\Controllers\Vendedor;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Pedido;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class MetaController extends Controller
{
    public function index()
    {
        $vendedor = Auth::user();

        // Meta actual
        $metaActual = $vendedor->meta_mensual ?? 0;

        // Progreso de la meta
        $progreso = $this->calcularProgresoMeta($vendedor);

        // Historial de metas (últimos 6 meses)
        $historialMetas = $this->obtenerHistorialMetas($vendedor);

        // Comparación con otros vendedores (ranking)
        $ranking = $this->calcularRanking($vendedor);

        // Predicción de cumplimiento
        $prediccion = $this->calcularPrediccion($vendedor, $progreso);

        return view('vendedor.metas.index', compact(
            'metaActual',
            'progreso',
            'historialMetas',
            'ranking',
            'prediccion'
        ));
    }

    public function actualizar(Request $request)
    {
        $request->validate([
            'meta_mensual' => 'required|numeric|min:0',
            'notas' => 'nullable|string|max:500'
        ]);

        $vendedor = Auth::user();

        $vendedor->update([
            'meta_mensual' => $request->meta_mensual
        ]);

        // Aquí podrías guardar las notas en una tabla separada si es necesario

        return redirect()->back()
                        ->with('success', 'Meta actualizada exitosamente.');
    }

    private function calcularProgresoMeta($vendedor)
    {
        $mesActual = Carbon::now();
        $inicioMes = $mesActual->copy()->startOfMonth();
        $finMes = $mesActual->copy()->endOfMonth();

        $metaMensual = $vendedor->meta_mensual ?? 0;

        if ($metaMensual <= 0) {
            return [
                'meta_mensual' => 0,
                'ventas_actuales' => 0,
                'porcentaje_cumplimiento' => 0,
                'dias_transcurridos' => 0,
                'dias_restantes' => 0,
                'promedio_diario_actual' => 0,
                'promedio_diario_necesario' => 0,
                'proyeccion_fin_mes' => 0,
                'diferencia_meta' => 0,
                'estado' => 'sin_meta'
            ];
        }

        // Ventas del mes actual
        $ventasActuales = Pedido::where('vendedor_id', $vendedor->id)
                               ->whereBetween('created_at', [$inicioMes, $finMes])
                               ->sum('total_final');

        // Cálculos de tiempo
        $diasTranscurridos = Carbon::now()->day;
        $diasRestantes = $finMes->day - Carbon::now()->day;
        $diasTotalesMes = $finMes->day;

        // Promedios
        $promedioDiarioActual = $diasTranscurridos > 0 ? $ventasActuales / $diasTranscurridos : 0;
        $ventasFaltantes = max($metaMensual - $ventasActuales, 0);
        $promedioDiarioNecesario = $diasRestantes > 0 ? $ventasFaltantes / $diasRestantes : 0;

        // Proyección
        $proyeccionFinMes = $promedioDiarioActual * $diasTotalesMes;

        // Porcentaje de cumplimiento
        $porcentajeCumplimiento = ($ventasActuales / $metaMensual) * 100;

        // Estado
        $estado = 'en_riesgo';
        if ($porcentajeCumplimiento >= 100) {
            $estado = 'completada';
        } elseif ($porcentajeCumplimiento >= 80) {
            $estado = 'en_camino';
        } elseif ($proyeccionFinMes >= $metaMensual * 0.9) {
            $estado = 'posible';
        }

        return [
            'meta_mensual' => $metaMensual,
            'ventas_actuales' => $ventasActuales,
            'porcentaje_cumplimiento' => $porcentajeCumplimiento,
            'dias_transcurridos' => $diasTranscurridos,
            'dias_restantes' => $diasRestantes,
            'promedio_diario_actual' => $promedioDiarioActual,
            'promedio_diario_necesario' => $promedioDiarioNecesario,
            'proyeccion_fin_mes' => $proyeccionFinMes,
            'diferencia_meta' => $ventasActuales - $metaMensual,
            'estado' => $estado
        ];
    }

    private function obtenerHistorialMetas($vendedor)
    {
        $historial = [];

        // Últimos 6 meses
        for ($i = 5; $i >= 0; $i--) {
            $mes = Carbon::now()->subMonths($i);
            $inicioMes = $mes->copy()->startOfMonth();
            $finMes = $mes->copy()->endOfMonth();

            $ventas = Pedido::where('vendedor_id', $vendedor->id)
                           ->whereBetween('created_at', [$inicioMes, $finMes])
                           ->sum('total_final');

            // Por simplicidad, asumimos que la meta actual se aplicaba también en meses anteriores
            $meta = $vendedor->meta_mensual ?? 0;
            $cumplimiento = $meta > 0 ? ($ventas / $meta) * 100 : 0;

            $historial[] = [
                'mes' => $mes->format('M Y'),
                'meta' => $meta,
                'ventas' => $ventas,
                'cumplimiento' => $cumplimiento,
                'cumplida' => $cumplimiento >= 100
            ];
        }

        return $historial;
    }

    private function calcularRanking($vendedor)
    {
        $mesActual = Carbon::now();
        $inicioMes = $mesActual->copy()->startOfMonth();
        $finMes = $mesActual->copy()->endOfMonth();

        // Obtener todos los vendedores con sus ventas del mes
        $vendedores = User::where('rol', 'vendedor')
                         ->withSum(['pedidosVendedor as ventas_mes' => function($q) use ($inicioMes, $finMes) {
                             $q->whereBetween('created_at', [$inicioMes, $finMes]);
                         }], 'total_final')
                         ->orderBy('ventas_mes', 'desc')
                         ->get();

        $posicion = 1;
        $miPosicion = null;
        $totalVendedores = $vendedores->count();

        foreach ($vendedores as $index => $v) {
            if ($v->id === $vendedor->id) {
                $miPosicion = $index + 1;
                break;
            }
        }

        // Top 5 vendedores
        $topVendedores = $vendedores->take(5);

        return [
            'mi_posicion' => $miPosicion,
            'total_vendedores' => $totalVendedores,
            'top_vendedores' => $topVendedores,
            'mis_ventas_mes' => $vendedores->where('id', $vendedor->id)->first()->ventas_mes ?? 0
        ];
    }

    private function calcularPrediccion($vendedor, $progreso)
    {
        if ($progreso['estado'] === 'sin_meta') {
            return null;
        }

        $probabilidadCumplimiento = 0;
        $recomendaciones = [];

        // Calcular probabilidad basada en el progreso actual
        if ($progreso['proyeccion_fin_mes'] >= $progreso['meta_mensual']) {
            $probabilidadCumplimiento = min(95, 70 + ($progreso['porcentaje_cumplimiento'] - 80) * 2);
        } else {
            $probabilidadCumplimiento = max(5, $progreso['porcentaje_cumplimiento'] * 0.8);
        }

        // Generar recomendaciones
        if ($progreso['estado'] === 'en_riesgo') {
            $recomendaciones[] = "Necesitas aumentar tu promedio diario a $" . number_format($progreso['promedio_diario_necesario'], 0);
            $recomendaciones[] = "Contacta a tus clientes inactivos para generar nuevas ventas";
            $recomendaciones[] = "Considera ofrecer promociones especiales";
        } elseif ($progreso['estado'] === 'posible') {
            $recomendaciones[] = "Mantén tu ritmo actual de ventas";
            $recomendaciones[] = "Enfócate en clientes con mayor potencial de compra";
        } elseif ($progreso['estado'] === 'en_camino') {
            $recomendaciones[] = "¡Excelente progreso! Sigue así";
            $recomendaciones[] = "Considera aumentar tu meta para el próximo mes";
        } else {
            $recomendaciones[] = "¡Felicitaciones! Has superado tu meta";
            $recomendaciones[] = "Ayuda a otros vendedores compartiendo tus estrategias";
        }

        return [
            'probabilidad_cumplimiento' => $probabilidadCumplimiento,
            'recomendaciones' => $recomendaciones,
            'dias_criticos' => max(0, $progreso['dias_restantes'] - 5) // Últimos 5 días son críticos
        ];
    }

    public function getProgreso()
    {
        $vendedor = Auth::user();
        $progreso = $this->calcularProgresoMeta($vendedor);

        return response()->json($progreso);
    }

    public function getHistorial()
    {
        $vendedor = Auth::user();
        $historial = $this->obtenerHistorialMetas($vendedor);

        return response()->json($historial);
    }

    public function sugerirMeta()
    {
        $vendedor = Auth::user();

        // Calcular meta sugerida basada en historial
        $promedioUltimos3Meses = 0;
        $ventasUltimos3Meses = [];

        for ($i = 2; $i >= 0; $i--) {
            $mes = Carbon::now()->subMonths($i + 1); // Mes anterior
            $inicioMes = $mes->copy()->startOfMonth();
            $finMes = $mes->copy()->endOfMonth();

            $ventas = Pedido::where('vendedor_id', $vendedor->id)
                           ->whereBetween('created_at', [$inicioMes, $finMes])
                           ->sum('total_final');

            $ventasUltimos3Meses[] = $ventas;
        }

        if (count($ventasUltimos3Meses) > 0) {
            $promedioUltimos3Meses = array_sum($ventasUltimos3Meses) / count($ventasUltimos3Meses);
        }

        // Sugerir un incremento del 10-20%
        $metaSugerida = $promedioUltimos3Meses * 1.15; // 15% más que el promedio

        return response()->json([
            'meta_sugerida' => $metaSugerida,
            'promedio_ultimos_meses' => $promedioUltimos3Meses,
            'ventas_por_mes' => $ventasUltimos3Meses
        ]);
    }
}