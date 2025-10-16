<?php

namespace App\Http\Controllers\Lider;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Pedido;
use Illuminate\Http\Request;
use Carbon\Carbon;

class EquipoController extends Controller
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
        $estado = $request->get('estado');
        $ordenPor = $request->get('orden_por', 'rendimiento');

        // Obtener equipo directo
        $equipoQuery = $lider->referidos()->with(['referidos', 'pedidosComoVendedor']);

        if ($search) {
            $equipoQuery->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('cedula', 'like', "%{$search}%");
            });
        }

        if ($estado) {
            $equipoQuery->where('activo', $estado === 'activo');
        }

        $equipo = $equipoQuery->get();

        // Calcular estadísticas para cada miembro
        $equipoConStats = $equipo->map(function($miembro) {
            $inicioMes = Carbon::now()->startOfMonth();

            $ventasMes = $miembro->pedidosComoVendedor()
                ->where('created_at', '>=', $inicioMes)
                ->where('estado', '!=', 'cancelado')
                ->sum('total_final');

            $pedidosMes = $miembro->pedidosComoVendedor()
                ->where('created_at', '>=', $inicioMes)
                ->where('estado', '!=', 'cancelado')
                ->count();

            $referidosMes = $miembro->referidos()
                ->where('created_at', '>=', $inicioMes)
                ->count();

            $progresoMeta = 0;
            if ($miembro->meta_mensual && $miembro->meta_mensual > 0) {
                $progresoMeta = min(($ventasMes / $miembro->meta_mensual) * 100, 100);
            }

            return [
                'miembro' => $miembro,
                'ventas_mes' => $ventasMes,
                'pedidos_mes' => $pedidosMes,
                'referidos_mes' => $referidosMes,
                'referidos_totales' => $miembro->referidos->count(),
                'progreso_meta' => $progresoMeta,
                'rendimiento' => $this->calcularRendimiento($miembro)
            ];
        });

        // Ordenar según criterio
        switch ($ordenPor) {
            case 'ventas':
                $equipoConStats = $equipoConStats->sortByDesc('ventas_mes');
                break;
            case 'referidos':
                $equipoConStats = $equipoConStats->sortByDesc('referidos_totales');
                break;
            case 'meta':
                $equipoConStats = $equipoConStats->sortByDesc('progreso_meta');
                break;
            default:
                $equipoConStats = $equipoConStats->sortByDesc('rendimiento');
        }

        // Estadísticas generales del equipo
        $statsEquipo = [
            'total_miembros' => $equipo->count(),
            'activos' => $equipo->where('activo', true)->count(),
            'ventas_totales' => $equipoConStats->sum('ventas_mes'),
            'promedio_ventas' => $equipo->count() > 0 ? $equipoConStats->avg('ventas_mes') : 0,
            'metas_cumplidas' => $equipoConStats->where('progreso_meta', '>=', 100)->count(),
            'nuevos_mes' => $equipo->where('created_at', '>=', Carbon::now()->startOfMonth())->count()
        ];

        return view('lider.equipo.index', compact(
            'equipoConStats',
            'statsEquipo',
            'search',
            'estado',
            'ordenPor'
        ));
    }

    public function show($id)
    {
        $lider = auth()->user();
        $miembro = $lider->referidos()->findOrFail($id);

        // Estadísticas detalladas del miembro
        $stats = $this->obtenerStatsDetalladas($miembro);

        // Historial de ventas (últimos 6 meses)
        $historialVentas = $this->obtenerHistorialVentas($miembro);

        // Red del miembro
        $redMiembro = $this->obtenerRedMiembro($miembro);

        // Actividad reciente
        $actividadReciente = $this->obtenerActividadReciente($miembro);

        return view('lider.equipo.show', compact(
            'miembro',
            'stats',
            'historialVentas',
            'redMiembro',
            'actividadReciente'
        ));
    }

    public function asignarMeta(Request $request, $id)
    {
        $request->validate([
            'meta_mensual' => 'required|numeric|min:0',
            'mes' => 'required|date_format:Y-m'
        ]);

        $lider = auth()->user();
        $miembro = $lider->referidos()->findOrFail($id);

        $miembro->update([
            'meta_mensual' => $request->meta_mensual
        ]);

        // Si es una petición AJAX, devolver JSON
        if ($request->ajax() || $request->wantsJson()) {
            // Obtener estadísticas actualizadas
            $inicioMes = Carbon::now()->startOfMonth();
            $ventasMes = $miembro->pedidosComoVendedor()
                ->where('created_at', '>=', $inicioMes)
                ->where('estado', '!=', 'cancelado')
                ->sum('total_final');

            $rendimiento = 0;
            if ($miembro->meta_mensual && $miembro->meta_mensual > 0) {
                $rendimiento = min(($ventasMes / $miembro->meta_mensual) * 100, 100);
            }

            $progressColor = $rendimiento >= 80 ? 'success' : ($rendimiento >= 50 ? 'warning' : 'danger');

            return response()->json([
                'success' => true,
                'message' => 'Meta asignada exitosamente',
                'data' => [
                    'meta_mensual' => $miembro->meta_mensual,
                    'meta_mensual_formateado' => '$' . number_format($miembro->meta_mensual, 0),
                    'ventas_mes' => $ventasMes,
                    'rendimiento' => number_format($rendimiento, 1),
                    'progress_color' => $progressColor,
                    'falta' => max(0, $miembro->meta_mensual - $ventasMes),
                    'falta_formateado' => '$' . number_format(max(0, $miembro->meta_mensual - $ventasMes), 0),
                    'stroke_dashoffset' => 565.48 - (565.48 * $rendimiento / 100)
                ]
            ]);
        }

        return redirect()->back()->with('success', 'Meta asignada exitosamente.');
    }

    private function calcularRendimiento($miembro)
    {
        $inicioMes = Carbon::now()->startOfMonth();

        // Factores de rendimiento
        $ventasMes = $miembro->pedidosComoVendedor()
            ->where('created_at', '>=', $inicioMes)
            ->where('estado', '!=', 'cancelado')
            ->sum('total_final');

        $referidosMes = $miembro->referidos()
            ->where('created_at', '>=', $inicioMes)
            ->count();

        $consistencia = $this->calcularConsistencia($miembro);

        // Cálculo de rendimiento (0-100)
        $scoreVentas = min(($ventasMes / 1000000) * 40, 40); // Máximo 40 puntos por ventas
        $scoreReferidos = min($referidosMes * 10, 30); // Máximo 30 puntos por referidos
        $scoreConsistencia = $consistencia * 30; // Máximo 30 puntos por consistencia

        return min($scoreVentas + $scoreReferidos + $scoreConsistencia, 100);
    }

    private function calcularConsistencia($miembro)
    {
        $ultimosTresMeses = collect();

        for ($i = 0; $i < 3; $i++) {
            $mes = Carbon::now()->subMonths($i);
            $ventasMes = $miembro->pedidosComoVendedor()
                ->whereYear('created_at', $mes->year)
                ->whereMonth('created_at', $mes->month)
                ->where('estado', '!=', 'cancelado')
                ->sum('total_final');

            $ultimosTresMeses->push($ventasMes);
        }

        // Calcular consistencia basada en variación
        $promedio = $ultimosTresMeses->avg();
        if ($promedio == 0) return 0;

        $variacion = $ultimosTresMeses->map(function($venta) use ($promedio) {
            return abs($venta - $promedio) / $promedio;
        })->avg();

        return max(0, 1 - $variacion); // Menos variación = más consistencia
    }

    private function obtenerStatsDetalladas($miembro)
    {
        $inicioMes = Carbon::now()->startOfMonth();
        $inicioAno = Carbon::now()->startOfYear();

        return [
            'ventas_mes' => $miembro->pedidosComoVendedor()
                ->where('created_at', '>=', $inicioMes)
                ->where('estado', '!=', 'cancelado')
                ->sum('total_final'),

            'ventas_ano' => $miembro->pedidosComoVendedor()
                ->where('created_at', '>=', $inicioAno)
                ->where('estado', '!=', 'cancelado')
                ->sum('total_final'),

            'pedidos_mes' => $miembro->pedidosComoVendedor()
                ->where('created_at', '>=', $inicioMes)
                ->where('estado', '!=', 'cancelado')
                ->count(),

            'referidos_mes' => $miembro->referidos()
                ->where('created_at', '>=', $inicioMes)
                ->count(),

            'referidos_totales' => $miembro->referidos->count(),

            'ticket_promedio' => $miembro->pedidosComoVendedor()
                ->where('created_at', '>=', $inicioMes)
                ->where('estado', '!=', 'cancelado')
                ->avg('total_final') ?? 0,

            'comisiones_mes' => $miembro->comisiones()
                ->whereMonth('created_at', Carbon::now()->month)
                ->sum('monto'),

            'dias_activo' => $miembro->created_at->diffInDays(now()),

            'ultima_venta' => $miembro->pedidosComoVendedor()
                ->latest()
                ->first()?->created_at
        ];
    }

    public function obtenerHistorialVentasAjax(Request $request, $id)
    {
        $lider = auth()->user();
        $miembro = $lider->referidos()->findOrFail($id);

        // Determinar el rango de fechas
        if ($request->has('fecha_inicial') && $request->has('fecha_final')) {
            $fechaInicial = Carbon::createFromFormat('Y-m', $request->fecha_inicial)->startOfMonth();
            $fechaFinal = Carbon::createFromFormat('Y-m', $request->fecha_final)->endOfMonth();
        } else {
            $meses = $request->get('meses', 6);
            $fechaFinal = Carbon::now();
            $fechaInicial = Carbon::now()->subMonths($meses - 1)->startOfMonth();
        }

        // Generar datos para cada mes
        $ventas = [];
        $fecha = $fechaInicial->copy();

        while ($fecha <= $fechaFinal) {
            $ventasMes = $miembro->pedidosComoVendedor()
                ->whereYear('created_at', $fecha->year)
                ->whereMonth('created_at', $fecha->month)
                ->where('estado', '!=', 'cancelado')
                ->sum('total_final');

            $ventas[] = [
                'mes' => $fecha->translatedFormat('M Y'),
                'ventas' => $ventasMes
            ];

            $fecha->addMonth();
        }

        // Calcular estadísticas
        $total = array_sum(array_column($ventas, 'ventas'));
        $promedio = count($ventas) > 0 ? $total / count($ventas) : 0;
        $mejorMesData = collect($ventas)->sortByDesc('ventas')->first();
        $mejorMes = $mejorMesData['mes'] ?? 'N/A';

        return response()->json([
            'success' => true,
            'data' => [
                'ventas' => $ventas,
                'total' => $total,
                'promedio' => $promedio,
                'mejor_mes' => $mejorMes
            ]
        ]);
    }

    public function exportarHistorial(Request $request, $id)
    {
        $lider = auth()->user();
        $miembro = $lider->referidos()->findOrFail($id);

        // Determinar el rango de fechas
        if ($request->has('fecha_inicial') && $request->has('fecha_final')) {
            $fechaInicial = Carbon::createFromFormat('Y-m', $request->fecha_inicial)->startOfMonth();
            $fechaFinal = Carbon::createFromFormat('Y-m', $request->fecha_final)->endOfMonth();
        } else {
            $meses = $request->get('meses', 6);
            $fechaFinal = Carbon::now();
            $fechaInicial = Carbon::now()->subMonths($meses - 1)->startOfMonth();
        }

        // Generar CSV
        $ventas = [];
        $fecha = $fechaInicial->copy();

        while ($fecha <= $fechaFinal) {
            $ventasMes = $miembro->pedidosComoVendedor()
                ->whereYear('created_at', $fecha->year)
                ->whereMonth('created_at', $fecha->month)
                ->where('estado', '!=', 'cancelado')
                ->sum('total_final');

            $ventas[] = [
                'mes' => $fecha->translatedFormat('F Y'),
                'ventas' => $ventasMes
            ];

            $fecha->addMonth();
        }

        // Crear archivo CSV
        $filename = 'historial_ventas_' . $miembro->name . '_' . now()->format('Y-m-d') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($ventas, $miembro) {
            $file = fopen('php://output', 'w');

            // BOM para UTF-8
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

            // Encabezados
            fputcsv($file, ['Miembro', 'Mes', 'Ventas']);

            // Datos
            foreach ($ventas as $venta) {
                fputcsv($file, [
                    $miembro->name . ' ' . $miembro->apellidos,
                    $venta['mes'],
                    '$' . number_format($venta['ventas'], 0)
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    private function obtenerHistorialVentas($miembro)
    {
        return collect(range(0, 5))->map(function($i) use ($miembro) {
            $fecha = Carbon::now()->subMonths($i);
            $ventas = $miembro->pedidosComoVendedor()
                ->whereYear('created_at', $fecha->year)
                ->whereMonth('created_at', $fecha->month)
                ->where('estado', '!=', 'cancelado')
                ->sum('total_final');

            return [
                'mes' => $fecha->translatedFormat('M Y'),
                'ventas' => $ventas
            ];
        })->reverse()->values();
    }

    private function obtenerRedMiembro($miembro)
    {
        return $miembro->referidos->map(function($referido) {
            return [
                'usuario' => $referido,
                'referidos_propios' => $referido->referidos->count(),
                'ventas_mes' => $referido->pedidosComoVendedor()
                    ->where('created_at', '>=', Carbon::now()->startOfMonth())
                    ->where('estado', '!=', 'cancelado')
                    ->sum('total_final')
            ];
        });
    }

    private function obtenerActividadReciente($miembro)
    {
        $actividades = collect();

        // Ventas recientes
        $ventasRecientes = $miembro->pedidosComoVendedor()
            ->with('cliente')
            ->latest()
            ->take(5)
            ->get()
            ->map(function($pedido) {
                return [
                    'tipo' => 'venta',
                    'descripcion' => "Venta a {$pedido->cliente->name}",
                    'monto' => $pedido->total_final,
                    'fecha' => $pedido->created_at
                ];
            });

        // Nuevos referidos
        $nuevosReferidos = $miembro->referidos()
            ->latest()
            ->take(3)
            ->get()
            ->map(function($referido) {
                return [
                    'tipo' => 'referido',
                    'descripcion' => "Nuevo referido: {$referido->name}",
                    'fecha' => $referido->created_at
                ];
            });

        return $actividades->concat($ventasRecientes)
            ->concat($nuevosReferidos)
            ->sortByDesc('fecha')
            ->take(10)
            ->values();
    }
}