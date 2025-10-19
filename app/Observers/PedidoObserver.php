<?php

namespace App\Observers;

use App\Models\Pedido;
use App\Models\Comision;
use App\Models\User;
use App\Services\ComisionService;
use Illuminate\Support\Facades\Log;

class PedidoObserver
{
    /**
     * Handle the Pedido "created" event.
     * Crea comisión automáticamente cuando se crea un pedido.
     */
    public function created(Pedido $pedido): void
    {
        try {
            // Crear comisión automáticamente al crear el pedido (solo si tiene vendedor y no está cancelado)
            if ($pedido->vendedor_id && $pedido->estado !== 'cancelado') {
                Log::info('Creando comisión automáticamente al crear pedido', [
                    'pedido_id' => $pedido->_id,
                    'numero_pedido' => $pedido->numero_pedido,
                    'vendedor_id' => $pedido->vendedor_id
                ]);
                
                ComisionService::crearComisionPorPedido($pedido);
            }
        } catch (\Exception $e) {
            Log::error('Error al crear comisión automáticamente en created event', [
                'pedido_id' => $pedido->_id ?? null,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Handle the Pedido "updated" event.
     * Actualiza o elimina comisiones cuando el pedido cambia de estado.
     */
    public function updated(Pedido $pedido): void
    {
        try {
            $original = $pedido->getOriginal();
            $estadoAnterior = $original['estado'] ?? null;
            $estadoActual = $pedido->estado;

            // Si el estado no cambió, no hacer nada
            if ($estadoAnterior === $estadoActual) {
                return;
            }

            Log::info('Estado de pedido actualizado', [
                'pedido_id' => $pedido->_id,
                'estado_anterior' => $estadoAnterior,
                'estado_actual' => $estadoActual
            ]);

            // Si se cancela el pedido, eliminar comisiones
            if ($estadoActual === 'cancelado' && $estadoAnterior !== 'cancelado') {
                Log::info('Eliminando comisiones por cancelación de pedido');
                ComisionService::eliminarComisionPorPedido($pedido);
            }
            // Si se reactiva un pedido cancelado, crear comisiones nuevamente
            else if ($estadoAnterior === 'cancelado' && $estadoActual !== 'cancelado' && $pedido->vendedor_id) {
                Log::info('Recreando comisiones por reactivación de pedido');
                ComisionService::crearComisionPorPedido($pedido);
            }
            // Si cambia el total del pedido, actualizar comisiones
            else if (isset($original['total_final']) && $original['total_final'] != $pedido->total_final && $estadoActual !== 'cancelado') {
                Log::info('Actualizando comisiones por cambio en total del pedido');
                ComisionService::actualizarComisionPorPedido($pedido);
            }

        } catch (\Exception $e) {
            Log::error('Error al actualizar comisiones en updated event', [
                'pedido_id' => $pedido->_id ?? null,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Handle the Pedido "deleting" event.
     * Elimina comisiones cuando se elimina un pedido.
     */
    public function deleting(Pedido $pedido): void
    {
        try {
            Log::info('Eliminando comisiones por eliminación de pedido', [
                'pedido_id' => $pedido->_id,
                'numero_pedido' => $pedido->numero_pedido
            ]);
            
            ComisionService::eliminarComisionPorPedido($pedido);
        } catch (\Exception $e) {
            Log::error('Error al eliminar comisiones en deleting event', [
                'pedido_id' => $pedido->_id ?? null,
                'error' => $e->getMessage()
            ]);
        }
    }
}
