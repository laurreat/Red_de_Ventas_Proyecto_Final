<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\UserMongo;
use App\Models\CategoriaMongo;
use App\Models\ProductoMongo;
use App\Models\PedidoMongo;
use App\Models\ComisionMongo;
use App\Models\ReferidoMongo;
use App\Models\NotificacionMongo;
use App\Models\MovimientoInventarioMongo;
use App\Models\ZonaEntregaMongo;
use App\Models\ConfiguracionMongo;
use App\Models\CuponMongo;
use App\Models\AuditoriaMongo;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class MongoDBCompleteSeeder extends Seeder
{
    /**
     * Ejecutar los seeders para MongoDB
     * Sistema completo Arepa la Llanerita - Red de Ventas MLM
     */
    public function run()
    {
        $this->command->info('🚀 Iniciando seeding completo de MongoDB...');

        // Limpiar colecciones existentes
        $this->limpiarColecciones();

        // Ejecutar seeders en orden correcto (respetando dependencias)
        $this->seedConfiguraciones();
        $this->seedCategorias();
        $this->seedUsuarios();
        $this->seedProductos();
        $this->seedZonasEntrega();
        $this->seedCupones();
        $this->seedPedidos();
        $this->seedReferidos();
        $this->seedComisiones();
        $this->seedNotificaciones();
        $this->seedMovimientosInventario();
        $this->seedAuditoria();

        $this->command->info('✅ Seeding completo de MongoDB finalizado exitosamente!');
        $this->mostrarEstadisticas();
    }

    private function limpiarColecciones()
    {
        $this->command->info('🧹 Limpiando colecciones existentes...');

        $modelos = [
            UserMongo::class,
            CategoriaMongo::class,
            ProductoMongo::class,
            PedidoMongo::class,
            ComisionMongo::class,
            ReferidoMongo::class,
            NotificacionMongo::class,
            MovimientoInventarioMongo::class,
            ZonaEntregaMongo::class,
            ConfiguracionMongo::class,
            CuponMongo::class,
            AuditoriaMongo::class,
        ];

        foreach ($modelos as $modelo) {
            $modelo::truncate();
        }

        $this->command->info('  ✅ Colecciones limpiadas');
    }

    private function seedConfiguraciones()
    {
        $this->command->info('⚙️ Creando configuraciones del sistema...');

        $configuraciones = [
            // Configuraciones MLM
            [
                'clave' => 'comision_venta_directa',
                'valor' => ['valor' => 15.0],
                'tipo' => 'decimal',
                'categoria' => 'comisiones',
                'descripcion' => 'Porcentaje de comisión por venta directa',
                'es_publica' => false,
                'es_editable' => true,
                'grupo' => 'mlm',
                'orden' => 1,
            ],
            [
                'clave' => 'comision_referido_nivel_1',
                'valor' => ['valor' => 10.0],
                'tipo' => 'decimal',
                'categoria' => 'comisiones',
                'descripcion' => 'Porcentaje de comisión por referido nivel 1',
                'es_publica' => false,
                'es_editable' => true,
                'grupo' => 'mlm',
                'orden' => 2,
            ],
            [
                'clave' => 'comision_referido_nivel_2',
                'valor' => ['valor' => 5.0],
                'tipo' => 'decimal',
                'categoria' => 'comisiones',
                'descripcion' => 'Porcentaje de comisión por referido nivel 2',
                'es_publica' => false,
                'es_editable' => true,
                'grupo' => 'mlm',
                'orden' => 3,
            ],
            [
                'clave' => 'bono_liderazgo',
                'valor' => ['valor' => 20.0],
                'tipo' => 'decimal',
                'categoria' => 'comisiones',
                'descripcion' => 'Porcentaje de bono por liderazgo',
                'es_publica' => false,
                'es_editable' => true,
                'grupo' => 'mlm',
                'orden' => 4,
            ],

            // Configuraciones generales
            [
                'clave' => 'nombre_empresa',
                'valor' => ['valor' => 'Arepa la Llanerita'],
                'tipo' => 'string',
                'categoria' => 'general',
                'descripcion' => 'Nombre de la empresa',
                'es_publica' => true,
                'es_editable' => true,
                'grupo' => 'empresa',
                'orden' => 1,
            ],
            [
                'clave' => 'telefono_empresa',
                'valor' => ['valor' => '+57 301 234 5678'],
                'tipo' => 'string',
                'categoria' => 'general',
                'descripcion' => 'Teléfono principal de la empresa',
                'es_publica' => true,
                'es_editable' => true,
                'grupo' => 'empresa',
                'orden' => 2,
            ],
            [
                'clave' => 'email_empresa',
                'valor' => ['valor' => 'contacto@arepallanerita.com'],
                'tipo' => 'string',
                'categoria' => 'general',
                'descripcion' => 'Email principal de la empresa',
                'es_publica' => true,
                'es_editable' => true,
                'grupo' => 'empresa',
                'orden' => 3,
            ],

            // Configuraciones de pedidos
            [
                'clave' => 'tiempo_preparacion_default',
                'valor' => ['valor' => 20],
                'tipo' => 'integer',
                'categoria' => 'pedidos',
                'descripcion' => 'Tiempo de preparación por defecto (minutos)',
                'es_publica' => true,
                'es_editable' => true,
                'grupo' => 'operaciones',
                'orden' => 1,
            ],
            [
                'clave' => 'costo_domicilio_default',
                'valor' => ['valor' => 5000.0],
                'tipo' => 'decimal',
                'categoria' => 'pedidos',
                'descripcion' => 'Costo de domicilio por defecto',
                'es_publica' => true,
                'es_editable' => true,
                'grupo' => 'operaciones',
                'orden' => 2,
            ],

            // Configuraciones de inventario
            [
                'clave' => 'stock_minimo_default',
                'valor' => ['valor' => 5],
                'tipo' => 'integer',
                'categoria' => 'inventario',
                'descripcion' => 'Stock mínimo por defecto para productos',
                'es_publica' => false,
                'es_editable' => true,
                'grupo' => 'inventario',
                'orden' => 1,
            ],
        ];

        foreach ($configuraciones as $config) {
            ConfiguracionMongo::create($config);
        }

        $this->command->info('  ✅ ' . count($configuraciones) . ' configuraciones creadas');
    }

    private function seedCategorias()
    {
        $this->command->info('📂 Creando categorías de productos...');

        $categorias = [
            [
                'nombre' => 'Arepas Tradicionales',
                'descripcion' => 'Arepas clásicas venezolanas y colombianas',
                'activo' => true,
                'orden' => 1,
                'productos_count' => 0,
                'configuracion' => [
                    'mostrar_destacado' => true,
                    'color_categoria' => '#E53E3E',
                ]
            ],
            [
                'nombre' => 'Arepas Especiales',
                'descripcion' => 'Arepas con rellenos especiales y gourmet',
                'activo' => true,
                'orden' => 2,
                'productos_count' => 0,
                'configuracion' => [
                    'mostrar_destacado' => true,
                    'color_categoria' => '#38A169',
                ]
            ],
            [
                'nombre' => 'Arepas Dulces',
                'descripcion' => 'Arepas dulces y postres',
                'activo' => true,
                'orden' => 3,
                'productos_count' => 0,
                'configuracion' => [
                    'mostrar_destacado' => false,
                    'color_categoria' => '#D69E2E',
                ]
            ],
            [
                'nombre' => 'Bebidas',
                'descripcion' => 'Bebidas frías y calientes',
                'activo' => true,
                'orden' => 4,
                'productos_count' => 0,
                'configuracion' => [
                    'mostrar_destacado' => true,
                    'color_categoria' => '#3182CE',
                ]
            ],
            [
                'nombre' => 'Complementos',
                'descripcion' => 'Acompañamientos y extras',
                'activo' => true,
                'orden' => 5,
                'productos_count' => 0,
                'configuracion' => [
                    'mostrar_destacado' => false,
                    'color_categoria' => '#805AD5',
                ]
            ],
            [
                'nombre' => 'Combos',
                'descripcion' => 'Combos especiales y promocionales',
                'activo' => true,
                'orden' => 6,
                'productos_count' => 0,
                'configuracion' => [
                    'mostrar_destacado' => true,
                    'color_categoria' => '#DD6B20',
                ]
            ],
        ];

        foreach ($categorias as $categoria) {
            CategoriaMongo::create($categoria);
        }

        $this->command->info('  ✅ ' . count($categorias) . ' categorías creadas');
    }

    private function seedUsuarios()
    {
        $this->command->info('👥 Creando usuarios del sistema...');

        // Administrador
        $admin = UserMongo::create([
            'name' => 'Carlos',
            'apellidos' => 'Administrador',
            'cedula' => '12345678',
            'email' => 'admin@arepallanerita.com',
            'password' => Hash::make('admin123'),
            'telefono' => '+57 300 123 4567',
            'direccion' => 'Calle 123 #45-67',
            'ciudad' => 'Bogotá',
            'departamento' => 'Cundinamarca',
            'fecha_nacimiento' => Carbon::parse('1985-05-15'),
            'rol' => 'administrador',
            'activo' => true,
            'codigo_referido' => 'ADMIN001',
            'meta_mensual' => 0,
            'configuracion_personal' => [
                'notificaciones_email' => true,
                'notificaciones_push' => true,
                'tema' => 'oscuro',
                'dashboard_widgets' => ['ventas', 'usuarios', 'productos', 'comisiones']
            ]
        ]);

        // Líder
        $lider = UserMongo::create([
            'name' => 'Carlos',
            'apellidos' => 'Rodriguez',
            'cedula' => '87654321',
            'email' => 'carlos.rodriguez@arepallanerita.com',
            'password' => Hash::make('lider123'),
            'telefono' => '+57 301 234 5678',
            'direccion' => 'Carrera 45 #23-12',
            'ciudad' => 'Medellín',
            'departamento' => 'Antioquia',
            'fecha_nacimiento' => Carbon::parse('1980-08-22'),
            'rol' => 'lider',
            'activo' => true,
            'codigo_referido' => 'LIDER001',
            'meta_mensual' => 500000,
            'ventas_mes_actual' => 350000,
            'comisiones_ganadas' => 75000,
            'comisiones_disponibles' => 25000,
            'total_referidos' => 5,
            'nivel_vendedor' => 3,
            'configuracion_personal' => [
                'notificaciones_email' => true,
                'notificaciones_push' => true,
                'tema' => 'claro',
                'dashboard_widgets' => ['equipo', 'ventas', 'metas', 'comisiones']
            ]
        ]);

        // Vendedores
        $vendedores = [
            [
                'name' => 'Ana',
                'apellidos' => 'López',
                'cedula' => '11223344',
                'email' => 'ana.lopez@arepallanerita.com',
                'password' => Hash::make('vendedor123'),
                'telefono' => '+57 302 345 6789',
                'direccion' => 'Avenida 67 #34-56',
                'ciudad' => 'Cali',
                'departamento' => 'Valle del Cauca',
                'referido_por' => $lider->_id,
                'codigo_referido' => 'VEND001',
                'meta_mensual' => 200000,
                'ventas_mes_actual' => 180000,
            ],
            [
                'name' => 'Miguel',
                'apellidos' => 'Torres',
                'cedula' => '55667788',
                'email' => 'miguel.torres@arepallanerita.com',
                'password' => Hash::make('vendedor123'),
                'telefono' => '+57 303 456 7890',
                'direccion' => 'Calle 89 #12-34',
                'ciudad' => 'Barranquilla',
                'departamento' => 'Atlántico',
                'referido_por' => $lider->_id,
                'codigo_referido' => 'VEND002',
                'meta_mensual' => 150000,
                'ventas_mes_actual' => 120000,
            ],
            [
                'name' => 'Sofia',
                'apellidos' => 'García',
                'cedula' => '99887766',
                'email' => 'sofia.garcia@arepallanerita.com',
                'password' => Hash::make('vendedor123'),
                'telefono' => '+57 304 567 8901',
                'direccion' => 'Carrera 12 #45-67',
                'ciudad' => 'Bucaramanga',
                'departamento' => 'Santander',
                'referido_por' => $lider->_id,
                'codigo_referido' => 'VEND003',
                'meta_mensual' => 180000,
                'ventas_mes_actual' => 200000,
            ]
        ];

        $vendedoresCreados = [];
        foreach ($vendedores as $vendedorData) {
            $vendedorData['rol'] = 'vendedor';
            $vendedorData['activo'] = true;
            $vendedorData['fecha_nacimiento'] = Carbon::parse('1990-' . rand(1, 12) . '-' . rand(1, 28));
            $vendedorData['comisiones_ganadas'] = $vendedorData['ventas_mes_actual'] * 0.15;
            $vendedorData['comisiones_disponibles'] = $vendedorData['comisiones_ganadas'] * 0.6;
            $vendedorData['total_referidos'] = rand(1, 3);
            $vendedorData['nivel_vendedor'] = 2;
            $vendedorData['configuracion_personal'] = [
                'notificaciones_email' => true,
                'notificaciones_push' => true,
                'tema' => 'claro',
                'dashboard_widgets' => ['ventas', 'metas', 'referidos', 'comisiones']
            ];

            $vendedoresCreados[] = UserMongo::create($vendedorData);
        }

        // Clientes
        $clientes = [
            [
                'name' => 'María',
                'apellidos' => 'González',
                'cedula' => '22334455',
                'email' => 'maria.gonzalez@email.com',
                'password' => Hash::make('cliente123'),
                'telefono' => '+57 305 678 9012',
                'direccion' => 'Calle 56 #78-90',
                'ciudad' => 'Bogotá',
                'departamento' => 'Cundinamarca',
                'referido_por' => $vendedoresCreados[0]->_id,
                'codigo_referido' => 'CLIE001',
            ],
            [
                'name' => 'Pedro',
                'apellidos' => 'Ramírez',
                'cedula' => '66778899',
                'email' => 'pedro.ramirez@email.com',
                'password' => Hash::make('cliente123'),
                'telefono' => '+57 306 789 0123',
                'direccion' => 'Carrera 34 #56-78',
                'ciudad' => 'Medellín',
                'departamento' => 'Antioquia',
                'referido_por' => $vendedoresCreados[1]->_id,
                'codigo_referido' => 'CLIE002',
            ],
            [
                'name' => 'Laura',
                'apellidos' => 'Martínez',
                'cedula' => '33445566',
                'email' => 'laura.martinez@email.com',
                'password' => Hash::make('cliente123'),
                'telefono' => '+57 307 890 1234',
                'direccion' => 'Avenida 12 #34-56',
                'ciudad' => 'Cali',
                'departamento' => 'Valle del Cauca',
                'referido_por' => $vendedoresCreados[2]->_id,
                'codigo_referido' => 'CLIE003',
            ],
            [
                'name' => 'Roberto',
                'apellidos' => 'Silva',
                'cedula' => '77889900',
                'email' => 'roberto.silva@email.com',
                'password' => Hash::make('cliente123'),
                'telefono' => '+57 308 901 2345',
                'direccion' => 'Calle 90 #12-34',
                'ciudad' => 'Barranquilla',
                'departamento' => 'Atlántico',
                'referido_por' => $vendedoresCreados[0]->_id,
                'codigo_referido' => 'CLIE004',
            ]
        ];

        foreach ($clientes as $clienteData) {
            $clienteData['rol'] = 'cliente';
            $clienteData['activo'] = true;
            $clienteData['fecha_nacimiento'] = Carbon::parse('1985-' . rand(1, 12) . '-' . rand(1, 28));
            $clienteData['meta_mensual'] = 0;
            $clienteData['ventas_mes_actual'] = 0;
            $clienteData['comisiones_ganadas'] = 0;
            $clienteData['comisiones_disponibles'] = 0;
            $clienteData['total_referidos'] = 0;
            $clienteData['nivel_vendedor'] = 1;
            $clienteData['configuracion_personal'] = [
                'notificaciones_email' => true,
                'notificaciones_push' => false,
                'tema' => 'claro',
                'dashboard_widgets' => ['pedidos', 'favoritos']
            ];

            UserMongo::create($clienteData);
        }

        $totalUsuarios = 1 + 1 + count($vendedores) + count($clientes);
        $this->command->info("  ✅ $totalUsuarios usuarios creados (1 admin, 1 líder, " . count($vendedores) . " vendedores, " . count($clientes) . " clientes)");
    }

    private function seedProductos()
    {
        $this->command->info('🥞 Creando productos...');

        $categorias = CategoriaMongo::all()->keyBy('nombre');

        $productos = [
            // Arepas Tradicionales
            [
                'nombre' => 'Arepa Reina Pepiada',
                'descripcion' => 'Arepa rellena con pollo desmenuzado y palta',
                'categoria' => 'Arepas Tradicionales',
                'precio' => 12000,
                'stock' => 50,
                'ingredientes' => ['harina de maíz', 'pollo', 'palta', 'mayonesa', 'sal'],
                'tiempo_preparacion' => 15,
            ],
            [
                'nombre' => 'Arepa con Carne Mechada',
                'descripcion' => 'Arepa rellena con carne mechada venezolana',
                'categoria' => 'Arepas Tradicionales',
                'precio' => 14000,
                'stock' => 40,
                'ingredientes' => ['harina de maíz', 'carne', 'cebolla', 'ajo', 'especias'],
                'tiempo_preparacion' => 20,
            ],
            [
                'nombre' => 'Arepa de Queso',
                'descripcion' => 'Arepa tradicional rellena de queso blanco',
                'categoria' => 'Arepas Tradicionales',
                'precio' => 8000,
                'stock' => 60,
                'ingredientes' => ['harina de maíz', 'queso blanco', 'sal'],
                'tiempo_preparacion' => 10,
            ],
            [
                'nombre' => 'Arepa Pelúa',
                'descripcion' => 'Arepa con carne mechada y queso amarillo',
                'categoria' => 'Arepas Tradicionales',
                'precio' => 15000,
                'stock' => 35,
                'ingredientes' => ['harina de maíz', 'carne mechada', 'queso amarillo'],
                'tiempo_preparacion' => 18,
            ],

            // Arepas Especiales
            [
                'nombre' => 'Arepa La Llanerita Especial',
                'descripcion' => 'Arepa signature con pollo, queso, palta y salsa especial',
                'categoria' => 'Arepas Especiales',
                'precio' => 18000,
                'stock' => 30,
                'ingredientes' => ['harina de maíz', 'pollo', 'queso', 'palta', 'salsa especial'],
                'tiempo_preparacion' => 25,
            ],
            [
                'nombre' => 'Arepa Gourmet de Salmón',
                'descripcion' => 'Arepa gourmet con salmón ahumado y cream cheese',
                'categoria' => 'Arepas Especiales',
                'precio' => 25000,
                'stock' => 20,
                'ingredientes' => ['harina de maíz', 'salmón ahumado', 'cream cheese', 'alcaparras'],
                'tiempo_preparacion' => 20,
            ],
            [
                'nombre' => 'Arepa Vegetariana',
                'descripcion' => 'Arepa con vegetales asados y queso de cabra',
                'categoria' => 'Arepas Especiales',
                'precio' => 16000,
                'stock' => 25,
                'ingredientes' => ['harina de maíz', 'vegetales asados', 'queso de cabra', 'herbs'],
                'tiempo_preparacion' => 22,
            ],

            // Arepas Dulces
            [
                'nombre' => 'Arepa Dulce con Queso',
                'descripcion' => 'Arepa dulce tradicional con queso fresco',
                'categoria' => 'Arepas Dulces',
                'precio' => 10000,
                'stock' => 40,
                'ingredientes' => ['harina de maíz', 'azúcar', 'queso fresco', 'leche'],
                'tiempo_preparacion' => 15,
            ],
            [
                'nombre' => 'Arepa con Nutella',
                'descripcion' => 'Arepa dulce rellena con Nutella y banano',
                'categoria' => 'Arepas Dulces',
                'precio' => 12000,
                'stock' => 35,
                'ingredientes' => ['harina de maíz', 'azúcar', 'nutella', 'banano'],
                'tiempo_preparacion' => 12,
            ],

            // Bebidas
            [
                'nombre' => 'Chicha Venezolana',
                'descripcion' => 'Bebida tradicional venezolana de arroz',
                'categoria' => 'Bebidas',
                'precio' => 6000,
                'stock' => 100,
                'ingredientes' => ['arroz', 'leche', 'azúcar', 'canela', 'vainilla'],
                'tiempo_preparacion' => 5,
            ],
            [
                'nombre' => 'Café Llanerito',
                'descripcion' => 'Café especial de la casa',
                'categoria' => 'Bebidas',
                'precio' => 4000,
                'stock' => 150,
                'ingredientes' => ['café', 'azúcar', 'leche'],
                'tiempo_preparacion' => 3,
            ],
            [
                'nombre' => 'Jugo Natural',
                'descripcion' => 'Jugo natural de frutas frescas',
                'categoria' => 'Bebidas',
                'precio' => 5000,
                'stock' => 80,
                'ingredientes' => ['frutas frescas', 'agua', 'azúcar'],
                'tiempo_preparacion' => 5,
            ],

            // Complementos
            [
                'nombre' => 'Tequeños',
                'descripcion' => 'Tequeños de queso (6 unidades)',
                'categoria' => 'Complementos',
                'precio' => 8000,
                'stock' => 60,
                'ingredientes' => ['masa', 'queso', 'aceite'],
                'tiempo_preparacion' => 10,
            ],
            [
                'nombre' => 'Cachapas',
                'descripcion' => 'Cachapas de maíz con queso',
                'categoria' => 'Complementos',
                'precio' => 14000,
                'stock' => 30,
                'ingredientes' => ['maíz', 'queso', 'mantequilla'],
                'tiempo_preparacion' => 18,
            ],

            // Combos
            [
                'nombre' => 'Combo Familiar',
                'descripcion' => 'Combo para 4 personas: 4 arepas + 4 bebidas',
                'categoria' => 'Combos',
                'precio' => 50000,
                'stock' => 20,
                'ingredientes' => ['arepas variadas', 'bebidas', 'salsas'],
                'tiempo_preparacion' => 30,
            ],
            [
                'nombre' => 'Combo Individual',
                'descripcion' => 'Arepa + bebida + tequeños',
                'categoria' => 'Combos',
                'precio' => 22000,
                'stock' => 40,
                'ingredientes' => ['arepa', 'bebida', 'tequeños'],
                'tiempo_preparacion' => 20,
            ],
        ];

        foreach ($productos as $productoData) {
            $categoria = $categorias->get($productoData['categoria']);

            ProductoMongo::create([
                'nombre' => $productoData['nombre'],
                'descripcion' => $productoData['descripcion'],
                'categoria_id' => $categoria->_id,
                'categoria_data' => [
                    'nombre' => $categoria->nombre,
                    'descripcion' => $categoria->descripcion,
                ],
                'precio' => $productoData['precio'],
                'stock' => $productoData['stock'],
                'stock_minimo' => 10,
                'activo' => true,
                'imagen' => 'productos/' . str_replace(' ', '_', strtolower($productoData['nombre'])) . '.jpg',
                'imagenes_adicionales' => [],
                'especificaciones' => [
                    'peso_aproximado' => '200g',
                    'temperatura_servir' => 'Caliente',
                    'alergenos' => 'Puede contener trazas de gluten',
                ],
                'tiempo_preparacion' => $productoData['tiempo_preparacion'],
                'ingredientes' => $productoData['ingredientes'],
                'historial_precios' => [
                    [
                        'precio_anterior' => 0,
                        'precio_nuevo' => $productoData['precio'],
                        'motivo' => 'Precio inicial',
                        'fecha' => now(),
                        'usuario_id' => null,
                    ]
                ],
                'reviews' => $this->generarReviewsAleatorias(),
            ]);

            // Actualizar contador de productos en categoría
            $categoria->increment('productos_count');
        }

        $this->command->info('  ✅ ' . count($productos) . ' productos creados');
    }

    private function generarReviewsAleatorias()
    {
        $reviews = [];
        $cantidadReviews = rand(0, 5);

        $comentariosPosibles = [
            'Excelente sabor, muy recomendado',
            'Delicioso, volveré a pedirlo',
            'Buena calidad y buen precio',
            'Muy sabroso, me encantó',
            'Perfecto, justo como esperaba',
            'Rico sabor tradicional',
            'Muy buena atención y producto',
        ];

        $nombres = ['Juan', 'María', 'Carlos', 'Ana', 'Luis', 'Sofia', 'Pedro', 'Laura'];

        for ($i = 0; $i < $cantidadReviews; $i++) {
            $reviews[] = [
                'usuario_nombre' => $nombres[array_rand($nombres)],
                'calificacion' => rand(4, 5),
                'comentario' => $comentariosPosibles[array_rand($comentariosPosibles)],
                'fecha' => Carbon::now()->subDays(rand(1, 30)),
                'id' => uniqid(),
            ];
        }

        return $reviews;
    }

    private function seedZonasEntrega()
    {
        $this->command->info('📍 Creando zonas de entrega...');

        $zonas = [
            [
                'nombre' => 'Centro Histórico',
                'descripcion' => 'Zona del centro histórico de la ciudad',
                'ciudad' => 'Bogotá',
                'departamento' => 'Cundinamarca',
                'codigo_postal' => '110111',
                'coordenadas' => [4.5981, -74.0758],
                'precio_domicilio' => 5000.0,
                'tiempo_entrega_estimado' => 45,
                'activa' => true,
                'cobertura_barrios' => ['La Candelaria', 'Centro', 'Santa Fe', 'Las Nieves'],
                'horarios_entrega' => [
                    'lunes' => ['activo' => true, 'hora_inicio' => '08:00', 'hora_fin' => '18:00'],
                    'martes' => ['activo' => true, 'hora_inicio' => '08:00', 'hora_fin' => '18:00'],
                    'miercoles' => ['activo' => true, 'hora_inicio' => '08:00', 'hora_fin' => '18:00'],
                    'jueves' => ['activo' => true, 'hora_inicio' => '08:00', 'hora_fin' => '18:00'],
                    'viernes' => ['activo' => true, 'hora_inicio' => '08:00', 'hora_fin' => '18:00'],
                    'sabado' => ['activo' => true, 'hora_inicio' => '09:00', 'hora_fin' => '16:00'],
                    'domingo' => ['activo' => false],
                ],
            ],
            [
                'nombre' => 'Zona Norte',
                'descripcion' => 'Zona norte de la ciudad',
                'ciudad' => 'Bogotá',
                'departamento' => 'Cundinamarca',
                'codigo_postal' => '110221',
                'coordenadas' => [4.6482, -74.0776],
                'precio_domicilio' => 8000.0,
                'tiempo_entrega_estimado' => 60,
                'activa' => true,
                'cobertura_barrios' => ['Chapinero', 'Zona Rosa', 'Chicó', 'Rosales'],
                'horarios_entrega' => [
                    'lunes' => ['activo' => true, 'hora_inicio' => '08:00', 'hora_fin' => '18:00'],
                    'martes' => ['activo' => true, 'hora_inicio' => '08:00', 'hora_fin' => '18:00'],
                    'miercoles' => ['activo' => true, 'hora_inicio' => '08:00', 'hora_fin' => '18:00'],
                    'jueves' => ['activo' => true, 'hora_inicio' => '08:00', 'hora_fin' => '18:00'],
                    'viernes' => ['activo' => true, 'hora_inicio' => '08:00', 'hora_fin' => '18:00'],
                    'sabado' => ['activo' => true, 'hora_inicio' => '09:00', 'hora_fin' => '16:00'],
                    'domingo' => ['activo' => true, 'hora_inicio' => '10:00', 'hora_fin' => '15:00'],
                ],
            ],
            [
                'nombre' => 'Zona Sur',
                'descripcion' => 'Zona sur de la ciudad',
                'ciudad' => 'Bogotá',
                'departamento' => 'Cundinamarca',
                'codigo_postal' => '110821',
                'coordenadas' => [4.5709, -74.0950],
                'precio_domicilio' => 7000.0,
                'tiempo_entrega_estimado' => 55,
                'activa' => true,
                'cobertura_barrios' => ['San Cristóbal', 'Usme', 'Rafael Uribe', 'Ciudad Bolívar'],
                'horarios_entrega' => [
                    'lunes' => ['activo' => true, 'hora_inicio' => '08:00', 'hora_fin' => '17:00'],
                    'martes' => ['activo' => true, 'hora_inicio' => '08:00', 'hora_fin' => '17:00'],
                    'miercoles' => ['activo' => true, 'hora_inicio' => '08:00', 'hora_fin' => '17:00'],
                    'jueves' => ['activo' => true, 'hora_inicio' => '08:00', 'hora_fin' => '17:00'],
                    'viernes' => ['activo' => true, 'hora_inicio' => '08:00', 'hora_fin' => '17:00'],
                    'sabado' => ['activo' => true, 'hora_inicio' => '09:00', 'hora_fin' => '15:00'],
                    'domingo' => ['activo' => false],
                ],
            ],
        ];

        foreach ($zonas as $zona) {
            $zona['vendedores_asignados'] = [];
            $zona['restricciones'] = [];
            $zona['configuracion_especial'] = [];
            $zona['estadisticas'] = [];
            $zona['observaciones'] = [];

            ZonaEntregaMongo::create($zona);
        }

        $this->command->info('  ✅ ' . count($zonas) . ' zonas de entrega creadas');
    }

    private function seedCupones()
    {
        $this->command->info('🎫 Creando cupones de descuento...');

        $admin = UserMongo::where('rol', 'administrador')->first();

        $cupones = [
            [
                'codigo' => 'BIENVENIDA20',
                'tipo' => 'porcentaje',
                'porcentaje_descuento' => 20.0,
                'descripcion' => 'Descuento de bienvenida del 20% para nuevos clientes',
                'fecha_inicio' => now(),
                'fecha_vencimiento' => now()->addDays(30),
                'limite_usos' => 100,
                'monto_minimo_compra' => 20000.0,
                'monto_maximo_descuento' => 15000.0,
                'aplicable_a' => 'todos',
            ],
            [
                'codigo' => 'COMBO50',
                'tipo' => 'monto_fijo',
                'monto_descuento' => 5000.0,
                'descripcion' => '$5,000 de descuento en combos familiares',
                'fecha_inicio' => now(),
                'fecha_vencimiento' => now()->addDays(15),
                'limite_usos' => 50,
                'monto_minimo_compra' => 40000.0,
                'aplicable_a' => 'categorias',
                'categorias_aplicables' => [CategoriaMongo::where('nombre', 'Combos')->first()->_id],
            ],
            [
                'codigo' => 'ENVIOGRATIS',
                'tipo' => 'envio_gratis',
                'descripcion' => 'Envío gratis en pedidos superiores a $30,000',
                'fecha_inicio' => now(),
                'fecha_vencimiento' => now()->addDays(60),
                'limite_usos' => 200,
                'monto_minimo_compra' => 30000.0,
                'aplicable_a' => 'todos',
            ],
            [
                'codigo' => 'WEEKEND15',
                'tipo' => 'porcentaje',
                'porcentaje_descuento' => 15.0,
                'descripcion' => '15% de descuento los fines de semana',
                'fecha_inicio' => now(),
                'fecha_vencimiento' => now()->addDays(90),
                'limite_usos' => null, // Sin límite
                'monto_minimo_compra' => 15000.0,
                'monto_maximo_descuento' => 10000.0,
                'aplicable_a' => 'todos',
                'restricciones' => [
                    [
                        'tipo' => 'dia_semana',
                        'valor' => [5, 6], // Sábado y domingo (0 = domingo, 6 = sábado)
                        'descripcion' => 'Solo válido sábados y domingos',
                    ]
                ],
            ],
        ];

        foreach ($cupones as $cuponData) {
            $cuponData['usos_actuales'] = 0;
            $cuponData['activo'] = true;
            $cuponData['productos_aplicables'] = $cuponData['productos_aplicables'] ?? [];
            $cuponData['categorias_aplicables'] = $cuponData['categorias_aplicables'] ?? [];
            $cuponData['usuarios_aplicables'] = $cuponData['usuarios_aplicables'] ?? [];
            $cuponData['restricciones'] = $cuponData['restricciones'] ?? [];
            $cuponData['condiciones_especiales'] = $cuponData['condiciones_especiales'] ?? [];
            $cuponData['historial_usos'] = [];
            $cuponData['created_by'] = $admin->_id;
            $cuponData['estadisticas'] = [];

            CuponMongo::create($cuponData);
        }

        $this->command->info('  ✅ ' . count($cupones) . ' cupones creados');
    }

    private function seedPedidos()
    {
        $this->command->info('📋 Creando pedidos de ejemplo...');

        $vendedores = UserMongo::where('rol', 'vendedor')->get();
        $clientes = UserMongo::where('rol', 'cliente')->get();
        $productos = ProductoMongo::all();

        $estadosPedidos = ['pendiente', 'confirmado', 'en_preparacion', 'listo', 'en_camino', 'entregado'];

        for ($i = 0; $i < 20; $i++) {
            $cliente = $clientes->random();
            $vendedor = $vendedores->random();
            $fechaPedido = Carbon::now()->subDays(rand(1, 30));

            $pedido = PedidoMongo::create([
                'numero_pedido' => 'ARE-' . now()->year . '-' . str_pad($i + 1, 4, '0', STR_PAD_LEFT),
                'user_id' => $cliente->_id,
                'cliente_data' => [
                    '_id' => $cliente->_id,
                    'name' => $cliente->name,
                    'apellidos' => $cliente->apellidos,
                    'email' => $cliente->email,
                    'telefono' => $cliente->telefono,
                    'cedula' => $cliente->cedula,
                ],
                'vendedor_id' => $vendedor->_id,
                'vendedor_data' => [
                    '_id' => $vendedor->_id,
                    'name' => $vendedor->name,
                    'apellidos' => $vendedor->apellidos,
                    'email' => $vendedor->email,
                    'telefono' => $vendedor->telefono,
                ],
                'estado' => $estadosPedidos[array_rand($estadosPedidos)],
                'direccion_entrega' => $cliente->direccion,
                'telefono_entrega' => $cliente->telefono,
                'notas' => $i % 3 == 0 ? 'Entregar en portería' : null,
                'fecha_entrega_estimada' => $fechaPedido->addHours(2),
                'metodo_pago' => ['efectivo', 'tarjeta', 'transferencia'][rand(0, 2)],
                'datos_entrega' => [
                    'metodo' => 'domicilio',
                    'costo_envio' => 5000,
                    'tiempo_estimado' => 60,
                ],
                'created_at' => $fechaPedido,
                'updated_at' => $fechaPedido,
            ]);

            // Agregar productos al pedido
            $cantidadProductos = rand(1, 4);
            $total = 0;
            $detalles = [];

            for ($j = 0; $j < $cantidadProductos; $j++) {
                $producto = $productos->random();
                $cantidad = rand(1, 3);
                $subtotal = $producto->precio * $cantidad;
                $total += $subtotal;

                $detalles[] = [
                    'producto_id' => $producto->_id,
                    'producto_data' => [
                        '_id' => $producto->_id,
                        'nombre' => $producto->nombre,
                        'descripcion' => $producto->descripcion,
                        'imagen' => $producto->imagen,
                        'categoria_data' => $producto->categoria_data,
                    ],
                    'cantidad' => $cantidad,
                    'precio_unitario' => $producto->precio,
                    'subtotal' => $subtotal,
                    'fecha_agregado' => $fechaPedido,
                ];
            }

            $pedido->update([
                'total' => $total,
                'total_final' => $total + 5000, // + costo envío
                'detalles' => $detalles,
                'historial_estados' => [
                    [
                        'estado_anterior' => null,
                        'estado_nuevo' => $pedido->estado,
                        'motivo' => 'Pedido creado',
                        'fecha' => $fechaPedido,
                        'usuario_id' => $vendedor->_id,
                    ]
                ],
            ]);
        }

        $this->command->info('  ✅ 20 pedidos de ejemplo creados');
    }

    private function seedReferidos()
    {
        $this->command->info('🤝 Creando relaciones de referidos...');

        $lider = UserMongo::where('rol', 'lider')->first();
        $vendedores = UserMongo::where('rol', 'vendedor')->get();
        $clientes = UserMongo::where('rol', 'cliente')->get();

        // Referidos del líder (vendedores)
        foreach ($vendedores as $vendedor) {
            ReferidoMongo::create([
                'referidor_id' => $lider->_id,
                'referidor_data' => [
                    '_id' => $lider->_id,
                    'name' => $lider->name,
                    'apellidos' => $lider->apellidos,
                    'email' => $lider->email,
                    'telefono' => $lider->telefono,
                    'rol' => $lider->rol,
                    'codigo_referido' => $lider->codigo_referido,
                ],
                'referido_id' => $vendedor->_id,
                'referido_data' => [
                    '_id' => $vendedor->_id,
                    'name' => $vendedor->name,
                    'apellidos' => $vendedor->apellidos,
                    'email' => $vendedor->email,
                    'telefono' => $vendedor->telefono,
                    'rol' => $vendedor->rol,
                    'fecha_registro' => $vendedor->created_at,
                ],
                'fecha_registro' => $vendedor->created_at,
                'activo' => true,
                'nivel' => 1,
                'comisiones_generadas' => rand(50000, 150000),
                'comisiones_pendientes' => rand(20000, 50000),
                'comisiones_pagadas' => rand(30000, 100000),
                'total_ventas_referido' => $vendedor->ventas_mes_actual,
                'meta_referido' => $vendedor->meta_mensual,
                'estado_referido' => 'activo',
                'historial_comisiones' => [],
                'bonificaciones_especiales' => [],
            ]);
        }

        // Referidos de vendedores (clientes)
        foreach ($clientes as $cliente) {
            $vendedor = $vendedores->random();

            ReferidoMongo::create([
                'referidor_id' => $vendedor->_id,
                'referidor_data' => [
                    '_id' => $vendedor->_id,
                    'name' => $vendedor->name,
                    'apellidos' => $vendedor->apellidos,
                    'email' => $vendedor->email,
                    'telefono' => $vendedor->telefono,
                    'rol' => $vendedor->rol,
                    'codigo_referido' => $vendedor->codigo_referido,
                ],
                'referido_id' => $cliente->_id,
                'referido_data' => [
                    '_id' => $cliente->_id,
                    'name' => $cliente->name,
                    'apellidos' => $cliente->apellidos,
                    'email' => $cliente->email,
                    'telefono' => $cliente->telefono,
                    'rol' => $cliente->rol,
                    'fecha_registro' => $cliente->created_at,
                ],
                'fecha_registro' => $cliente->created_at,
                'activo' => true,
                'nivel' => 1,
                'comisiones_generadas' => rand(10000, 30000),
                'comisiones_pendientes' => rand(5000, 15000),
                'comisiones_pagadas' => rand(5000, 15000),
                'total_ventas_referido' => 0,
                'meta_referido' => 0,
                'estado_referido' => 'activo',
                'historial_comisiones' => [],
                'bonificaciones_especiales' => [],
            ]);
        }

        $totalReferidos = $vendedores->count() + $clientes->count();
        $this->command->info("  ✅ $totalReferidos relaciones de referidos creadas");
    }

    private function seedComisiones()
    {
        $this->command->info('💰 Creando comisiones...');

        $pedidos = PedidoMongo::where('estado', 'entregado')->get();
        $tiposComision = ['venta_directa', 'referido_nivel_1', 'referido_nivel_2', 'bono_liderazgo'];
        $estadosComision = ['pendiente', 'aprobada', 'pagada'];

        foreach ($pedidos as $pedido) {
            // Comisión por venta directa al vendedor
            if ($pedido->vendedor_id) {
                $vendedor = UserMongo::find($pedido->vendedor_id);

                ComisionMongo::create([
                    'user_id' => $vendedor->_id,
                    'user_data' => [
                        '_id' => $vendedor->_id,
                        'name' => $vendedor->name,
                        'apellidos' => $vendedor->apellidos,
                        'email' => $vendedor->email,
                        'rol' => $vendedor->rol,
                    ],
                    'pedido_id' => $pedido->_id,
                    'pedido_data' => [
                        '_id' => $pedido->_id,
                        'numero_pedido' => $pedido->numero_pedido,
                        'total_final' => $pedido->total_final,
                    ],
                    'tipo' => 'venta_directa',
                    'porcentaje' => 15.0,
                    'monto' => $pedido->total_final * 0.15,
                    'estado' => $estadosComision[array_rand($estadosComision)],
                    'fecha_pago' => rand(0, 1) ? now()->subDays(rand(1, 15)) : null,
                    'detalles_calculo' => [
                        'base_calculo' => $pedido->total_final,
                        'porcentaje_aplicado' => 15.0,
                        'formula' => 'base_calculo * (porcentaje_aplicado / 100)',
                        'fecha_calculo' => $pedido->created_at,
                    ],
                    'created_at' => $pedido->created_at,
                    'updated_at' => $pedido->updated_at,
                ]);

                // Comisión por referido nivel 1 (si el vendedor tiene referidor)
                $referidor = UserMongo::find($vendedor->referido_por);
                if ($referidor) {
                    ComisionMongo::create([
                        'user_id' => $referidor->_id,
                        'user_data' => [
                            '_id' => $referidor->_id,
                            'name' => $referidor->name,
                            'apellidos' => $referidor->apellidos,
                            'email' => $referidor->email,
                            'rol' => $referidor->rol,
                        ],
                        'pedido_id' => $pedido->_id,
                        'pedido_data' => [
                            '_id' => $pedido->_id,
                            'numero_pedido' => $pedido->numero_pedido,
                            'total_final' => $pedido->total_final,
                        ],
                        'tipo' => 'referido_nivel_1',
                        'porcentaje' => 10.0,
                        'monto' => $pedido->total_final * 0.10,
                        'estado' => $estadosComision[array_rand($estadosComision)],
                        'fecha_pago' => rand(0, 1) ? now()->subDays(rand(1, 15)) : null,
                        'detalles_calculo' => [
                            'base_calculo' => $pedido->total_final,
                            'porcentaje_aplicado' => 10.0,
                            'formula' => 'base_calculo * (porcentaje_aplicado / 100)',
                            'fecha_calculo' => $pedido->created_at,
                            'vendedor_referido' => $vendedor->_id,
                        ],
                        'created_at' => $pedido->created_at,
                        'updated_at' => $pedido->updated_at,
                    ]);
                }
            }
        }

        $totalComisiones = ComisionMongo::count();
        $this->command->info("  ✅ $totalComisiones comisiones creadas");
    }

    private function seedNotificaciones()
    {
        $this->command->info('🔔 Creando notificaciones...');

        $usuarios = UserMongo::all();
        $tiposNotificacion = ['pedido', 'comision', 'sistema', 'promocion'];

        $mensajes = [
            'pedido' => [
                'Tu pedido #{numero} ha sido confirmado',
                'Pedido #{numero} está en preparación',
                'Tu pedido #{numero} está listo para entrega',
                'Pedido #{numero} ha sido entregado exitosamente',
            ],
            'comision' => [
                'Has ganado una nueva comisión de ${monto}',
                'Tu comisión de ${monto} ha sido aprobada',
                'Comisión de ${monto} ha sido pagada',
                'Tienes ${total} en comisiones pendientes',
            ],
            'sistema' => [
                'Bienvenido al sistema Arepa la Llanerita',
                'Tu perfil ha sido actualizado',
                'Nueva funcionalidad disponible en el sistema',
                'Mantenimiento programado para mañana',
            ],
            'promocion' => [
                'Nueva promoción: 20% de descuento en arepas',
                'Combo familiar con descuento especial',
                'Envío gratis en pedidos superiores a $30,000',
                'Oferta limitada: Arepa + bebida por $15,000',
            ],
        ];

        foreach ($usuarios as $usuario) {
            $cantidadNotificaciones = rand(3, 8);

            for ($i = 0; $i < $cantidadNotificaciones; $i++) {
                $tipo = $tiposNotificacion[array_rand($tiposNotificacion)];
                $mensaje = $mensajes[$tipo][array_rand($mensajes[$tipo])];

                // Personalizar mensaje según tipo
                if ($tipo === 'pedido') {
                    $mensaje = str_replace('{numero}', 'ARE-2024-' . str_pad(rand(1, 100), 4, '0', STR_PAD_LEFT), $mensaje);
                } elseif ($tipo === 'comision') {
                    $monto = number_format(rand(10000, 50000), 0, ',', '.');
                    $mensaje = str_replace('{monto}', $monto, $mensaje);
                    $mensaje = str_replace('{total}', number_format(rand(50000, 200000), 0, ',', '.'), $mensaje);
                }

                NotificacionMongo::create([
                    'user_id' => $usuario->_id,
                    'user_data' => [
                        '_id' => $usuario->_id,
                        'name' => $usuario->name,
                        'apellidos' => $usuario->apellidos,
                        'email' => $usuario->email,
                        'rol' => $usuario->rol,
                    ],
                    'titulo' => $this->generarTituloNotificacion($tipo),
                    'mensaje' => $mensaje,
                    'tipo' => $tipo,
                    'leida' => rand(0, 1) == 1,
                    'fecha_lectura' => rand(0, 1) ? now()->subDays(rand(1, 10)) : null,
                    'datos_adicionales' => [
                        'categoria' => $tipo,
                        'prioridad' => ['baja', 'media', 'alta'][rand(0, 2)],
                    ],
                    'canal' => 'sistema',
                    'created_at' => now()->subDays(rand(1, 30)),
                ]);
            }
        }

        $totalNotificaciones = NotificacionMongo::count();
        $this->command->info("  ✅ $totalNotificaciones notificaciones creadas");
    }

    private function generarTituloNotificacion($tipo)
    {
        $titulos = [
            'pedido' => ['Estado de Pedido', 'Actualización de Pedido', 'Confirmación de Pedido'],
            'comision' => ['Nueva Comisión', 'Comisión Aprobada', 'Pago de Comisión'],
            'sistema' => ['Notificación del Sistema', 'Actualización', 'Información Importante'],
            'promocion' => ['Nueva Promoción', 'Oferta Especial', 'Descuento Disponible'],
        ];

        return $titulos[$tipo][array_rand($titulos[$tipo])];
    }

    private function seedMovimientosInventario()
    {
        $this->command->info('📦 Creando movimientos de inventario...');

        $productos = ProductoMongo::all();
        $usuarios = UserMongo::whereIn('rol', ['administrador', 'lider'])->get();
        $tiposMovimiento = ['entrada', 'salida', 'ajuste'];

        foreach ($productos as $producto) {
            $cantidadMovimientos = rand(2, 5);

            for ($i = 0; $i < $cantidadMovimientos; $i++) {
                $usuario = $usuarios->random();
                $tipo = $tiposMovimiento[array_rand($tiposMovimiento)];
                $cantidad = rand(1, 20);

                if ($tipo === 'salida') {
                    $cantidad = -$cantidad;
                }

                $stockAnterior = rand(10, 100);
                $stockNuevo = $stockAnterior + $cantidad;

                MovimientoInventarioMongo::create([
                    'producto_id' => $producto->_id,
                    'producto_data' => [
                        '_id' => $producto->_id,
                        'nombre' => $producto->nombre,
                        'descripcion' => $producto->descripcion,
                        'categoria_data' => $producto->categoria_data,
                        'precio' => $producto->precio,
                        'stock_minimo' => $producto->stock_minimo,
                    ],
                    'user_id' => $usuario->_id,
                    'user_data' => [
                        '_id' => $usuario->_id,
                        'name' => $usuario->name,
                        'apellidos' => $usuario->apellidos,
                        'email' => $usuario->email,
                        'rol' => $usuario->rol,
                    ],
                    'tipo' => $tipo,
                    'cantidad' => abs($cantidad),
                    'stock_anterior' => $stockAnterior,
                    'stock_nuevo' => $stockNuevo,
                    'motivo' => $this->generarMotivoMovimiento($tipo),
                    'costo_unitario' => $tipo === 'entrada' ? rand(3000, 8000) : 0,
                    'costo_total' => $tipo === 'entrada' ? abs($cantidad) * rand(3000, 8000) : 0,
                    'ubicacion_almacen' => 'Almacén Principal',
                    'documento_respaldo' => [],
                    'observaciones' => [],
                    'aprobado_por' => $usuario->rol === 'administrador' ? $usuario->_id : null,
                    'datos_adicionales' => [
                        'automatico' => false,
                        'lote' => 'LOTE-' . date('Ymd') . '-' . str_pad($i + 1, 3, '0', STR_PAD_LEFT),
                    ],
                    'created_at' => now()->subDays(rand(1, 30)),
                ]);
            }
        }

        $totalMovimientos = MovimientoInventarioMongo::count();
        $this->command->info("  ✅ $totalMovimientos movimientos de inventario creados");
    }

    private function generarMotivoMovimiento($tipo)
    {
        $motivos = [
            'entrada' => [
                'Compra a proveedor',
                'Reposición de inventario',
                'Devolución de cliente',
                'Ajuste por inventario físico',
            ],
            'salida' => [
                'Venta a cliente',
                'Merma por vencimiento',
                'Producto dañado',
                'Muestra gratuita',
            ],
            'ajuste' => [
                'Ajuste por inventario físico',
                'Corrección de sistema',
                'Diferencia en conteo',
                'Actualización de stock',
            ],
        ];

        return $motivos[$tipo][array_rand($motivos[$tipo])];
    }

    private function seedAuditoria()
    {
        $this->command->info('📝 Creando registros de auditoría...');

        $usuarios = UserMongo::all();
        $modelos = ['UserMongo', 'ProductoMongo', 'PedidoMongo', 'ComisionMongo'];
        $acciones = ['created', 'updated', 'deleted'];

        for ($i = 0; $i < 50; $i++) {
            $usuario = $usuarios->random();
            $modelo = $modelos[array_rand($modelos)];
            $accion = $acciones[array_rand($acciones)];

            AuditoriaMongo::create([
                'modelo' => $modelo,
                'modelo_id' => (string) $i + 1,
                'accion' => $accion,
                'usuario_id' => $usuario->_id,
                'usuario_data' => [
                    '_id' => $usuario->_id,
                    'name' => $usuario->name,
                    'apellidos' => $usuario->apellidos,
                    'email' => $usuario->email,
                    'rol' => $usuario->rol,
                ],
                'datos_anteriores' => $accion !== 'created' ? ['campo' => 'valor_anterior'] : null,
                'datos_nuevos' => ['campo' => 'valor_nuevo'],
                'cambios_detectados' => [
                    'campo' => [
                        'anterior' => 'valor_anterior',
                        'nuevo' => 'valor_nuevo',
                        'tipo_cambio' => 'modificacion',
                    ]
                ],
                'ip_address' => '192.168.1.' . rand(100, 200),
                'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
                'url' => '/admin/' . strtolower($modelo),
                'metodo_http' => ['GET', 'POST', 'PUT', 'DELETE'][rand(0, 3)],
                'session_id' => uniqid('session_'),
                'tabla_afectada' => strtolower(str_replace('Mongo', '', $modelo)),
                'contexto_adicional' => [
                    'navegador' => 'Chrome',
                    'dispositivo' => 'Desktop',
                ],
                'nivel_criticidad' => ['bajo', 'medio', 'alto', 'critico'][rand(0, 3)],
                'categoria' => 'crud',
                'tags' => ['sistema', 'usuario', strtolower($modelo)],
                'datos_request' => [],
                'tiempo_ejecucion' => rand(100, 2000) / 1000, // milisegundos a segundos
                'created_at' => now()->subDays(rand(1, 30)),
            ]);
        }

        $totalAuditorias = AuditoriaMongo::count();
        $this->command->info("  ✅ $totalAuditorias registros de auditoría creados");
    }

    private function mostrarEstadisticas()
    {
        $this->command->info("\n📊 ESTADÍSTICAS FINALES DEL SEEDING");
        $this->command->info("=====================================");

        $estadisticas = [
            'Configuraciones' => ConfiguracionMongo::count(),
            'Categorías' => CategoriaMongo::count(),
            'Usuarios' => UserMongo::count(),
            'Productos' => ProductoMongo::count(),
            'Zonas de Entrega' => ZonaEntregaMongo::count(),
            'Cupones' => CuponMongo::count(),
            'Pedidos' => PedidoMongo::count(),
            'Referidos' => ReferidoMongo::count(),
            'Comisiones' => ComisionMongo::count(),
            'Notificaciones' => NotificacionMongo::count(),
            'Movimientos Inventario' => MovimientoInventarioMongo::count(),
            'Registros Auditoría' => AuditoriaMongo::count(),
        ];

        foreach ($estadisticas as $modelo => $cantidad) {
            $this->command->info(sprintf("%-25s %6d registros", $modelo . ':', $cantidad));
        }

        $total = array_sum($estadisticas);
        $this->command->info("\n" . str_repeat('-', 35));
        $this->command->info(sprintf("%-25s %6d registros", 'TOTAL:', $total));

        $this->command->info("\n🎯 CREDENCIALES DE ACCESO:");
        $this->command->info("Admin: admin@arepallanerita.com / admin123");
        $this->command->info("Líder: carlos.rodriguez@arepallanerita.com / lider123");
        $this->command->info("Vendedor: ana.lopez@arepallanerita.com / vendedor123");
        $this->command->info("Cliente: maria.gonzalez@email.com / cliente123");

        $this->command->info("\n✅ SEEDING MONGODB COMPLETADO EXITOSAMENTE!");
    }
}