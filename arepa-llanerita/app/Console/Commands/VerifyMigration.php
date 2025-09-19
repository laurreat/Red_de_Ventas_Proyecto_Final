<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\UserMongo;
use App\Models\ProductoMongo;
use App\Models\CategoriaMongo;
use App\Models\PedidoMongo;
use App\Models\ComisionMongo;
use App\Models\NotificacionMongo;

class VerifyMigration extends Command
{
    protected $signature = 'verify:migration';
    protected $description = 'Verificar que la migración a MongoDB fue exitosa';

    public function handle()
    {
        $this->info('🔍 Verificando datos migrados en MongoDB...');

        try {
            // Verificar conteos
            $users = UserMongo::count();
            $this->info("👥 Usuarios en MongoDB: {$users}");

            $categorias = CategoriaMongo::count();
            $this->info("📂 Categorías en MongoDB: {$categorias}");

            $productos = ProductoMongo::count();
            $this->info("🍽️ Productos en MongoDB: {$productos}");

            $pedidos = PedidoMongo::count();
            $this->info("📦 Pedidos en MongoDB: {$pedidos}");

            $comisiones = ComisionMongo::count();
            $this->info("💰 Comisiones en MongoDB: {$comisiones}");

            $notificaciones = NotificacionMongo::count();
            $this->info("🔔 Notificaciones en MongoDB: {$notificaciones}");

            $this->info("\n📊 Ejemplos de datos:");

            // Mostrar un usuario
            $user = UserMongo::first();
            if ($user) {
                $this->info("👤 Usuario ejemplo: {$user->name} {$user->apellidos} ({$user->email})");
                $this->info("   Rol: {$user->rol}");
                $this->info("   Referidos: {$user->total_referidos}");
            }

            // Mostrar un pedido con detalles embebidos
            $pedido = PedidoMongo::first();
            if ($pedido) {
                $this->info("\n📋 Pedido ejemplo: {$pedido->numero_pedido}");
                $this->info("   Estado: {$pedido->estado}");
                $this->info("   Total: \${$pedido->total_final}");
                $this->info("   Productos: " . count($pedido->detalles) . " items");

                if (isset($pedido->cliente_data)) {
                    $this->info("   Cliente: {$pedido->cliente_data['name']} {$pedido->cliente_data['apellidos']}");
                }

                if (isset($pedido->vendedor_data)) {
                    $this->info("   Vendedor: {$pedido->vendedor_data['name']} {$pedido->vendedor_data['apellidos']}");
                }

                // Mostrar detalles embebidos
                if (count($pedido->detalles) > 0) {
                    $this->info("   Detalle primer producto:");
                    $detalle = $pedido->detalles[0];
                    $this->info("     - {$detalle['producto_data']['nombre']}");
                    $this->info("     - Cantidad: {$detalle['cantidad']}");
                    $this->info("     - Precio: \${$detalle['precio_unitario']}");
                }
            }

            // Verificar relaciones embebidas
            $producto = ProductoMongo::first();
            if ($producto && $producto->categoria_data) {
                $this->info("\n🍽️ Producto con categoría embebida:");
                $this->info("   Producto: {$producto->nombre}");
                $this->info("   Categoría: {$producto->categoria_data['nombre']}");
            }

            $this->info("\n✅ ¡Migración verificada exitosamente!");
            $this->info("🎯 MongoDB está funcionando correctamente con datos embebidos");

        } catch (\Exception $e) {
            $this->error("❌ Error durante la verificación:");
            $this->error($e->getMessage());
            $this->error("📍 Archivo: " . $e->getFile());
            $this->error("📍 Línea: " . $e->getLine());
        }
    }
}