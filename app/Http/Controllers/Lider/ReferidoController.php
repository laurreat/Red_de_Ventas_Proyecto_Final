<?php

namespace App\Http\Controllers\Lider;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Pedido;
use App\Models\MensajeLider;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ReferidoController extends Controller
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
        $search = $request->get('search');
        $nivel = $request->get('nivel', 'todos');
        $periodo = $request->get('periodo', 'mes_actual');
        $estado = $request->get('estado');

        // Obtener red completa del líder
        $redQuery = User::with(['referidos', 'pedidosComoVendedor']);

        // Filtro por nivel
        if ($nivel === 'directos') {
            $redQuery->where('referido_por', $lider->id);
        } elseif ($nivel === 'segundo') {
            $directos = $lider->referidos->pluck('id');
            $redQuery->whereIn('referido_por', $directos);
        } else {
            // Todos los niveles - obtener toda la red
            $redCompleta = $this->obtenerRedCompleta($lider);
            $redQuery->whereIn('id', $redCompleta);
        }

        // Filtro de búsqueda
        if ($search) {
            $redQuery->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('cedula', 'like', "%{$search}%");
            });
        }

        // Filtro por estado
        if ($estado) {
            $redQuery->where('activo', $estado === 'activo');
        }

        $redReferidos = $redQuery->get();

        // Calcular estadísticas para cada referido
        $redConStats = $redReferidos->map(function($referido) use ($periodo) {
            $stats = $this->calcularStatsReferido($referido, $periodo);
            $stats['referido'] = $referido;
            $stats['nivel'] = $this->calcularNivel($referido);
            return $stats;
        });

        // Estadísticas generales de la red
        $statsRed = [
            'total_referidos' => $redReferidos->count(),
            'activos' => $redReferidos->where('activo', true)->count(),
            'nivel_1' => $lider->referidos->count(),
            'nivel_2' => $this->contarReferidosNivel($lider, 2),
            'nivel_3_mas' => $this->contarReferidosNivel($lider, 3, true),
            'ventas_totales' => $redConStats->sum('ventas_periodo'),
            'nuevos_mes' => $redReferidos->where('created_at', '>=', Carbon::now()->startOfMonth())->count(),
            'crecimiento_mensual' => $this->calcularCrecimientoMensual($lider)
        ];

        // Top performers de la red
        $topPerformers = $redConStats->sortByDesc('ventas_periodo')->take(10);

        // Evolución de crecimiento de la red
        $evolucionRed = $this->obtenerEvolucionRed($lider);

        // Distribución por niveles
        $distribucionNiveles = $this->obtenerDistribucionNiveles($lider);

        return view('lider.referidos.index', compact(
            'redConStats',
            'statsRed',
            'topPerformers',
            'evolucionRed',
            'distribucionNiveles',
            'search',
            'nivel',
            'periodo',
            'estado'
        ));
    }

    public function red()
    {
        $lider = auth()->user();

        // Obtener estructura completa de la red
        $redCompleta = $this->construirEstructuraRed($lider);

        // Estadísticas por nivel
        $estadisticasNivel = [];
        for ($i = 1; $i <= 5; $i++) {
            $estadisticasNivel[$i] = $this->obtenerEstadisticasNivel($lider, $i);
        }

        // Métricas de crecimiento
        $metricas = [
            'volumen_red' => $this->calcularVolumenRed($lider),
            'comisiones_generadas' => $this->calcularComisionesRed($lider),
            'tasa_actividad' => $this->calcularTasaActividad($lider),
            'profundidad_maxima' => $this->calcularProfundidadMaxima($lider),
            'amplitud_maxima' => $this->calcularAmplitudMaxima($lider)
        ];

        return view('lider.referidos.red', compact(
            'redCompleta',
            'estadisticasNivel',
            'metricas'
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

    private function calcularStatsReferido($referido, $periodo)
    {
        $fechaInicio = $this->obtenerFechaInicioPeriodo($periodo);

        $ventasPeriodo = to_float($referido->pedidosComoVendedor()
            ->where('created_at', '>=', $fechaInicio)
            ->where('estado', '!=', 'cancelado')
            ->sum('total_final'));

        $pedidosPeriodo = $referido->pedidosComoVendedor()
            ->where('created_at', '>=', $fechaInicio)
            ->where('estado', '!=', 'cancelado')
            ->count();

        $referidosPeriodo = $referido->referidos()
            ->where('created_at', '>=', $fechaInicio)
            ->count();

        return [
            'ventas_periodo' => $ventasPeriodo,
            'pedidos_periodo' => $pedidosPeriodo,
            'referidos_periodo' => $referidosPeriodo,
            'referidos_totales' => $referido->referidos->count(),
            'fecha_ingreso' => $referido->created_at,
            'dias_activo' => $referido->created_at->diffInDays(now()),
            'ultima_actividad' => $this->obtenerUltimaActividad($referido),
            'rendimiento' => $this->calcularRendimientoReferido($referido)
        ];
    }

    private function calcularNivel($referido)
    {
        $nivel = 1;
        $actual = $referido;

        while ($actual->referido_por && $nivel < 10) {
            $actual = User::find($actual->referido_por);
            if (!$actual) break;
            $nivel++;
        }

        return $nivel;
    }

    private function contarReferidosNivel($lider, $nivel, $masOIgual = false)
    {
        if ($nivel === 1) {
            return $lider->referidos->count();
        }

        $count = 0;
        $this->contarReferidosRecursivo($lider, 1, $nivel, $count, $masOIgual);
        return $count;
    }

    private function contarReferidosRecursivo($usuario, $nivelActual, $nivelObjetivo, &$count, $masOIgual = false)
    {
        if ($nivelActual === $nivelObjetivo) {
            if ($masOIgual) {
                $count += $usuario->referidos->count();
                foreach ($usuario->referidos as $referido) {
                    $this->contarReferidosRecursivo($referido, $nivelActual + 1, $nivelObjetivo, $count, $masOIgual);
                }
            } else {
                $count += $usuario->referidos->count();
            }
        } elseif ($nivelActual < $nivelObjetivo) {
            foreach ($usuario->referidos as $referido) {
                $this->contarReferidosRecursivo($referido, $nivelActual + 1, $nivelObjetivo, $count, $masOIgual);
            }
        }
    }

    private function calcularCrecimientoMensual($lider)
    {
        $mesActual = Carbon::now()->startOfMonth();
        $finMesAnterior = Carbon::now()->subMonth()->endOfMonth();
        $inicioMesAnterior = Carbon::now()->subMonth()->startOfMonth();

        $redActual = $this->obtenerRedCompleta($lider);

        // Nuevos miembros este mes
        $nuevosEsteMes = User::whereIn('id', $redActual)
            ->where('created_at', '>=', $mesActual)
            ->count();

        // Total de miembros al final del mes anterior
        $totalMesAnterior = User::whereIn('id', $redActual)
            ->where('created_at', '<=', $finMesAnterior)
            ->count();

        // Si no hay base de comparación, retornar 0
        if ($totalMesAnterior == 0) {
            // Si hay nuevos este mes pero no había nadie antes, es crecimiento infinito
            // Pero si tampoco hay nuevos este mes, es 0
            return $nuevosEsteMes > 0 ? 100 : 0;
        }

        // Si no hay nuevos este mes, el crecimiento es 0%
        if ($nuevosEsteMes == 0) {
            return 0;
        }

        // Calcular porcentaje de crecimiento
        return round(($nuevosEsteMes / $totalMesAnterior) * 100, 2);
    }

    private function obtenerEvolucionRed($lider)
    {
        return collect(range(0, 5))->map(function($i) use ($lider) {
            $fecha = Carbon::now()->subMonths($i);
            $totalRed = User::whereIn('id', $this->obtenerRedCompleta($lider))
                ->where('created_at', '<=', $fecha->endOfMonth())
                ->count();

            return [
                'mes' => $fecha->format('M Y'),
                'total' => $totalRed
            ];
        })->reverse()->values();
    }

    private function obtenerDistribucionNiveles($lider)
    {
        $distribucion = [];
        for ($i = 1; $i <= 5; $i++) {
            $distribucion[$i] = $this->contarReferidosNivel($lider, $i);
        }
        return $distribucion;
    }

    private function construirEstructuraRed($lider, $nivel = 0, $maxNivel = 3)
    {
        if ($nivel >= $maxNivel) {
            return [];
        }

        return $lider->referidos->map(function($referido) use ($nivel, $maxNivel) {
            return [
                'usuario' => $referido,
                'nivel' => $nivel + 1,
                'hijos' => $this->construirEstructuraRed($referido, $nivel + 1, $maxNivel),
                'stats' => $this->calcularStatsReferido($referido, 'mes_actual')
            ];
        })->toArray();
    }

    private function obtenerEstadisticasNivel($lider, $nivel)
    {
        $count = $this->contarReferidosNivel($lider, $nivel);
        $ventas = 0;
        $activos = 0;

        if ($count > 0) {
            // Calcular ventas y activos del nivel
            $this->calcularStatsNivel($lider, 1, $nivel, $ventas, $activos);
        }

        return [
            'total' => $count,
            'activos' => $activos,
            'ventas_mes' => $ventas,
            'promedio_ventas' => $count > 0 ? $ventas / $count : 0
        ];
    }

    private function calcularStatsNivel($usuario, $nivelActual, $nivelObjetivo, &$ventas, &$activos)
    {
        if ($nivelActual === $nivelObjetivo) {
            foreach ($usuario->referidos as $referido) {
                $ventasReferido = to_float($referido->pedidosComoVendedor()
                    ->where('created_at', '>=', Carbon::now()->startOfMonth())
                    ->where('estado', '!=', 'cancelado')
                    ->sum('total_final'));

                $ventas += $ventasReferido;

                if ($referido->activo && $ventasReferido > 0) {
                    $activos++;
                }
            }
        } elseif ($nivelActual < $nivelObjetivo) {
            foreach ($usuario->referidos as $referido) {
                $this->calcularStatsNivel($referido, $nivelActual + 1, $nivelObjetivo, $ventas, $activos);
            }
        }
    }

    private function calcularVolumenRed($lider)
    {
        $redIds = $this->obtenerRedCompleta($lider);
        return to_float(Pedido::whereIn('vendedor_id', $redIds)
            ->where('created_at', '>=', Carbon::now()->startOfMonth())
            ->where('estado', '!=', 'cancelado')
            ->sum('total_final'));
    }

    private function calcularComisionesRed($lider)
    {
        return to_float($lider->comisiones()
            ->whereMonth('created_at', Carbon::now()->month)
            ->sum('monto'));
    }

    private function calcularTasaActividad($lider)
    {
        $redIds = $this->obtenerRedCompleta($lider);
        $totalRed = $redIds->count();

        if ($totalRed == 0) return 0;

        $activos = User::whereIn('id', $redIds)
            ->where('activo', true)
            ->whereHas('pedidosComoVendedor', function($q) {
                $q->where('created_at', '>=', Carbon::now()->subDays(30))
                  ->where('estado', '!=', 'cancelado');
            })
            ->count();

        return round(($activos / $totalRed) * 100, 2);
    }

    private function calcularProfundidadMaxima($lider, $nivelActual = 0)
    {
        $maxProfundidad = $nivelActual;

        foreach ($lider->referidos as $referido) {
            $profundidadReferido = $this->calcularProfundidadMaxima($referido, $nivelActual + 1);
            $maxProfundidad = max($maxProfundidad, $profundidadReferido);
        }

        return $maxProfundidad;
    }

    private function calcularAmplitudMaxima($lider)
    {
        $maxAmplitud = $lider->referidos->count();

        foreach ($lider->referidos as $referido) {
            $amplitudReferido = $this->calcularAmplitudMaxima($referido);
            $maxAmplitud = max($maxAmplitud, $amplitudReferido);
        }

        return $maxAmplitud;
    }

    private function obtenerFechaInicioPeriodo($periodo)
    {
        switch ($periodo) {
            case 'semana_actual':
                return Carbon::now()->startOfWeek();
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

    private function obtenerUltimaActividad($referido)
    {
        $ultimaVenta = $referido->pedidosComoVendedor()->latest()->first();
        $ultimoReferido = $referido->referidos()->latest()->first();

        $fechas = collect([
            $ultimaVenta?->created_at,
            $ultimoReferido?->created_at,
            $referido->updated_at
        ])->filter();

        return $fechas->max();
    }

    private function calcularRendimientoReferido($referido)
    {
        // Si no hay actividad reciente, rendimiento es 0
        $ventasMes = to_float($referido->pedidosComoVendedor()
            ->where('created_at', '>=', Carbon::now()->startOfMonth())
            ->where('estado', '!=', 'cancelado')
            ->sum('total_final'));

        $referidosMes = $referido->referidos()
            ->where('created_at', '>=', Carbon::now()->startOfMonth())
            ->count();

        // Si no hay ventas ni referidos este mes, rendimiento es 0
        if ($ventasMes == 0 && $referidosMes == 0) {
            return 0;
        }

        $consistencia = $this->calcularConsistenciaVentas($referido);

        // Cálculo de score (máximo 100)
        // Ventas: hasta 50 puntos (meta: $500,000/mes)
        $scoreVentas = min(($ventasMes / 500000) * 50, 50);

        // Referidos: hasta 30 puntos (1 referido = 20 puntos, máximo en 2)
        $scoreReferidos = min($referidosMes * 15, 30);

        // Consistencia: hasta 20 puntos
        $scoreConsistencia = $consistencia * 20;

        $rendimientoTotal = $scoreVentas + $scoreReferidos + $scoreConsistencia;

        // Si el rendimiento es muy bajo (menos de 5), redondear a 0
        return $rendimientoTotal < 5 ? 0 : min($rendimientoTotal, 100);
    }

    private function calcularConsistenciaVentas($referido)
    {
        $ultimosTresMeses = collect();

        for ($i = 0; $i < 3; $i++) {
            $mes = Carbon::now()->subMonths($i);
            $ventasMes = to_float($referido->pedidosComoVendedor()
                ->whereYear('created_at', $mes->year)
                ->whereMonth('created_at', $mes->month)
                ->where('estado', '!=', 'cancelado')
                ->sum('total_final'));

            $ultimosTresMeses->push($ventasMes);
        }

        $promedio = $ultimosTresMeses->avg();
        if ($promedio == 0) return 0;

        $variacion = $ultimosTresMeses->map(function($venta) use ($promedio) {
            return abs($venta - $promedio) / $promedio;
        })->avg();

        return max(0, 1 - $variacion);
    }

    /**
     * Enviar mensaje a un vendedor
     */
    public function enviarMensaje(Request $request)
    {
        $request->validate([
            'vendedor_id' => 'required|exists:users,_id',
            'mensaje' => 'required|string|max:1000',
            'tipo_mensaje' => 'required|in:motivacion,recomendacion,alerta,felicitacion,otro'
        ]);

        $lider = auth()->user();
        $vendedor = User::findOrFail($request->vendedor_id);

        // Verificar que el vendedor pertenezca a la red del líder
        $redCompleta = $this->obtenerRedCompleta($lider);
        if (!$redCompleta->contains($vendedor->id)) {
            return response()->json([
                'success' => false,
                'message' => 'No tienes permiso para enviar mensajes a este vendedor.'
            ], 403);
        }

        // Crear el mensaje
        $mensaje = MensajeLider::create([
            'lider_id' => $lider->id,
            'vendedor_id' => $vendedor->id,
            'mensaje' => $request->mensaje,
            'tipo_mensaje' => $request->tipo_mensaje,
            'leido' => false,
            'metadata' => [
                'lider_nombre' => $lider->name,
                'vendedor_nombre' => $vendedor->name,
                'enviado_desde' => 'red_referidos'
            ]
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Mensaje enviado exitosamente a ' . $vendedor->name,
            'data' => $mensaje
        ]);
    }
}