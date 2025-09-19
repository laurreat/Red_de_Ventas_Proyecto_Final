<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Producto;
use App\Models\Categoria;
use App\Models\Pedido;
use App\Models\Comision;
use App\Models\Notificacion;
use App\Models\UserMongo;
use App\Models\ProductoMongo;
use App\Models\CategoriaMongo;
use App\Models\PedidoMongo;
use App\Models\ComisionMongo;
use App\Models\NotificacionMongo;

class MigrateToMongoDB extends Command
{
    protected $signature = 'migrate:to-mongodb {--table=all : Tabla específica a migrar} {--chunk=100 : Tamaño del chunk}';
    protected $description = 'Migrar datos de MySQL a MongoDB';

    protected $migratedCount = [
        'categorias' => 0,
        'users' => 0,
        'productos' => 0,
        'pedidos' => 0,
        'comisiones' => 0,
        'notificaciones' => 0
    ];

    public function handle()
    {
        $table = $this->option('table');
        $chunkSize = (int) $this->option('chunk');

        $this->info('🚀 Iniciando migración de MySQL a MongoDB...');
        $this->info("📊 Tabla: {$table}, Chunk: {$chunkSize}");

        try {
            if ($table === 'all') {
                $this->migrateAll($chunkSize);
            } else {
                $this->migrateTable($table, $chunkSize);
            }

            $this->mostrarResumen();

        } catch (\Exception $e) {
            $this->error('❌ Error durante la migración:');
            $this->error($e->getMessage());
            $this->error('📍 Archivo: ' . $e->getFile());
            $this->error('📍 Línea: ' . $e->getLine());
        }
    }

    protected function migrateAll($chunkSize)
    {
        $tablas = ['categorias', 'users', 'productos', 'pedidos', 'comisiones', 'notificaciones'];

        foreach ($tablas as $tabla) {
            $this->info("\n📂 Migrando tabla: {$tabla}");
            $this->migrateTable($tabla, $chunkSize);
        }
    }

    protected function migrateTable($tabla, $chunkSize)
    {
        switch ($tabla) {
            case 'categorias':
                $this->migrateCategorias($chunkSize);
                break;
            case 'users':
                $this->migrateUsers($chunkSize);
                break;
            case 'productos':
                $this->migrateProductos($chunkSize);
                break;
            case 'pedidos':
                $this->migratePedidos($chunkSize);
                break;
            case 'comisiones':
                $this->migrateComisiones($chunkSize);
                break;
            case 'notificaciones':
                $this->migrateNotificaciones($chunkSize);
                break;
            default:
                $this->error("❌ Tabla no reconocida: {$tabla}");
        }
    }

    protected function migrateCategorias($chunkSize)
    {
        $total = DB::connection('mysql')->table('categorias')->count();
        if ($total === 0) {
            $this->info('📭 No hay categorías para migrar');
            return;
        }

        $this->info("📊 Total categorías: {$total}");
        $bar = $this->output->createProgressBar($total);

        DB::connection('mysql')->table('categorias')->orderBy('id')->chunk($chunkSize, function ($categorias) use ($bar) {
            foreach ($categorias as $categoria) {
                CategoriaMongo::create([
                    'nombre' => $categoria->nombre,
                    'descripcion' => $categoria->descripcion,
                    'activo' => (bool) $categoria->activo,
                    'imagen' => $categoria->imagen ?? null,
                    'orden' => $categoria->id,
                    'productos_count' => 0,
                    'mysql_id' => $categoria->id // Mantener referencia para relaciones
                ]);

                $this->migratedCount['categorias']++;
                $bar->advance();
            }
        });

        $bar->finish();
        $this->line('');
    }

    protected function migrateUsers($chunkSize)
    {
        $total = DB::connection('mysql')->table('users')->count();
        if ($total === 0) {
            $this->info('📭 No hay usuarios para migrar');
            return;
        }

        $this->info("📊 Total usuarios: {$total}");
        $bar = $this->output->createProgressBar($total);

        DB::connection('mysql')->table('users')->orderBy('id')->chunk($chunkSize, function ($users) use ($bar) {
            foreach ($users as $user) {
                $zonasAsignadas = [];
                if ($user->zonas_asignadas) {
                    $zonasAsignadas = json_decode($user->zonas_asignadas, true) ?? [];
                }

                UserMongo::create([
                    'name' => $user->name,
                    'apellidos' => $user->apellidos ?? '',
                    'cedula' => $user->cedula ?? '',
                    'email' => $user->email,
                    'password' => $user->password,
                    'telefono' => $user->telefono ?? '',
                    'direccion' => $user->direccion ?? '',
                    'ciudad' => $user->ciudad ?? '',
                    'departamento' => $user->departamento ?? '',
                    'fecha_nacimiento' => $user->fecha_nacimiento,
                    'rol' => $user->rol ?? 'cliente',
                    'activo' => (bool) ($user->activo ?? true),
                    'ultimo_acceso' => $user->ultimo_acceso,
                    'referido_por' => $user->referido_por,
                    'codigo_referido' => $user->codigo_referido ?? '',
                    'total_referidos' => (int) ($user->total_referidos ?? 0),
                    'comisiones_ganadas' => (float) ($user->comisiones_ganadas ?? 0),
                    'comisiones_disponibles' => (float) ($user->comisiones_disponibles ?? 0),
                    'meta_mensual' => (float) ($user->meta_mensual ?? 0),
                    'ventas_mes_actual' => (float) ($user->ventas_mes_actual ?? 0),
                    'nivel_vendedor' => (int) ($user->nivel_vendedor ?? 1),
                    'zonas_asignadas' => $zonasAsignadas,
                    'referidos_data' => [],
                    'historial_ventas' => [],
                    'configuracion_personal' => [],
                    'mysql_id' => $user->id
                ]);

                $this->migratedCount['users']++;
                $bar->advance();
            }
        });

        $bar->finish();
        $this->line('');
    }

