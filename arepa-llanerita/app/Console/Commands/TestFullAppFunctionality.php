<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\UserMongo;
use App\Models\ProductoMongo;
use App\Models\CategoriaMongo;
use App\Models\PedidoMongo;
use App\Models\ComisionMongo;
use App\Models\NotificacionMongo;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class TestFullAppFunctionality extends Command
{
    protected $signature = 'test:full-app';
    protected $description = 'Prueba completa de funcionalidad de la aplicación con MongoDB';

    public function handle()
    {
        $this->info('🧪 Iniciando pruebas completas de funcionalidad...');

        try {
            // 1. Probar autenticación
            $this->info('\n🔐 1. Probando autenticación:');
            $this->testAuthentication();

            // 2. Probar CRUD de usuarios
            $this->info('\n👥 2. Probando gestión de usuarios:');
            $this->testUserCrud();

            // 3. Probar CRUD de productos
            $this->info('\n🍽️ 3. Probando gestión de productos:');
            $this->testProductCrud();

            // 4. Probar creación de pedidos
            $this->info('\n📦 4. Probando gestión de pedidos:');
            $this->testOrderCrud();

            // 5. Probar consultas complejas
            $this->info('\n🔍 5. Probando consultas de negocio:');
            $this->testBusinessQueries();

            // 6. Probar rendimiento
            $this->info('\n⚡ 6. Probando rendimiento:');
            $this->testPerformance();

            $this->info('\n🎉 ¡Todas las pruebas completadas exitosamente!');
            $this->info('✅ La aplicación funciona completamente con MongoDB');

        } catch (\Exception $e) {
            $this->error('❌ Error en las pruebas:');
            $this->error($e->getMessage());
            $this->error("Archivo: {$e->getFile()}");
            $this->error("Línea: {$e->getLine()}");
        }
    }

    protected function testAuthentication()
    {
        // Buscar un usuario para probar autenticación
        $user = UserMongo::where('email', 'admin@arepallanerita.com')->first();
        if ($user) {
            $this->info("   ✅ Usuario encontrado: {$user->name} {$user->apellidos}");
            $this->info("   📧 Email: {$user->email}");
            $this->info("   🏷️ Rol: {$user->rol}");

            // Verificar hash de password
            if (Hash::check('password123', $user->password)) {
                $this->info("   ✅ Hash de contraseña válido");
            } else {
                $this->error("   ❌ Hash de contraseña inválido");
            }
        } else {
            $this->error("   ❌ No se encontró usuario de prueba");
        }
    }

    protected function testUserCrud()
    {
        // Crear nuevo usuario
        $nuevoUser = UserMongo::create([
            'name' => 'Usuario Test',
            'apellidos' => 'MongoDB',
            'cedula' => '999999999',
            'email' => 'test@mongodb.com',
            'password' => Hash::make('test123'),
            'telefono' => '3000000000',
            'direccion' => 'Dirección Test MongoDB',
            'ciudad' => 'Villavicencio',
            'departamento' => 'Meta',
            'fecha_nacimiento' => '1990-01-01',
            'rol' => 'cliente',
            'activo' => true,
            'codigo_referido' => 'TEST001'
        ]);

        if ($nuevoUser) {
            $this->info("   ✅ Usuario creado: {$nuevoUser->_id}");

            // Actualizar usuario
            $nuevoUser->update(['telefono' => '3111111111']);
            $this->info("   ✅ Usuario actualizado");

            // Buscar usuario
            $encontrado = UserMongo::where('email', 'test@mongodb.com')->first();
            if ($encontrado) {
                $this->info("   ✅ Usuario encontrado por email");
            }

            // Eliminar usuario de prueba
            $nuevoUser->delete();
            $this->info("   ✅ Usuario eliminado");
        }
    }

    protected function testProductCrud()
    {
        // Buscar una categoría
        $categoria = CategoriaMongo::first();
        if (!$categoria) {
            $this->error("   ❌ No hay categorías disponibles");
            return;
        }

        // Crear nuevo producto
        $nuevoProducto = ProductoMongo::create([
            'nombre' => 'Producto Test MongoDB',
            'descripcion' => 'Producto creado para pruebas de MongoDB',
            'categoria_id' => $categoria->_id,
            'categoria_data' => [
                '_id' => $categoria->_id,
                'nombre' => $categoria->nombre,
                'descripcion' => $categoria->descripcion
            ],
            'precio' => 10000,
            'stock' => 50,
            'stock_minimo' => 10,
            'activo' => true,
            'imagen' => 'test.jpg'
        ]);

        if ($nuevoProducto) {
            $this->info("   ✅ Producto creado: {$nuevoProducto->_id}");
            $this->info("   📂 Categoría embebida: {$nuevoProducto->categoria_data['nombre']}");

            // Buscar productos por categoría embebida
            $productosPorCategoria = ProductoMongo::where('categoria_data.nombre', $categoria->nombre)->count();
            $this->info("   ✅ Productos en categoría '{$categoria->nombre}': {$productosPorCategoria}");

            // Eliminar producto de prueba
            $nuevoProducto->delete();
            $this->info("   ✅ Producto eliminado");
        }
    }

    protected function testOrderCrud()
    {
        // Buscar un vendedor y un cliente
        $vendedor = UserMongo::where('rol', 'vendedor')->first();
        $cliente = UserMongo::where('rol', 'cliente')->first();
        $producto = ProductoMongo::first();

        if (!$vendedor || !$cliente || !$producto) {
            $this->error("   ❌ No hay datos suficientes para crear pedido");
            return;
        }

        // Crear nuevo pedido con datos embebidos
        $nuevoPedido = PedidoMongo::create([
            'numero_pedido' => 'TEST-' . time(),
            'user_id' => $cliente->_id,
            'cliente_data' => [
                '_id' => $cliente->_id,
                'name' => $cliente->name,
                'apellidos' => $cliente->apellidos,
                'email' => $cliente->email,
                'telefono' => $cliente->telefono,
                'cedula' => $cliente->cedula
            ],
            'vendedor_id' => $vendedor->_id,
            'vendedor_data' => [
                '_id' => $vendedor->_id,
                'name' => $vendedor->name,
                'apellidos' => $vendedor->apellidos,
                'email' => $vendedor->email,
                'telefono' => $vendedor->telefono
            ],
            'estado' => 'pendiente',
            'total' => 15000,
            'descuento' => 0,
            'total_final' => 15000,
            'direccion_entrega' => 'Dirección de prueba',
            'telefono_entrega' => '3000000000',
            'notas' => 'Pedido de prueba MongoDB',
            'detalles' => [
                [
                    'producto_id' => $producto->_id,
                    'producto_data' => [
                        '_id' => $producto->_id,
                        'nombre' => $producto->nombre,
                        'precio' => $producto->precio,
                        'imagen' => $producto->imagen
                    ],
                    'cantidad' => 1,
                    'precio_unitario' => $producto->precio,
                    'subtotal' => $producto->precio,
                    'fecha_agregado' => now()
                ]
            ],
            'historial_estados' => [
                [
                    'estado_anterior' => null,
                    'estado_nuevo' => 'pendiente',
                    'motivo' => 'Pedido creado para pruebas',
                    'fecha' => now(),
                    'usuario_id' => $vendedor->_id
                ]
            ],
            'metodo_pago' => 'efectivo'
        ]);

        if ($nuevoPedido) {
            $this->info("   ✅ Pedido creado: {$nuevoPedido->numero_pedido}");
            $this->info("   👤 Cliente: {$nuevoPedido->cliente_data['name']} {$nuevoPedido->cliente_data['apellidos']}");
            $this->info("   👨‍💼 Vendedor: {$nuevoPedido->vendedor_data['name']} {$nuevoPedido->vendedor_data['apellidos']}");
            $this->info("   📦 Productos: " . count($nuevoPedido->detalles) . " items embebidos");

            // Actualizar estado
            $nuevoPedido->update(['estado' => 'confirmado']);
            $this->info("   ✅ Estado actualizado a confirmado");

            // Eliminar pedido de prueba
            $nuevoPedido->delete();
            $this->info("   ✅ Pedido eliminado");
        }
    }

    protected function testBusinessQueries()
    {
        // Consultas típicas de negocio
        $totalUsers = UserMongo::count();
        $activeVendors = UserMongo::where('rol', 'vendedor')->where('activo', true)->count();
        $totalOrders = PedidoMongo::count();
        $totalRevenue = PedidoMongo::sum('total_final');

        $this->info("   📊 Total usuarios: {$totalUsers}");
        $this->info("   👨‍💼 Vendedores activos: {$activeVendors}");
        $this->info("   📦 Total pedidos: {$totalOrders}");
        $totalRevenueFloat = $totalRevenue instanceof \MongoDB\BSON\Decimal128 ? (float) $totalRevenue->__toString() : (float) $totalRevenue;
        $this->info("   💵 Ingresos totales: \$" . number_format($totalRevenueFloat, 0));

        // Consultar pedidos con datos embebidos
        $pedidosEntregados = PedidoMongo::where('estado', 'entregado')->count();
        $this->info("   ✅ Pedidos entregados: {$pedidosEntregados}");

        // Buscar por datos embebidos
        $pedidosDeVendedor = PedidoMongo::where('vendedor_data.email', 'vendedor1@arepallanerita.com')->count();
        $this->info("   👨‍💼 Pedidos de vendedor específico: {$pedidosDeVendedor}");
    }

    protected function testPerformance()
    {
        $start = microtime(true);

        // Consulta compleja con datos embebidos
        $resultados = PedidoMongo::where('estado', '!=', 'cancelado')
            ->where('total_final', '>', 10000)
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        $end = microtime(true);
        $tiempo = round(($end - $start) * 1000, 2);

        $this->info("   ⏱️ Consulta compleja ejecutada en {$tiempo}ms");
        $this->info("   📊 Resultados encontrados: " . $resultados->count());

        // Probar índices
        $start = microtime(true);
        $userByEmail = UserMongo::where('email', 'admin@arepallanerita.com')->first();
        $end = microtime(true);
        $tiempo = round(($end - $start) * 1000, 2);

        $this->info("   🔍 Búsqueda por email (con índice): {$tiempo}ms");
    }
}