<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\UserMongo;
use App\Models\ComisionMongo;
use App\Models\NotificacionMongo;

class TestMongoDB extends Command
{
    protected $signature = 'test:mongodb';
    protected $description = 'Probar conexión y datos de MongoDB';

    public function handle()
    {
        $this->info('🔍 Probando conexión a MongoDB...');

        try {
            // Test a través de modelos
            $usersCount = UserMongo::count();
            $this->info("📊 Usuarios via modelo: {$usersCount}");

            $comisionesCount = ComisionMongo::count();
            $this->info("📊 Comisiones via modelo: {$comisionesCount}");

            $notificacionesCount = NotificacionMongo::count();
            $this->info("📊 Notificaciones via modelo: {$notificacionesCount}");

            // Test conexión directa usando tabla
            $this->info("\n📊 Conteos de colecciones vía tabla:");
            $tables = ['users', 'categorias', 'productos', 'pedidos', 'comisiones', 'notificaciones'];
            foreach ($tables as $table) {
                $count = DB::connection('mongodb')->table($table)->count();
                $this->info("   {$table}: {$count}");
            }

            $this->info("\n📊 Conteos de colecciones pluralizadas:");
            $pluralTables = ['user_mongos', 'categoria_mongos', 'producto_mongos', 'pedido_mongos', 'comision_mongos', 'notificacion_mongos'];
            foreach ($pluralTables as $table) {
                $count = DB::connection('mongodb')->table($table)->count();
                $this->info("   {$table}: {$count}");
            }

            // Debug: Mostrar tabla/colección que usa el modelo
            $userModel = new UserMongo();
            $comisionModel = new ComisionMongo();
            $notificacionModel = new NotificacionMongo();
            $this->info("\n🔧 Debug info:");
            $this->info("   UserMongo connection: {$userModel->getConnectionName()}");
            $this->info("   UserMongo collection: {$userModel->getTable()}");
            $this->info("   ComisionMongo collection: {$comisionModel->getTable()}");
            $this->info("   NotificacionMongo collection: {$notificacionModel->getTable()}");

            // Mostrar un usuario ejemplo
            $firstUser = UserMongo::first();
            if ($firstUser) {
                $this->info("\n👤 Primer usuario encontrado:");
                $this->info("   ID: {$firstUser->_id}");
                $this->info("   Nombre: {$firstUser->name} {$firstUser->apellidos}");
                $this->info("   Email: {$firstUser->email}");
            }

            // Mostrar comisiones si existen
            $firstComision = ComisionMongo::first();
            if ($firstComision) {
                $this->info("\n💰 Primera comisión encontrada:");
                $this->info("   ID: {$firstComision->_id}");
                $this->info("   Monto: {$firstComision->monto}");
                $this->info("   Estado: {$firstComision->estado}");
            }

        } catch (\Exception $e) {
            $this->error('❌ Error conectando a MongoDB:');
            $this->error($e->getMessage());
            $this->error("Archivo: {$e->getFile()}");
            $this->error("Línea: {$e->getLine()}");
        }
    }
}