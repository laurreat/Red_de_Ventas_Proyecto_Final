<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Categoria;
use App\Models\Producto;
use App\Models\ZonaEntrega;
use App\Models\Configuracion;
use App\Models\Cupon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class CompleteArepasSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('🚀 Iniciando seeding completo basado en BdArepas.sql...');

        $this->cleanDatabase();

        // Crear en orden de dependencias
        $usuarios = $this->createUsers();
        $categorias = $this->createCategorias();
        $productos = $this->createProductos($categorias);
        $zonas = $this->createZonasEntrega();
        $this->createConfiguraciones();
        $this->createCupones();

        $this->command->info('✅ Seeding completo terminado exitosamente!');
        $this->showSummary();
    }

    private function cleanDatabase(): void
    {
        $this->command->info('🧹 Limpiando base de datos...');

        $models = [
            User::class, Categoria::class, Producto::class,
            ZonaEntrega::class, Configuracion::class, Cupon::class
        ];

        foreach ($models as $model) {
            try {
                $model::truncate();
            } catch (\Exception $e) {
                // Colección no existe, continuar
            }
        }
    }

    private function createUsers(): array
    {
        $this->command->info('👥 Creando usuarios del sistema...');

        $users = [];

        // ADMINISTRADOR PRINCIPAL
        $users['admin'] = User::create([
            'name' => 'Super Administrador',
            'apellidos' => 'Sistema',
            'cedula' => '12345678',
            'email' => 'admin@arepallanerita.com',
            'password' => Hash::make('admin123'),
            'telefono' => '+57 300 123 4567',
            'direccion' => 'Oficina Central Calle 100 #15-20',
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
            'nivel_vendedor' => 10,
            'zonas_asignadas' => ['todas']
        ]);

        // LÍDER REGIONAL BOGOTÁ
        $users['lider_bogota'] = User::create([
            'name' => 'Carlos Alberto',
            'apellidos' => 'Rodríguez López',
            'cedula' => '87654321',
            'email' => 'carlos.rodriguez@arepallanerita.com',
            'password' => Hash::make('lider123'),
            'telefono' => '+57 310 987 6543',
            'direccion' => 'Carrera 15 #93-47, Apto 502',
            'ciudad' => 'Bogotá',
            'departamento' => 'Cundinamarca',
            'fecha_nacimiento' => '1988-03-22',
            'rol' => 'lider',
            'activo' => true,
            'ultimo_acceso' => now(),
            'codigo_referido' => 'LID_BOG_001',
            'total_referidos' => 0,
            'comisiones_ganadas' => 0,
            'comisiones_disponibles' => 0,
            'meta_mensual' => 150000,
            'ventas_mes_actual' => 0,
            'nivel_vendedor' => 3,
            'zonas_asignadas' => ['bogota_norte', 'bogota_centro']
        ]);

        // LÍDER REGIONAL MEDELLÍN
        $users['lider_medellin'] = User::create([
            'name' => 'Ana María',
            'apellidos' => 'Gómez Ruiz',
            'cedula' => '11223344',
            'email' => 'ana.gomez@arepallanerita.com',
            'password' => Hash::make('lider123'),
            'telefono' => '+57 315 234 5678',
            'direccion' => 'Calle 10 Sur #43E-15',
            'ciudad' => 'Medellín',
            'departamento' => 'Antioquia',
            'fecha_nacimiento' => '1990-07-10',
            'rol' => 'lider',
            'activo' => true,
            'ultimo_acceso' => now(),
            'codigo_referido' => 'LID_MED_001',
            'total_referidos' => 0,
            'comisiones_ganadas' => 0,
            'comisiones_disponibles' => 0,
            'meta_mensual' => 120000,
            'ventas_mes_actual' => 0,
            'nivel_vendedor' => 3,
            'zonas_asignadas' => ['medellin_poblado', 'medellin_laureles']
        ]);

        // VENDEDORES
        $vendedores = [
            [
                'name' => 'María José',
                'apellidos' => 'Martínez Sánchez',
                'cedula' => '33445566',
                'email' => 'maria.martinez@arepallanerita.com',
                'ciudad' => 'Bogotá',
                'departamento' => 'Cundinamarca',
                'referido_por' => $users['lider_bogota']->_id,
                'codigo_referido' => 'VEN_BOG_001',
                'meta_mensual' => 80000,
                'zonas_asignadas' => ['bogota_norte']
            ],
            [
                'name' => 'Luis Fernando',
                'apellidos' => 'García Pérez',
                'cedula' => '55667788',
                'email' => 'luis.garcia@arepallanerita.com',
                'ciudad' => 'Bogotá',
                'departamento' => 'Cundinamarca',
                'referido_por' => $users['lider_bogota']->_id,
                'codigo_referido' => 'VEN_BOG_002',
                'meta_mensual' => 75000,
                'zonas_asignadas' => ['bogota_centro']
            ],
            [
                'name' => 'Sandra Patricia',
                'apellidos' => 'López Herrera',
                'cedula' => '77889900',
                'email' => 'sandra.lopez@arepallanerita.com',
                'ciudad' => 'Medellín',
                'departamento' => 'Antioquia',
                'referido_por' => $users['lider_medellin']->_id,
                'codigo_referido' => 'VEN_MED_001',
                'meta_mensual' => 70000,
                'zonas_asignadas' => ['medellin_poblado']
            ],
            [
                'name' => 'Roberto Carlos',
                'apellidos' => 'Jiménez Torres',
                'cedula' => '99001122',
                'email' => 'roberto.jimenez@arepallanerita.com',
                'ciudad' => 'Cali',
                'departamento' => 'Valle del Cauca',
                'referido_por' => $users['lider_medellin']->_id,
                'codigo_referido' => 'VEN_CAL_001',
                'meta_mensual' => 65000,
                'zonas_asignadas' => ['cali_sur']
            ]
        ];

        foreach ($vendedores as $vendedorData) {
            $users[] = User::create(array_merge([
                'password' => Hash::make('vendedor123'),
                'telefono' => '+57 300 ' . rand(100, 999) . ' ' . rand(1000, 9999),
                'direccion' => 'Dirección comercial',
                'fecha_nacimiento' => now()->subYears(rand(25, 40))->format('Y-m-d'),
                'rol' => 'vendedor',
                'activo' => true,
                'ultimo_acceso' => now(),
                'total_referidos' => 0,
                'comisiones_ganadas' => 0,
                'comisiones_disponibles' => 0,
                'ventas_mes_actual' => 0,
                'nivel_vendedor' => 1
            ], $vendedorData));
        }

        // CLIENTES
        $clientes = [
            [
                'name' => 'Andrea',
                'apellidos' => 'Ramírez',
                'cedula' => '22334455',
                'email' => 'andrea.ramirez@gmail.com',
                'ciudad' => 'Bogotá',
                'departamento' => 'Cundinamarca'
            ],
            [
                'name' => 'Jorge',
                'apellidos' => 'Mendoza',
                'cedula' => '44556677',
                'email' => 'jorge.mendoza@hotmail.com',
                'ciudad' => 'Medellín',
                'departamento' => 'Antioquia'
            ],
            [
                'name' => 'Patricia',
                'apellidos' => 'Vargas',
                'cedula' => '66778899',
                'email' => 'patricia.vargas@yahoo.com',
                'ciudad' => 'Cali',
                'departamento' => 'Valle del Cauca'
            ]
        ];

        foreach ($clientes as $clienteData) {
            $users[] = User::create(array_merge([
                'password' => Hash::make('cliente123'),
                'telefono' => '+57 300 ' . rand(100, 999) . ' ' . rand(1000, 9999),
                'direccion' => 'Dirección residencial',
                'fecha_nacimiento' => now()->subYears(rand(20, 50))->format('Y-m-d'),
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
                'zonas_asignadas' => []
            ], $clienteData));
        }

        $this->command->info('   ✅ ' . count($users) . ' usuarios creados');
        return $users;
    }

    private function createCategorias(): array
    {
        $this->command->info('📂 Creando categorías de productos...');

        $categorias = [
            [
                'nombre' => 'Arepas Dulces',
                'descripcion' => 'Deliciosas arepas tradicionales con sabores dulces del llano',
                'imagen' => 'categorias/arepas-dulces.jpg',
                'activo' => true
            ],
            [
                'nombre' => 'Arepas Rellenas',
                'descripcion' => 'Arepas con rellenos variados de carnes, pollo y vegetales',
                'imagen' => 'categorias/arepas-rellenas.jpg',
                'activo' => true
            ],
            [
                'nombre' => 'Arepas Tradicionales',
                'descripcion' => 'Arepas clásicas con queso, mantequilla y acompañamientos básicos',
                'imagen' => 'categorias/arepas-tradicionales.jpg',
                'activo' => true
            ],
            [
                'nombre' => 'Bebidas Naturales',
                'descripcion' => 'Jugos naturales, chichas y bebidas típicas de la región',
                'imagen' => 'categorias/bebidas.jpg',
                'activo' => true
            ],
            [
                'nombre' => 'Adicionales',
                'descripcion' => 'Acompañamientos extra, salsas y porciones adicionales',
                'imagen' => 'categorias/adicionales.jpg',
                'activo' => true
            ],
            [
                'nombre' => 'Postres Caseros',
                'descripcion' => 'Postres tradicionales y dulces caseros',
                'imagen' => 'categorias/postres.jpg',
                'activo' => true
            ]
        ];

        $categoriasCreadas = [];
        foreach ($categorias as $categoriaData) {
            $categoriasCreadas[] = Categoria::create($categoriaData);
        }

        $this->command->info('   ✅ ' . count($categoriasCreadas) . ' categorías creadas');
        return $categoriasCreadas;
    }

    private function createProductos($categorias): array
    {
        $this->command->info('🥪 Creando catálogo completo de productos...');

        $productos = [];

        // AREPAS DULCES
        $categoriaArepasDulces = collect($categorias)->firstWhere('nombre', 'Arepas Dulces');
        $productos = array_merge($productos, [
            [
                'codigo' => 'AD001',
                'nombre' => 'Arepa de Chócolo',
                'descripcion' => 'Deliciosa arepa dulce hecha con maíz tierno y azúcar natural',
                'categoria_id' => $categoriaArepasDulces->_id,
                'precio' => 4500,
                'precio_costo' => 2200,
                'precio_mayorista' => 3800,
                'stock' => 50,
                'stock_minimo' => 10,
                'stock_maximo' => 100,
                'unidad_medida' => 'unidad',
                'ingredientes' => ['Maíz tierno', 'Azúcar', 'Sal', 'Mantequilla'],
                'alergenos' => ['Lácteos'],
                'tiempo_preparacion' => 15,
                'peso' => 180,
                'calorias' => 320,
                'imagen_principal' => 'productos/arepa-chocolo.jpg',
                'imagenes' => ['productos/arepa-chocolo-1.jpg', 'productos/arepa-chocolo-2.jpg'],
                'disponible' => true,
                'destacado' => true,
                'requiere_preparacion' => true,
                'permite_personalizacion' => false,
                'slug' => 'arepa-de-chocolo',
                'tags' => ['dulce', 'tradicional', 'maíz tierno'],
                'veces_vendido' => 0,
                'rating_promedio' => 0,
                'total_reviews' => 0
            ],
            [
                'codigo' => 'AD002',
                'nombre' => 'Arepa de Queso Dulce',
                'descripcion' => 'Arepa dulce rellena con queso fresco y un toque de panela',
                'categoria_id' => $categoriaArepasDulces->_id,
                'precio' => 5500,
                'precio_costo' => 2800,
                'precio_mayorista' => 4500,
                'stock' => 40,
                'stock_minimo' => 8,
                'stock_maximo' => 80,
                'unidad_medida' => 'unidad',
                'ingredientes' => ['Maíz', 'Queso fresco', 'Panela', 'Mantequilla'],
                'alergenos' => ['Lácteos'],
                'tiempo_preparacion' => 18,
                'peso' => 200,
                'calorias' => 380,
                'imagen_principal' => 'productos/arepa-queso-dulce.jpg',
                'disponible' => true,
                'destacado' => true,
                'requiere_preparacion' => true,
                'permite_personalizacion' => false,
                'slug' => 'arepa-de-queso-dulce',
                'tags' => ['dulce', 'queso', 'panela'],
                'veces_vendido' => 0,
                'rating_promedio' => 0,
                'total_reviews' => 0
            ]
        ]);

        // AREPAS RELLENAS
        $categoriaArepasRellenas = collect($categorias)->firstWhere('nombre', 'Arepas Rellenas');
        $productos = array_merge($productos, [
            [
                'codigo' => 'AR001',
                'nombre' => 'Arepa Reina Pepiada',
                'descripcion' => 'Arepa rellena con pollo desmenuzado, aguacate y mayonesa',
                'categoria_id' => $categoriaArepasRellenas->_id,
                'precio' => 12000,
                'precio_costo' => 6500,
                'precio_mayorista' => 10000,
                'stock' => 30,
                'stock_minimo' => 8,
                'stock_maximo' => 60,
                'unidad_medida' => 'unidad',
                'ingredientes' => ['Harina de maíz', 'Pollo', 'Aguacate', 'Mayonesa', 'Especias'],
                'alergenos' => ['Huevos'],
                'tiempo_preparacion' => 25,
                'peso' => 280,
                'calorias' => 450,
                'imagen_principal' => 'productos/arepa-reina-pepiada.jpg',
                'disponible' => true,
                'destacado' => true,
                'requiere_preparacion' => true,
                'permite_personalizacion' => true,
                'slug' => 'arepa-reina-pepiada',
                'tags' => ['pollo', 'aguacate', 'popular'],
                'veces_vendido' => 0,
                'rating_promedio' => 0,
                'total_reviews' => 0
            ],
            [
                'codigo' => 'AR002',
                'nombre' => 'Arepa de Carne Mechada',
                'descripcion' => 'Arepa con jugosa carne de res desmechada y sofrito de verduras',
                'categoria_id' => $categoriaArepasRellenas->_id,
                'precio' => 14000,
                'precio_costo' => 7500,
                'precio_mayorista' => 11500,
                'stock' => 25,
                'stock_minimo' => 6,
                'stock_maximo' => 50,
                'unidad_medida' => 'unidad',
                'ingredientes' => ['Harina de maíz', 'Carne de res', 'Cebolla', 'Tomate', 'Pimentón', 'Especias'],
                'alergenos' => [],
                'tiempo_preparacion' => 30,
                'peso' => 300,
                'calorias' => 520,
                'imagen_principal' => 'productos/arepa-carne-mechada.jpg',
                'disponible' => true,
                'destacado' => true,
                'requiere_preparacion' => true,
                'permite_personalizacion' => true,
                'slug' => 'arepa-carne-mechada',
                'tags' => ['carne', 'tradicional', 'sustanciosa'],
                'veces_vendido' => 0,
                'rating_promedio' => 0,
                'total_reviews' => 0
            ]
        ]);

        // AREPAS TRADICIONALES
        $categoriaArepasTradicionales = collect($categorias)->firstWhere('nombre', 'Arepas Tradicionales');
        $productos = array_merge($productos, [
            [
                'codigo' => 'AT001',
                'nombre' => 'Arepa con Queso',
                'descripcion' => 'Arepa tradicional acompañada con queso fresco',
                'categoria_id' => $categoriaArepasTradicionales->_id,
                'precio' => 7000,
                'precio_costo' => 3500,
                'precio_mayorista' => 5800,
                'stock' => 60,
                'stock_minimo' => 15,
                'stock_maximo' => 120,
                'unidad_medida' => 'unidad',
                'ingredientes' => ['Harina de maíz', 'Queso blanco', 'Sal'],
                'alergenos' => ['Lácteos'],
                'tiempo_preparacion' => 15,
                'peso' => 220,
                'calorias' => 350,
                'imagen_principal' => 'productos/arepa-con-queso.jpg',
                'disponible' => true,
                'destacado' => false,
                'requiere_preparacion' => true,
                'permite_personalizacion' => false,
                'slug' => 'arepa-con-queso',
                'tags' => ['queso', 'clásica', 'sencilla'],
                'veces_vendido' => 0,
                'rating_promedio' => 0,
                'total_reviews' => 0
            ]
        ]);

        // BEBIDAS
        $categoriaBebidas = collect($categorias)->firstWhere('nombre', 'Bebidas Naturales');
        $productos = array_merge($productos, [
            [
                'codigo' => 'BE001',
                'nombre' => 'Jugo de Maracuyá',
                'descripcion' => 'Refrescante jugo natural de maracuyá endulzado con panela',
                'categoria_id' => $categoriaBebidas->_id,
                'precio' => 4000,
                'precio_costo' => 1800,
                'precio_mayorista' => 3200,
                'stock' => 80,
                'stock_minimo' => 20,
                'stock_maximo' => 150,
                'unidad_medida' => 'vaso 350ml',
                'ingredientes' => ['Maracuyá', 'Agua', 'Panela'],
                'alergenos' => [],
                'tiempo_preparacion' => 5,
                'peso' => 350,
                'calorias' => 120,
                'imagen_principal' => 'productos/jugo-maracuya.jpg',
                'disponible' => true,
                'destacado' => false,
                'requiere_preparacion' => true,
                'permite_personalizacion' => true,
                'slug' => 'jugo-de-maracuya',
                'tags' => ['natural', 'refrescante', 'tropical'],
                'veces_vendido' => 0,
                'rating_promedio' => 0,
                'total_reviews' => 0
            ],
            [
                'codigo' => 'BE002',
                'nombre' => 'Chicha de Arroz',
                'descripcion' => 'Bebida tradicional cremosa de arroz con canela y vainilla',
                'categoria_id' => $categoriaBebidas->_id,
                'precio' => 4500,
                'precio_costo' => 2000,
                'precio_mayorista' => 3600,
                'stock' => 50,
                'stock_minimo' => 12,
                'stock_maximo' => 100,
                'unidad_medida' => 'vaso 400ml',
                'ingredientes' => ['Arroz', 'Leche', 'Canela', 'Azúcar', 'Vainilla'],
                'alergenos' => ['Lácteos'],
                'tiempo_preparacion' => 10,
                'peso' => 400,
                'calorias' => 200,
                'imagen_principal' => 'productos/chicha-arroz.jpg',
                'disponible' => true,
                'destacado' => false,
                'requiere_preparacion' => true,
                'permite_personalizacion' => false,
                'slug' => 'chicha-de-arroz',
                'tags' => ['tradicional', 'cremosa', 'casera'],
                'veces_vendido' => 0,
                'rating_promedio' => 0,
                'total_reviews' => 0
            ]
        ]);

        // ADICIONALES
        $categoriaAdicionales = collect($categorias)->firstWhere('nombre', 'Adicionales');
        $productos = array_merge($productos, [
            [
                'codigo' => 'AD001',
                'nombre' => 'Porción de Queso Extra',
                'descripcion' => 'Porción adicional de queso fresco',
                'categoria_id' => $categoriaAdicionales->_id,
                'precio' => 2500,
                'precio_costo' => 1200,
                'precio_mayorista' => 2000,
                'stock' => 40,
                'stock_minimo' => 10,
                'stock_maximo' => 80,
                'unidad_medida' => 'porción',
                'ingredientes' => ['Queso blanco'],
                'alergenos' => ['Lácteos'],
                'tiempo_preparacion' => 2,
                'peso' => 50,
                'calorias' => 180,
                'imagen_principal' => 'productos/queso-extra.jpg',
                'disponible' => true,
                'destacado' => false,
                'requiere_preparacion' => false,
                'permite_personalizacion' => false,
                'slug' => 'porcion-queso-extra',
                'tags' => ['adicional', 'queso'],
                'veces_vendido' => 0,
                'rating_promedio' => 0,
                'total_reviews' => 0
            ]
        ]);

        // Crear productos con datos embebidos de categoría
        $productosCreados = [];
        foreach ($productos as $productoData) {
            $categoria = collect($categorias)->firstWhere('_id', $productoData['categoria_id']);
            $productoData['categoria_data'] = [
                '_id' => $categoria->_id,
                'nombre' => $categoria->nombre,
                'descripcion' => $categoria->descripcion
            ];
            $productosCreados[] = Producto::create($productoData);
        }

        $this->command->info('   ✅ ' . count($productosCreados) . ' productos creados');
        return $productosCreados;
    }

    private function createZonasEntrega(): array
    {
        $this->command->info('🚚 Creando zonas de entrega...');

        $zonas = [
            [
                'nombre' => 'Bogotá Norte',
                'descripcion' => 'Zona norte de Bogotá incluyendo Chapinero, Zona Rosa y alrededores',
                'costo_envio' => 5000,
                'tiempo_entrega_min' => 35,
                'tiempo_entrega_max' => 55,
                'pedido_minimo' => 15000,
                'barrios' => [
                    'Chapinero', 'Zona Rosa', 'Chicó', 'El Nogal', 'Rosales',
                    'La Cabrera', 'El Retiro', 'Quinta Camacho'
                ],
                'coordenadas' => [
                    'tipo' => 'Polygon',
                    'coordinates' => [[
                        [-74.0659, 4.6378], [-74.0425, 4.6378],
                        [-74.0425, 4.6798], [-74.0659, 4.6798],
                        [-74.0659, 4.6378]
                    ]]
                ],
                'referencias' => 'Zona de estratos 4, 5 y 6 al norte de la calle 63',
                'horarios' => [
                    'lunes' => ['apertura' => '08:00', 'cierre' => '20:00'],
                    'martes' => ['apertura' => '08:00', 'cierre' => '20:00'],
                    'miercoles' => ['apertura' => '08:00', 'cierre' => '20:00'],
                    'jueves' => ['apertura' => '08:00', 'cierre' => '20:00'],
                    'viernes' => ['apertura' => '08:00', 'cierre' => '21:00'],
                    'sabado' => ['apertura' => '09:00', 'cierre' => '21:00'],
                    'domingo' => ['apertura' => '10:00', 'cierre' => '18:00']
                ],
                'activo' => true
            ],
            [
                'nombre' => 'Bogotá Centro',
                'descripcion' => 'Centro histórico y zona comercial de Bogotá',
                'costo_envio' => 4000,
                'tiempo_entrega_min' => 25,
                'tiempo_entrega_max' => 45,
                'pedido_minimo' => 12000,
                'barrios' => [
                    'La Candelaria', 'Centro', 'Santa Bárbara', 'Macarena',
                    'Las Nieves', 'Egipto', 'San Diego'
                ],
                'coordenadas' => [
                    'tipo' => 'Polygon',
                    'coordinates' => [[
                        [-74.0859, 4.5878], [-74.0625, 4.5878],
                        [-74.0625, 4.6298], [-74.0859, 4.6298],
                        [-74.0859, 4.5878]
                    ]]
                ],
                'referencias' => 'Centro histórico y comercial de Bogotá',
                'horarios' => [
                    'lunes' => ['apertura' => '08:30', 'cierre' => '19:00'],
                    'martes' => ['apertura' => '08:30', 'cierre' => '19:00'],
                    'miercoles' => ['apertura' => '08:30', 'cierre' => '19:00'],
                    'jueves' => ['apertura' => '08:30', 'cierre' => '19:00'],
                    'viernes' => ['apertura' => '08:30', 'cierre' => '20:00'],
                    'sabado' => ['apertura' => '09:00', 'cierre' => '18:00'],
                    'domingo' => ['apertura' => '11:00', 'cierre' => '16:00']
                ],
                'activo' => true
            ],
            [
                'nombre' => 'Medellín Poblado',
                'descripcion' => 'El Poblado y zonas aledañas en Medellín',
                'costo_envio' => 6000,
                'tiempo_entrega_min' => 40,
                'tiempo_entrega_max' => 60,
                'pedido_minimo' => 18000,
                'barrios' => [
                    'El Poblado', 'Envigado', 'Sabaneta', 'Manila',
                    'Los Balsos', 'Las Lomas', 'Provenza'
                ],
                'coordenadas' => [
                    'tipo' => 'Polygon',
                    'coordinates' => [[
                        [-75.5912, 6.1942], [-75.5612, 6.1942],
                        [-75.5612, 6.2542], [-75.5912, 6.2542],
                        [-75.5912, 6.1942]
                    ]]
                ],
                'referencias' => 'Zona exclusiva del sur de Medellín',
                'horarios' => [
                    'lunes' => ['apertura' => '09:00', 'cierre' => '19:00'],
                    'martes' => ['apertura' => '09:00', 'cierre' => '19:00'],
                    'miercoles' => ['apertura' => '09:00', 'cierre' => '19:00'],
                    'jueves' => ['apertura' => '09:00', 'cierre' => '19:00'],
                    'viernes' => ['apertura' => '09:00', 'cierre' => '20:00'],
                    'sabado' => ['apertura' => '10:00', 'cierre' => '18:00'],
                    'domingo' => ['activo' => false]
                ],
                'activo' => true
            ]
        ];

        $zonasCreadas = [];
        foreach ($zonas as $zonaData) {
            $zonasCreadas[] = ZonaEntrega::create($zonaData);
        }

        $this->command->info('   ✅ ' . count($zonasCreadas) . ' zonas de entrega creadas');
        return $zonasCreadas;
    }

    private function createConfiguraciones(): void
    {
        $this->command->info('⚙️ Creando configuraciones del sistema...');

        $configuraciones = [
            // COMISIONES MLM
            [
                'clave' => 'comision_venta_directa',
                'valor' => '15.00',
                'tipo' => 'decimal',
                'descripcion' => 'Porcentaje de comisión por venta directa (%)',
                'categoria' => 'comisiones',
                'editable' => true
            ],
            [
                'clave' => 'comision_referido_nuevo',
                'valor' => '20.00',
                'tipo' => 'decimal',
                'descripcion' => 'Comisión fija por nuevo referido registrado (COP)',
                'categoria' => 'comisiones',
                'editable' => true
            ],
            [
                'clave' => 'comision_referido_compra',
                'valor' => '8.00',
                'tipo' => 'decimal',
                'descripcion' => 'Porcentaje de comisión por compra de referido (%)',
                'categoria' => 'comisiones',
                'editable' => true
            ],
            [
                'clave' => 'comision_liderazgo',
                'valor' => '5.00',
                'tipo' => 'decimal',
                'descripcion' => 'Porcentaje adicional por bono de liderazgo (%)',
                'categoria' => 'comisiones',
                'editable' => true
            ],

            // INFORMACIÓN EMPRESA
            [
                'clave' => 'empresa_nombre',
                'valor' => 'Arepa la Llanerita',
                'tipo' => 'string',
                'descripcion' => 'Nombre oficial de la empresa',
                'categoria' => 'empresa',
                'editable' => true
            ],
            [
                'clave' => 'empresa_email',
                'valor' => 'contacto@arepallanerita.com',
                'tipo' => 'email',
                'descripcion' => 'Email principal de contacto',
                'categoria' => 'empresa',
                'editable' => true
            ],
            [
                'clave' => 'empresa_telefono',
                'valor' => '+57 300 123 4567',
                'tipo' => 'string',
                'descripcion' => 'Teléfono principal de contacto',
                'categoria' => 'empresa',
                'editable' => true
            ],
            [
                'clave' => 'empresa_direccion',
                'valor' => 'Calle 100 #15-20, Bogotá, Colombia',
                'tipo' => 'string',
                'descripcion' => 'Dirección principal de la empresa',
                'categoria' => 'empresa',
                'editable' => true
            ],

            // CONFIGURACIÓN PEDIDOS
            [
                'clave' => 'pedido_minimo_general',
                'valor' => '10000',
                'tipo' => 'integer',
                'descripcion' => 'Monto mínimo para realizar pedidos (COP)',
                'categoria' => 'pedidos',
                'editable' => true
            ],
            [
                'clave' => 'domicilio_gratis_desde',
                'valor' => '50000',
                'tipo' => 'integer',
                'descripcion' => 'Monto desde el cual el domicilio es gratis (COP)',
                'categoria' => 'pedidos',
                'editable' => true
            ],
            [
                'clave' => 'tiempo_max_preparacion',
                'valor' => '60',
                'tipo' => 'integer',
                'descripcion' => 'Tiempo máximo de preparación en minutos',
                'categoria' => 'pedidos',
                'editable' => true
            ],

            // CONFIGURACIÓN VENDEDORES
            [
                'clave' => 'meta_vendedor_default',
                'valor' => '50000',
                'tipo' => 'integer',
                'descripcion' => 'Meta mensual por defecto para vendedores nuevos (COP)',
                'categoria' => 'ventas',
                'editable' => true
            ],
            [
                'clave' => 'meta_lider_default',
                'valor' => '150000',
                'tipo' => 'integer',
                'descripcion' => 'Meta mensual por defecto para líderes nuevos (COP)',
                'categoria' => 'ventas',
                'editable' => true
            ]
        ];

        foreach ($configuraciones as $config) {
            Configuracion::create($config);
        }

        $this->command->info('   ✅ ' . count($configuraciones) . ' configuraciones creadas');
    }

    private function createCupones(): void
    {
        $this->command->info('🎟️ Creando cupones de descuento...');

        $cupones = [
            [
                'codigo' => 'BIENVENIDO20',
                'nombre' => 'Cupón de Bienvenida',
                'descripcion' => 'Descuento del 20% para nuevos clientes',
                'tipo' => 'porcentaje',
                'valor' => 20.00,
                'monto_minimo' => 15000,
                'usos_maximos' => 100,
                'usos_realizados' => 0,
                'fecha_inicio' => now()->format('Y-m-d'),
                'fecha_fin' => now()->addMonths(3)->format('Y-m-d'),
                'activo' => true,
                'categorias_aplicables' => null,
                'productos_aplicables' => null
            ],
            [
                'codigo' => 'PRIMERA10',
                'nombre' => 'Primera Compra',
                'descripcion' => '$10.000 de descuento en tu primera compra',
                'tipo' => 'monto_fijo',
                'valor' => 10000,
                'monto_minimo' => 25000,
                'usos_maximos' => 50,
                'usos_realizados' => 0,
                'fecha_inicio' => now()->format('Y-m-d'),
                'fecha_fin' => now()->addMonths(2)->format('Y-m-d'),
                'activo' => true,
                'categorias_aplicables' => null,
                'productos_aplicables' => null
            ],
            [
                'codigo' => 'FINDELARGO',
                'nombre' => 'Fin de Semana Largo',
                'descripcion' => '15% de descuento en fines de semana largos',
                'tipo' => 'porcentaje',
                'valor' => 15.00,
                'monto_minimo' => 20000,
                'usos_maximos' => null,
                'usos_realizados' => 0,
                'fecha_inicio' => now()->addDays(10)->format('Y-m-d'),
                'fecha_fin' => now()->addDays(15)->format('Y-m-d'),
                'activo' => true,
                'categorias_aplicables' => ['Arepas Rellenas', 'Bebidas Naturales'],
                'productos_aplicables' => null
            ]
        ];

        foreach ($cupones as $cupon) {
            Cupon::create($cupon);
        }

        $this->command->info('   ✅ ' . count($cupones) . ' cupones creados');
    }

    private function showSummary(): void
    {
        $this->command->info("\n📊 RESUMEN DE DATOS CREADOS:");
        $this->command->info("   👥 Usuarios: " . User::count());
        $this->command->info("   📂 Categorías: " . Categoria::count());
        $this->command->info("   🥪 Productos: " . Producto::count());
        $this->command->info("   🚚 Zonas de entrega: " . ZonaEntrega::count());
        $this->command->info("   ⚙️  Configuraciones: " . Configuracion::count());
        $this->command->info("   🎟️  Cupones: " . Cupon::count());

        $this->command->info("\n🔑 CREDENCIALES DE ACCESO:");
        $this->command->info("   Admin: admin@arepallanerita.com / admin123");
        $this->command->info("   Líder: carlos.rodriguez@arepallanerita.com / lider123");
        $this->command->info("   Vendedor: maria.martinez@arepallanerita.com / vendedor123");
        $this->command->info("   Cliente: andrea.ramirez@gmail.com / cliente123");
    }
}