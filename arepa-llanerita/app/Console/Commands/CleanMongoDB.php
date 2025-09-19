<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\UserMongo;
use App\Models\ProductoMongo;
use App\Models\CategoriaMongo;
use App\Models\PedidoMongo;
use App\Models\ComisionMongo;
use App\Models\NotificacionMongo;

class CleanMongoDB extends Command
{
    protected $signature = 'clean:mongodb';
    protected $description = 'Limpiar todas las colecciones de MongoDB';

    public function handle()
    {
        $this->info('🧹 Limpiando colecciones de MongoDB...');

        try {
            UserMongo::truncate();
            $this->info('✅ Users limpiados');

            CategoriaMongo::truncate();
            $this->info('✅ Categorías limpiadas');

            ProductoMongo::truncate();
            $this->info('✅ Productos limpiados');

            PedidoMongo::truncate();
            $this->info('✅ Pedidos limpiados');

            ComisionMongo::truncate();
            $this->info('✅ Comisiones limpiadas');

            NotificacionMongo::truncate();
            $this->info('✅ Notificaciones limpiadas');

            $this->info('🎉 MongoDB limpiado completamente!');

        } catch (\Exception $e) {
            $this->error('❌ Error limpiando MongoDB:');
            $this->error($e->getMessage());
        }
    }
}