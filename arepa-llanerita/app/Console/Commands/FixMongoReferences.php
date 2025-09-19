<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class FixMongoReferences extends Command
{
    protected $signature = 'fix:mongo-references';
    protected $description = 'Arreglar referencias directas a modelos MongoDB en controladores';

    public function handle()
    {
        $this->info('🔧 Arreglando referencias directas a modelos MongoDB...');

        $fixes = [
            // En controladores que ya tienen "use App\Models\UserMongo as User"
            'UserMongo::' => 'User::',
            'ProductoMongo::' => 'Producto::',
            'CategoriaMongo::' => 'Categoria::',
            'PedidoMongo::' => 'Pedido::',
            'ComisionMongo::' => 'Comision::',
            'NotificacionMongo::' => 'Notificacion::',

            // Referencias con namespace completo
            '\\App\\Models\\UserMongo::' => '\\App\\Models\\UserMongo::',
            '\App\Models\UserMongo::' => 'User::',
            '\App\Models\ProductoMongo::' => 'Producto::',
            '\App\Models\CategoriaMongo::' => 'Categoria::',
            '\App\Models\PedidoMongo::' => 'Pedido::',
            '\App\Models\ComisionMongo::' => 'Comision::',
            '\App\Models\NotificacionMongo::' => 'Notificacion::'
        ];

        $controllersPath = app_path('Http/Controllers');
        $controllers = $this->getAllControllers($controllersPath);

        $fixedCount = 0;

        foreach ($controllers as $controller) {
            $content = File::get($controller);
            $originalContent = $content;

            foreach ($fixes as $wrong => $correct) {
                $content = str_replace($wrong, $correct, $content);
            }

            if ($content !== $originalContent) {
                File::put($controller, $content);
                $relativePath = str_replace(base_path() . '/', '', $controller);
                $this->info("✅ Arreglado: {$relativePath}");
                $fixedCount++;
            }
        }

        $this->info("\n🎉 Proceso completado!");
        $this->info("📊 Archivos arreglados: {$fixedCount}");
    }

    protected function getAllControllers($path)
    {
        $controllers = [];
        $files = File::allFiles($path);

        foreach ($files as $file) {
            if ($file->getExtension() === 'php') {
                $controllers[] = $file->getPathname();
            }
        }

        return $controllers;
    }
}