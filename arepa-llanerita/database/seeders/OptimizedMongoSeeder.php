<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Producto;
use App\Models\ZonaEntrega;
use App\Models\Configuracion;
use Illuminate\Support\Facades\Hash;

class OptimizedMongoSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('🚀 Iniciando seeding optimizado de MongoDB...');

        // Limpiar datos existentes
        $this->cleanDatabase();

        // Crear datos esenciales
        $this->createUsers();
        $this->createProductos();
        $this->createZonasEntrega();
        $this->createConfiguraciones();

        $this->command->info('✅ Seeding optimizado completado!');
    }

    private function cleanDatabase(): void
    {
        $this->command->info('🧹 Limpiando base de datos...');

        try {
            User::truncate();
            Producto::truncate();
            ZonaEntrega::truncate();
            Configuracion::truncate();
        } catch (\Exception $e) {
            $this->command->warn('Algunas colecciones no existían, continuando...');
        }
    }

    private function createUsers(): void
    {
        $this->command->info('👥 Creando usuarios del sistema...');

        $users = [
            [
                'name' => 'Super Admin',
                'apellidos' => 'Sistema',
                'cedula' => '12345678',
                'email' => 'admin@arepallanerita.com',
                'password' => Hash::make('admin123'),
                'telefono' => '3001234567',
                'direccion' => 'Oficina Principal',
                'ciudad' => 'Bogotá',
                'departamento' => 'Cundinamarca',
                'rol' => 'administrador',
                'activo' => true,
                'codigo_referido' => 'ADMIN001',
                'comisiones_ganadas' => 0,
                'comisiones_disponibles' => 0,
                'meta_mensual' => 0,
                'ventas_mes_actual' => 0,
                'total_referidos' => 0
            ],
            [
                'name' => 'Carlos',
                'apellidos' => 'Líder',
                'cedula' => '87654321',
                'email' => 'lider@arepallanerita.com',
                'password' => Hash::make('lider123'),
                'telefono' => '3009876543',
                'direccion' => 'Zona Norte',
                'ciudad' => 'Medellín',
                'departamento' => 'Antioquia',
                'rol' => 'lider',
                'activo' => true,
                'codigo_referido' => 'LID001',
                'comisiones_ganadas' => 0,
                'comisiones_disponibles' => 0,
                'meta_mensual' => 100000,
                'ventas_mes_actual' => 0,
                'total_referidos' => 0
            ],
            [
                'name' => 'María',
                'apellidos' => 'Vendedora',
                'cedula' => '11223344',
                'email' => 'vendedor@arepallanerita.com',
                'password' => Hash::make('vendedor123'),
                'telefono' => '3001122334',
                'direccion' => 'Centro Comercial',
                'ciudad' => 'Cali',
                'departamento' => 'Valle del Cauca',
                'rol' => 'vendedor',
                'activo' => true,
                'codigo_referido' => 'VEN001',
                'comisiones_ganadas' => 0,
                'comisiones_disponibles' => 0,
                'meta_mensual' => 50000,
                'ventas_mes_actual' => 0,
                'total_referidos' => 0
            ],
            [
                'name' => 'Ana',
                'apellidos' => 'Cliente',
                'cedula' => '99887766',
                'email' => 'cliente@example.com',
                'password' => Hash::make('cliente123'),
                'telefono' => '3009988776',
                'direccion' => 'Residencial Sur',
                'ciudad' => 'Barranquilla',
                'departamento' => 'Atlántico',
                'rol' => 'cliente',
                'activo' => true,
                'codigo_referido' => null,
                'comisiones_ganadas' => 0,
                'comisiones_disponibles' => 0,
                'meta_mensual' => 0,
                'ventas_mes_actual' => 0,
                'total_referidos' => 0
            ]
        ];

        foreach ($users as $userData) {
            User::create($userData);
        }

        $this->command->info('   ✅ 4 usuarios creados');
    }

    private function createProductos(): void
    {
        $this->command->info('🥪 Creando catálogo de productos...');

        $productos = [
            // Arepas Principales
            [
                'nombre' => 'Arepa de Chócolo',
                'descripcion' => 'Deliciosa arepa dulce de maíz tierno',
                'categoria' => 'Arepas Dulces',
                'precio' => 4000,
                'stock' => 50,
                'stock_minimo' => 10,
                'activo' => true,
                'tiempo_preparacion' => 15,
                'ingredientes' => ['Maíz tierno', 'Azúcar', 'Sal']
            ],
            [
                'nombre' => 'Arepa Reina Pepiada',
                'descripcion' => 'Arepa rellena con pollo y aguacate',
                'categoria' => 'Arepas Rellenas',
                'precio' => 9000,
                'stock' => 30,
                'stock_minimo' => 8,
                'activo' => true,
                'tiempo_preparacion' => 25,
                'ingredientes' => ['Harina maíz', 'Pollo', 'Aguacate', 'Mayonesa']
            ],
            [
                'nombre' => 'Arepa de Carne Mechada',
                'descripcion' => 'Arepa con carne desmechada jugosa',
                'categoria' => 'Arepas Rellenas',
                'precio' => 10000,
                'stock' => 25,
                'stock_minimo' => 8,
                'activo' => true,
                'tiempo_preparacion' => 30,
                'ingredientes' => ['Harina maíz', 'Carne res', 'Cebolla', 'Especias']
            ],
            [
                'nombre' => 'Arepa de Queso',
                'descripcion' => 'Arepa tradicional con queso derretido',
                'categoria' => 'Arepas Tradicionales',
                'precio' => 6000,
                'stock' => 40,
                'stock_minimo' => 12,
                'activo' => true,
                'tiempo_preparacion' => 18,
                'ingredientes' => ['Harina maíz', 'Queso blanco', 'Sal']
            ],
            // Bebidas
            [
                'nombre' => 'Jugo de Maracuyá',
                'descripcion' => 'Refrescante jugo natural',
                'categoria' => 'Bebidas',
                'precio' => 3000,
                'stock' => 60,
                'stock_minimo' => 15,
                'activo' => true,
                'tiempo_preparacion' => 5,
                'ingredientes' => ['Maracuyá', 'Agua', 'Azúcar']
            ],
            [
                'nombre' => 'Chicha de Arroz',
                'descripcion' => 'Bebida tradicional cremosa',
                'categoria' => 'Bebidas',
                'precio' => 3500,
                'stock' => 40,
                'stock_minimo' => 10,
                'activo' => true,
                'tiempo_preparacion' => 10,
                'ingredientes' => ['Arroz', 'Leche', 'Canela', 'Azúcar']
            ],
            // Adicionales
            [
                'nombre' => 'Guacamole Extra',
                'descripcion' => 'Porción adicional de guacamole',
                'categoria' => 'Adicionales',
                'precio' => 2000,
                'stock' => 20,
                'stock_minimo' => 5,
                'activo' => true,
                'tiempo_preparacion' => 5,
                'ingredientes' => ['Aguacate', 'Limón', 'Sal', 'Cebolla']
            ],
            [
                'nombre' => 'Queso Extra',
                'descripcion' => 'Porción adicional de queso',
                'categoria' => 'Adicionales',
                'precio' => 1500,
                'stock' => 30,
                'stock_minimo' => 8,
                'activo' => true,
                'tiempo_preparacion' => 2,
                'ingredientes' => ['Queso blanco']
            ]
        ];

        foreach ($productos as $producto) {
            Producto::create($producto);
        }

        $this->command->info('   ✅ 8 productos creados');
    }

    private function createZonasEntrega(): void
    {
        $this->command->info('🚚 Creando zonas de entrega...');

        $zonas = [
            [
                'nombre' => 'Bogotá Centro',
                'descripcion' => 'Zona centro y norte de Bogotá',
                'ciudad' => 'Bogotá',
                'departamento' => 'Cundinamarca',
                'precio_domicilio' => 4000,
                'tiempo_entrega_estimado' => 45,
                'activa' => true,
                'cobertura_barrios' => [
                    'Centro', 'Chapinero', 'Zona Rosa', 'La Candelaria',
                    'Santa Bárbara', 'Chicó', 'Rosales'
                ],
                'horarios_entrega' => [
                    'lunes_viernes' => ['inicio' => '08:00', 'fin' => '20:00'],
                    'sabado' => ['inicio' => '09:00', 'fin' => '18:00'],
                    'domingo' => ['inicio' => '10:00', 'fin' => '16:00']
                ]
            ],
            [
                'nombre' => 'Medellín Poblado',
                'descripcion' => 'El Poblado y zonas cercanas',
                'ciudad' => 'Medellín',
                'departamento' => 'Antioquia',
                'precio_domicilio' => 5000,
                'tiempo_entrega_estimado' => 50,
                'activa' => true,
                'cobertura_barrios' => [
                    'El Poblado', 'Laureles', 'Envigado', 'Sabaneta',
                    'Estadio', 'Prado Centro'
                ],
                'horarios_entrega' => [
                    'lunes_viernes' => ['inicio' => '09:00', 'fin' => '19:00'],
                    'sabado' => ['inicio' => '10:00', 'fin' => '17:00'],
                    'domingo' => ['activo' => false]
                ]
            ],
            [
                'nombre' => 'Cali Sur',
                'descripcion' => 'Zona sur de Cali',
                'ciudad' => 'Cali',
                'departamento' => 'Valle del Cauca',
                'precio_domicilio' => 4500,
                'tiempo_entrega_estimado' => 40,
                'activa' => true,
                'cobertura_barrios' => [
                    'Granada', 'San Fernando', 'Ciudad Jardín',
                    'Tequendama', 'El Peñón'
                ],
                'horarios_entrega' => [
                    'lunes_viernes' => ['inicio' => '08:30', 'fin' => '19:30'],
                    'sabado' => ['inicio' => '09:00', 'fin' => '17:00'],
                    'domingo' => ['inicio' => '11:00', 'fin' => '15:00']
                ]
            ]
        ];

        foreach ($zonas as $zona) {
            ZonaEntrega::create($zona);
        }

        $this->command->info('   ✅ 3 zonas de entrega creadas');
    }

    private function createConfiguraciones(): void
    {
        $this->command->info('⚙️ Creando configuraciones del sistema...');

        $configuraciones = [
            // Sistema de Comisiones MLM
            [
                'clave' => 'comision_venta_directa',
                'valor' => [15.0],
                'tipo' => 'float',
                'categoria' => 'comisiones',
                'descripcion' => 'Porcentaje de comisión por venta directa (%)',
                'activa' => true
            ],
            [
                'clave' => 'comision_referido_nivel_1',
                'valor' => [8.0],
                'tipo' => 'float',
                'categoria' => 'comisiones',
                'descripcion' => 'Porcentaje de comisión por referido nivel 1 (%)',
                'activa' => true
            ],
            [
                'clave' => 'comision_referido_nivel_2',
                'valor' => [3.0],
                'tipo' => 'float',
                'categoria' => 'comisiones',
                'descripcion' => 'Porcentaje de comisión por referido nivel 2 (%)',
                'activa' => true
            ],
            [
                'clave' => 'comision_lider_bono',
                'valor' => [5.0],
                'tipo' => 'float',
                'categoria' => 'comisiones',
                'descripcion' => 'Bono adicional para líderes (%)',
                'activa' => true
            ],

            // Información de la Empresa
            [
                'clave' => 'empresa_nombre',
                'valor' => ['Arepa la Llanerita'],
                'tipo' => 'string',
                'categoria' => 'empresa',
                'descripcion' => 'Nombre oficial de la empresa',
                'activa' => true
            ],
            [
                'clave' => 'empresa_email',
                'valor' => ['contacto@arepallanerita.com'],
                'tipo' => 'string',
                'categoria' => 'empresa',
                'descripcion' => 'Email principal de contacto',
                'activa' => true
            ],
            [
                'clave' => 'empresa_telefono',
                'valor' => ['+57 300 123 4567'],
                'tipo' => 'string',
                'categoria' => 'empresa',
                'descripcion' => 'Teléfono principal de contacto',
                'activa' => true
            ],
            [
                'clave' => 'empresa_direccion',
                'valor' => ['Calle 100 #15-20, Bogotá, Colombia'],
                'tipo' => 'string',
                'categoria' => 'empresa',
                'descripcion' => 'Dirección principal de la empresa',
                'activa' => true
            ],

            // Configuraciones de Pedidos
            [
                'clave' => 'pedido_monto_minimo',
                'valor' => [10000],
                'tipo' => 'integer',
                'categoria' => 'pedidos',
                'descripcion' => 'Monto mínimo para realizar un pedido (COP)',
                'activa' => true
            ],
            [
                'clave' => 'domicilio_gratis_desde',
                'valor' => [50000],
                'tipo' => 'integer',
                'categoria' => 'pedidos',
                'descripcion' => 'Monto desde el cual el domicilio es gratis (COP)',
                'activa' => true
            ],

            // Meta por defecto para vendedores
            [
                'clave' => 'meta_vendedor_nueva',
                'valor' => [30000],
                'tipo' => 'integer',
                'categoria' => 'ventas',
                'descripcion' => 'Meta mensual por defecto para vendedores nuevos (COP)',
                'activa' => true
            ]
        ];

        foreach ($configuraciones as $config) {
            Configuracion::create($config);
        }

        $this->command->info('   ✅ 11 configuraciones creadas');
    }
}