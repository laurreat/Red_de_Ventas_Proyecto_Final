<?php

namespace App\Services;

use App\Models\Comision;
use App\Models\Pedido;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class ComisionService
{
    /**
     * Crear comisión automáticamente cuando se crea un pedido
     */
    public static function crearComisionPorPedido(Pedido $pedido): ?Comision
    {
        try {
            // Solo crear comisión si hay vendedor asignado y el pedido no está cancelado
            if (!$pedido->vendedor_id || $pedido->estado === 'cancelado') {
                Log::info('No se crea comisión: sin vendedor o pedido cancelado', [
                    'pedido_id' => $pedido->_id,
                    'vendedor_id' => $pedido->vendedor_id,
                    'estado' => $pedido->estado
                ]);
                return null;
            }

            // Verificar si ya existe una comisión para este pedido
            $comisionExistente = Comision::where('pedido_id', $pedido->_id)
                ->where('user_id', $pedido->vendedor_id)
                ->first();

            if ($comisionExistente) {
                Log::info('Ya existe comisión para este pedido', [
                    'pedido_id' => $pedido->_id,
                    'comision_id' => $comisionExistente->_id
                ]);
                return $comisionExistente;
            }

            // Obtener datos del vendedor
            $vendedor = User::find($pedido->vendedor_id);
            if (!$vendedor) {
                Log::warning('Vendedor no encontrado', ['vendedor_id' => $pedido->vendedor_id]);
                return null;
            }

            // Calcular comisión (15% del total del pedido por defecto)
            $porcentajeComision = config('comisiones.venta_directa', 15);
            $montoComision = ($pedido->total_final * $porcentajeComision) / 100;

            // Crear la comisión
            $comision = Comision::create([
                'user_id' => $vendedor->_id,
                'user_data' => [
                    '_id' => $vendedor->_id,
                    'name' => $vendedor->name,
                    'email' => $vendedor->email,
                    'rol' => $vendedor->rol
                ],
                'pedido_id' => $pedido->_id,
                'pedido_data' => [
                    '_id' => $pedido->_id,
                    'numero_pedido' => $pedido->numero_pedido,
                    'total' => $pedido->total_final,
                    'fecha' => $pedido->created_at
                ],
                'tipo' => 'venta_directa',
                'porcentaje' => $porcentajeComision,
                'monto' => $montoComision,
                'estado' => 'pendiente',
                'detalles_calculo' => [
                    'total_pedido' => $pedido->total_final,
                    'porcentaje_aplicado' => $porcentajeComision,
                    'fecha_calculo' => now()
                ]
            ]);

            Log::info('Comisión creada exitosamente', [
                'comision_id' => $comision->_id,
                'pedido_id' => $pedido->_id,
                'vendedor_id' => $vendedor->_id,
                'monto' => $montoComision
            ]);

            return $comision;

        } catch (\Exception $e) {
            Log::error('Error al crear comisión por pedido', [
                'pedido_id' => $pedido->_id ?? null,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return null;
        }
    }

    /**
     * Eliminar comisión cuando se elimina un pedido
     */
    public static function eliminarComisionPorPedido(Pedido $pedido): bool
    {
        try {
            // Buscar todas las comisiones relacionadas con este pedido
            $comisiones = Comision::where('pedido_id', $pedido->_id)->get();

            if ($comisiones->isEmpty()) {
                Log::info('No hay comisiones para eliminar', ['pedido_id' => $pedido->_id]);
                return true;
            }

            $eliminadas = 0;
            foreach ($comisiones as $comision) {
                // Solo eliminar comisiones que no han sido pagadas
                if ($comision->estado !== 'pagada') {
                    $comision->delete();
                    $eliminadas++;
                    
                    Log::info('Comisión eliminada', [
                        'comision_id' => $comision->_id,
                        'pedido_id' => $pedido->_id,
                        'estado' => $comision->estado
                    ]);
                } else {
                    Log::warning('Comisión no eliminada - ya está pagada', [
                        'comision_id' => $comision->_id,
                        'pedido_id' => $pedido->_id
                    ]);
                }
            }

            Log::info('Proceso de eliminación de comisiones completado', [
                'pedido_id' => $pedido->_id,
                'comisiones_eliminadas' => $eliminadas,
                'total_comisiones' => $comisiones->count()
            ]);

            return true;

        } catch (\Exception $e) {
            Log::error('Error al eliminar comisiones por pedido', [
                'pedido_id' => $pedido->_id ?? null,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Actualizar comisión cuando se actualiza el pedido
     */
    public static function actualizarComisionPorPedido(Pedido $pedido): ?Comision
    {
        try {
            // Si el pedido está cancelado, eliminar la comisión
            if ($pedido->estado === 'cancelado') {
                self::eliminarComisionPorPedido($pedido);
                return null;
            }

            // Buscar comisión existente
            $comision = Comision::where('pedido_id', $pedido->_id)
                ->where('user_id', $pedido->vendedor_id)
                ->first();

            if (!$comision) {
                Log::info('No existe comisión para este pedido, creando nueva', [
                    'pedido_id' => $pedido->_id
                ]);
                // Si no existe, crear nueva comisión
                return self::crearComisionPorPedido($pedido);
            }

            // Si ya está pagada o en proceso, no actualizar el monto
            if (in_array($comision->estado, ['pagado', 'en_proceso'])) {
                Log::info('Comisión no actualizada - estado protegido', [
                    'comision_id' => $comision->_id,
                    'estado' => $comision->estado
                ]);
                
                // Actualizar solo los datos del pedido (no el monto)
                $comision->update([
                    'pedido_data' => [
                        '_id' => $pedido->_id,
                        'numero_pedido' => $pedido->numero_pedido,
                        'total' => $pedido->total_final,
                        'fecha' => $pedido->created_at
                    ]
                ]);
                
                return $comision;
            }

            // Recalcular monto de comisión solo si está pendiente
            $porcentajeComision = $comision->porcentaje ?? config('comisiones.venta_directa', 15);
            $montoComisionAnterior = $comision->monto;
            $montoComisionNuevo = ($pedido->total_final * $porcentajeComision) / 100;

            Log::info('Recalculando comisión', [
                'comision_id' => $comision->_id,
                'monto_anterior' => $montoComisionAnterior,
                'monto_nuevo' => $montoComisionNuevo,
                'total_pedido' => $pedido->total_final,
                'porcentaje' => $porcentajeComision
            ]);

            // Actualizar comisión
            $comision->update([
                'monto' => $montoComisionNuevo,
                'pedido_data' => [
                    '_id' => $pedido->_id,
                    'numero_pedido' => $pedido->numero_pedido,
                    'total' => $pedido->total_final,
                    'fecha' => $pedido->created_at
                ],
                'detalles_calculo' => [
                    'total_pedido' => $pedido->total_final,
                    'porcentaje_aplicado' => $porcentajeComision,
                    'monto_anterior' => $montoComisionAnterior,
                    'fecha_actualizacion' => now(),
                    'fecha_calculo_original' => $comision->detalles_calculo['fecha_calculo'] ?? $comision->created_at
                ]
            ]);

            Log::info('Comisión actualizada exitosamente', [
                'comision_id' => $comision->_id,
                'monto_anterior' => $montoComisionAnterior,
                'monto_nuevo' => $montoComisionNuevo,
                'diferencia' => $montoComisionNuevo - $montoComisionAnterior
            ]);

            return $comision;

        } catch (\Exception $e) {
            Log::error('Error al actualizar comisión', [
                'pedido_id' => $pedido->_id ?? null,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return null;
        }
    }
}
