<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Pedido;
use App\Models\DetallePedido;
use App\Models\User;
use App\Models\Producto;
use Carbon\Carbon;

class PedidosSeeder extends Seeder
{
    public function run(): void
    {
        $clientes = User::where('rol', 'cliente')->get();
        $vendedores = User::where('rol', 'vendedor')->get();
        $productos = Producto::all();

        // Crear pedidos de ejemplo
        $pedidosData = [
            [
                'cliente_id' => $clientes->first()->id,
                'vendedor_id' => $vendedores->first()->id,
                'estado' => 'en_preparacion',
                'direccion_entrega' => 'Calle 45 #23-12, Bogotá',
                'telefono_entrega' => '3066778899',
                'productos' => [
                    ['producto_id' => 1, 'cantidad' => 4], // Arepa de Queso Llanero
                    ['producto_id' => 4, 'cantidad' => 2], // Arepa de Carne Mechada
                ],
                'created_at' => now()->subMinutes(15)
            ],
            [
                'cliente_id' => $clientes->get(1)->id,
                'vendedor_id' => $vendedores->get(1)->id,
                'estado' => 'confirmado',
                'direccion_entrega' => 'Carrera 30 #15-45, Villavicencio',
                'telefono_entrega' => '3077889900',
                'productos' => [
                    ['producto_id' => 9, 'cantidad' => 1], // Arepa Mixta
                    ['producto_id' => 13, 'cantidad' => 2], // Chicha Llanera
                ],
                'created_at' => now()->subHour(1)
            ],
            [
                'cliente_id' => $clientes->get(2)->id,
                'vendedor_id' => $vendedores->get(2)->id,
                'estado' => 'listo',
                'direccion_entrega' => 'Calle 20 #10-25, Villavicencio',
                'telefono_entrega' => '3088990011',
                'productos' => [
                    ['producto_id' => 9, 'cantidad' => 3], // Arepa Mixta
                    ['producto_id' => 15, 'cantidad' => 1], // Jugo de Maracuyá
                ],
                'created_at' => now()->subHours(2)
            ],
            [
                'cliente_id' => $clientes->get(3)->id,
                'vendedor_id' => $vendedores->get(3)->id,
                'estado' => 'entregado',
                'direccion_entrega' => 'Carrera 25 #35-60, Villavicencio',
                'telefono_entrega' => '3099001122',
                'productos' => [
                    ['producto_id' => 7, 'cantidad' => 6], // Arepa de Pollo Desmechado
                    ['producto_id' => 14, 'cantidad' => 3], // Guarapo de Caña
                ],
                'created_at' => now()->subHours(3)
            ],
            [
                'cliente_id' => $clientes->get(4)->id,
                'vendedor_id' => $vendedores->get(4)->id,
                'estado' => 'entregado',
                'direccion_entrega' => 'Calle 50 #40-15, Villavicencio',
                'telefono_entrega' => '3010203040',
                'productos' => [
                    ['producto_id' => 10, 'cantidad' => 1], // Arepa Llanera Especial
                    ['producto_id' => 17, 'cantidad' => 1], // Quesillo Llanero
                ],
                'created_at' => now()->subHours(4)
            ]
        ];

        $numeroPedido = 1287;

        foreach ($pedidosData as $pedidoData) {
            $total = 0;
            
            // Calcular total
            foreach ($pedidoData['productos'] as $item) {
                $producto = Producto::find($item['producto_id']);
                $total += $producto->precio * $item['cantidad'];
            }

            // Crear el pedido
            $pedido = Pedido::create([
                'numero_pedido' => '#ARF-2024-00' . $numeroPedido,
                'user_id' => $pedidoData['cliente_id'],
                'vendedor_id' => $pedidoData['vendedor_id'],
                'estado' => $pedidoData['estado'],
                'total' => $total,
                'descuento' => 0,
                'total_final' => $total,
                'direccion_entrega' => $pedidoData['direccion_entrega'],
                'telefono_entrega' => $pedidoData['telefono_entrega'],
                'created_at' => $pedidoData['created_at'],
                'updated_at' => $pedidoData['created_at'],
            ]);

            // Crear los detalles del pedido
            foreach ($pedidoData['productos'] as $item) {
                $producto = Producto::find($item['producto_id']);
                DetallePedido::create([
                    'pedido_id' => $pedido->id,
                    'producto_id' => $producto->id,
                    'cantidad' => $item['cantidad'],
                    'precio_unitario' => $producto->precio,
                    'subtotal' => $producto->precio * $item['cantidad'],
                ]);
            }

            $numeroPedido++;
        }
    }
}