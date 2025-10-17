<?php

namespace App\Http\Controllers\Lider;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Comision;
use App\Models\SolicitudPago;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ComisionController extends Controller
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
        $tipo = $request->get('tipo', 'todas');

        // Consulta base de comisiones
        $comisionesQuery = $lider->comisiones()->with(['pedido', 'referido']);

        // Aplicar filtros de periodo
        switch ($periodo) {
            case 'mes_actual':
                $comisionesQuery->whereMonth('created_at', Carbon::now()->month)
                               ->whereYear('created_at', Carbon::now()->year);
                break;
            case 'mes_anterior':
                $fechaAnterior = Carbon::now()->subMonth();
                $comisionesQuery->whereMonth('created_at', $fechaAnterior->month)
                               ->whereYear('created_at', $fechaAnterior->year);
                break;
            case 'ultimo_trimestre':
                $comisionesQuery->where('created_at', '>=', Carbon::now()->subMonths(3));
                break;
            case 'ultimo_ano':
                $comisionesQuery->where('created_at', '>=', Carbon::now()->subYear());
                break;
        }

        // Aplicar filtros de tipo
        if ($tipo !== 'todas') {
            $comisionesQuery->where('tipo', $tipo);
        }

        $comisiones = $comisionesQuery->orderBy('created_at', 'desc')
                                    ->paginate(20);

        // Estadísticas de comisiones
        $stats = [
            'total_ganado' => to_float($lider->comisiones_ganadas ?? 0),
            'disponible' => to_float($lider->comisiones_disponibles ?? 0),
            'cobrado' => $this->calcularComisionesCobradas($lider),
            'mes_actual' => $this->calcularComisionesMes($lider),
            'promedio_mensual' => $this->calcularPromedioMensual($lider),
            'comisiones_equipo' => $this->calcularComisionesEquipo($lider)
        ];

        // Evolución de comisiones (últimos 6 meses)
        $evolucionComisiones = $this->obtenerEvolucionComisiones($lider);

        // Breakdown por tipo
        $breakdownTipo = $this->obtenerBreakdownTipo($lider, $periodo);

        // Top generadores de comisiones
        $topGeneradores = $this->obtenerTopGeneradores($lider);

        // Solicitudes de pago pendientes
        $solicitudesPendientes = $this->obtenerSolicitudesPendientes($lider);

        return view('lider.comisiones.index', compact(
            'comisiones',
            'stats',
            'evolucionComisiones',
            'breakdownTipo',
            'topGeneradores',
            'solicitudesPendientes',
            'periodo',
            'tipo'
        ));
    }

    public function solicitar()
    {
        $lider = auth()->user();

        // Verificar que tenga comisiones disponibles
        if ($lider->comisiones_disponibles <= 0) {
            return redirect()->route('lider.comisiones.index')
                           ->with('error', 'No tienes comisiones disponibles para cobrar.');
        }

        // Obtener métodos de pago disponibles
        $metodosPago = [
            'transferencia' => 'Transferencia Bancaria',
            'nequi' => 'Nequi',
            'daviplata' => 'Daviplata',
            'efectivo' => 'Efectivo'
        ];

        // Historial de solicitudes
        $historialSolicitudes = SolicitudPago::where('user_id', $lider->id)
                                           ->orderBy('created_at', 'desc')
                                           ->take(10)
                                           ->get();

        return view('lider.comisiones.solicitar', compact(
            'metodosPago',
            'historialSolicitudes'
        ));
    }

    public function procesarSolicitud(Request $request)
    {
        $request->validate([
            'monto' => 'required|numeric|min:50000', // Mínimo $50,000
            'metodo_pago' => 'required|in:transferencia,nequi,daviplata,efectivo',
            'datos_pago' => 'required|string|max:500',
            'observaciones' => 'nullable|string|max:1000'
        ]);

        $lider = auth()->user();

        // Verificar que tenga suficientes comisiones disponibles
        if ($request->monto > $lider->comisiones_disponibles) {
            return redirect()->back()
                           ->with('error', 'El monto solicitado excede tus comisiones disponibles.');
        }

        // Crear solicitud de pago
        $solicitud = SolicitudPago::create([
            'user_id' => $lider->id,
            'monto' => $request->monto,
            'metodo_pago' => $request->metodo_pago,
            'datos_pago' => $request->datos_pago,
            'observaciones' => $request->observaciones,
            'estado' => 'pendiente'
        ]);

        // Actualizar comisiones disponibles
        $lider->update([
            'comisiones_disponibles' => $lider->comisiones_disponibles - $request->monto
        ]);

        return redirect()->route('lider.comisiones.index')
                        ->with('success', 'Solicitud de pago enviada exitosamente. Se procesará en 24-48 horas.');
    }

    private function calcularComisionesCobradas($lider)
    {
        return to_float(SolicitudPago::where('user_id', $lider->id)
                          ->where('estado', 'pagado')
                          ->sum('monto'));
    }

    private function calcularComisionesMes($lider)
    {
        return to_float($lider->comisiones()
                    ->whereMonth('created_at', Carbon::now()->month)
                    ->whereYear('created_at', Carbon::now()->year)
                    ->sum('monto'));
    }

    private function calcularPromedioMensual($lider)
    {
        $meses = 6;
        $total = to_float($lider->comisiones()
                      ->where('created_at', '>=', Carbon::now()->subMonths($meses))
                      ->sum('monto'));

        return $total / $meses;
    }

    private function calcularComisionesEquipo($lider)
    {
        $equipoIds = $lider->referidos->pluck('id');

        return to_float(Comision::whereIn('user_id', $equipoIds)
                      ->whereMonth('created_at', Carbon::now()->month)
                      ->whereYear('created_at', Carbon::now()->year)
                      ->sum('monto'));
    }

    private function obtenerEvolucionComisiones($lider)
    {
        return collect(range(0, 5))->map(function($i) use ($lider) {
            $fecha = Carbon::now()->subMonths($i);
            $total = to_float($lider->comisiones()
                          ->whereYear('created_at', $fecha->year)
                          ->whereMonth('created_at', $fecha->month)
                          ->sum('monto'));

            return [
                'mes' => $fecha->format('M Y'),
                'total' => $total
            ];
        })->reverse()->values();
    }

    private function obtenerBreakdownTipo($lider, $periodo)
    {
        $query = $lider->comisiones();

        // Aplicar mismo filtro de periodo que en index
        switch ($periodo) {
            case 'mes_actual':
                $query->whereMonth('created_at', Carbon::now()->month)
                      ->whereYear('created_at', Carbon::now()->year);
                break;
            case 'mes_anterior':
                $fechaAnterior = Carbon::now()->subMonth();
                $query->whereMonth('created_at', $fechaAnterior->month)
                      ->whereYear('created_at', $fechaAnterior->year);
                break;
            case 'ultimo_trimestre':
                $query->where('created_at', '>=', Carbon::now()->subMonths(3));
                break;
            case 'ultimo_ano':
                $query->where('created_at', '>=', Carbon::now()->subYear());
                break;
        }

        // Obtener comisiones y agrupar por tipo usando colecciones
        $comisiones = $query->get();

        return $comisiones->groupBy('tipo')->map(function($items, $tipo) {
            return (object)[
                'tipo' => $tipo,
                'total' => to_float($items->sum('monto'))
            ];
        });
    }

    private function obtenerTopGeneradores($lider)
    {
        // Top 5 miembros que más comisiones han generado para el líder
        $comisiones = $lider->comisiones()
                    ->with('referido')
                    ->whereNotNull('referido_id')
                    ->whereMonth('created_at', Carbon::now()->month)
                    ->get();

        // Agrupar por referido_id y sumar montos
        return $comisiones->groupBy('referido_id')
                         ->map(function($items, $referidoId) {
                             $referido = $items->first()->referido;
                             return (object)[
                                 'referido_id' => $referidoId,
                                 'referido' => $referido,
                                 'total_generado' => to_float($items->sum('monto'))
                             ];
                         })
                         ->sortByDesc('total_generado')
                         ->take(5)
                         ->values();
    }

    private function obtenerSolicitudesPendientes($lider)
    {
        return SolicitudPago::where('user_id', $lider->id)
                          ->where('estado', 'pendiente')
                          ->orderBy('created_at', 'desc')
                          ->get();
    }
}