    protected function migrateProductos($chunkSize)
    {
        $total = DB::connection('mysql')->table('productos')->count();
        if ($total === 0) {
            $this->info('📭 No hay productos para migrar');
            return;
        }

        $this->info("📊 Total productos: {$total}");
        $bar = $this->output->createProgressBar($total);

        DB::connection('mysql')->table('productos')->orderBy('id')->chunk($chunkSize, function ($productos) use ($bar) {
            foreach ($productos as $producto) {
                // Buscar categoría en MongoDB
                $categoria = CategoriaMongo::where('mysql_id', $producto->categoria_id)->first();
                $categoriaData = null;
                $categoriaId = null;

                if ($categoria) {
                    $categoriaId = $categoria->_id;
                    $categoriaData = [
                        '_id' => $categoria->_id,
                        'nombre' => $categoria->nombre,
                        'descripcion' => $categoria->descripcion
                    ];
                }

                ProductoMongo::create([
                    'nombre' => $producto->nombre,
                    'descripcion' => $producto->descripcion,
                    'categoria_id' => $categoriaId,
                    'categoria_data' => $categoriaData,
                    'precio' => (float) $producto->precio,
                    'stock' => (int) $producto->stock,
                    'stock_minimo' => (int) $producto->stock_minimo,
                    'activo' => (bool) $producto->activo,
                    'imagen' => $producto->imagen,
                    'imagenes_adicionales' => [],
                    'especificaciones' => [],
                    'tiempo_preparacion' => 15,
                    'ingredientes' => [],
                    'historial_precios' => [],
                    'reviews' => [],
                    'mysql_id' => $producto->id
                ]);

                $this->migratedCount['productos']++;
                $bar->advance();
            }
        });

        $bar->finish();
        $this->line('');
    }

    protected function migratePedidos($chunkSize)
    {
        $total = DB::connection('mysql')->table('pedidos')->count();
        if ($total === 0) {
            $this->info('📭 No hay pedidos para migrar');
            return;
        }

        $this->info("📊 Total pedidos: {$total}");
        $bar = $this->output->createProgressBar($total);

        DB::connection('mysql')->table('pedidos')->orderBy('id')->chunk($chunkSize, function ($pedidos) use ($bar) {
            foreach ($pedidos as $pedido) {
                // Buscar cliente y vendedor en MongoDB
                $cliente = UserMongo::where('mysql_id', $pedido->user_id)->first();
                $vendedor = $pedido->vendedor_id ? UserMongo::where('mysql_id', $pedido->vendedor_id)->first() : null;

                $clienteData = null;
                $vendedorData = null;

                if ($cliente) {
                    $clienteData = [
                        '_id' => $cliente->_id,
                        'name' => $cliente->name,
                        'apellidos' => $cliente->apellidos,
                        'email' => $cliente->email,
                        'telefono' => $cliente->telefono,
                        'cedula' => $cliente->cedula
                    ];
                }

                if ($vendedor) {
                    $vendedorData = [
                        '_id' => $vendedor->_id,
                        'name' => $vendedor->name,
                        'apellidos' => $vendedor->apellidos,
                        'email' => $vendedor->email,
                        'telefono' => $vendedor->telefono
                    ];
                }

                // Obtener detalles del pedido
                $detalles = DB::connection('mysql')
                    ->table('detalle_pedidos')
                    ->where('pedido_id', $pedido->id)
                    ->get();

                $detallesArray = [];
                foreach ($detalles as $detalle) {
                    $producto = ProductoMongo::where('mysql_id', $detalle->producto_id)->first();
                    if ($producto) {
                        $detallesArray[] = [
                            'producto_id' => $producto->_id,
                            'producto_data' => [
                                '_id' => $producto->_id,
                                'nombre' => $producto->nombre,
                                'precio' => $producto->precio,
                                'imagen' => $producto->imagen
                            ],
                            'cantidad' => $detalle->cantidad,
                            'precio_unitario' => (float) $detalle->precio_unitario,
                            'subtotal' => (float) $detalle->subtotal,
                            'fecha_agregado' => $pedido->created_at
                        ];
                    }
                }

                PedidoMongo::create([
                    'numero_pedido' => $pedido->numero_pedido,
                    'user_id' => $cliente ? $cliente->_id : null,
                    'cliente_data' => $clienteData,
                    'vendedor_id' => $vendedor ? $vendedor->_id : null,
                    'vendedor_data' => $vendedorData,
                    'estado' => $pedido->estado,
                    'total' => (float) $pedido->total,
                    'descuento' => (float) $pedido->descuento,
                    'total_final' => (float) $pedido->total_final,
                    'direccion_entrega' => $pedido->direccion_entrega,
                    'telefono_entrega' => $pedido->telefono_entrega,
                    'notas' => $pedido->notas,
                    'fecha_entrega_estimada' => $pedido->fecha_entrega_estimada,
                    'detalles' => $detallesArray,
                    'historial_estados' => [
                        [
                            'estado_anterior' => null,
                            'estado_nuevo' => $pedido->estado,
                            'motivo' => 'Migración de MySQL',
                            'fecha' => $pedido->created_at,
                            'usuario_id' => null
                        ]
                    ],
                    'metodo_pago' => 'efectivo',
                    'datos_entrega' => [],
                    'comisiones_calculadas' => [],
                    'mysql_id' => $pedido->id,
                    'created_at' => $pedido->created_at,
                    'updated_at' => $pedido->updated_at
                ]);

                $this->migratedCount['pedidos']++;
                $bar->advance();
            }
        });

        $bar->finish();
        $this->line('');
    }

