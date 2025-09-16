<?php

namespace App\Http\Controllers\Vendedor;

use App\Http\Controllers\Controller;
use App\Models\Comision;
use App\Models\SolicitudRetiro;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ComisionController extends Controller
{
    public function index(Request $request)
    {
        $vendedor = Auth::user();

        // Obtener comisiones del vendedor
        $query = Comision::where('user_id', $vendedor->id)
                         ->with(['pedido', 'referido']);

        // Filtros
        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        if ($request->filled('tipo')) {
            $query->where('tipo_comision', $request->tipo);
        }

        if ($request->filled('fecha_desde')) {
            $query->whereDate('created_at', '>=', $request->fecha_desde);
        }

        if ($request->filled('fecha_hasta')) {
            $query->whereDate('created_at', '<=', $request->fecha_hasta);
        }

        $comisiones = $query->orderBy('created_at', 'desc')->paginate(15);

        // Estadísticas de comisiones
        $stats = $this->calcularEstadisticasComisiones($vendedor);

        // Comisiones por tipo para el gráfico
        $comisionesPorTipo = Comision::where('user_id', $vendedor->id)
                                   ->select('tipo_comision', DB::raw('SUM(monto_comision) as total'))
                                   ->groupBy('tipo_comision')
                                   ->get();

        return view('vendedor.comisiones.index', compact('comisiones', 'stats', 'comisionesPorTipo'));
    }

    public function solicitar()
    {
        $vendedor = Auth::user();

        // Comisiones disponibles para retiro
        $comisionesDisponibles = Comision::where('user_id', $vendedor->id)
                                        ->where('estado', 'pendiente')
                                        ->sum('monto_comision');

        // Historial de solicitudes de retiro
        $solicitudesRetiro = SolicitudRetiro::where('user_id', $vendedor->id)
                                           ->orderBy('created_at', 'desc')
                                           ->limit(10)
                                           ->get();

        return view('vendedor.comisiones.solicitar', compact('comisionesDisponibles', 'solicitudesRetiro'));
    }

    public function procesarSolicitud(Request $request)
    {
        $request->validate([
            'monto' => 'required|numeric|min:50000', // Mínimo $50,000 COP
            'metodo_pago' => 'required|in:transferencia,nequi,daviplata',
            'datos_pago' => 'required|string|max:500'
        ]);

        $vendedor = Auth::user();

        // Verificar que tiene comisiones suficientes
        $comisionesDisponibles = Comision::where('user_id', $vendedor->id)
                                        ->where('estado', 'pendiente')
                                        ->sum('monto_comision');

        if ($request->monto > $comisionesDisponibles) {
            return redirect()->back()
                           ->with('error', 'No tienes suficientes comisiones disponibles para este retiro.');
        }

        DB::beginTransaction();
        try {
            // Crear solicitud de retiro
            $solicitud = SolicitudRetiro::create([
                'user_id' => $vendedor->id,
                'monto_solicitado' => $request->monto,
                'metodo_pago' => $request->metodo_pago,
                'datos_pago' => $request->datos_pago,
                'estado' => 'pendiente',
                'fecha_solicitud' => now()
            ]);

            // Marcar comisiones como "en_proceso" hasta completar el monto
            $comisiones = Comision::where('user_id', $vendedor->id)
                                 ->where('estado', 'pendiente')
                                 ->orderBy('created_at', 'asc')
                                 ->get();

            $montoRestante = $request->monto;
            $comisionesAfectadas = [];

            foreach ($comisiones as $comision) {
                if ($montoRestante <= 0) break;

                if ($comision->monto_comision <= $montoRestante) {
                    $comision->update(['estado' => 'en_proceso']);
                    $montoRestante -= $comision->monto_comision;
                    $comisionesAfectadas[] = $comision->id;
                } else {
                    // Si la comisión es mayor que lo que resta, dividir (esto es más complejo)
                    // Por simplicidad, no permitimos retiros parciales de comisiones individuales
                    break;
                }
            }

            // Asociar comisiones a la solicitud
            $solicitud->comisiones()->sync($comisionesAfectadas);

            DB::commit();

            return redirect()->back()
                           ->with('success', 'Solicitud de retiro enviada exitosamente. Será procesada en las próximas 24-48 horas.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                           ->with('error', 'Error al procesar la solicitud: ' . $e->getMessage());
        }
    }

    public function historial()
    {
        $vendedor = Auth::user();

        $solicitudes = SolicitudRetiro::where('user_id', $vendedor->id)
                                     ->orderBy('created_at', 'desc')
                                     ->paginate(15);

        return view('vendedor.comisiones.historial', compact('solicitudes'));
    }

    public function show($id)
    {
        $vendedor = Auth::user();

        $comision = Comision::where('user_id', $vendedor->id)
                           ->where('id', $id)
                           ->with(['pedido', 'referido'])
                           ->firstOrFail();

        return view('vendedor.comisiones.show', compact('comision'));
    }

    private function calcularEstadisticasComisiones($vendedor)
    {
        $mesActual = Carbon::now()->startOfMonth();
        $finMes = Carbon::now()->endOfMonth();

        return [
            // Totales generales
            'total_ganado' => Comision::where('user_id', $vendedor->id)->sum('monto_comision'),
            'disponible_retiro' => Comision::where('user_id', $vendedor->id)
                                          ->where('estado', 'pendiente')
                                          ->sum('monto_comision'),
            'en_proceso' => Comision::where('user_id', $vendedor->id)
                                   ->where('estado', 'en_proceso')
                                   ->sum('monto_comision'),
            'pagado' => Comision::where('user_id', $vendedor->id)
                               ->where('estado', 'pagado')
                               ->sum('monto_comision'),

            // Este mes
            'mes_actual' => Comision::where('user_id', $vendedor->id)
                                   ->whereBetween('created_at', [$mesActual, $finMes])
                                   ->sum('monto_comision'),

            // Por tipo
            'ventas_directas' => Comision::where('user_id', $vendedor->id)
                                        ->where('tipo_comision', 'venta_directa')
                                        ->sum('monto_comision'),
            'referidos' => Comision::where('user_id', $vendedor->id)
                                  ->where('tipo_comision', 'referido')
                                  ->sum('monto_comision'),

            // Métricas adicionales
            'total_retiros' => SolicitudRetiro::where('user_id', $vendedor->id)
                                             ->where('estado', 'completado')
                                             ->sum('monto_solicitado'),
            'retiros_pendientes' => SolicitudRetiro::where('user_id', $vendedor->id)
                                                  ->whereIn('estado', ['pendiente', 'en_proceso'])
                                                  ->sum('monto_solicitado')
        ];
    }

    public function getEvolucionComisiones()
    {
        $vendedor = Auth::user();
        $meses = [];

        // Últimos 6 meses
        for ($i = 5; $i >= 0; $i--) {
            $mes = Carbon::now()->subMonths($i);

            $comisiones = Comision::where('user_id', $vendedor->id)
                                 ->whereYear('created_at', $mes->year)
                                 ->whereMonth('created_at', $mes->month)
                                 ->sum('monto_comision');

            $meses[] = [
                'mes' => $mes->format('M Y'),
                'comisiones' => $comisiones
            ];
        }

        return response()->json($meses);
    }

    public function exportar(Request $request)
    {
        $vendedor = Auth::user();
        $query = Comision::where('user_id', $vendedor->id);

        if ($request->filled('fecha_desde')) {
            $query->whereDate('created_at', '>=', $request->fecha_desde);
        }

        if ($request->filled('fecha_hasta')) {
            $query->whereDate('created_at', '<=', $request->fecha_hasta);
        }

        $comisiones = $query->with(['pedido', 'referido'])->get();

        // Aquí implementarías la lógica de exportación
        return response()->json($comisiones);
    }
}