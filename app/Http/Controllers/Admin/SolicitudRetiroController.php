<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SolicitudPago;
use App\Models\Comision;
use App\Models\User;
use App\Models\Notificacion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SolicitudRetiroController extends Controller
{
    /**
     * Aprobar una solicitud de retiro
     */
    public function aprobar($id)
    {
        try {
            $solicitud = SolicitudPago::findOrFail($id);
            
            \Log::info('Intento de aprobar solicitud', [
                'solicitud_id' => $id,
                'estado_actual' => $solicitud->estado,
                'tipo_estado' => gettype($solicitud->estado)
            ]);
            
            // Verificar estado - más flexible
            $estadoActual = strtolower(trim((string) $solicitud->estado));
            
            if (!in_array($estadoActual, ['pendiente', 'pending'])) {
                \Log::warning('Solicitud ya procesada', [
                    'solicitud_id' => $id,
                    'estado' => $estadoActual
                ]);
                
                return response()->json([
                    'success' => false,
                    'message' => "Esta solicitud ya ha sido procesada. Estado actual: {$estadoActual}",
                    'estado_actual' => $estadoActual
                ], 400);
            }

            $solicitud->update([
                'estado' => 'aprobado',
                'aprobado_por' => (string) Auth::id(),
                'aprobado_en' => now()
            ]);

            // Actualizar todas las notificaciones relacionadas con esta solicitud
            $this->actualizarNotificacionesSolicitud($solicitud->_id ?? $solicitud->id, 'aprobado');

            // Notificar al vendedor
            $this->notificarVendedor($solicitud, 'aprobado');

            \Log::info('Solicitud aprobada exitosamente', ['solicitud_id' => $id]);

            if (request()->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Solicitud aprobada exitosamente.',
                    'nuevo_estado' => 'aprobado'
                ]);
            }

            return back()->with('success', 'Solicitud aprobada exitosamente.');

        } catch (\Exception $e) {
            \Log::error('Error al aprobar solicitud', [
                'solicitud_id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            if (request()->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al aprobar la solicitud: ' . $e->getMessage()
                ], 500);
            }
            return back()->with('error', 'Error al aprobar la solicitud: ' . $e->getMessage());
        }
    }

    /**
     * Rechazar una solicitud de retiro
     */
    public function rechazar(Request $request, $id)
    {
        try {
            $solicitud = SolicitudPago::findOrFail($id);
            
            \Log::info('Intento de rechazar solicitud', [
                'solicitud_id' => $id,
                'estado_actual' => $solicitud->estado,
                'user_id' => $solicitud->user_id
            ]);
            
            // Verificar estado - más flexible
            $estadoActual = strtolower(trim((string) $solicitud->estado));
            
            if (!in_array($estadoActual, ['pendiente', 'pending'])) {
                return response()->json([
                    'success' => false,
                    'message' => "Esta solicitud ya ha sido procesada. Estado actual: {$estadoActual}",
                    'estado_actual' => $estadoActual
                ], 400);
            }

            $solicitud->update([
                'estado' => 'rechazado',
                'rechazado_por' => (string) Auth::id(),
                'rechazado_en' => now(),
                'motivo_rechazo' => $request->input('motivo', 'No especificado')
            ]);

            // Convertir user_id a string
            $userId = (string) $solicitud->user_id;
            
            // Devolver las comisiones al estado "pendiente"
            $comisionesEnProceso = Comision::where('user_id', $userId)
                                          ->where('estado', 'en_proceso')
                                          ->get();
            
            \Log::info('Comisiones a devolver a pendiente', [
                'user_id' => $userId,
                'cantidad' => $comisionesEnProceso->count()
            ]);
            
            $actualizadas = 0;
            foreach ($comisionesEnProceso as $comision) {
                $comision->update(['estado' => 'pendiente']);
                $actualizadas++;
            }
            
            \Log::info('Comisiones devueltas a pendiente', [
                'cantidad_actualizada' => $actualizadas
            ]);

            // Actualizar todas las notificaciones relacionadas con esta solicitud
            $this->actualizarNotificacionesSolicitud($solicitud->_id ?? $solicitud->id, 'rechazado', $request->input('motivo'));

            // Notificar al vendedor
            $this->notificarVendedor($solicitud, 'rechazado', $request->input('motivo'));

            \Log::info('Solicitud rechazada exitosamente', [
                'solicitud_id' => $id,
                'comisiones_devueltas' => $actualizadas
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Solicitud rechazada. Las comisiones han sido devueltas.',
                    'nuevo_estado' => 'rechazado',
                    'motivo' => $request->input('motivo'),
                    'comisiones_devueltas' => $actualizadas
                ]);
            }

            return back()->with('success', 'Solicitud rechazada. Las comisiones han sido devueltas.');

        } catch (\Exception $e) {
            \Log::error('Error al rechazar solicitud', [
                'solicitud_id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al rechazar la solicitud: ' . $e->getMessage()
                ], 500);
            }
            return back()->with('error', 'Error al rechazar la solicitud: ' . $e->getMessage());
        }
    }

    /**
     * Marcar una solicitud como pagada
     */
    public function marcarPagado(Request $request, $id)
    {
        try {
            $solicitud = SolicitudPago::findOrFail($id);
            
            \Log::info('Intento de marcar como pagado', [
                'solicitud_id' => $id,
                'estado_actual' => $solicitud->estado,
                'user_id' => $solicitud->user_id,
                'comisiones_ids' => $solicitud->comisiones_ids ?? []
            ]);
            
            // Verificar estado - más flexible
            $estadoActual = strtolower(trim((string) $solicitud->estado));
            
            if (!in_array($estadoActual, ['pendiente', 'pending', 'aprobado', 'approved'])) {
                return response()->json([
                    'success' => false,
                    'message' => "Esta solicitud no puede ser marcada como pagada. Estado actual: {$estadoActual}",
                    'estado_actual' => $estadoActual
                ], 400);
            }

            // Actualizar la solicitud
            $solicitud->update([
                'estado' => 'pagado',
                'pagado_por' => (string) Auth::id(),
                'pagado_en' => now(),
                'referencia_pago' => $request->input('referencia_pago'),
                'notas_pago' => $request->input('notas_pago')
            ]);

            $actualizadas = 0;
            
            // Método 1: Actualizar usando los IDs guardados en la solicitud
            if (!empty($solicitud->comisiones_ids)) {
                \Log::info('Actualizando comisiones por IDs guardados', [
                    'comisiones_ids' => $solicitud->comisiones_ids
                ]);
                
                foreach ($solicitud->comisiones_ids as $comisionId) {
                    $comision = Comision::find($comisionId);
                    if ($comision) {
                        $comision->update([
                            'estado' => 'pagado',
                            'fecha_pago' => now()
                        ]);
                        $actualizadas++;
                    }
                }
            } 
            // Método 2: Buscar por solicitud_pago_id
            else {
                $solicitudId = (string) ($solicitud->_id ?? $solicitud->id);
                \Log::info('Actualizando comisiones por solicitud_pago_id', [
                    'solicitud_id' => $solicitudId
                ]);
                
                $comisiones = Comision::where('solicitud_pago_id', $solicitudId)->get();
                foreach ($comisiones as $comision) {
                    $comision->update([
                        'estado' => 'pagado',
                        'fecha_pago' => now()
                    ]);
                    $actualizadas++;
                }
            }
            
            // Método 3: Fallback - buscar por user_id y estado en_proceso
            if ($actualizadas === 0) {
                $userId = (string) $solicitud->user_id;
                \Log::warning('No se encontraron comisiones vinculadas, buscando por user_id y estado', [
                    'user_id' => $userId
                ]);
                
                $comisionesEnProceso = Comision::where('user_id', $userId)
                                              ->where('estado', 'en_proceso')
                                              ->get();
                
                foreach ($comisionesEnProceso as $comision) {
                    $comision->update([
                        'estado' => 'pagado',
                        'fecha_pago' => now()
                    ]);
                    $actualizadas++;
                }
            }
            
            \Log::info('Comisiones actualizadas a pagado', [
                'cantidad_actualizada' => $actualizadas,
                'solicitud_id' => $id
            ]);

            // Si aún no se actualizó ninguna, registrar para debug
            if ($actualizadas === 0) {
                $userId = (string) $solicitud->user_id;
                $todasComisiones = Comision::where('user_id', $userId)->get();
                \Log::warning('No se pudo actualizar ninguna comisión', [
                    'user_id' => $userId,
                    'todas_comisiones_count' => $todasComisiones->count(),
                    'estados' => $todasComisiones->pluck('estado', '_id')->toArray()
                ]);
            }

            // Actualizar todas las notificaciones relacionadas con esta solicitud
            $this->actualizarNotificacionesSolicitud($solicitud->_id ?? $solicitud->id, 'pagado', null, $request->input('referencia_pago'));

            // Notificar al vendedor
            $this->notificarVendedor($solicitud, 'pagado');

            \Log::info('Solicitud marcada como pagada exitosamente', [
                'solicitud_id' => $id,
                'comisiones_actualizadas' => $actualizadas
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Solicitud marcada como pagada exitosamente.',
                    'nuevo_estado' => 'pagado',
                    'referencia' => $request->input('referencia_pago'),
                    'comisiones_actualizadas' => $actualizadas
                ]);
            }

            return back()->with('success', 'Solicitud marcada como pagada exitosamente.');

        } catch (\Exception $e) {
            \Log::error('Error al marcar como pagado', [
                'solicitud_id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al marcar como pagado: ' . $e->getMessage()
                ], 500);
            }
            return back()->with('error', 'Error al marcar como pagado: ' . $e->getMessage());
        }
    }

    /**
     * Actualizar notificaciones relacionadas con la solicitud
     */
    private function actualizarNotificacionesSolicitud($solicitudId, $nuevoEstado, $motivo = null, $referencia = null)
    {
        $notificaciones = Notificacion::where('datos_adicionales.solicitud_id', (string) $solicitudId)
                                      ->where('tipo', 'solicitud_retiro')
                                      ->get();

        foreach ($notificaciones as $notificacion) {
            $datosAdicionales = $notificacion->datos_adicionales;
            $datosAdicionales['estado'] = $nuevoEstado;
            $datosAdicionales['puede_cambiar_estado'] = false; // Ya no se puede cambiar
            $datosAdicionales['procesado_en'] = now()->toIso8601String();
            
            if ($motivo) {
                $datosAdicionales['motivo_rechazo'] = $motivo;
            }
            
            if ($referencia) {
                $datosAdicionales['referencia_pago'] = $referencia;
            }
            
            $notificacion->update([
                'datos_adicionales' => $datosAdicionales
            ]);
        }
    }

    /**
     * Notificar al vendedor sobre cambio de estado
     */
    private function notificarVendedor($solicitud, $nuevoEstado, $motivo = null)
    {
        $vendedor = User::find($solicitud->user_id);
        
        if (!$vendedor) {
            return;
        }

        $mensajes = [
            'aprobado' => "Tu solicitud de retiro de \${$solicitud->monto} ha sido aprobada. El pago será procesado en las próximas horas.",
            'rechazado' => "Tu solicitud de retiro de \${$solicitud->monto} ha sido rechazada." . ($motivo ? " Motivo: {$motivo}" : ""),
            'pagado' => "Tu solicitud de retiro de \${$solicitud->monto} ha sido pagada exitosamente."
        ];

        Notificacion::create([
            'user_id' => (string) ($vendedor->_id ?? $vendedor->id),
            'user_data' => [
                'nombre' => $vendedor->name,
                'email' => $vendedor->email
            ],
            'titulo' => 'Actualización de Solicitud de Retiro',
            'mensaje' => $mensajes[$nuevoEstado],
            'tipo' => 'solicitud_retiro_actualizada',
            'leida' => false,
            'datos_adicionales' => [
                'solicitud_id' => (string) ($solicitud->_id ?? $solicitud->id),
                'monto' => $solicitud->monto,
                'estado' => $nuevoEstado,
                'metodo_pago' => $solicitud->metodo_pago
            ],
            'canal' => 'web'
        ]);
    }

    /**
     * Cambiar estado desde notificación (AJAX)
     */
    public function cambiarEstado(Request $request, $id)
    {
        $request->validate([
            'accion' => 'required|in:aprobar,rechazar,marcar_pagado'
        ]);

        switch ($request->accion) {
            case 'aprobar':
                return $this->aprobar($id);
            case 'rechazar':
                return $this->rechazar($request, $id);
            case 'marcar_pagado':
                return $this->marcarPagado($request, $id);
        }
    }
}