    protected function migrateComisiones($chunkSize)
    {
        $total = DB::connection('mysql')->table('comisiones')->count();
        if ($total === 0) {
            $this->info('📭 No hay comisiones para migrar');
            return;
        }

        $this->info("📊 Total comisiones: {$total}");
        $bar = $this->output->createProgressBar($total);

        DB::connection('mysql')->table('comisiones')->orderBy('id')->chunk($chunkSize, function ($comisiones) use ($bar) {
            foreach ($comisiones as $comision) {
                $user = UserMongo::where('mysql_id', $comision->user_id)->first();
                $pedido = PedidoMongo::where('mysql_id', $comision->pedido_id)->first();

                if ($user && $pedido) {
                    ComisionMongo::create([
                        'user_id' => $user->_id,
                        'user_data' => [
                            '_id' => $user->_id,
                            'name' => $user->name,
                            'apellidos' => $user->apellidos,
                            'email' => $user->email
                        ],
                        'pedido_id' => $pedido->_id,
                        'pedido_data' => [
                            '_id' => $pedido->_id,
                            'numero_pedido' => $pedido->numero_pedido,
                            'total' => $pedido->total
                        ],
                        'tipo' => $comision->tipo,
                        'porcentaje' => (float) $comision->porcentaje,
                        'monto' => (float) $comision->monto,
                        'estado' => $comision->estado,
                        'fecha_pago' => $comision->fecha_pago,
                        'detalles_calculo' => [],
                        'metodo_pago' => null,
                        'mysql_id' => $comision->id,
                        'created_at' => $comision->created_at,
                        'updated_at' => $comision->updated_at
                    ]);
                }

                $this->migratedCount['comisiones']++;
                $bar->advance();
            }
        });

        $bar->finish();
        $this->line('');
    }

    protected function migrateNotificaciones($chunkSize)
    {
        $total = DB::connection('mysql')->table('notificaciones')->count();
        if ($total === 0) {
            $this->info('📭 No hay notificaciones para migrar');
            return;
        }

        $this->info("📊 Total notificaciones: {$total}");
        $bar = $this->output->createProgressBar($total);

        DB::connection('mysql')->table('notificaciones')->orderBy('id')->chunk($chunkSize, function ($notificaciones) use ($bar) {
            foreach ($notificaciones as $notificacion) {
                $user = UserMongo::where('mysql_id', $notificacion->user_id)->first();

                if ($user) {
                    NotificacionMongo::create([
                        'user_id' => $user->_id,
                        'user_data' => [
                            '_id' => $user->_id,
                            'name' => $user->name,
                            'apellidos' => $user->apellidos,
                            'email' => $user->email
                        ],
                        'titulo' => $notificacion->titulo,
                        'mensaje' => $notificacion->mensaje,
                        'tipo' => $notificacion->tipo,
                        'leida' => (bool) $notificacion->leida,
                        'fecha_lectura' => $notificacion->fecha_lectura,
                        'datos_adicionales' => [],
                        'canal' => 'sistema',
                        'mysql_id' => $notificacion->id,
                        'created_at' => $notificacion->created_at,
                        'updated_at' => $notificacion->updated_at
                    ]);
                }

                $this->migratedCount['notificaciones']++;
                $bar->advance();
            }
        });

        $bar->finish();
        $this->line('');
    }

    protected function mostrarResumen()
    {
        $this->info("\n🎉 ¡Migración completada exitosamente!");
        $this->info("📊 Resumen de registros migrados:");

        foreach ($this->migratedCount as $tabla => $count) {
            $this->info("   {$tabla}: {$count} registros");
        }

        $total = array_sum($this->migratedCount);
        $this->info("\n📈 Total general: {$total} registros migrados");
        $this->info("🔥 ¡Tu aplicación Laravel ahora usa MongoDB!");
    }
}
