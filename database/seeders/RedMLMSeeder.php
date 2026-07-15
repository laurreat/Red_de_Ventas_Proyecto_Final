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
        $this->command->info('🌟 Iniciando construcción de Red MLM robusta...');

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

        // NIVEL 1: Crear líderes principales (4-6 líderes directos del admin)
        $this->command->info("\n📊 NIVEL 1: Creando líderes principales...");
        $lideresNivel1 = $this->crearLideres($admin->_id, 5, 1);
        $this->command->info("✅ Creados " . count($lideresNivel1) . " líderes principales");

        // NIVEL 2: Cada líder principal tiene 3-5 sub-líderes
        $this->command->info("\n📊 NIVEL 2: Creando sub-líderes...");
        $lideresNivel2 = [];
        foreach ($lideresNivel1 as $lider) {
            $subLideres = $this->crearLideres($lider->_id, rand(3, 5), 2);
            $lideresNivel2 = array_merge($lideresNivel2, $subLideres);
        }
        $this->command->info("✅ Creados " . count($lideresNivel2) . " sub-líderes");

        // NIVEL 3: Vendedores activos (cada sub-líder tiene 5-8 vendedores activos)
        $this->command->info("\n📊 NIVEL 3: Creando vendedores activos...");
        $vendedoresActivos = [];
        foreach ($lideresNivel2 as $lider) {
            $vendedores = $this->crearVendedores($lider->_id, rand(5, 8), 'activo');
            $vendedoresActivos = array_merge($vendedoresActivos, $vendedores);
        }
        $this->command->info("✅ Creados " . count($vendedoresActivos) . " vendedores activos");

        // NIVEL 4: Vendedores regulares (cada vendedor activo tiene 2-4 vendedores)
        $this->command->info("\n📊 NIVEL 4: Creando vendedores regulares...");
        $vendedoresRegulares = [];
        foreach ($vendedoresActivos as $vendedor) {
            $subVendedores = $this->crearVendedores($vendedor->_id, rand(2, 4), 'regular');
            $vendedoresRegulares = array_merge($vendedoresRegulares, $subVendedores);
        }
        $this->command->info("✅ Creados " . count($vendedoresRegulares) . " vendedores regulares");

        // NIVEL 5: Clientes que compran y algunos referidos (cada vendedor regular tiene 1-3 clientes)
        $this->command->info("\n📊 NIVEL 5: Creando clientes/vendedores iniciales...");
        $clientesVendedores = [];
        foreach ($vendedoresRegulares as $vendedor) {
            $clientes = $this->crearVendedores($vendedor->_id, rand(1, 3), 'cliente');
            $clientesVendedores = array_merge($clientesVendedores, $clientes);
        }
        $this->command->info("✅ Creados " . count($clientesVendedores) . " clientes/vendedores iniciales");

        // NIVEL 6: Crear clientes reales (no vendedores) para las compras
        $this->command->info("\n📊 NIVEL 6: Creando clientes reales para compras...");
        $this->crearClientesReales(100); // Crear 100 clientes
        $this->command->info("✅ Creados " . count($this->clientes) . " clientes reales");

        // NIVEL 7: Algunos líderes de nivel 1 tienen vendedores directos también
        $this->command->info("\n📊 NIVEL 7: Agregando vendedores directos a líderes principales...");
        $vendedoresDirectos = [];
        foreach ($lideresNivel1 as $lider) {
            $vendedores = $this->crearVendedores($lider->_id, rand(3, 6), 'directo');
            $vendedoresDirectos = array_merge($vendedoresDirectos, $vendedores);
        }
        $this->command->info("✅ Creados " . count($vendedoresDirectos) . " vendedores directos de líderes");

        // Actualizar contadores de referidos
        $this->command->info("\n🔄 Actualizando contadores de referidos...");
        $this->actualizarContadores();

        // Crear ventas para dar realismo
        $this->command->info("\n💰 Generando ventas para la red...");
        $this->generarVentas();

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
        // Obtener vendedores y líderes
        $usuarios = User::whereIn('rol', ['lider', 'vendedor'])
            ->where('activo', true)
            ->get();

        $ventasCreadas = 0;
        $detallesCreados = 0;

        foreach ($usuarios as $usuario) {
            // Cantidad de ventas según nivel
            $cantidadVentas = 0;
            
            if ($usuario->rol === 'lider') {
                $cantidadVentas = rand(5, 15);
            } elseif ($usuario->ventas_mes_actual > 300000) {
                $cantidadVentas = rand(3, 8);
            } elseif ($usuario->ventas_mes_actual > 100000) {
                $cantidadVentas = rand(1, 4);
            } else {
                $cantidadVentas = rand(0, 2);
            }

            for ($i = 0; $i < $cantidadVentas; $i++) {
                // Seleccionar un cliente aleatorio
                $cliente = $this->clientes[array_rand($this->clientes)];
                
                // Generar número de pedido único
                $numeroPedido = 'PED-' . date('Ymd') . '-' . str_pad($ventasCreadas + 1, 6, '0', STR_PAD_LEFT);
                
                // Seleccionar productos aleatorios (1-4 productos por pedido)
                $cantidadProductos = rand(1, 4);
                $productosDelPedido = $this->productos->random($cantidadProductos);
                
                $totalPedido = 0;
                $detalles = [];
                $totalProductosVendidos = 0;
                
                // Calcular total y preparar detalles
                foreach ($productosDelPedido as $producto) {
                    $cantidad = rand(1, 3);
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
        $this->command->info("   📦 Total de productos vendidos embebidos en pedidos");
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

    private function crearClientesReales($cantidad)
    {
        for ($i = 0; $i < $cantidad; $i++) {
            $nombre = $this->nombres[array_rand($this->nombres)];
            $apellido = $this->apellidos[array_rand($this->apellidos)];
            $cedula = str_pad(rand(10000000, 99999999), 8, '0', STR_PAD_LEFT);
            
            // Los clientes pueden ser referidos por vendedores activos
            $vendedoresActivos = User::where('rol', 'vendedor')
                ->where('activo', true)
                ->where('total_referidos', '<', 10)
                ->get();
                
            $referidoPor = $vendedoresActivos->isNotEmpty() && rand(0, 100) < 70 
                ? $vendedoresActivos->random()->_id 
                : null;
            
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

    private function mostrarResumen()
    {
        $this->command->info("\n");
        $this->command->info("╔════════════════════════════════════════════════════════╗");
        $this->command->info("║        🎉 RED MLM CREADA EXITOSAMENTE 🎉              ║");
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
                ['Usuarios Activos', $stats['activos']],
                ['Usuarios con Referidos', $stats['con_referidos']],
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
}
