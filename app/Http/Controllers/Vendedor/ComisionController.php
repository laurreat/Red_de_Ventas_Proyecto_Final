<?php

namespace App\Http\Controllers\Vendedor;

use App\Http\Controllers\Controller;
use App\Models\Comision;
use App\Models\SolicitudPago;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ComisionController extends Controller
{
    public function index(Request $request)
    {
        $vendedor = Auth::user();

        // Obtener comisiones del vendedor - solo aprobadas (excluir rechazadas y pendientes de aprobación)
        $query = Comision::where('user_id', $vendedor->_id ?? $vendedor->id)
                        ->whereNotIn('estado', ['rechazada', 'pendiente_aprobacion']);

        // Filtros
        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        if ($request->filled('tipo')) {
            $query->where('tipo', $request->tipo);
        }

        if ($request->filled('fecha_desde')) {
            $query->where('created_at', '>=', Carbon::parse($request->fecha_desde)->startOfDay());
        }

        if ($request->filled('fecha_hasta')) {
            $query->where('created_at', '<=', Carbon::parse($request->fecha_hasta)->endOfDay());
        }

        $comisiones = $query->orderBy('created_at', 'desc')->paginate(15);

        // Estadísticas de comisiones
        $stats = $this->calcularEstadisticasComisiones($vendedor);

        return view('vendedor.comisiones.index', compact('comisiones', 'stats'));
    }

    public function solicitar()
    {
        $vendedor = Auth::user();
        $userId = $vendedor->_id ?? $vendedor->id;

        // Comisiones disponibles para retiro (solo las pendientes que no están en proceso)
        $comisionesDisponibles = Comision::where('user_id', $userId)
                                        ->where('estado', 'pendiente')
                                        ->sum('monto');

        // Historial de solicitudes de retiro
        $solicitudesRetiro = SolicitudPago::where('user_id', $userId)
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
        $userId = $vendedor->_id ?? $vendedor->id;

        // Verificar que tiene comisiones suficientes
        $comisionesDisponibles = Comision::where('user_id', $userId)
                                        ->where('estado', 'pendiente')
                                        ->sum('monto');

        if ($request->monto > $comisionesDisponibles) {
            return redirect()->back()
                           ->with('error', 'No tienes suficientes comisiones disponibles para este retiro.');
        }

        try {
            // Crear solicitud de pago
            $solicitud = SolicitudPago::create([
                'user_id' => $userId,
                'user_data' => [
                    'nombre' => $vendedor->name,
                    'email' => $vendedor->email,
                    'telefono' => $vendedor->telefono ?? null
                ],
                'monto' => $request->monto,
                'metodo_pago' => $request->metodo_pago,
                'datos_pago' => $request->datos_pago,
                'observaciones' => $request->observaciones ?? null,
                'estado' => 'pendiente'
            ]);

            // Marcar comisiones como "en_proceso" hasta completar el monto
            $comisiones = Comision::where('user_id', $userId)
                                 ->where('estado', 'pendiente')
                                 ->orderBy('created_at', 'asc')
                                 ->get();

            $montoRestante = $request->monto;
            $comisionesAfectadas = [];

            foreach ($comisiones as $comision) {
                if ($montoRestante <= 0) break;

                if ($comision->monto <= $montoRestante) {
                    $comision->update(['estado' => 'en_proceso']);
                    $montoRestante -= $comision->monto;
                    $comisionesAfectadas[] = (string) ($comision->_id ?? $comision->id);
                } else {
                    // Si la comisión es mayor que lo que resta, dividir (esto es más complejo)
                    // Por simplicidad, no permitimos retiros parciales de comisiones individuales
                    break;
                }
            }

            return redirect()->route('vendedor.comisiones.index')
                           ->with('success', 'Solicitud de retiro enviada exitosamente. Será procesada en las próximas 24-48 horas.');

        } catch (\Exception $e) {
            return redirect()->back()
                           ->with('error', 'Error al procesar la solicitud: ' . $e->getMessage());
        }
    }

    public function historial()
    {
        $vendedor = Auth::user();
        $userId = $vendedor->_id ?? $vendedor->id;

        $solicitudes = SolicitudPago::where('user_id', $userId)
                                     ->orderBy('created_at', 'desc')
                                     ->paginate(15);

        return view('vendedor.comisiones.historial', compact('solicitudes'));
    }

    public function show($id)
    {
        $vendedor = Auth::user();

        $comision = Comision::where('user_id', $vendedor->_id ?? $vendedor->id)
                           ->where('_id', $id)
                           ->firstOrFail();

        return view('vendedor.comisiones.show', compact('comision'));
    }

    private function calcularEstadisticasComisiones($vendedor)
    {
        $mesActual = Carbon::now()->startOfMonth();
        $finMes = Carbon::now()->endOfMonth();
        $userId = $vendedor->_id ?? $vendedor->id;

        // Solo obtener comisiones aprobadas/aceptadas (excluir 'rechazada' y 'pendiente_aprobacion')
        $comisionesData = Comision::where('user_id', $userId)
                                  ->whereNotIn('estado', ['rechazada', 'pendiente_aprobacion'])
                                  ->get();

        return [
            // Totales generales - solo comisiones aprobadas
            'total_ganado' => $comisionesData->sum('monto') ?? 0,
            'disponible_retiro' => $comisionesData->where('estado', 'pendiente')->sum('monto') ?? 0,
            'en_proceso' => $comisionesData->where('estado', 'en_proceso')->sum('monto') ?? 0,
            'pagado' => $comisionesData->where('estado', 'pagado')->sum('monto') ?? 0,

            // Este mes
            'mes_actual' => $comisionesData->whereBetween('created_at', [$mesActual, $finMes])->sum('monto') ?? 0,

            // Por tipo
            'ventas_directas' => $comisionesData->where('tipo', 'venta_directa')->sum('monto') ?? 0,
            'referidos' => $comisionesData->where('tipo', 'referido')->sum('monto') ?? 0,

            // Métricas adicionales - usar 0 si no hay SolicitudRetiro
            'total_retiros' => 0,
            'retiros_pendientes' => 0
        ];
    }

    public function getEvolucionComisiones()
    {
        $vendedor = Auth::user();
        $userId = $vendedor->_id ?? $vendedor->id;
        $meses = [];

        // Últimos 6 meses
        for ($i = 5; $i >= 0; $i--) {
            $mes = Carbon::now()->subMonths($i);

            $comisiones = Comision::where('user_id', $userId)
                                 ->whereYear('created_at', $mes->year)
                                 ->whereMonth('created_at', $mes->month)
                                 ->sum('monto');

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
        $userId = $vendedor->_id ?? $vendedor->id;
        $query = Comision::where('user_id', $userId);

        if ($request->filled('fecha_desde')) {
            $query->whereDate('created_at', '>=', $request->fecha_desde);
        }

        if ($request->filled('fecha_hasta')) {
            $query->whereDate('created_at', '<=', $request->fecha_hasta);
        }

        $comisiones = $query->orderBy('created_at', 'desc')->get();

        // Aquí implementarías la lógica de exportación (CSV, Excel, etc.)
        return response()->json($comisiones);
    }
}