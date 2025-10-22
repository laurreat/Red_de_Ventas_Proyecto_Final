<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Pedido;
use App\Models\DetallePedido;
use App\Models\Producto;
use App\Models\Categoria;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class RedMLMSeeder extends Seeder
{
    private $nombres = [
        'Juan', 'María', 'Carlos', 'Ana', 'Luis', 'Laura', 'Pedro', 'Sofia', 'Diego', 'Carmen',
        'Jorge', 'Isabel', 'Fernando', 'Patricia', 'Ricardo', 'Monica', 'Andres', 'Claudia', 'Roberto', 'Natalia',
        'Oscar', 'Diana', 'Gabriel', 'Valentina', 'Sergio', 'Camila', 'Daniel', 'Paola', 'Felipe', 'Marcela',
        'Alejandro', 'Sandra', 'Javier', 'Andrea', 'Miguel', 'Carolina', 'Raul', 'Juliana', 'Cristian', 'Liliana',
        'Eduardo', 'Adriana', 'Mauricio', 'Beatriz', 'Gustavo', 'Veronica', 'Hector', 'Rocio', 'Enrique', 'Gloria'
    ];

    private $apellidos = [
        'Garcia', 'Rodriguez', 'Martinez', 'Lopez', 'Gonzalez', 'Hernandez', 'Perez', 'Sanchez', 'Ramirez', 'Torres',
        'Flores', 'Rivera', 'Gomez', 'Diaz', 'Cruz', 'Morales', 'Reyes', 'Jimenez', 'Alvarez', 'Romero',
        'Vargas', 'Castro', 'Ortiz', 'Silva', 'Rojas', 'Mendoza', 'Castillo', 'Moreno', 'Gutierrez', 'Chavez',
        'Ruiz', 'Vazquez', 'Ramos', 'Fernandez', 'Medina', 'Aguilar', 'Rios', 'Soto', 'Delgado', 'Herrera'
    ];

    private $ciudades = [
        'Villavicencio', 'Acacias', 'Granada', 'San Martin', 'Puerto Lopez', 'Restrepo', 'Cumaral', 'Guamal'
    ];

    private $departamento = 'Meta';
    private $productos = null;
    private $clientes = [];

    public function run(): void
    {
        $this->command->info('🌟 Iniciando construcción de Red MLM compacta con TODAS las categorías de leyenda...');

        // Verificar y crear productos si no existen
        $this->command->info('🔍 Verificando productos disponibles...');
        $this->verificarYCrearProductos();

        // Verificar si ya existe el admin
        $admin = User::where('email', 'admin@arepallanerita.com')->first();
        
        if (!$admin) {
            $this->command->warn('No se encontró usuario administrador. Creando...');
            $admin = $this->crearAdmin();
        }

        $this->command->info("✅ Admin encontrado: {$admin->name}");

        // NIVEL 1: Crear líderes principales (3 líderes directos del admin)
        $this->command->info("\n📊 NIVEL 1: Creando líderes principales...");
        $lideresNivel1 = $this->crearLideres($admin->_id, 3, 1);
        $this->command->info("✅ Creados " . count($lideresNivel1) . " líderes principales");

        // NIVEL 2: Cada líder principal tiene 2-3 sub-líderes
        $this->command->info("\n📊 NIVEL 2: Creando sub-líderes...");
        $lideresNivel2 = [];
        foreach ($lideresNivel1 as $lider) {
            $subLideres = $this->crearLideres($lider->_id, rand(2, 3), 2);
            $lideresNivel2 = array_merge($lideresNivel2, $subLideres);
        }
        $this->command->info("✅ Creados " . count($lideresNivel2) . " sub-líderes");

        // NIVEL 3: Vendedores activos (cada sub-líder tiene 2-3 vendedores activos)
        $this->command->info("\n📊 NIVEL 3: Creando vendedores activos...");
        $vendedoresActivos = [];
        foreach ($lideresNivel2 as $lider) {
            $vendedores = $this->crearVendedores($lider->_id, rand(2, 3), 'activo');
            $vendedoresActivos = array_merge($vendedoresActivos, $vendedores);
        }
        $this->command->info("✅ Creados " . count($vendedoresActivos) . " vendedores activos");

        // NIVEL 4: Vendedores regulares (cada vendedor activo tiene 1-2 vendedores)
        $this->command->info("\n📊 NIVEL 4: Creando vendedores regulares...");
        $vendedoresRegulares = [];
        foreach ($vendedoresActivos as $vendedor) {
            $subVendedores = $this->crearVendedores($vendedor->_id, rand(1, 2), 'regular');
            $vendedoresRegulares = array_merge($vendedoresRegulares, $subVendedores);
        }
        $this->command->info("✅ Creados " . count($vendedoresRegulares) . " vendedores regulares");

        // NIVEL 5: Crear clientes reales referidos por vendedores
        $this->command->info("\n📊 NIVEL 5: Creando clientes referidos por vendedores...");
        $todosVendedores = array_merge($vendedoresActivos, $vendedoresRegulares);
        $this->crearClientesReales($todosVendedores, rand(40, 50));
        $this->command->info("✅ Creados " . count($this->clientes) . " clientes referidos por vendedores");

        // NIVEL 6: Algunos clientes refieren a otros clientes (red de clientes)
        $this->command->info("\n📊 NIVEL 6: Creando clientes referidos por otros clientes...");
        $clientesIniciales = $this->clientes;
        $this->crearClientesReferidosPorClientes($clientesIniciales, rand(20, 30));
        $this->command->info("✅ Creados clientes adicionales referidos por otros clientes");

        // Actualizar contadores de referidos
        $this->command->info("\n🔄 Actualizando contadores de referidos...");
        $this->actualizarContadores();

        // CREAR USUARIOS ESPECÍFICOS PARA CADA CATEGORÍA DE LEYENDA
        $this->command->info("\n🎨 Creando usuarios específicos para todas las categorías de leyenda...");
        $this->crearUsuariosParaLeyendas($admin->_id);

        // Crear ventas SOLO para líderes y vendedores
        $this->command->info("\n💰 Generando ventas para líderes y vendedores...");
        $this->generarVentas();

        // Asegurar ventas altas para categorías específicas
        $this->command->info("\n💎 Generando ventas adicionales para categorías TOP...");
        $this->generarVentasParaTopCategorias();

        // Resumen final
        $this->mostrarResumen();
    }

    private function crearAdmin()
    {
        return User::create([
            'name' => 'Administrador',
            'apellidos' => 'Principal',
            'cedula' => '12345678',
            'email' => 'admin@arepallanerita.com',
            'password' => Hash::make('admin123'),
            'telefono' => '3001234567',
            'direccion' => 'Oficina Principal - Arepa la Llanerita',
            'ciudad' => 'Villavicencio',
            'departamento' => 'Meta',
            'fecha_nacimiento' => '1975-01-15',
            'rol' => 'administrador',
            'activo' => true,
            'referido_por' => null,
            'codigo_referido' => 'ADMIN001',
            'total_referidos' => 0,
            'comisiones_ganadas' => 0.00,
            'comisiones_disponibles' => 0.00,
            'meta_mensual' => 10000000.00,
            'ventas_mes_actual' => 0.00,
            'nivel_vendedor' => 0,
            'email_verified_at' => now(),
            'created_at' => now()->subMonths(12),
        ]);
    }

    private function crearLideres($referidoPorId, $cantidad, $nivel)
    {
        $lideres = [];
        
        for ($i = 0; $i < $cantidad; $i++) {
            $nombre = $this->nombres[array_rand($this->nombres)];
            $apellido = $this->apellidos[array_rand($this->apellidos)];
            $cedula = str_pad(rand(10000000, 99999999), 8, '0', STR_PAD_LEFT);
            $codigoReferido = 'LID' . str_pad(rand(100, 9999), 4, '0', STR_PAD_LEFT);
            
            // Calcular antigüedad (más antiguos los de niveles superiores)
            $mesesAntiguedad = rand(6, 24) - ($nivel * 2);
            $fechaCreacion = now()->subMonths($mesesAntiguedad);
            
            // Generar métricas realistas basadas en antigüedad
            $comisionesBase = rand(500000, 2000000);
            $comisionesAjustadas = $comisionesBase * ($mesesAntiguedad / 12);
            
            $lider = User::create([
                'name' => $nombre,
                'apellidos' => $apellido,
                'cedula' => $cedula,
                'email' => strtolower($nombre . '.' . $apellido . rand(1, 999) . '@vendedor.com'),
                'password' => Hash::make('password123'),
                'telefono' => '30' . rand(10000000, 99999999),
                'direccion' => 'Calle ' . rand(1, 100) . ' #' . rand(1, 50) . '-' . rand(1, 99),
                'ciudad' => $this->ciudades[array_rand($this->ciudades)],
                'departamento' => $this->departamento,
                'fecha_nacimiento' => now()->subYears(rand(25, 55))->format('Y-m-d'),
                'rol' => 'lider',
                'activo' => true,
                'referido_por' => $referidoPorId,
                'codigo_referido' => $codigoReferido,
                'total_referidos' => 0, // Se actualizará después
                'comisiones_ganadas' => $comisionesAjustadas,
                'comisiones_disponibles' => rand(100000, 500000),
                'meta_mensual' => rand(800000, 1500000),
                'ventas_mes_actual' => rand(400000, 1200000),
                'nivel_vendedor' => rand(2, 3),
                'zonas_asignadas' => 'Zona ' . chr(65 + $i),
                // Campos adicionales para métricas
                'historial_ventas' => [
                    [
                        'mes' => now()->subMonth(1)->format('Y-m'),
                        'total_ventas' => rand(600000, 1400000),
                        'total_pedidos' => rand(15, 40),
                        'promedio_ticket' => rand(25000, 40000),
                    ],
                    [
                        'mes' => now()->format('Y-m'),
                        'total_ventas' => rand(400000, 1200000),
                        'total_pedidos' => rand(10, 35),
                        'promedio_ticket' => rand(25000, 40000),
                    ],
                ],
                'referidos_data' => [], // Se llenará cuando se agreguen referidos
                'configuracion_personal' => [
                    'notificaciones_email' => true,
                    'notificaciones_sms' => false,
                    'meta_personal' => rand(1000000, 2000000),
                    'dias_laborales' => ['lunes', 'martes', 'miercoles', 'jueves', 'viernes', 'sabado'],
                ],
                'email_verified_at' => $fechaCreacion,
                'created_at' => $fechaCreacion,
                'updated_at' => now()->subDays(rand(1, 30)),
            ]);

            $lideres[] = $lider;
            $this->command->info("  → Líder N{$nivel}: {$nombre} {$apellido} ({$codigoReferido})");
        }

        return $lideres;
    }

    private function crearVendedores($referidoPorId, $cantidad, $tipo)
    {
        $vendedores = [];
        
        for ($i = 0; $i < $cantidad; $i++) {
            $nombre = $this->nombres[array_rand($this->nombres)];
            $apellido = $this->apellidos[array_rand($this->apellidos)];
            $cedula = str_pad(rand(10000000, 99999999), 8, '0', STR_PAD_LEFT);
            $codigoReferido = 'VEN' . str_pad(rand(100, 9999), 4, '0', STR_PAD_LEFT);
            
            // Configurar según tipo
            $config = $this->getConfiguracionPorTipo($tipo);
            $fechaCreacion = now()->subMonths($config['meses_antiguedad']);
            
            // Historial de ventas basado en tipo y antigüedad
            $historialVentas = [];
            for ($mes = 0; $mes < min(3, $config['meses_antiguedad']); $mes++) {
                $ventasMes = rand(
                    (int)($config['ventas_mes_actual'] * 0.7),
                    (int)($config['ventas_mes_actual'] * 1.3)
                );
                $historialVentas[] = [
                    'mes' => now()->subMonths($mes + 1)->format('Y-m'),
                    'total_ventas' => $ventasMes,
                    'total_pedidos' => rand(5, 25),
                    'promedio_ticket' => $ventasMes / rand(5, 25),
                    'productos_vendidos' => rand(10, 60),
                ];
            }
            
            $vendedor = User::create([
                'name' => $nombre,
                'apellidos' => $apellido,
                'cedula' => $cedula,
                'email' => strtolower($nombre . '.' . $apellido . rand(1, 999) . '@cliente.com'),
                'password' => Hash::make('password123'),
                'telefono' => '31' . rand(10000000, 99999999),
                'direccion' => 'Carrera ' . rand(1, 100) . ' #' . rand(1, 50) . '-' . rand(1, 99),
                'ciudad' => $this->ciudades[array_rand($this->ciudades)],
                'departamento' => $this->departamento,
                'fecha_nacimiento' => now()->subYears(rand(20, 50))->format('Y-m-d'),
                'rol' => 'vendedor',
                'activo' => $config['activo'],
                'referido_por' => $referidoPorId,
                'codigo_referido' => $codigoReferido,
                'total_referidos' => 0, // Se actualizará después
                'comisiones_ganadas' => $config['comisiones_ganadas'],
                'comisiones_disponibles' => $config['comisiones_disponibles'],
                'meta_mensual' => $config['meta_mensual'],
                'ventas_mes_actual' => $config['ventas_mes_actual'],
                'nivel_vendedor' => $config['nivel_vendedor'],
                'zonas_asignadas' => $config['zonas_asignadas'],
                // Campos adicionales para métricas
                'historial_ventas' => $historialVentas,
                'referidos_data' => [],
                'configuracion_personal' => [
                    'notificaciones_email' => rand(0, 1) == 1,
                    'notificaciones_sms' => rand(0, 1) == 1,
                    'meta_personal' => $config['meta_mensual'],
                    'horario_preferido' => rand(0, 1) == 1 ? 'mañana' : 'tarde',
                ],
                'email_verified_at' => $fechaCreacion,
                'created_at' => $fechaCreacion,
                'updated_at' => now()->subDays(rand(1, 15)),
            ]);

            $vendedores[] = $vendedor;
        }

        return $vendedores;
    }

    private function getConfiguracionPorTipo($tipo)
    {
        switch ($tipo) {
            case 'activo': // Vendedores muy activos (5-10 referidos)
                return [
                    'activo' => true,
                    'comisiones_ganadas' => rand(300000, 800000),
                    'comisiones_disponibles' => rand(50000, 200000),
                    'meta_mensual' => rand(400000, 800000),
                    'ventas_mes_actual' => rand(250000, 600000),
                    'nivel_vendedor' => rand(1, 2),
                    'zonas_asignadas' => 'Zona Activa',
                    'meses_antiguedad' => rand(4, 18),
                ];

            case 'regular': // Vendedores regulares (1-5 referidos)
                return [
                    'activo' => true,
                    'comisiones_ganadas' => rand(80000, 300000),
                    'comisiones_disponibles' => rand(10000, 80000),
                    'meta_mensual' => rand(150000, 400000),
                    'ventas_mes_actual' => rand(80000, 250000),
                    'nivel_vendedor' => 1,
                    'zonas_asignadas' => null,
                    'meses_antiguedad' => rand(2, 12),
                ];

            case 'cliente': // Clientes/vendedores nuevos (0-1 referidos)
                return [
                    'activo' => rand(0, 1) == 1, // 50% activos
                    'comisiones_ganadas' => rand(0, 50000),
                    'comisiones_disponibles' => rand(0, 15000),
                    'meta_mensual' => rand(50000, 150000),
                    'ventas_mes_actual' => rand(0, 80000),
                    'nivel_vendedor' => 0,
                    'zonas_asignadas' => null,
                    'meses_antiguedad' => rand(0, 6),
                ];

            case 'directo': // Vendedores directos de líderes
                return [
                    'activo' => true,
                    'comisiones_ganadas' => rand(150000, 500000),
                    'comisiones_disponibles' => rand(30000, 150000),
                    'meta_mensual' => rand(250000, 600000),
                    'ventas_mes_actual' => rand(150000, 450000),
                    'nivel_vendedor' => 1,
                    'zonas_asignadas' => 'Directo',
                    'meses_antiguedad' => rand(3, 15),
                ];

            default:
                return [
                    'activo' => true,
                    'comisiones_ganadas' => rand(50000, 200000),
                    'comisiones_disponibles' => rand(10000, 50000),
                    'meta_mensual' => rand(100000, 300000),
                    'ventas_mes_actual' => rand(50000, 200000),
                    'nivel_vendedor' => 0,
                    'zonas_asignadas' => null,
                    'meses_antiguedad' => rand(1, 8),
                ];
        }
    }

    private function actualizarContadores()
    {
        $todosUsuarios = User::all(); // Incluir todos los usuarios (admin, lideres, vendedores y clientes)

        foreach ($todosUsuarios as $usuario) {
            $referidosDirectos = User::where('referido_por', $usuario->_id)->count();
            
            if ($referidosDirectos > 0) {
                $usuario->update(['total_referidos' => $referidosDirectos]);
                $this->command->info("  → {$usuario->name} ({$usuario->rol}): {$referidosDirectos} referidos");
            }
        }
    }

    private function generarVentas()
    {
        // Obtener SOLO vendedores y líderes (NO clientes)
        $usuarios = User::whereIn('rol', ['lider', 'vendedor'])
            ->where('activo', true)
            ->get();

        $ventasCreadas = 0;
        $detallesCreados = 0;

        foreach ($usuarios as $usuario) {
            // Cantidad de ventas según nivel (ajustado para pruebas reales)
            $cantidadVentas = 0;
            
            if ($usuario->rol === 'lider') {
                $cantidadVentas = rand(8, 15); // Líderes generan más ventas
            } elseif ($usuario->ventas_mes_actual > 300000) {
                $cantidadVentas = rand(5, 10); // Vendedores muy activos
            } elseif ($usuario->ventas_mes_actual > 100000) {
                $cantidadVentas = rand(3, 7); // Vendedores activos
            } else {
                $cantidadVentas = rand(1, 4); // Vendedores regulares
            }

            for ($i = 0; $i < $cantidadVentas; $i++) {
                // Seleccionar un cliente aleatorio (pueden comprar a cualquier vendedor/líder)
                if (empty($this->clientes)) {
                    break; // Si no hay clientes, salir
                }
                
                $cliente = $this->clientes[array_rand($this->clientes)];
                
                // Generar número de pedido único
                $numeroPedido = 'PED-' . date('Ymd') . '-' . str_pad($ventasCreadas + 1, 6, '0', STR_PAD_LEFT);
                
                // Seleccionar productos aleatorios (1-5 productos por pedido para más realismo)
                $cantidadProductos = rand(1, 5);
                $productosDelPedido = $this->productos->random(min($cantidadProductos, $this->productos->count()));
                
                $totalPedido = 0;
                $detalles = [];
                $totalProductosVendidos = 0;
                
                // Calcular total y preparar detalles
                foreach ($productosDelPedido as $producto) {
                    $cantidad = rand(1, 4); // Más unidades por producto
                    $precioUnitario = $producto->precio;
                    $subtotal = $cantidad * $precioUnitario;
                    $totalPedido += $subtotal;
                    $totalProductosVendidos += $cantidad;
                    
                    // Obtener el nombre de la categoría con mejor manejo
                    $categoriaNombre = 'Sin categoría';
                    $categoriaData = null;
                    
                    if (isset($producto->categoria_data['nombre'])) {
                        $categoriaNombre = $producto->categoria_data['nombre'];
                        $categoriaData = $producto->categoria_data;
                    } elseif ($producto->categoria_id) {
                        $categoria = Categoria::find($producto->categoria_id);
                        if ($categoria) {
                            $categoriaNombre = $categoria->nombre;
                            $categoriaData = [
                                '_id' => $categoria->_id,
                                'nombre' => $categoria->nombre,
                                'descripcion' => $categoria->descripcion ?? null,
                            ];
                        }
                    }
                    
                    $detalles[] = [
                        'producto_id' => $producto->_id,
                        'producto_data' => [
                            'nombre' => $producto->nombre,
                            'precio' => $producto->precio,
                            'sku' => $producto->sku ?? null,
                            'categoria' => $categoriaNombre,
                            'categoria_data' => $categoriaData,
                            'imagen' => $producto->imagen ?? null,
                            'descripcion' => $producto->descripcion ?? null,
                        ],
                        'cantidad' => $cantidad,
                        'precio_unitario' => $precioUnitario,
                        'subtotal' => $subtotal,
                        'descuento_aplicado' => 0,
                    ];
                }
                
                // Estados posibles con pesos (más común = más peso)
                $estadosPosibles = [
                    'pendiente' => 10,
                    'confirmado' => 15,
                    'en_preparacion' => 12,
                    'listo' => 8,
                    'en_camino' => 10,
                    'entregado' => 30, // Más pedidos entregados
                    'cancelado' => 3,
                ];
                
                $estadoSeleccionado = $this->seleccionarEstadoPonderado($estadosPosibles);
                $fechaCreacion = now()->subDays(rand(1, 60));
                
                // Generar historial de estados basado en el estado actual
                $historialEstados = $this->generarHistorialEstados($estadoSeleccionado, $fechaCreacion);
                
                // Calcular fecha de última actualización coherente con el historial
                $fechaUltimaActualizacion = $fechaCreacion->copy();
                if (!empty($historialEstados)) {
                    $ultimoEstado = end($historialEstados);
                    if (isset($ultimoEstado['fecha'])) {
                        $fechaUltimaActualizacion = Carbon::parse($ultimoEstado['fecha']);
                    }
                }
                
                // Si el pedido está entregado, la actualización es la fecha de entrega
                // Si no, puede tener actualizaciones más recientes
                if ($estadoSeleccionado !== 'entregado' && $estadoSeleccionado !== 'cancelado') {
                    $fechaUltimaActualizacion = now()->subDays(rand(0, 10));
                }
                
                // Aplicar descuento ocasional (20% de probabilidad)
                $descuento = 0;
                if (rand(1, 100) <= 20) {
                    $descuento = $totalPedido * (rand(5, 15) / 100); // 5-15% descuento
                }
                
                $totalFinal = $totalPedido - $descuento;
                
                // Crear el pedido con datos completos (detalles embebidos)
                $pedido = Pedido::create([
                    'numero_pedido' => $numeroPedido,
                    'user_id' => $cliente->_id,
                    'cliente_data' => [
                        'nombre' => $cliente->name . ' ' . $cliente->apellidos,
                        'email' => $cliente->email,
                        'telefono' => $cliente->telefono,
                        'cedula' => $cliente->cedula,
                        'ciudad' => $cliente->ciudad,
                        'direccion' => $cliente->direccion,
                    ],
                    'vendedor_id' => $usuario->_id,
                    'vendedor_data' => [
                        'nombre' => $usuario->name . ' ' . $usuario->apellidos,
                        'email' => $usuario->email,
                        'codigo_referido' => $usuario->codigo_referido,
                        'telefono' => $usuario->telefono,
                        'rol' => $usuario->rol,
                        'nivel' => $usuario->nivel_vendedor,
                    ],
                    'direccion_entrega' => $cliente->direccion,
                    'telefono_entrega' => $cliente->telefono,
                    'estado' => $estadoSeleccionado,
                    'subtotal' => $totalPedido,
                    'total' => $totalPedido,
                    'descuento' => $descuento,
                    'total_final' => $totalFinal,
                    'notas' => rand(0, 100) < 30 ? 'Pedido generado automáticamente - Cliente preferencial' : null,
                    'metodo_pago' => ['efectivo', 'transferencia', 'nequi', 'daviplata', 'tarjeta'][array_rand(['efectivo', 'transferencia', 'nequi', 'daviplata', 'tarjeta'])],
                    'detalles' => $detalles, // Detalles embebidos en el pedido
                    'historial_estados' => $historialEstados,
                    'datos_entrega' => [
                        'conductor' => $estadoSeleccionado === 'en_camino' || $estadoSeleccionado === 'entregado' ? 'Conductor-' . rand(1, 10) : null,
                        'tiempo_estimado' => rand(20, 60) . ' minutos',
                        'instrucciones_especiales' => rand(0, 100) < 20 ? 'Llamar al llegar' : null,
                    ],
                    'comisiones_calculadas' => [
                        [
                            'vendedor_id' => $usuario->_id,
                            'tipo' => 'venta_directa',
                            'porcentaje' => 10,
                            'monto' => $totalFinal * 0.10,
                        ]
                    ],
                    'cantidad_productos' => count($detalles),
                    'total_unidades' => $totalProductosVendidos,
                    'created_at' => $fechaCreacion,
                    'updated_at' => $fechaUltimaActualizacion,
                ]);

                $ventasCreadas++;
                $detallesCreados += count($detalles);
            }
        }

        $this->command->info("✅ Creadas {$ventasCreadas} ventas con {$detallesCreados} líneas de productos");
        $this->command->info("   📦 Solo líderes y vendedores generan ventas (8-15 por líder)");
        $this->command->info("   💼 Vendedores activos: 5-10 ventas c/u");
        $this->command->info("   📊 Vendedores regulares: 1-7 ventas c/u");
        $this->command->info("   🛒 Clientes solo realizan compras (no generan ventas)");
        $this->command->info("   📦 Cada pedido tiene 1-5 productos con 1-4 unidades c/u");
    }

    private function verificarYCrearProductos()
    {
        // Cargar productos existentes con sus categorías embebidas
        $this->productos = Producto::where('activo', true)->get();
        
        if ($this->productos->isEmpty()) {
            $this->command->error('❌ No hay productos activos en la base de datos.');
            $this->command->error('   Por favor ejecuta: php artisan db:seed --class=ProductosSeeder');
            throw new \Exception('No hay productos disponibles para generar ventas.');
        }
        
        // Verificar que los productos tengan categorías embebidas
        $productosSinCategoria = $this->productos->filter(function($producto) {
            return empty($producto->categoria_data) && empty($producto->categoria_id);
        });
        
        if ($productosSinCategoria->count() > 0) {
            $this->command->warn("⚠️  {$productosSinCategoria->count()} productos sin categoría embebida. Embebiendo datos...");
            
            foreach ($productosSinCategoria as $producto) {
                if ($producto->categoria_id) {
                    $categoria = Categoria::find($producto->categoria_id);
                    if ($categoria) {
                        $producto->categoria_data = [
                            '_id' => $categoria->_id,
                            'nombre' => $categoria->nombre,
                            'descripcion' => $categoria->descripcion ?? null,
                            'slug' => $categoria->slug ?? null,
                        ];
                        $producto->save();
                    }
                }
            }
            
            // Recargar productos
            $this->productos = Producto::where('activo', true)->get();
        }
        
        $countCategorias = Categoria::where('activo', true)->count();
        
        $this->command->info("✅ {$this->productos->count()} productos y {$countCategorias} categorías encontrados");
        $this->command->info("   (Usando productos existentes con imágenes y categorías embebidas)");
    }

    private function crearClientesReales($vendedores, $cantidad)
    {
        for ($i = 0; $i < $cantidad; $i++) {
            $nombre = $this->nombres[array_rand($this->nombres)];
            $apellido = $this->apellidos[array_rand($this->apellidos)];
            $cedula = str_pad(rand(10000000, 99999999), 8, '0', STR_PAD_LEFT);
            
            // Los clientes son referidos por vendedores
            $referidoPor = !empty($vendedores) ? $vendedores[array_rand($vendedores)]->_id : null;
            
            $cliente = User::create([
                'name' => $nombre,
                'apellidos' => $apellido,
                'cedula' => $cedula,
                'email' => strtolower($nombre . '.' . $apellido . rand(1, 9999) . '@cliente.com'),
                'password' => Hash::make('password123'),
                'telefono' => '32' . rand(10000000, 99999999),
                'direccion' => 'Calle ' . rand(1, 100) . ' #' . rand(1, 50) . '-' . rand(1, 99),
                'ciudad' => $this->ciudades[array_rand($this->ciudades)],
                'departamento' => $this->departamento,
                'fecha_nacimiento' => now()->subYears(rand(18, 65))->format('Y-m-d'),
                'rol' => 'cliente',
                'activo' => true,
                'referido_por' => $referidoPor,
                'codigo_referido' => null, // Los clientes no tienen código de referido
                'total_referidos' => 0,
                'comisiones_ganadas' => 0,
                'comisiones_disponibles' => 0,
                'meta_mensual' => 0,
                'ventas_mes_actual' => 0,
                'nivel_vendedor' => 0,
                'email_verified_at' => now()->subDays(rand(1, 180)),
                'created_at' => now()->subDays(rand(1, 180)),
                'updated_at' => now()->subDays(rand(0, 30)),
            ]);

            $this->clientes[] = $cliente;
        }
    }

    private function crearClientesReferidosPorClientes($clientesReferidores, $cantidad)
    {
        for ($i = 0; $i < $cantidad; $i++) {
            $nombre = $this->nombres[array_rand($this->nombres)];
            $apellido = $this->apellidos[array_rand($this->apellidos)];
            $cedula = str_pad(rand(10000000, 99999999), 8, '0', STR_PAD_LEFT);
            
            // Estos clientes son referidos por otros clientes
            $referidoPor = !empty($clientesReferidores) ? $clientesReferidores[array_rand($clientesReferidores)]->_id : null;
            
            $cliente = User::create([
                'name' => $nombre,
                'apellidos' => $apellido,
                'cedula' => $cedula,
                'email' => strtolower($nombre . '.' . $apellido . rand(1, 9999) . '@cliente.com'),
                'password' => Hash::make('password123'),
                'telefono' => '32' . rand(10000000, 99999999),
                'direccion' => 'Carrera ' . rand(1, 100) . ' #' . rand(1, 50) . '-' . rand(1, 99),
                'ciudad' => $this->ciudades[array_rand($this->ciudades)],
                'departamento' => $this->departamento,
                'fecha_nacimiento' => now()->subYears(rand(18, 65))->format('Y-m-d'),
                'rol' => 'cliente',
                'activo' => true,
                'referido_por' => $referidoPor,
                'codigo_referido' => null, // Los clientes no tienen código de referido
                'total_referidos' => 0,
                'comisiones_ganadas' => 0,
                'comisiones_disponibles' => 0,
                'meta_mensual' => 0,
                'ventas_mes_actual' => 0,
                'nivel_vendedor' => 0,
                'email_verified_at' => now()->subDays(rand(1, 90)),
                'created_at' => now()->subDays(rand(1, 90)),
                'updated_at' => now()->subDays(rand(0, 15)),
            ]);

            $this->clientes[] = $cliente;
        }
    }

    private function mostrarResumen()
    {
        $this->command->info("\n");
        $this->command->info("╔════════════════════════════════════════════════════════╗");
        $this->command->info("║     🎉 RED MLM COMPACTA CREADA EXITOSAMENTE 🎉       ║");
        $this->command->info("╚════════════════════════════════════════════════════════╝");
        $this->command->info("");

        // Contar líneas de productos en pedidos (embebidos)
        $totalLineasProductos = 0;
        $pedidos = Pedido::all();
        foreach ($pedidos as $pedido) {
            $totalLineasProductos += count($pedido->detalles ?? []);
        }

        $stats = [
            'total_usuarios' => User::count(),
            'administradores' => User::where('rol', 'administrador')->count(),
            'lideres' => User::where('rol', 'lider')->count(),
            'vendedores' => User::where('rol', 'vendedor')->count(),
            'clientes' => User::where('rol', 'cliente')->count(),
            'activos' => User::where('activo', true)->whereIn('rol', ['lider', 'vendedor'])->count(),
            'con_referidos' => User::where('total_referidos', '>', 0)->count(),
            'clientes_con_referidos' => User::where('rol', 'cliente')->where('total_referidos', '>', 0)->count(),
            'top_ventas' => User::where('total_referidos', '>=', 20)->count(),
            'top_referidos' => User::whereBetween('total_referidos', [10, 19])->count(),
            'vendedores_activos' => User::whereBetween('total_referidos', [5, 9])->count(),
            'vendedores_regulares' => User::whereBetween('total_referidos', [1, 4])->count(),
            'inactivos' => User::where('total_referidos', 0)->whereIn('rol', ['lider', 'vendedor'])->count(),
            'total_pedidos' => Pedido::count(),
            'total_lineas_productos' => $totalLineasProductos,
            'valor_total_ventas' => to_float(Pedido::sum('total_final')), // Convertir a float
            'productos_disponibles' => Producto::where('activo', true)->count(),
        ];

        $this->command->table(
            ['Métrica', 'Valor'],
            [
                ['Total Usuarios en Red', $stats['total_usuarios']],
                ['├─ Administradores', $stats['administradores']],
                ['├─ Líderes', $stats['lideres']],
                ['├─ Vendedores', $stats['vendedores']],
                ['└─ Clientes', $stats['clientes']],
                ['', ''],
                ['Usuarios Activos (L+V)', $stats['activos']],
                ['Usuarios con Referidos', $stats['con_referidos']],
                ['Clientes con Referidos', $stats['clientes_con_referidos'] . ' (visibles en red)'],
                ['', ''],
                ['🏆 Top Ventas (+20 ref.)', $stats['top_ventas']],
                ['⭐ Top Referidos (10-20)', $stats['top_referidos']],
                ['✅ Vendedores Activos (5-10)', $stats['vendedores_activos']],
                ['👤 Vendedores (1-4)', $stats['vendedores_regulares']],
                ['❌ Sin Referidos', $stats['inactivos']],
                ['', ''],
                ['💰 Total Pedidos', $stats['total_pedidos']],
                ['📦 Líneas de Productos', $stats['total_lineas_productos'] . ' (embebidos)'],
                ['💵 Valor Total Ventas', '$' . number_format($stats['valor_total_ventas'], 0, ',', '.')],
                ['🏪 Productos Disponibles', $stats['productos_disponibles']],
            ]
        );

        $this->command->info("\n📊 Usuarios con más referidos:");
        $topReferidores = User::whereIn('rol', ['lider', 'vendedor'])
            ->orderBy('total_referidos', 'desc')
            ->take(10)
            ->get(['name', 'apellidos', 'rol', 'total_referidos', 'codigo_referido']);

        foreach ($topReferidores as $index => $usuario) {
            $emoji = $index == 0 ? '🥇' : ($index == 1 ? '🥈' : ($index == 2 ? '🥉' : '  '));
            $this->command->info("  {$emoji} {$usuario->name} {$usuario->apellidos} ({$usuario->rol}) - {$usuario->total_referidos} referidos - {$usuario->codigo_referido}");
        }
        
        // Mostrar clientes con referidos
        $this->command->info("\n👥 Clientes que también refieren:");
        $clientesReferidores = User::where('rol', 'cliente')
            ->where('total_referidos', '>', 0)
            ->orderBy('total_referidos', 'desc')
            ->take(5)
            ->get(['name', 'apellidos', 'total_referidos']);
            
        if ($clientesReferidores->count() > 0) {
            foreach ($clientesReferidores as $cliente) {
                $this->command->info("  • {$cliente->name} {$cliente->apellidos} - {$cliente->total_referidos} cliente(s) referido(s)");
            }
        } else {
            $this->command->info("  (No hay clientes con referidos)");
        }

        $this->command->info("\n📈 Top 5 Productos Más Vendidos:");
        
        // Calcular productos más vendidos desde los detalles embebidos
        $productosVendidos = [];
        foreach ($pedidos as $pedido) {
            foreach ($pedido->detalles ?? [] as $detalle) {
                $productoId = $detalle['producto_id'] ?? null;
                if ($productoId) {
                    if (!isset($productosVendidos[$productoId])) {
                        $productosVendidos[$productoId] = [
                            'cantidad' => 0,
                            'nombre' => $detalle['producto_data']['nombre'] ?? 'Producto',
                        ];
                    }
                    $productosVendidos[$productoId]['cantidad'] += $detalle['cantidad'] ?? 0;
                }
            }
        }
        
        // Ordenar y tomar top 5
        uasort($productosVendidos, function($a, $b) {
            return $b['cantidad'] <=> $a['cantidad'];
        });
        
        $topProductos = array_slice($productosVendidos, 0, 5, true);
        
        $index = 1;
        foreach ($topProductos as $data) {
            $this->command->info("  {$index}. {$data['nombre']} - {$data['cantidad']} unidades");
            $index++;
        }

        $this->command->info("\n🎨 CATEGORÍAS DE LEYENDA IMPLEMENTADAS:");
        $this->command->info("  🏆 TOP VENTAS (>$5M ventas) - Color #DC143C: " . 
            User::where('comisiones_ganadas', '>=', 5000000)->count());
        $this->command->info("  ⭐ RED GRANDE (>20 ref.) - Color #8B0000: " . 
            User::where('total_referidos', '>=', 20)->count());
        $this->command->info("  💰 VENTAS ALTAS ($2M-$5M) - Color #B8860B: " . 
            User::whereBetween('comisiones_ganadas', [2000000, 4999999])->count());
        $this->command->info("  🌟 CLIENTE TOP REFERIDOR (5+ ref.) - Color #4169E1: " . 
            User::where('rol', 'cliente')->where('total_referidos', '>=', 5)->count());
        $this->command->info("  ✅ RED ACTIVA (5-10 ref.) - Color #A8556A: " . 
            User::where('rol', '!=', 'cliente')->whereBetween('total_referidos', [5, 10])->count());
        $this->command->info("  👑 LÍDER (rol líder) - Color #722F37: " . 
            User::where('rol', 'lider')->count());
        $this->command->info("  👤 VENDEDOR (1-4 ref.) - Color #C89FA6: " . 
            User::where('rol', 'vendedor')->whereBetween('total_referidos', [1, 4])->count());
        $this->command->info("  🛒 CLIENTE con referidos (1-4) - Color #87CEEB: " . 
            User::where('rol', 'cliente')->whereBetween('total_referidos', [1, 4])->count());
        $this->command->info("  ❌ INACTIVO (0 ref.) - Color #E8D5D9: " . 
            User::where('total_referidos', 0)->whereIn('rol', ['vendedor', 'lider'])->count());

        $this->command->info("\n✅ RECORDATORIO:");
        $this->command->info("  • Solo líderes y vendedores GENERAN ventas");
        $this->command->info("  • Los clientes solo COMPRAN (aparecen en pedidos)");
        $this->command->info("  • Los clientes SÍ aparecen en la red cuando tienen referidos");
        $this->command->info("  • Clientes pueden referir a otros clientes");
        $this->command->info("  • TODAS las categorías de leyenda están representadas (10 categorías)");
        $this->command->info("  • Red optimizada para pruebas completas del sistema");
        
        $this->command->info("\n✨ ¡Red MLM lista para visualizar en: http://127.0.0.1:8000/admin/referidos");
        $this->command->info("");
    }

    private function seleccionarEstadoPonderado($estadosPosibles)
    {
        $total = array_sum($estadosPosibles);
        $random = rand(1, $total);
        
        $acumulado = 0;
        foreach ($estadosPosibles as $estado => $peso) {
            $acumulado += $peso;
            if ($random <= $acumulado) {
                return $estado;
            }
        }
        
        return 'pendiente'; // Por defecto
    }

    private function generarHistorialEstados($estadoActual, $fechaInicio)
    {
        $historial = [];
        $fecha = $fechaInicio->copy();
        
        // Orden lógico de estados
        $flujoEstados = [
            'pendiente' => ['horas' => 0],
            'confirmado' => ['horas' => rand(1, 6)],
            'en_preparacion' => ['horas' => rand(2, 12)],
            'listo' => ['horas' => rand(1, 3)],
            'en_camino' => ['horas' => rand(1, 2)],
            'entregado' => ['horas' => rand(1, 4)],
        ];
        
        // Generar historial hasta el estado actual
        foreach ($flujoEstados as $estado => $config) {
            $historial[] = [
                'estado' => $estado,
                'fecha' => $fecha->toDateTimeString(),
                'usuario' => $estado === 'pendiente' ? 'Sistema' : 'Usuario-' . rand(1, 5),
                'comentario' => $this->getComentarioEstado($estado),
            ];
            
            $fecha->addHours($config['horas']);
            
            // Detener si llegamos al estado actual
            if ($estado === $estadoActual) {
                break;
            }
        }
        
        // Si es cancelado, agregar solo pendiente y cancelado
        if ($estadoActual === 'cancelado') {
            $historial = [
                [
                    'estado' => 'pendiente',
                    'fecha' => $fechaInicio->toDateTimeString(),
                    'usuario' => 'Sistema',
                    'comentario' => 'Pedido creado',
                ],
                [
                    'estado' => 'cancelado',
                    'fecha' => $fechaInicio->addHours(rand(1, 24))->toDateTimeString(),
                    'usuario' => 'Cliente',
                    'comentario' => 'Cancelado por el cliente',
                ],
            ];
        }
        
        return $historial;
    }

    private function getComentarioEstado($estado)
    {
        $comentarios = [
            'pendiente' => 'Pedido creado y en espera de confirmación',
            'confirmado' => 'Pedido confirmado por el vendedor',
            'en_preparacion' => 'Pedido en preparación en cocina',
            'listo' => 'Pedido listo para entrega',
            'en_camino' => 'Pedido en camino al cliente',
            'entregado' => 'Pedido entregado exitosamente',
            'cancelado' => 'Pedido cancelado',
        ];
        
        return $comentarios[$estado] ?? 'Estado actualizado';
    }

    /**
     * Crear usuarios específicos para TODAS las categorías de leyenda del MLM
     * Garantiza que al menos tengamos usuarios en cada categoría de color
     */
    private function crearUsuariosParaLeyendas($adminId)
    {
        // 1. TOP VENTAS (Más de $5,000,000 en ventas totales) - Color #DC143C
        $this->command->info("  🏆 Creando usuarios TOP VENTAS (>$5M en ventas)...");
        $topVentas = $this->crearUsuarioTopVentas($adminId);
        $this->agregarReferidosAUsuario($topVentas->_id, rand(25, 35), 'cliente'); // >20 referidos
        
        // 2. RED GRANDE (Más de 20 referidos directos) - Color #8B0000
        $this->command->info("  ⭐ Creando usuarios RED GRANDE (>20 referidos)...");
        $redGrande = $this->crearUsuarioRedGrande($adminId);
        $this->agregarReferidosAUsuario($redGrande->_id, rand(22, 30), 'vendedor');
        
        // 3. VENTAS ALTAS (Entre $2M - $5M en ventas) - Color #B8860B
        $this->command->info("  💰 Creando usuarios VENTAS ALTAS ($2M-$5M)...");
        $ventasAltas = $this->crearUsuarioVentasAltas($adminId);
        $this->agregarReferidosAUsuario($ventasAltas->_id, rand(12, 18), 'vendedor');
        
        // 4. CLIENTE TOP REFERIDOR (Cliente con 5+ referidos) - Color #4169E1 ⭐ NUEVO
        $this->command->info("  🌟 Creando CLIENTES TOP REFERIDORES (5+ ref.)...");
        for ($i = 0; $i < 2; $i++) {
            $clienteTop = $this->crearClienteTopReferidor($adminId);
            $this->agregarReferidosAUsuario($clienteTop->_id, rand(5, 10), 'cliente');
        }
        
        // 5. RED ACTIVA (Entre 5-10 referidos directos) - Color #A8556A
        $this->command->info("  ✅ Creando usuarios RED ACTIVA (5-10 referidos)...");
        for ($i = 0; $i < 2; $i++) {
            $redActiva = $this->crearUsuarioRedActiva($adminId);
            $this->agregarReferidosAUsuario($redActiva->_id, rand(5, 10), 'cliente');
        }
        
        // 6. VENDEDOR (Entre 1-4 referidos directos) - Color #C89FA6
        $this->command->info("  👤 Vendedores con 1-4 referidos ya creados en niveles anteriores");
        
        // 7. CLIENTE CON REFERIDOS (Para que aparezca en la red) - Color #87CEEB ⭐ MEJORADO
        $this->command->info("  🛒 Clientes con 1-4 referidos ya creados en nivel 6");
        
        // 8. INACTIVO/SIN REFERIDOS - Color #E8D5D9
        $this->command->info("  ❌ Creando usuarios INACTIVOS (sin referidos)...");
        for ($i = 0; $i < 3; $i++) {
            $this->crearUsuarioInactivo($adminId);
        }
        
        $this->command->info("  ✨ Usuarios para todas las categorías de leyenda creados!");
    }

    private function crearUsuarioTopVentas($referidoPorId)
    {
        $nombre = $this->nombres[array_rand($this->nombres)];
        $apellido = $this->apellidos[array_rand($this->apellidos)];
        $cedula = str_pad(rand(10000000, 99999999), 8, '0', STR_PAD_LEFT);
        $codigoReferido = 'TOP' . str_pad(rand(100, 999), 3, '0', STR_PAD_LEFT);
        
        return User::create([
            'name' => $nombre,
            'apellidos' => $apellido,
            'cedula' => $cedula,
            'email' => strtolower($nombre . '.' . $apellido . '.top@vendedor.com'),
            'password' => Hash::make('password123'),
            'telefono' => '30' . rand(10000000, 99999999),
            'direccion' => 'Av. Principal ' . rand(1, 100) . ' - Oficina TOP',
            'ciudad' => 'Villavicencio',
            'departamento' => $this->departamento,
            'fecha_nacimiento' => now()->subYears(rand(30, 50))->format('Y-m-d'),
            'rol' => 'lider',
            'activo' => true,
            'referido_por' => $referidoPorId,
            'codigo_referido' => $codigoReferido,
            'total_referidos' => 0,
            'comisiones_ganadas' => rand(3000000, 5000000),
            'comisiones_disponibles' => rand(500000, 1000000),
            'meta_mensual' => 2500000,
            'ventas_mes_actual' => rand(2000000, 3000000),
            'nivel_vendedor' => 3,
            'zonas_asignadas' => 'Zona Premium',
            'historial_ventas' => [
                ['mes' => now()->subMonth(2)->format('Y-m'), 'total_ventas' => rand(4000000, 6000000), 'total_pedidos' => rand(80, 120)],
                ['mes' => now()->subMonth(1)->format('Y-m'), 'total_ventas' => rand(4500000, 6500000), 'total_pedidos' => rand(90, 130)],
                ['mes' => now()->format('Y-m'), 'total_ventas' => rand(5000000, 7000000), 'total_pedidos' => rand(100, 150)],
            ],
            'email_verified_at' => now()->subYears(2),
            'created_at' => now()->subYears(2),
        ]);
    }

    private function crearUsuarioRedGrande($referidoPorId)
    {
        $nombre = $this->nombres[array_rand($this->nombres)];
        $apellido = $this->apellidos[array_rand($this->apellidos)];
        $cedula = str_pad(rand(10000000, 99999999), 8, '0', STR_PAD_LEFT);
        $codigoReferido = 'BIG' . str_pad(rand(100, 999), 3, '0', STR_PAD_LEFT);
        
        return User::create([
            'name' => $nombre,
            'apellidos' => $apellido,
            'cedula' => $cedula,
            'email' => strtolower($nombre . '.' . $apellido . '.big@vendedor.com'),
            'password' => Hash::make('password123'),
            'telefono' => '31' . rand(10000000, 99999999),
            'direccion' => 'Calle Grande ' . rand(1, 100),
            'ciudad' => $this->ciudades[array_rand($this->ciudades)],
            'departamento' => $this->departamento,
            'fecha_nacimiento' => now()->subYears(rand(28, 48))->format('Y-m-d'),
            'rol' => 'lider',
            'activo' => true,
            'referido_por' => $referidoPorId,
            'codigo_referido' => $codigoReferido,
            'total_referidos' => 0,
            'comisiones_ganadas' => rand(1500000, 3000000),
            'comisiones_disponibles' => rand(300000, 700000),
            'meta_mensual' => 1800000,
            'ventas_mes_actual' => rand(1200000, 1800000),
            'nivel_vendedor' => 3,
            'zonas_asignadas' => 'Zona Expansión',
            'historial_ventas' => [
                ['mes' => now()->subMonth(1)->format('Y-m'), 'total_ventas' => rand(2000000, 3000000), 'total_pedidos' => rand(50, 80)],
                ['mes' => now()->format('Y-m'), 'total_ventas' => rand(2500000, 3500000), 'total_pedidos' => rand(60, 90)],
            ],
            'email_verified_at' => now()->subMonths(18),
            'created_at' => now()->subMonths(18),
        ]);
    }

    private function crearUsuarioVentasAltas($referidoPorId)
    {
        $nombre = $this->nombres[array_rand($this->nombres)];
        $apellido = $this->apellidos[array_rand($this->apellidos)];
        $cedula = str_pad(rand(10000000, 99999999), 8, '0', STR_PAD_LEFT);
        $codigoReferido = 'HIG' . str_pad(rand(100, 999), 3, '0', STR_PAD_LEFT);
        
        return User::create([
            'name' => $nombre,
            'apellidos' => $apellido,
            'cedula' => $cedula,
            'email' => strtolower($nombre . '.' . $apellido . '.high@vendedor.com'),
            'password' => Hash::make('password123'),
            'telefono' => '30' . rand(10000000, 99999999),
            'direccion' => 'Diagonal ' . rand(1, 100),
            'ciudad' => $this->ciudades[array_rand($this->ciudades)],
            'departamento' => $this->departamento,
            'fecha_nacimiento' => now()->subYears(rand(26, 45))->format('Y-m-d'),
            'rol' => 'vendedor',
            'activo' => true,
            'referido_por' => $referidoPorId,
            'codigo_referido' => $codigoReferido,
            'total_referidos' => 0,
            'comisiones_ganadas' => rand(800000, 1500000),
            'comisiones_disponibles' => rand(200000, 400000),
            'meta_mensual' => 1200000,
            'ventas_mes_actual' => rand(900000, 1300000),
            'nivel_vendedor' => 2,
            'zonas_asignadas' => 'Zona High',
            'historial_ventas' => [
                ['mes' => now()->subMonth(1)->format('Y-m'), 'total_ventas' => rand(2200000, 3000000), 'total_pedidos' => rand(40, 60)],
                ['mes' => now()->format('Y-m'), 'total_ventas' => rand(2500000, 3500000), 'total_pedidos' => rand(45, 70)],
            ],
            'email_verified_at' => now()->subMonths(14),
            'created_at' => now()->subMonths(14),
        ]);
    }

    private function crearUsuarioRedActiva($referidoPorId)
    {
        $nombre = $this->nombres[array_rand($this->nombres)];
        $apellido = $this->apellidos[array_rand($this->apellidos)];
        $cedula = str_pad(rand(10000000, 99999999), 8, '0', STR_PAD_LEFT);
        $codigoReferido = 'ACT' . str_pad(rand(100, 999), 3, '0', STR_PAD_LEFT);
        
        return User::create([
            'name' => $nombre,
            'apellidos' => $apellido,
            'cedula' => $cedula,
            'email' => strtolower($nombre . '.' . $apellido . '.active@vendedor.com'),
            'password' => Hash::make('password123'),
            'telefono' => '31' . rand(10000000, 99999999),
            'direccion' => 'Carrera ' . rand(1, 100),
            'ciudad' => $this->ciudades[array_rand($this->ciudades)],
            'departamento' => $this->departamento,
            'fecha_nacimiento' => now()->subYears(rand(24, 42))->format('Y-m-d'),
            'rol' => 'vendedor',
            'activo' => true,
            'referido_por' => $referidoPorId,
            'codigo_referido' => $codigoReferido,
            'total_referidos' => 0,
            'comisiones_ganadas' => rand(300000, 700000),
            'comisiones_disponibles' => rand(80000, 150000),
            'meta_mensual' => 600000,
            'ventas_mes_actual' => rand(400000, 600000),
            'nivel_vendedor' => 1,
            'zonas_asignadas' => 'Zona Activa',
            'historial_ventas' => [
                ['mes' => now()->subMonth(1)->format('Y-m'), 'total_ventas' => rand(500000, 800000), 'total_pedidos' => rand(15, 30)],
                ['mes' => now()->format('Y-m'), 'total_ventas' => rand(600000, 900000), 'total_pedidos' => rand(20, 35)],
            ],
            'email_verified_at' => now()->subMonths(10),
            'created_at' => now()->subMonths(10),
        ]);
    }

    private function crearUsuarioInactivo($referidoPorId)
    {
        $nombre = $this->nombres[array_rand($this->nombres)];
        $apellido = $this->apellidos[array_rand($this->apellidos)];
        $cedula = str_pad(rand(10000000, 99999999), 8, '0', STR_PAD_LEFT);
        $codigoReferido = 'NEW' . str_pad(rand(100, 999), 3, '0', STR_PAD_LEFT);
        
        return User::create([
            'name' => $nombre,
            'apellidos' => $apellido,
            'cedula' => $cedula,
            'email' => strtolower($nombre . '.' . $apellido . '.new@vendedor.com'),
            'password' => Hash::make('password123'),
            'telefono' => '32' . rand(10000000, 99999999),
            'direccion' => 'Calle ' . rand(1, 100),
            'ciudad' => $this->ciudades[array_rand($this->ciudades)],
            'departamento' => $this->departamento,
            'fecha_nacimiento' => now()->subYears(rand(20, 35))->format('Y-m-d'),
            'rol' => 'vendedor',
            'activo' => rand(0, 1) == 1, // 50% activos
            'referido_por' => $referidoPorId,
            'codigo_referido' => $codigoReferido,
            'total_referidos' => 0, // Sin referidos = inactivo
            'comisiones_ganadas' => rand(0, 50000),
            'comisiones_disponibles' => rand(0, 10000),
            'meta_mensual' => 200000,
            'ventas_mes_actual' => rand(0, 100000),
            'nivel_vendedor' => 0,
            'zonas_asignadas' => null,
            'historial_ventas' => [],
            'email_verified_at' => now()->subMonths(rand(1, 3)),
            'created_at' => now()->subMonths(rand(1, 3)),
        ]);
    }

    private function crearClienteTopReferidor($referidoPorId)
    {
        $nombre = $this->nombres[array_rand($this->nombres)];
        $apellido = $this->apellidos[array_rand($this->apellidos)];
        $cedula = str_pad(rand(10000000, 99999999), 8, '0', STR_PAD_LEFT);
        
        $cliente = User::create([
            'name' => $nombre,
            'apellidos' => $apellido,
            'cedula' => $cedula,
            'email' => strtolower($nombre . '.' . $apellido . '.topcli@cliente.com'),
            'password' => Hash::make('password123'),
            'telefono' => '32' . rand(10000000, 99999999),
            'direccion' => 'Avenida ' . rand(1, 100) . ' #' . rand(1, 50) . '-' . rand(1, 99),
            'ciudad' => $this->ciudades[array_rand($this->ciudades)],
            'departamento' => $this->departamento,
            'fecha_nacimiento' => now()->subYears(rand(25, 55))->format('Y-m-d'),
            'rol' => 'cliente', // ROL CLIENTE
            'activo' => true,
            'referido_por' => $referidoPorId,
            'codigo_referido' => null, // Clientes no tienen código
            'total_referidos' => 0, // Se actualizará después
            'comisiones_ganadas' => 0, // Clientes no ganan comisiones
            'comisiones_disponibles' => 0,
            'meta_mensual' => 0,
            'ventas_mes_actual' => 0, // Clientes NO generan ventas
            'nivel_vendedor' => 0,
            'zonas_asignadas' => null,
            'historial_ventas' => [], // Clientes no tienen historial de ventas
            'email_verified_at' => now()->subMonths(rand(12, 24)),
            'created_at' => now()->subMonths(rand(12, 24)),
        ]);
        
        // Agregar a la lista de clientes
        $this->clientes[] = $cliente;
        
        return $cliente;
    }

    /**
     * Agregar referidos a un usuario específico
     */
    private function agregarReferidosAUsuario($userId, $cantidad, $tipoReferido = 'cliente')
    {
        for ($i = 0; $i < $cantidad; $i++) {
            $nombre = $this->nombres[array_rand($this->nombres)];
            $apellido = $this->apellidos[array_rand($this->apellidos)];
            $cedula = str_pad(rand(10000000, 99999999), 8, '0', STR_PAD_LEFT);
            
            if ($tipoReferido === 'vendedor') {
                $codigoReferido = 'REF' . str_pad(rand(100, 9999), 4, '0', STR_PAD_LEFT);
                $rol = 'vendedor';
            } else {
                $codigoReferido = null;
                $rol = 'cliente';
            }
            
            $referido = User::create([
                'name' => $nombre,
                'apellidos' => $apellido,
                'cedula' => $cedula,
                'email' => strtolower($nombre . '.' . $apellido . rand(1, 9999) . '@referido.com'),
                'password' => Hash::make('password123'),
                'telefono' => '32' . rand(10000000, 99999999),
                'direccion' => 'Calle ' . rand(1, 100) . ' #' . rand(1, 50) . '-' . rand(1, 99),
                'ciudad' => $this->ciudades[array_rand($this->ciudades)],
                'departamento' => $this->departamento,
                'fecha_nacimiento' => now()->subYears(rand(18, 60))->format('Y-m-d'),
                'rol' => $rol,
                'activo' => true,
                'referido_por' => $userId,
                'codigo_referido' => $codigoReferido,
                'total_referidos' => 0,
                'comisiones_ganadas' => $rol === 'vendedor' ? rand(50000, 200000) : 0,
                'comisiones_disponibles' => $rol === 'vendedor' ? rand(10000, 50000) : 0,
                'meta_mensual' => $rol === 'vendedor' ? rand(100000, 300000) : 0,
                'ventas_mes_actual' => $rol === 'vendedor' ? rand(50000, 200000) : 0,
                'nivel_vendedor' => $rol === 'vendedor' ? rand(0, 1) : 0,
                'email_verified_at' => now()->subDays(rand(1, 120)),
                'created_at' => now()->subDays(rand(1, 120)),
            ]);
            
            if ($rol === 'cliente') {
                $this->clientes[] = $referido;
            }
        }
    }

    /**
     * Generar ventas adicionales para usuarios TOP para alcanzar umbrales de leyenda
     */
    private function generarVentasParaTopCategorias()
    {
        // Obtener usuarios que necesitan más ventas para llegar a sus categorías
        $usuariosTop = User::whereIn('rol', ['lider', 'vendedor'])
            ->where('activo', true)
            ->where(function($q) {
                $q->where('codigo_referido', 'like', 'TOP%')
                  ->orWhere('codigo_referido', 'like', 'BIG%')
                  ->orWhere('codigo_referido', 'like', 'HIG%');
            })
            ->get();

        $ventasCreadas = 0;
        
        foreach ($usuariosTop as $usuario) {
            // Determinar cuántas ventas adicionales necesita según su categoría
            $ventasAdicionales = 0;
            
            if (strpos($usuario->codigo_referido, 'TOP') === 0) {
                $ventasAdicionales = rand(30, 50); // Top Ventas necesita muchas ventas
            } elseif (strpos($usuario->codigo_referido, 'BIG') === 0) {
                $ventasAdicionales = rand(20, 35); // Red Grande
            } elseif (strpos($usuario->codigo_referido, 'HIG') === 0) {
                $ventasAdicionales = rand(15, 25); // Ventas Altas
            }
            
            for ($i = 0; $i < $ventasAdicionales; $i++) {
                if (empty($this->clientes)) {
                    break;
                }
                
                $cliente = $this->clientes[array_rand($this->clientes)];
                $numeroPedido = 'PED-TOP-' . date('Ymd') . '-' . str_pad($ventasCreadas + 1, 6, '0', STR_PAD_LEFT);
                
                // Tickets más grandes para usuarios TOP
                $cantidadProductos = rand(2, 6);
                $productosDelPedido = $this->productos->random(min($cantidadProductos, $this->productos->count()));
                
                $totalPedido = 0;
                $detalles = [];
                
                foreach ($productosDelPedido as $producto) {
                    $cantidad = rand(2, 5); // Más cantidad para tickets grandes
                    $precioUnitario = $producto->precio;
                    $subtotal = $cantidad * $precioUnitario;
                    $totalPedido += $subtotal;
                    
                    $categoriaNombre = $producto->categoria_data['nombre'] ?? 'Sin categoría';
                    $categoriaData = $producto->categoria_data ?? null;
                    
                    $detalles[] = [
                        'producto_id' => $producto->_id,
                        'producto_data' => [
                            'nombre' => $producto->nombre,
                            'precio' => $producto->precio,
                            'categoria' => $categoriaNombre,
                            'categoria_data' => $categoriaData,
                        ],
                        'cantidad' => $cantidad,
                        'precio_unitario' => $precioUnitario,
                        'subtotal' => $subtotal,
                    ];
                }
                
                $fechaCreacion = now()->subDays(rand(1, 90));
                
                Pedido::create([
                    'numero_pedido' => $numeroPedido,
                    'user_id' => $cliente->_id,
                    'cliente_data' => [
                        'nombre' => $cliente->name . ' ' . $cliente->apellidos,
                        'email' => $cliente->email,
                        'telefono' => $cliente->telefono,
                        'cedula' => $cliente->cedula,
                    ],
                    'vendedor_id' => $usuario->_id,
                    'vendedor_data' => [
                        'nombre' => $usuario->name . ' ' . $usuario->apellidos,
                        'email' => $usuario->email,
                        'codigo_referido' => $usuario->codigo_referido,
                        'rol' => $usuario->rol,
                    ],
                    'direccion_entrega' => $cliente->direccion,
                    'telefono_entrega' => $cliente->telefono,
                    'estado' => 'entregado', // Mayoría entregados para TOP
                    'subtotal' => $totalPedido,
                    'total' => $totalPedido,
                    'descuento' => 0,
                    'total_final' => $totalPedido,
                    'metodo_pago' => ['efectivo', 'transferencia', 'nequi'][array_rand(['efectivo', 'transferencia', 'nequi'])],
                    'detalles' => $detalles,
                    'historial_estados' => $this->generarHistorialEstados('entregado', $fechaCreacion),
                    'created_at' => $fechaCreacion,
                    'updated_at' => $fechaCreacion->copy()->addHours(rand(2, 24)),
                ]);
                
                $ventasCreadas++;
            }
        }
        
        $this->command->info("✅ Creadas {$ventasCreadas} ventas adicionales para categorías TOP");
    }
}
