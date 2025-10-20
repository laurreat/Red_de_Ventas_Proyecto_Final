<?php

namespace App\Observers;

use App\Models\Pedido;
use App\Models\Comision;
use App\Models\User;
use App\Services\ComisionService;
use App\Services\NotificationService;
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

            // Generar notificaciones
            try {
                // Notificar al cliente sobre su nuevo pedido
                if ($pedido->cliente_id) {
                    NotificationService::crear(
                        $pedido->cliente_id,
                        'pedido',
                        "Pedido #{$pedido->numero_pedido} Creado",
                        "Tu pedido ha sido creado exitosamente. Total: $" . number_format($pedido->total_final, 0),
                        [
                            'pedido_id' => $pedido->_id,
                            'numero_pedido' => $pedido->numero_pedido,
                            'total' => $pedido->total_final,
                            'estado' => $pedido->estado
                        ],
                        'normal'
                    );
                }

                // Notificar a administradores sobre el nuevo pedido
                NotificationService::enviarPorRol(
                    'administrador',
                    'pedido',
                    "Nuevo Pedido #{$pedido->numero_pedido}",
                    "Se ha creado un nuevo pedido por $" . number_format($pedido->total_final, 0),
                    [
                        'pedido_id' => $pedido->_id,
                        'numero_pedido' => $pedido->numero_pedido,
                        'total' => $pedido->total_final,
                        'cliente_id' => $pedido->cliente_id
                    ],
                    'alta'
                );

                // Si tiene vendedor, notificar al vendedor sobre la nueva venta
                if ($pedido->vendedor_id) {
                    NotificationService::nuevaVenta($pedido);
                }
            } catch (\Exception $e) {
                Log::error('Error al generar notificaciones de pedido creado', [
                    'pedido_id' => $pedido->_id,
                    'error' => $e->getMessage()
                ]);
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
     * Actualiza o elimina comisiones cuando el pedido cambia.
     */
    public function updated(Pedido $pedido): void
    {
        try {
            $original = $pedido->getOriginal();
            $estadoAnterior = $original['estado'] ?? null;
            $estadoActual = $pedido->estado;
            
            // Obtener totales anterior y actual
            $totalAnterior = $original['total_final'] ?? null;
            $totalActual = $pedido->total_final;
            
            // Detectar si cambió el estado
            $cambioEstado = $estadoAnterior !== $estadoActual;
            
            // Detectar si cambió el total del pedido
            $cambioTotal = $totalAnterior != $totalActual;

            Log::info('Pedido actualizado - verificando comisiones', [
                'pedido_id' => $pedido->_id,
                'estado_anterior' => $estadoAnterior,
                'estado_actual' => $estadoActual,
                'total_anterior' => $totalAnterior,
                'total_actual' => $totalActual,
                'cambio_estado' => $cambioEstado,
                'cambio_total' => $cambioTotal
            ]);

            // CASO 1: Si se cancela el pedido, eliminar comisiones
            if ($estadoActual === 'cancelado' && $estadoAnterior !== 'cancelado') {
                Log::info('Eliminando comisiones por cancelación de pedido');
                ComisionService::eliminarComisionPorPedido($pedido);
            }
            // CASO 2: Si se reactiva un pedido cancelado, crear comisiones nuevamente
            else if ($estadoAnterior === 'cancelado' && $estadoActual !== 'cancelado' && $pedido->vendedor_id) {
                Log::info('Recreando comisiones por reactivación de pedido');
                ComisionService::crearComisionPorPedido($pedido);
            }
            // CASO 3: Si cambia el total del pedido (edición de productos/descuentos), actualizar comisiones
            else if ($cambioTotal && $estadoActual !== 'cancelado' && $pedido->vendedor_id) {
                Log::info('Actualizando comisiones por cambio en total del pedido', [
                    'total_anterior' => $totalAnterior,
                    'total_nuevo' => $totalActual
                ]);
                ComisionService::actualizarComisionPorPedido($pedido);
            }
            // CASO 4: Si no hubo cambios importantes pero el pedido tiene vendedor y no está cancelado,
            // verificar que exista la comisión (por si acaso)
            else if (!$cambioEstado && !$cambioTotal && $pedido->vendedor_id && $estadoActual !== 'cancelado') {
                Log::info('Verificando existencia de comisión');
                ComisionService::actualizarComisionPorPedido($pedido);
            }

            // Generar notificaciones si cambió el estado
            if ($cambioEstado && $pedido->cliente_id) {
                try {
                    NotificationService::cambioEstadoPedido($pedido, $estadoAnterior);
                } catch (\Exception $e) {
                    Log::error('Error al generar notificación de cambio de estado', [
                        'pedido_id' => $pedido->_id,
                        'error' => $e->getMessage()
                    ]);
                }
            }

        } catch (\Exception $e) {
            Log::error('Error al actualizar comisiones en updated event', [
                'pedido_id' => $pedido->_id ?? null,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
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
