<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CreateMongoIndexes extends Command
{
    protected $signature = 'mongo:create-indexes';
    protected $description = 'Crear índices optimizados en MongoDB';

    public function handle()
    {
        $this->info('🚀 Creando índices en MongoDB...');

        try {
            // Usar los modelos para crear índices
            $this->info('📊 Creando índices para usuarios...');

            // Crear índices usando raw MongoDB
            $mongodb = DB::connection('mongodb')->getMongoDB();

            // Usuarios
            $usersCollection = $mongodb->selectCollection('user_mongos');
            $usersCollection->createIndex(['email' => 1], ['unique' => true]);
            $usersCollection->createIndex(['cedula' => 1], ['unique' => true]);
            $usersCollection->createIndex(['rol' => 1]);
            $usersCollection->createIndex(['activo' => 1]);
            $usersCollection->createIndex(['referido_por' => 1]);
            $usersCollection->createIndex(['nivel_vendedor' => 1]);

            // Categorías
            $this->info('📊 Creando índices para categorías...');
            $categoriasCollection = $mongodb->selectCollection('categoria_mongos');
            $categoriasCollection->createIndex(['activo' => 1]);
            $categoriasCollection->createIndex(['orden' => 1]);

            // Productos
            $this->info('📊 Creando índices para productos...');
            $productosCollection = $mongodb->selectCollection('producto_mongos');
            $productosCollection->createIndex(['categoria_id' => 1]);
            $productosCollection->createIndex(['activo' => 1]);
            $productosCollection->createIndex(['precio' => 1]);
            $productosCollection->createIndex(['stock' => 1]);
            $productosCollection->createIndex(['nombre' => 'text', 'descripcion' => 'text']);

            // Pedidos
            $this->info('📊 Creando índices para pedidos...');
            $pedidosCollection = $mongodb->selectCollection('pedido_mongos');
            $pedidosCollection->createIndex(['user_id' => 1]);
            $pedidosCollection->createIndex(['vendedor_id' => 1]);
            $pedidosCollection->createIndex(['estado' => 1]);
            $pedidosCollection->createIndex(['numero_pedido' => 1], ['unique' => true]);
            $pedidosCollection->createIndex(['created_at' => -1]);
            $pedidosCollection->createIndex(['fecha_entrega_estimada' => 1]);

            // Comisiones
            $this->info('📊 Creando índices para comisiones...');
            $comisionesCollection = $mongodb->selectCollection('comision_mongos');
            $comisionesCollection->createIndex(['user_id' => 1]);
            $comisionesCollection->createIndex(['pedido_id' => 1]);
            $comisionesCollection->createIndex(['estado' => 1]);
            $comisionesCollection->createIndex(['tipo' => 1]);
            $comisionesCollection->createIndex(['created_at' => -1]);

            // Notificaciones
            $this->info('📊 Creando índices para notificaciones...');
            $notificacionesCollection = $mongodb->selectCollection('notificacion_mongos');
            $notificacionesCollection->createIndex(['user_id' => 1]);
            $notificacionesCollection->createIndex(['leida' => 1]);
            $notificacionesCollection->createIndex(['tipo' => 1]);
            $notificacionesCollection->createIndex(['created_at' => -1]);

            // Índices compuestos importantes
            $this->info('📊 Creando índices compuestos...');
            $pedidosCollection->createIndex(['user_id' => 1, 'estado' => 1]);
            $pedidosCollection->createIndex(['vendedor_id' => 1, 'estado' => 1]);
            $comisionesCollection->createIndex(['user_id' => 1, 'estado' => 1]);
            $notificacionesCollection->createIndex(['user_id' => 1, 'leida' => 1]);

            $this->info('✅ ¡Índices creados exitosamente!');
            $this->info('🚀 MongoDB optimizado para consultas rápidas');

        } catch (\Exception $e) {
            $this->error('❌ Error creando índices:');
            $this->error($e->getMessage());
            $this->error("Archivo: {$e->getFile()}");
            $this->error("Línea: {$e->getLine()}");
        }
    }
}