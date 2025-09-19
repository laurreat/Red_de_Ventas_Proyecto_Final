<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Categoria;
use App\Models\Producto;
use App\Models\ZonaEntrega;
use App\Models\Configuracion;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class MongoDBSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('🌱 Iniciando seeding de MongoDB...');

        // Limpiar datos existentes
        $this->command->info('🧹 Limpiando datos existentes...');
        User::truncate();
        Categoria::truncate();
        Producto::truncate();
        ZonaEntrega::truncate();
        Configuracion::truncate();

        // Crear usuarios del sistema
        $this->seedUsers();

        // Crear categorías
        $this->seedCategorias();

        // Crear productos
        $this->seedProductos();

        // Crear zonas de entrega
        $this->seedZonasEntrega();

        // Crear configuraciones del sistema
        $this->seedConfiguraciones();

        $this->command->info('✅ Seeding de MongoDB completado exitosamente!');
    }

    private function seedUsers(): void
    {
        $this->command->info('👥 Creando usuarios...');

        // Administrador principal
        $admin = User::create([
            'name' => 'Administrador',
            'apellidos' => 'Sistema',
            'cedula' => '12345678',
            'email' => 'admin@arepallanerita.com',
            'password' => Hash::make('admin123'),
            'telefono' => '3001234567',
            'direccion' => 'Calle Principal #123',
            'ciudad' => 'Bogotá',
            'departamento' => 'Cundinamarca',
            'fecha_nacimiento' => '1985-01-15',
            'rol' => 'administrador',
            'activo' => true,
            'ultimo_acceso' => now(),
            'codigo_referido' => 'ADMIN001',
            'total_referidos' => 0,
            'comisiones_ganadas' => 0,
            'comisiones_disponibles' => 0,
            'meta_mensual' => 0,
            'ventas_mes_actual' => 0,
            'nivel_vendedor' => 1,
            'zonas_asignadas' => [],
            'referidos_data' => [],
            'historial_ventas' => [],
            'configuracion_personal' => [
                'tema' => 'claro',
                'notificaciones_email' => true,
                'notificaciones_push' => true
            ]
        ]);

        // Líder de ventas
        $lider = User::create([
            'name' => 'Carlos',
            'apellidos' => 'Rodríguez',
            'cedula' => '87654321',
            'email' => 'carlos@arepallanerita.com',
            'password' => Hash::make('lider123'),
            'telefono' => '3009876543',
            'direccion' => 'Carrera 45 #67-89',
            'ciudad' => 'Medellín',
            'departamento' => 'Antioquia',
            'fecha_nacimiento' => '1990-03-20',
            'rol' => 'lider',
            'activo' => true,
            'ultimo_acceso' => now(),
            'codigo_referido' => 'LIDER001',
            'total_referidos' => 0,
            'comisiones_ganadas' => 0,
            'comisiones_disponibles' => 0,
            'meta_mensual' => 50000,
            'ventas_mes_actual' => 0,
            'nivel_vendedor' => 2,
            'zonas_asignadas' => [],
            'referidos_data' => [],
            'historial_ventas' => [],
            'configuracion_personal' => [
                'tema' => 'claro',
                'notificaciones_email' => true,
                'notificaciones_push' => true
            ]
        ]);

        // Vendedor ejemplo
        $vendedor = User::create([
            'name' => 'María',
            'apellidos' => 'González',
            'cedula' => '11223344',
            'email' => 'maria@arepallanerita.com',
            'password' => Hash::make('vendedor123'),
            'telefono' => '3001122334',
            'direccion' => 'Avenida 80 #123-45',
            'ciudad' => 'Cali',
            'departamento' => 'Valle del Cauca',
            'fecha_nacimiento' => '1992-07-10',
            'rol' => 'vendedor',
            'activo' => true,
            'ultimo_acceso' => now(),
            'referido_por' => $lider->_id,
            'codigo_referido' => 'VEND001',
            'total_referidos' => 0,
            'comisiones_ganadas' => 0,
            'comisiones_disponibles' => 0,
            'meta_mensual' => 25000,
            'ventas_mes_actual' => 0,
            'nivel_vendedor' => 1,
            'zonas_asignadas' => [],
            'referidos_data' => [],
            'historial_ventas' => [],
            'configuracion_personal' => [
                'tema' => 'claro',
                'notificaciones_email' => true,
                'notificaciones_push' => false
            ]
        ]);

        // Cliente ejemplo
        User::create([
            'name' => 'Ana',
            'apellidos' => 'Martínez',
            'cedula' => '55667788',
            'email' => 'ana@email.com',
            'password' => Hash::make('cliente123'),
            'telefono' => '3005566778',
            'direccion' => 'Calle 100 #50-25',
            'ciudad' => 'Barranquilla',
            'departamento' => 'Atlántico',
            'fecha_nacimiento' => '1988-12-05',
            'rol' => 'cliente',
            'activo' => true,
            'ultimo_acceso' => now(),
            'codigo_referido' => null,
            'total_referidos' => 0,
            'comisiones_ganadas' => 0,
            'comisiones_disponibles' => 0,
            'meta_mensual' => 0,
            'ventas_mes_actual' => 0,
            'nivel_vendedor' => 0,
            'zonas_asignadas' => [],
            'referidos_data' => [],
            'historial_ventas' => [],
            'configuracion_personal' => [
                'tema' => 'claro',
                'notificaciones_email' => true,
                'notificaciones_push' => true
            ]
        ]);

        $this->command->info('   ✅ 4 usuarios creados');
    }

    private function seedCategorias(): void
    {
        $this->command->info('📂 Creando categorías...');

        $categorias = [
            [
                'nombre' => 'Arepas Dulces',
                'descripcion' => 'Deliciosas arepas con sabores dulces tradicionales',
                'activo' => true,
                'imagen' => 'categorias/arepas-dulces.jpg',
                'orden' => 1,
                'productos_count' => 0,
                'configuracion' => [
                    'color_tema' => '#FF6B6B',
                    'mostrar_en_home' => true,
                    'destacada' => true
                ]
            ],
            [
                'nombre' => 'Arepas Saladas',
                'descripcion' => 'Arepas tradicionales con rellenos salados',
                'activo' => true,
                'imagen' => 'categorias/arepas-saladas.jpg',
                'orden' => 2,
                'productos_count' => 0,
                'configuracion' => [
                    'color_tema' => '#4ECDC4',
                    'mostrar_en_home' => true,
                    'destacada' => true
                ]
            ],
            [
                'nombre' => 'Bebidas',
                'descripcion' => 'Refrescantes bebidas para acompañar',
                'activo' => true,
                'imagen' => 'categorias/bebidas.jpg',
                'orden' => 3,
                'productos_count' => 0,
                'configuracion' => [
                    'color_tema' => '#45B7D1',
                    'mostrar_en_home' => true,
                    'destacada' => false
                ]
            ],
            [
                'nombre' => 'Postres',
                'descripcion' => 'Dulces postres caseros',
                'activo' => true,
                'imagen' => 'categorias/postres.jpg',
                'orden' => 4,
                'productos_count' => 0,
                'configuracion' => [
                    'color_tema' => '#F7DC6F',
                    'mostrar_en_home' => false,
                    'destacada' => false
                ]
            ]
        ];

        foreach ($categorias as $categoria) {
            Categoria::create($categoria);
        }

        $this->command->info('   ✅ 4 categorías creadas');
    }

    private function seedProductos(): void
    {
        $this->command->info('🥪 Creando productos...');

        $categorias = Categoria::all();

        $productos = [
            // Arepas Dulces
            [
                'nombre' => 'Arepa de Chócolo',
                'descripcion' => 'Deliciosa arepa dulce hecha con maíz tierno',
                'categoria_id' => $categorias->where('nombre', 'Arepas Dulces')->first()->_id,
                'precio' => 3500,
                'stock' => 50,
                'stock_minimo' => 10,
                'activo' => true,
                'imagen' => 'productos/arepa-chocolo.jpg',
                'tiempo_preparacion' => 15,
                'ingredientes' => ['Maíz tierno', 'Azúcar', 'Sal', 'Mantequilla'],
                'especificaciones' => [
                    'peso' => '150g',
                    'calorias' => 280,
                    'vegano' => false
                ]
            ],
            [
                'nombre' => 'Arepa de Queso',
                'descripcion' => 'Arepa dulce rellena con queso fresco',
                'categoria_id' => $categorias->where('nombre', 'Arepas Dulces')->first()->_id,
                'precio' => 4500,
                'stock' => 30,
                'stock_minimo' => 8,
                'activo' => true,
                'imagen' => 'productos/arepa-queso-dulce.jpg',
                'tiempo_preparacion' => 18,
                'ingredientes' => ['Maíz', 'Queso fresco', 'Azúcar', 'Mantequilla'],
                'especificaciones' => [
                    'peso' => '180g',
                    'calorias' => 320,
                    'vegano' => false
                ]
            ],
            // Arepas Saladas
            [
                'nombre' => 'Arepa Reina Pepiada',
                'descripcion' => 'Arepa rellena con pollo y aguacate',
                'categoria_id' => $categorias->where('nombre', 'Arepas Saladas')->first()->_id,
                'precio' => 8500,
                'stock' => 40,
                'stock_minimo' => 12,
                'activo' => true,
                'imagen' => 'productos/arepa-reina-pepiada.jpg',
                'tiempo_preparacion' => 25,
                'ingredientes' => ['Harina de maíz', 'Pollo', 'Aguacate', 'Mayonesa', 'Especias'],
                'especificaciones' => [
                    'peso' => '250g',
                    'calorias' => 420,
                    'vegano' => false
                ]
            ],
            [
                'nombre' => 'Arepa de Carne Mechada',
                'descripcion' => 'Arepa con jugosa carne mechada',
                'categoria_id' => $categorias->where('nombre', 'Arepas Saladas')->first()->_id,
                'precio' => 9500,
                'stock' => 25,
                'stock_minimo' => 8,
                'activo' => true,
                'imagen' => 'productos/arepa-carne-mechada.jpg',
                'tiempo_preparacion' => 30,
                'ingredientes' => ['Harina de maíz', 'Carne de res', 'Cebolla', 'Tomate', 'Especias'],
                'especificaciones' => [
                    'peso' => '280g',
                    'calorias' => 480,
                    'vegano' => false
                ]
            ],
            // Bebidas
            [
                'nombre' => 'Jugo de Maracuyá',
                'descripcion' => 'Refrescante jugo natural de maracuyá',
                'categoria_id' => $categorias->where('nombre', 'Bebidas')->first()->_id,
                'precio' => 2500,
                'stock' => 60,
                'stock_minimo' => 15,
                'activo' => true,
                'imagen' => 'productos/jugo-maracuya.jpg',
                'tiempo_preparacion' => 5,
                'ingredientes' => ['Maracuyá', 'Agua', 'Azúcar'],
                'especificaciones' => [
                    'volumen' => '350ml',
                    'calorias' => 120,
                    'vegano' => true
                ]
            ],
            [
                'nombre' => 'Chicha de Arroz',
                'descripcion' => 'Bebida tradicional de arroz con canela',
                'categoria_id' => $categorias->where('nombre', 'Bebidas')->first()->_id,
                'precio' => 3000,
                'stock' => 40,
                'stock_minimo' => 10,
                'activo' => true,
                'imagen' => 'productos/chicha-arroz.jpg',
                'tiempo_preparacion' => 10,
                'ingredientes' => ['Arroz', 'Leche', 'Canela', 'Azúcar', 'Vainilla'],
                'especificaciones' => [
                    'volumen' => '400ml',
                    'calorias' => 180,
                    'vegano' => false
                ]
            ]
        ];

        foreach ($productos as $producto) {
            $categoria = Categoria::find($producto['categoria_id']);
            $producto['categoria_data'] = [
                '_id' => $categoria->_id,
                'nombre' => $categoria->nombre,
                'descripcion' => $categoria->descripcion
            ];
            Producto::create($producto);
        }

        $this->command->info('   ✅ 6 productos creados');
    }

    private function seedZonasEntrega(): void
    {
        $this->command->info('🚚 Creando zonas de entrega...');

        $zonas = [
            [
                'nombre' => 'Centro Bogotá',
                'descripcion' => 'Zona centro de Bogotá',
                'ciudad' => 'Bogotá',
                'departamento' => 'Cundinamarca',
                'codigo_postal' => '110111',
                'coordenadas' => [4.6097, -74.0817],
                'precio_domicilio' => 3000,
                'tiempo_entrega_estimado' => 45,
                'activa' => true,
                'cobertura_barrios' => ['La Candelaria', 'Centro', 'Santa Bárbara'],
                'horarios_entrega' => [
                    'lunes' => ['inicio' => '08:00', 'fin' => '18:00'],
                    'martes' => ['inicio' => '08:00', 'fin' => '18:00'],
                    'miercoles' => ['inicio' => '08:00', 'fin' => '18:00'],
                    'jueves' => ['inicio' => '08:00', 'fin' => '18:00'],
                    'viernes' => ['inicio' => '08:00', 'fin' => '18:00'],
                    'sabado' => ['inicio' => '09:00', 'fin' => '16:00'],
                    'domingo' => ['inicio' => '10:00', 'fin' => '15:00']
                ]
            ],
            [
                'nombre' => 'Norte Medellín',
                'descripcion' => 'Zona norte de Medellín',
                'ciudad' => 'Medellín',
                'departamento' => 'Antioquia',
                'codigo_postal' => '050001',
                'coordenadas' => [6.2442, -75.5812],
                'precio_domicilio' => 4000,
                'tiempo_entrega_estimado' => 50,
                'activa' => true,
                'cobertura_barrios' => ['El Poblado', 'Laureles', 'Estadio'],
                'horarios_entrega' => [
                    'lunes' => ['inicio' => '09:00', 'fin' => '17:00'],
                    'martes' => ['inicio' => '09:00', 'fin' => '17:00'],
                    'miercoles' => ['inicio' => '09:00', 'fin' => '17:00'],
                    'jueves' => ['inicio' => '09:00', 'fin' => '17:00'],
                    'viernes' => ['inicio' => '09:00', 'fin' => '17:00'],
                    'sabado' => ['inicio' => '10:00', 'fin' => '15:00'],
                    'domingo' => ['activo' => false]
                ]
            ]
        ];

        foreach ($zonas as $zona) {
            ZonaEntrega::create($zona);
        }

        $this->command->info('   ✅ 2 zonas de entrega creadas');
    }

    private function seedConfiguraciones(): void
    {
        $this->command->info('⚙️ Creando configuraciones del sistema...');

        $configuraciones = [
            [
                'clave' => 'comision_venta_directa',
                'valor' => [15],
                'tipo' => 'float',
                'categoria' => 'comisiones',
                'descripcion' => 'Porcentaje de comisión por venta directa',
                'es_publica' => false,
                'es_editable' => true
            ],
            [
                'clave' => 'comision_referido_nivel_1',
                'valor' => [8],
                'tipo' => 'float',
                'categoria' => 'comisiones',
                'descripcion' => 'Porcentaje de comisión por referido nivel 1',
                'es_publica' => false,
                'es_editable' => true
            ],
            [
                'clave' => 'comision_referido_nivel_2',
                'valor' => [3],
                'tipo' => 'float',
                'categoria' => 'comisiones',
                'descripcion' => 'Porcentaje de comisión por referido nivel 2',
                'es_publica' => false,
                'es_editable' => true
            ],
            [
                'clave' => 'nombre_empresa',
                'valor' => ['Arepa la Llanerita'],
                'tipo' => 'string',
                'categoria' => 'general',
                'descripcion' => 'Nombre de la empresa',
                'es_publica' => true,
                'es_editable' => true
            ],
            [
                'clave' => 'email_contacto',
                'valor' => ['contacto@arepallanerita.com'],
                'tipo' => 'string',
                'categoria' => 'general',
                'descripcion' => 'Email de contacto principal',
                'es_publica' => true,
                'es_editable' => true
            ],
            [
                'clave' => 'telefono_contacto',
                'valor' => ['+57 300 123 4567'],
                'tipo' => 'string',
                'categoria' => 'general',
                'descripcion' => 'Teléfono de contacto principal',
                'es_publica' => true,
                'es_editable' => true
            ]
        ];

        foreach ($configuraciones as $config) {
            Configuracion::create($config);
        }

        $this->command->info('   ✅ 6 configuraciones creadas');
    }
}