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
                // Si no existe, crear nueva comisión
                return self::crearComisionPorPedido($pedido);
            }

            // Si ya está pagada, no actualizar
            if ($comision->estado === 'pagada') {
                Log::info('Comisión no actualizada - ya está pagada', [
                    'comision_id' => $comision->_id
                ]);
                return $comision;
            }

            // Recalcular monto de comisión
            $porcentajeComision = $comision->porcentaje ?? config('comisiones.venta_directa', 15);
            $montoComision = ($pedido->total_final * $porcentajeComision) / 100;

            // Actualizar comisión
            $comision->update([
                'monto' => $montoComision,
                'pedido_data' => [
                    '_id' => $pedido->_id,
                    'numero_pedido' => $pedido->numero_pedido,
                    'total' => $pedido->total_final,
                    'fecha' => $pedido->created_at
                ],
                'detalles_calculo' => [
                    'total_pedido' => $pedido->total_final,
                    'porcentaje_aplicado' => $porcentajeComision,
                    'fecha_calculo' => now()
                ]
            ]);

            Log::info('Comisión actualizada', [
                'comision_id' => $comision->_id,
                'nuevo_monto' => $montoComision
            ]);

            return $comision;

        } catch (\Exception $e) {
            Log::error('Error al actualizar comisión', [
                'pedido_id' => $pedido->_id ?? null,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }
}
