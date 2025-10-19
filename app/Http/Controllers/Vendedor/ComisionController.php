<?php

namespace App\Http\Controllers\Vendedor;

use App\Http\Controllers\Controller;
use App\Models\Comision;
use App\Models\SolicitudPago;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ComisionController extends Controller
{
    /**
     * Convierte MongoDB Decimal128 a float
     */
    private function convertirDecimal($valor)
    {
        if ($valor instanceof \MongoDB\BSON\Decimal128) {
            return (float) $valor->__toString();
        }
        return (float) ($valor ?? 0);
    }

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

        // Convertir a float
        $comisionesDisponibles = $this->convertirDecimal($comisionesDisponibles);

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
        
        // Convertir a float
        $comisionesDisponibles = $this->convertirDecimal($comisionesDisponibles);

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
                'estado' => 'pendiente',
                'comisiones_ids' => [] // Inicializar array
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

                $montoComision = $this->convertirDecimal($comision->monto);

                if ($montoComision <= $montoRestante) {
                    // La comisión completa cabe en el monto restante
                    $comision->update([
                        'estado' => 'en_proceso',
                        'solicitud_pago_id' => (string) ($solicitud->_id ?? $solicitud->id)
                    ]);
                    $montoRestante -= $montoComision;
                    $comisionesAfectadas[] = (string) ($comision->_id ?? $comision->id);
                } else {
                    // La comisión es mayor que lo que resta - dividirla
                    // Crear una nueva comisión con el sobrante
                    $montoSobrante = $montoComision - $montoRestante;
                    
                    // Actualizar la comisión actual con el monto usado
                    $comision->update([
                        'estado' => 'en_proceso',
                        'monto' => $montoRestante,
                        'dividida' => true,
                        'monto_original' => $montoComision,
                        'solicitud_pago_id' => (string) ($solicitud->_id ?? $solicitud->id)
                    ]);
                    
                    // Crear nueva comisión con el sobrante
                    Comision::create([
                        'user_id' => $userId,
                        'pedido_id' => $comision->pedido_id ?? null,
                        'referido_id' => $comision->referido_id ?? null,
                        'tipo' => $comision->tipo,
                        'monto' => $montoSobrante,
                        'estado' => 'pendiente',
                        'porcentaje' => $comision->porcentaje ?? null,
                        'descripcion' => ($comision->descripcion ?? '') . ' (Sobrante de división)',
                        'dividida_desde' => (string) ($comision->_id ?? $comision->id),
                        'created_at' => now()
                    ]);
                    
                    $comisionesAfectadas[] = (string) ($comision->_id ?? $comision->id);
                    $montoRestante = 0;
                    break;
                }
            }

            // Guardar los IDs de las comisiones en la solicitud
            $solicitud->update([
                'comisiones_ids' => $comisionesAfectadas
            ]);

            \Log::info('Solicitud de retiro procesada', [
                'solicitud_id' => $solicitud->_id ?? $solicitud->id,
                'monto_solicitado' => $request->monto,
                'comisiones_afectadas' => count($comisionesAfectadas),
                'comisiones_ids' => $comisionesAfectadas,
                'monto_restante' => $montoRestante
            ]);

            // Enviar notificación a los administradores
            $this->enviarNotificacionAdministradores($solicitud, $vendedor);

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

        // Buscar si hay una solicitud de pago relacionada con esta comisión
        $solicitudPago = null;
        if ($comision->estado === 'en_proceso' || $comision->estado === 'pagado') {
            $solicitudPago = SolicitudPago::where('user_id', $vendedor->_id ?? $vendedor->id)
                                          ->where('estado', '!=', 'rechazado')
                                          ->orderBy('created_at', 'desc')
                                          ->first();
        }

        return view('vendedor.comisiones.show', compact('comision', 'solicitudPago'));
    }

    /**
     * Reenviar notificación de solicitud de retiro a administradores
     */
    public function reenviarNotificacion($solicitudId)
    {
        try {
            $vendedor = Auth::user();
            $solicitud = SolicitudPago::findOrFail($solicitudId);

            // Verificar que la solicitud pertenezca al vendedor
            if ($solicitud->user_id != ($vendedor->_id ?? $vendedor->id)) {
                return back()->with('error', 'No tienes permiso para realizar esta acción.');
            }

            // Verificar que la solicitud esté en estado pendiente
            if ($solicitud->estado !== 'pendiente') {
                return back()->with('warning', 'Solo se pueden reenviar notificaciones de solicitudes pendientes.');
            }

            // Enviar nuevamente las notificaciones
            $this->enviarNotificacionAdministradores($solicitud, $vendedor);

            return back()->with('success', 'Notificación reenviada a los administradores exitosamente.');

        } catch (\Exception $e) {
            return back()->with('error', 'Error al reenviar la notificación: ' . $e->getMessage());
        }
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
            'total_ganado' => $this->convertirDecimal($comisionesData->sum('monto')),
            'disponible_retiro' => $this->convertirDecimal($comisionesData->where('estado', 'pendiente')->sum('monto')),
            'en_proceso' => $this->convertirDecimal($comisionesData->where('estado', 'en_proceso')->sum('monto')),
            'pagado' => $this->convertirDecimal($comisionesData->where('estado', 'pagado')->sum('monto')),

            // Este mes
            'mes_actual' => $this->convertirDecimal($comisionesData->whereBetween('created_at', [$mesActual, $finMes])->sum('monto')),

            // Por tipo
            'ventas_directas' => $this->convertirDecimal($comisionesData->where('tipo', 'venta_directa')->sum('monto')),
            'referidos' => $this->convertirDecimal($comisionesData->where('tipo', 'referido')->sum('monto')),

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
                'comisiones' => $this->convertirDecimal($comisiones)
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

    /**
     * Enviar notificación a los administradores sobre nueva solicitud de retiro
     */
    private function enviarNotificacionAdministradores($solicitud, $vendedor)
    {
        // Obtener todos los administradores
        $administradores = User::where('rol', 'administrador')
                              ->where('activo', true)
                              ->get();

        \Log::info('Enviando notificaciones a administradores', [
            'total_admins' => $administradores->count(),
            'solicitud_id' => $solicitud->_id ?? $solicitud->id
        ]);

        foreach ($administradores as $admin) {
            $notificacion = \App\Models\Notificacion::create([
                'user_id' => (string) ($admin->_id ?? $admin->id),
                'user_data' => [
                    'nombre' => $admin->name,
                    'email' => $admin->email
                ],
                'titulo' => 'Nueva Solicitud de Retiro de Comisiones',
                'mensaje' => "{$vendedor->name} ha solicitado un retiro de \${$solicitud->monto} por {$solicitud->metodo_pago}",
                'tipo' => 'solicitud_retiro',
                'leida' => false,
                'datos_adicionales' => [
                    'solicitud_id' => (string) ($solicitud->_id ?? $solicitud->id),
                    'vendedor_id' => (string) ($vendedor->_id ?? $vendedor->id),
                    'vendedor_nombre' => $vendedor->name,
                    'monto' => $solicitud->monto,
                    'metodo_pago' => $solicitud->metodo_pago,
                    'datos_pago' => $solicitud->datos_pago,
                    'estado' => $solicitud->estado,
                    'puede_cambiar_estado' => true,
                    'acciones' => [
                        'aprobar' => route('admin.solicitudes-retiro.aprobar', $solicitud->_id ?? $solicitud->id),
                        'rechazar' => route('admin.solicitudes-retiro.rechazar', $solicitud->_id ?? $solicitud->id),
                        'marcar_pagado' => route('admin.solicitudes-retiro.marcar-pagado', $solicitud->_id ?? $solicitud->id)
                    ]
                ],
                'canal' => 'web'
            ]);

            \Log::info('Notificación creada', [
                'admin_id' => $admin->_id ?? $admin->id,
                'notificacion_id' => $notificacion->_id ?? $notificacion->id,
                'user_id_guardado' => $notificacion->user_id
            ]);
        }
    }
}