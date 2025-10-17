<?php

namespace App\Observers;

use App\Models\Pedido;
use App\Models\Comision;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class PedidoObserver
{
    /**
     * Handle the Pedido "updated" event.
     * Calcula comisiones automáticamente cuando un pedido se marca como entregado o confirmado.
     */
    public function updated(Pedido $pedido): void
    {
        // Solo calcular comisiones si el pedido cambió a estado "entregado" o "confirmado"
        // y NO se han calculado comisiones previamente
        if (!$this->debeCalcularComisiones($pedido)) {
            return;
        }

        try {
            Log::info('Calculando comisiones automáticamente', [
                'pedido_id' => $pedido->_id,
                'numero_pedido' => $pedido->numero_pedido,
                'estado' => $pedido->estado,
                'total_final' => $pedido->total_final
            ]);

            // Calcular y crear comisiones
            $this->calcularComisiones($pedido);

            // Marcar que las comisiones ya fueron calculadas
            $pedido->comisiones_calculadas = [
                'calculado' => true,
                'fecha_calculo' => now(),
                'estado_cuando_calculo' => $pedido->estado
            ];
            $pedido->saveQuietly(); // Guardar sin disparar eventos

            Log::info('Comisiones calculadas exitosamente', ['pedido_id' => $pedido->_id]);
        } catch (\Exception $e) {
            Log::error('Error al calcular comisiones automáticamente', [
                'pedido_id' => $pedido->_id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }

    /**
     * Determina si se deben calcular comisiones para este pedido
     */
    private function debeCalcularComisiones(Pedido $pedido): bool
    {
        // No calcular si ya se calcularon previamente
        if (isset($pedido->comisiones_calculadas['calculado']) && $pedido->comisiones_calculadas['calculado'] === true) {
            return false;
        }

        // Solo calcular para pedidos entregados o confirmados
        if (!in_array($pedido->estado, ['entregado', 'confirmado'])) {
            return false;
        }

        // Verificar si hay un cambio de estado desde el estado original
        $original = $pedido->getOriginal();
        if (isset($original['estado']) && $original['estado'] === $pedido->estado) {
            // El estado no cambió, no calcular
            return false;
        }

        return true;
    }

    /**
     * Calcula y crea las comisiones correspondientes para un pedido
     */
    private function calcularComisiones(Pedido $pedido): void
    {
        if (!$pedido->vendedor_id) {
            Log::info('Pedido sin vendedor asignado, no se calculan comisiones', ['pedido_id' => $pedido->_id]);
            return;
        }

        $vendedor = User::find($pedido->vendedor_id);
        if (!$vendedor) {
            Log::warning('Vendedor no encontrado', ['vendedor_id' => $pedido->vendedor_id]);
            return;
        }

        $totalPedido = to_float($pedido->total_final);

        // 1. Comisión por venta directa del vendedor
        $this->crearComisionVentaDirecta($pedido, $vendedor, $totalPedido);

        // 2. Comisión por referido (si el vendedor fue referido por alguien)
        if ($vendedor->referido_por) {
            $this->crearComisionReferido($pedido, $vendedor, $totalPedido);

            // 3. Comisiones de liderazgo (hasta 3 niveles)
            $this->crearComisionesLiderazgo($pedido, $vendedor, $totalPedido);
        }
    }

    /**
     * Crea comisión por venta directa (10%)
     */
    private function crearComisionVentaDirecta(Pedido $pedido, User $vendedor, float $totalPedido): void
    {
        $porcentaje = config('comisiones.venta_directa', 10);
        $monto = $totalPedido * ($porcentaje / 100);

        $comision = Comision::create([
            'user_id' => $vendedor->_id,
            'user_data' => [
                'name' => $vendedor->name,
                'email' => $vendedor->email,
                'rol' => $vendedor->rol
            ],
            'pedido_id' => $pedido->_id,
            'pedido_data' => [
                'numero_pedido' => $pedido->numero_pedido,
                'total' => $totalPedido,
                'estado' => $pedido->estado,
                'cliente' => $pedido->cliente_data['name'] ?? 'Cliente',
                'vendedor' => $vendedor->name
            ],
            'referido_id' => null,
            'referido_data' => null,
            'tipo' => 'venta_directa',
            'porcentaje' => $porcentaje,
            'monto' => $monto,
            'estado' => 'pendiente',
            'fecha_pago' => null,
            'detalles_calculo' => [
                'base' => $totalPedido,
                'porcentaje_aplicado' => $porcentaje,
                'tipo_comision' => 'venta_directa',
                'fecha_calculo' => now()->toDateString(),
                'automatico' => true
            ]
        ]);

        // Actualizar comisiones ganadas del vendedor
        $vendedor->increment('comisiones_ganadas', $monto);

        Log::info('Comisión de venta directa creada', [
            'comision_id' => $comision->_id,
            'vendedor' => $vendedor->name,
            'monto' => $monto
        ]);
    }

    /**
     * Crea comisión por referido (5% para quien refirió)
     */
    private function crearComisionReferido(Pedido $pedido, User $vendedor, float $totalPedido): void
    {
        $referidor = User::find($vendedor->referido_por);
        if (!$referidor) {
            return;
        }

        $porcentaje = config('comisiones.referido', 5);
        $monto = $totalPedido * ($porcentaje / 100);

        $comision = Comision::create([
            'user_id' => $referidor->_id,
            'user_data' => [
                'name' => $referidor->name,
                'email' => $referidor->email,
                'rol' => $referidor->rol
            ],
            'pedido_id' => $pedido->_id,
            'pedido_data' => [
                'numero_pedido' => $pedido->numero_pedido,
                'total' => $totalPedido,
                'estado' => $pedido->estado,
                'cliente' => $pedido->cliente_data['name'] ?? 'Cliente',
                'vendedor' => $vendedor->name
            ],
            'referido_id' => $vendedor->_id,
            'referido_data' => [
                'name' => $vendedor->name,
                'email' => $vendedor->email
            ],
            'tipo' => 'referido',
            'porcentaje' => $porcentaje,
            'monto' => $monto,
            'estado' => 'pendiente',
            'fecha_pago' => null,
            'detalles_calculo' => [
                'base' => $totalPedido,
                'porcentaje_aplicado' => $porcentaje,
                'tipo_comision' => 'referido',
                'fecha_calculo' => now()->toDateString(),
                'automatico' => true
            ]
        ]);

        // Actualizar comisiones ganadas del referidor
        $referidor->increment('comisiones_ganadas', $monto);

        Log::info('Comisión de referido creada', [
            'comision_id' => $comision->_id,
            'referidor' => $referidor->name,
            'vendedor' => $vendedor->name,
            'monto' => $monto
        ]);
    }

    /**
     * Crea comisiones de liderazgo (3% para niveles superiores, hasta 3 niveles)
     */
    private function crearComisionesLiderazgo(Pedido $pedido, User $vendedor, float $totalPedido): void
    {
        $porcentaje = config('comisiones.liderazgo', 3);
        $nivelActual = $vendedor;
        $nivel = 1;
        $maxNiveles = 3;

        while ($nivel <= $maxNiveles && $nivelActual->referido_por) {
            $lider = User::find($nivelActual->referido_por);

            if (!$lider) {
                break;
            }

            // Solo dar comisión de liderazgo si el referidor es líder o admin
            if (!in_array($lider->rol, ['lider', 'administrador'])) {
                $nivelActual = $lider;
                $nivel++;
                continue;
            }

            $monto = $totalPedido * ($porcentaje / 100);

            $comision = Comision::create([
                'user_id' => $lider->_id,
                'user_data' => [
                    'name' => $lider->name,
                    'email' => $lider->email,
                    'rol' => $lider->rol
                ],
                'pedido_id' => $pedido->_id,
                'pedido_data' => [
                    'numero_pedido' => $pedido->numero_pedido,
                    'total' => $totalPedido,
                    'estado' => $pedido->estado,
                    'cliente' => $pedido->cliente_data['name'] ?? 'Cliente',
                    'vendedor' => $vendedor->name
                ],
                'referido_id' => $vendedor->_id,
                'referido_data' => [
                    'name' => $vendedor->name,
                    'email' => $vendedor->email
                ],
                'tipo' => 'liderazgo',
                'porcentaje' => $porcentaje,
                'monto' => $monto,
                'estado' => 'pendiente',
                'fecha_pago' => null,
                'detalles_calculo' => [
                    'base' => $totalPedido,
                    'porcentaje_aplicado' => $porcentaje,
                    'tipo_comision' => 'liderazgo',
                    'nivel' => $nivel,
                    'fecha_calculo' => now()->toDateString(),
                    'automatico' => true
                ]
            ]);

            // Actualizar comisiones ganadas del líder
            $lider->increment('comisiones_ganadas', $monto);

            Log::info('Comisión de liderazgo creada', [
                'comision_id' => $comision->_id,
                'lider' => $lider->name,
                'nivel' => $nivel,
                'monto' => $monto
            ]);

            $nivelActual = $lider;
            $nivel++;
        }
    }
}
