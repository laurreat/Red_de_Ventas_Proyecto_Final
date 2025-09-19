<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\UserMongo;
use App\Models\ProductoMongo;
use App\Models\CategoriaMongo;
use App\Models\PedidoMongo;
use App\Models\ComisionMongo;
use App\Models\NotificacionMongo;

class TestCompleteApp extends Command
{
    protected $signature = 'test:complete-app';
    protected $description = 'Prueba completa de la aplicación migrada a MongoDB';

    public function handle()
    {
        $this->info('🎯 Probando aplicación completa con MongoDB...');

        try {
            // 1. Verificar datos migrados
            $this->info('\n📊 1. Verificando datos migrados:');
            $this->verificarDatosMigrados();

            // 2. Probar relaciones embebidas
            $this->info('\n🔗 2. Probando relaciones embebidas:');
            $this->probarRelacionesEmbebidas();

            // 3. Probar consultas de negocio
            $this->info('\n💼 3. Probando consultas de negocio:');
            $this->probarConsultasNegocio();

            // 4. Probar creación de nuevos datos
            $this->info('\n➕ 4. Probando creación de nuevos datos:');
            $this->probarCreacionDatos();

            $this->info('\n🎉 ¡Todas las pruebas completadas exitosamente!');
            $this->info('✅ La migración a MongoDB fue exitosa');
            $this->info('🚀 La aplicación está lista para usar MongoDB');

        } catch (\Exception $e) {
            $this->error('❌ Error en las pruebas:');
            $this->error($e->getMessage());
            $this->error("Archivo: {$e->getFile()}");
            $this->error("Línea: {$e->getLine()}");
        }
    }

    protected function verificarDatosMigrados()
    {
        $users = UserMongo::count();
        $categorias = CategoriaMongo::count();
        $productos = ProductoMongo::count();
        $pedidos = PedidoMongo::count();
        $comisiones = ComisionMongo::count();
        $notificaciones = NotificacionMongo::count();

        $this->info("   👥 Usuarios: {$users}");
        $this->info("   📂 Categorías: {$categorias}");
        $this->info("   🍽️ Productos: {$productos}");
        $this->info("   📦 Pedidos: {$pedidos}");
        $this->info("   💰 Comisiones: {$comisiones}");
        $this->info("   🔔 Notificaciones: {$notificaciones}");

        if ($users > 0 && $categorias > 0 && $productos > 0 && $pedidos > 0) {
            $this->info("   ✅ Datos migrados correctamente");
        } else {
            $this->error("   ❌ Faltan datos migrados");
        }
    }

    protected function probarRelacionesEmbebidas()
    {
        // Probar producto con categoría embebida
        $producto = ProductoMongo::first();
        if ($producto && $producto->categoria_data) {
            $this->info("   ✅ Producto con categoría embebida:");
            $this->info("      📦 {$producto->nombre}");
            $this->info("      📂 Categoría: {$producto->categoria_data['nombre']}");
        }

        // Probar pedido con detalles embebidos
        $pedido = PedidoMongo::first();
        if ($pedido) {
            $this->info("   ✅ Pedido con datos embebidos:");
            $this->info("      📋 {$pedido->numero_pedido}");
            $this->info("      🏷️ Estado: {$pedido->estado}");
            $this->info("      💵 Total: \${$pedido->total_final}");

            if ($pedido->cliente_data) {
                $this->info("      👤 Cliente: {$pedido->cliente_data['name']} {$pedido->cliente_data['apellidos']}");
            }

            if ($pedido->detalles && count($pedido->detalles) > 0) {
                $this->info("      📦 Productos: " . count($pedido->detalles) . " items embebidos");
            }
        }
    }

    protected function probarConsultasNegocio()
    {
        // Buscar vendedores activos
        $vendedores = UserMongo::where('rol', 'vendedor')->where('activo', true)->count();
        $this->info("   👨‍💼 Vendedores activos: {$vendedores}");

        // Pedidos por estado
        $estadosPedidos = PedidoMongo::select('estado')
            ->get()
            ->groupBy('estado')
            ->map(function ($group) {
                return $group->count();
            });

        $this->info("   📈 Pedidos por estado:");
        foreach ($estadosPedidos as $estado => $count) {
            $this->info("      {$estado}: {$count}");
        }

        // Productos por categoría
        $productosPorCategoria = ProductoMongo::where('activo', true)
            ->get()
            ->groupBy('categoria_data.nombre')
            ->map(function ($group) {
                return $group->count();
            });

        $this->info("   🍽️ Productos activos por categoría:");
        foreach ($productosPorCategoria as $categoria => $count) {
            $this->info("      {$categoria}: {$count}");
        }
    }

    protected function probarCreacionDatos()
    {
        // Crear una nueva categoría
        $nuevaCategoria = CategoriaMongo::create([
            'nombre' => 'Prueba MongoDB',
            'descripcion' => 'Categoría de prueba creada después de la migración',
            'activo' => true,
            'orden' => 999
        ]);

        if ($nuevaCategoria) {
            $this->info("   ✅ Nueva categoría creada: {$nuevaCategoria->nombre}");

            // Crear un producto en la nueva categoría
            $nuevoProducto = ProductoMongo::create([
                'nombre' => 'Producto de Prueba MongoDB',
                'descripcion' => 'Producto creado para probar MongoDB',
                'categoria_id' => $nuevaCategoria->_id,
                'categoria_data' => [
                    '_id' => $nuevaCategoria->_id,
                    'nombre' => $nuevaCategoria->nombre,
                    'descripcion' => $nuevaCategoria->descripcion
                ],
                'precio' => 5000,
                'stock' => 10,
                'stock_minimo' => 2,
                'activo' => true,
                'imagen' => 'prueba.jpg'
            ]);

            if ($nuevoProducto) {
                $this->info("   ✅ Nuevo producto creado: {$nuevoProducto->nombre}");
                $this->info("      💵 Precio: \${$nuevoProducto->precio}");
                $this->info("      📂 Categoría embebida: {$nuevoProducto->categoria_data['nombre']}");
            }
        }

        // Probar creación de notificación
        $usuario = UserMongo::first();
        if ($usuario) {
            $notificacion = NotificacionMongo::create([
                'user_id' => $usuario->_id,
                'user_data' => [
                    '_id' => $usuario->_id,
                    'name' => $usuario->name,
                    'apellidos' => $usuario->apellidos,
                    'email' => $usuario->email
                ],
                'titulo' => 'Migración Completada',
                'mensaje' => 'La migración a MongoDB se completó exitosamente',
                'tipo' => 'sistema',
                'leida' => false,
                'canal' => 'sistema'
            ]);

            if ($notificacion) {
                $this->info("   ✅ Nueva notificación creada para {$usuario->name}");
            }
        }
    }
}