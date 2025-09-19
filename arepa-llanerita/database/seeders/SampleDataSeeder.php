<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class SampleDataSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('🚀 Creando datos de prueba en MySQL...');

        // Limpiar tablas existentes
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('notificaciones')->truncate();
        DB::table('comisiones')->truncate();
        DB::table('detalle_pedidos')->truncate();
        DB::table('pedidos')->truncate();
        DB::table('productos')->truncate();
        DB::table('categorias')->truncate();
        DB::table('users')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Crear categorías
        $this->command->info('📂 Creando categorías...');
        $categoriasIds = [];
        $categorias = [
            ['nombre' => 'Arepas Tradicionales', 'descripcion' => 'Arepas clásicas de la tradición llanera', 'activo' => 1],
            ['nombre' => 'Arepas Especiales', 'descripcion' => 'Arepas gourmet con ingredientes premium', 'activo' => 1],
            ['nombre' => 'Bebidas', 'descripcion' => 'Refrescos y bebidas tradicionales', 'activo' => 1],
            ['nombre' => 'Postres', 'descripcion' => 'Dulces típicos llaneros', 'activo' => 1]
        ];

        foreach ($categorias as $categoria) {
            $id = DB::table('categorias')->insertGetId(array_merge($categoria, [
                'created_at' => now(),
                'updated_at' => now()
            ]));
            $categoriasIds[] = $id;
        }

        // Crear usuarios
        $this->command->info('👥 Creando usuarios...');
        $usuariosIds = [];
        $usuarios = [
            [
                'name' => 'Juan Carlos',
                'apellidos' => 'Rodríguez García',
                'cedula' => '12345678',
                'email' => 'admin@arepallanerita.com',
                'password' => Hash::make('password123'),
                'telefono' => '3001234567',
                'direccion' => 'Calle 10 #15-20',
                'ciudad' => 'Villavicencio',
                'departamento' => 'Meta',
                'fecha_nacimiento' => '1985-05-15',
                'rol' => 'administrador',
                'activo' => 1,
                'codigo_referido' => 'ADMIN001',
                'total_referidos' => 0,
                'comisiones_ganadas' => 0,
                'comisiones_disponibles' => 0,
                'meta_mensual' => 5000000,
                'ventas_mes_actual' => 1200000,
                'nivel_vendedor' => 5
            ],
            [
                'name' => 'María Elena',
                'apellidos' => 'Gómez Pérez',
                'cedula' => '87654321',
                'email' => 'vendedor1@arepallanerita.com',
                'password' => Hash::make('password123'),
                'telefono' => '3009876543',
                'direccion' => 'Carrera 25 #8-45',
                'ciudad' => 'Villavicencio',
                'departamento' => 'Meta',
                'fecha_nacimiento' => '1990-08-22',
                'rol' => 'vendedor',
                'activo' => 1,
                'codigo_referido' => 'VEND001',
                'total_referidos' => 3,
                'comisiones_ganadas' => 150000,
                'comisiones_disponibles' => 75000,
                'meta_mensual' => 2000000,
                'ventas_mes_actual' => 800000,
                'nivel_vendedor' => 2
            ],
            [
                'name' => 'Carlos Antonio',
                'apellidos' => 'López Martínez',
                'cedula' => '45678912',
                'email' => 'cliente1@email.com',
                'password' => Hash::make('password123'),
                'telefono' => '3012345678',
                'direccion' => 'Avenida 40 #12-30',
                'ciudad' => 'Villavicencio',
                'departamento' => 'Meta',
                'fecha_nacimiento' => '1988-12-10',
                'rol' => 'cliente',
                'activo' => 1,
                'referido_por' => null,
                'codigo_referido' => 'CLI001',
                'total_referidos' => 1,
                'comisiones_ganadas' => 25000,
                'comisiones_disponibles' => 25000
            ]
        ];

        foreach ($usuarios as $usuario) {
            $id = DB::table('users')->insertGetId(array_merge($usuario, [
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
                'ultimo_acceso' => now()
            ]));
            $usuariosIds[] = $id;
        }

        // Arreglar referencia
        DB::table('users')->where('id', $usuariosIds[2])->update(['referido_por' => $usuariosIds[1]]);

        // Crear productos
        $this->command->info('🍽️ Creando productos...');
        $productosIds = [];
        $productos = [
            [
                'nombre' => 'Arepa Tradicional con Queso',
                'descripcion' => 'Arepa de maíz blanco con queso costeño derretido',
                'categoria_id' => $categoriasIds[0],
                'precio' => 8000,
                'stock' => 50,
                'stock_minimo' => 10,
                'activo' => 1,
                'imagen' => 'arepa_queso.jpg'
            ],
            [
                'nombre' => 'Arepa Reina Pepiada',
                'descripcion' => 'Arepa rellena con pollo desmenuzado, aguacate y mayonesa',
                'categoria_id' => $categoriasIds[1],
                'precio' => 12000,
                'stock' => 30,
                'stock_minimo' => 5,
                'activo' => 1,
                'imagen' => 'arepa_reina.jpg'
            ],
            [
                'nombre' => 'Arepa Llanera Especial',
                'descripcion' => 'Arepa con carne desmechada, queso y tajadas de plátano',
                'categoria_id' => $categoriasIds[1],
                'precio' => 15000,
                'stock' => 25,
                'stock_minimo' => 8,
                'activo' => 1,
                'imagen' => 'arepa_llanera.jpg'
            ],
            [
                'nombre' => 'Chicha de Maíz',
                'descripcion' => 'Bebida tradicional llanera hecha con maíz fermentado',
                'categoria_id' => $categoriasIds[2],
                'precio' => 5000,
                'stock' => 100,
                'stock_minimo' => 20,
                'activo' => 1,
                'imagen' => 'chicha_maiz.jpg'
            ]
        ];

        foreach ($productos as $producto) {
            $id = DB::table('productos')->insertGetId(array_merge($producto, [
                'created_at' => now(),
                'updated_at' => now()
            ]));
            $productosIds[] = $id;
        }

        // Crear pedidos
        $this->command->info('📦 Creando pedidos...');
        $pedidosIds = [];
        $pedidos = [
            [
                'numero_pedido' => 'ARE-2025-0001',
                'user_id' => $usuariosIds[2],
                'vendedor_id' => $usuariosIds[1],
                'estado' => 'entregado',
                'total' => 35000,
                'descuento' => 0,
                'total_final' => 35000,
                'direccion_entrega' => 'Avenida 40 #12-30, Villavicencio',
                'telefono_entrega' => '3012345678',
                'notas' => 'Entregar después de las 6 PM',
                'fecha_entrega_estimada' => now()->addHours(2)
            ],
            [
                'numero_pedido' => 'ARE-2025-0002',
                'user_id' => $usuariosIds[0],
                'vendedor_id' => $usuariosIds[1],
                'estado' => 'confirmado',
                'total' => 23000,
                'descuento' => 2000,
                'total_final' => 21000,
                'direccion_entrega' => 'Calle 10 #15-20, Villavicencio',
                'telefono_entrega' => '3001234567',
                'notas' => 'Pedido para reunión de trabajo',
                'fecha_entrega_estimada' => now()->addHours(4)
            ]
        ];

        foreach ($pedidos as $pedido) {
            $id = DB::table('pedidos')->insertGetId(array_merge($pedido, [
                'created_at' => now()->subDays(rand(1, 30)),
                'updated_at' => now()
            ]));
            $pedidosIds[] = $id;
        }

        // Crear detalles de pedidos
        $this->command->info('📋 Creando detalles de pedidos...');
        $detalles = [
            ['pedido_id' => $pedidosIds[0], 'producto_id' => $productosIds[0], 'cantidad' => 2, 'precio_unitario' => 8000, 'subtotal' => 16000],
            ['pedido_id' => $pedidosIds[0], 'producto_id' => $productosIds[1], 'cantidad' => 1, 'precio_unitario' => 12000, 'subtotal' => 12000],
            ['pedido_id' => $pedidosIds[0], 'producto_id' => $productosIds[3], 'cantidad' => 1, 'precio_unitario' => 5000, 'subtotal' => 5000],
            ['pedido_id' => $pedidosIds[1], 'producto_id' => $productosIds[2], 'cantidad' => 1, 'precio_unitario' => 15000, 'subtotal' => 15000],
            ['pedido_id' => $pedidosIds[1], 'producto_id' => $productosIds[0], 'cantidad' => 1, 'precio_unitario' => 8000, 'subtotal' => 8000]
        ];

        foreach ($detalles as $detalle) {
            DB::table('detalle_pedidos')->insert(array_merge($detalle, [
                'created_at' => now(),
                'updated_at' => now()
            ]));
        }

        // Crear comisiones
        $this->command->info('💰 Creando comisiones...');
        $comisiones = [
            ['user_id' => $usuariosIds[1], 'pedido_id' => $pedidosIds[0], 'tipo' => 'venta_directa', 'porcentaje' => 15.00, 'monto' => 5250, 'estado' => 'aprobada'],
            ['user_id' => $usuariosIds[1], 'pedido_id' => $pedidosIds[1], 'tipo' => 'venta_directa', 'porcentaje' => 15.00, 'monto' => 3150, 'estado' => 'pendiente']
        ];

        foreach ($comisiones as $comision) {
            DB::table('comisiones')->insert(array_merge($comision, [
                'created_at' => now()->subDays(rand(1, 15)),
                'updated_at' => now()
            ]));
        }

        // Crear notificaciones
        $this->command->info('🔔 Creando notificaciones...');
        $notificaciones = [
            ['user_id' => $usuariosIds[1], 'titulo' => 'Nueva comisión generada', 'mensaje' => 'Se ha generado una comisión de $5,250 por tu venta directa', 'tipo' => 'comision', 'leida' => 0],
            ['user_id' => $usuariosIds[2], 'titulo' => 'Pedido confirmado', 'mensaje' => 'Tu pedido ARE-2025-0001 ha sido confirmado y está en preparación', 'tipo' => 'pedido', 'leida' => 1, 'fecha_lectura' => now()->subHours(2)],
            ['user_id' => $usuariosIds[0], 'titulo' => 'Bienvenido a Arepa la Llanerita', 'mensaje' => 'Gracias por unirte a nuestra plataforma de ventas', 'tipo' => 'sistema', 'leida' => 1, 'fecha_lectura' => now()->subDays(5)]
        ];

        foreach ($notificaciones as $notificacion) {
            DB::table('notificaciones')->insert(array_merge($notificacion, [
                'created_at' => now()->subDays(rand(1, 10)),
                'updated_at' => now()
            ]));
        }

        $this->command->info('✅ ¡Datos de prueba creados exitosamente!');
        $this->command->info('📊 Listos para migrar a MongoDB');
    }
}
