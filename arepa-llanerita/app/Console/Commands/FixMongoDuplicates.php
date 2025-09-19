<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class FixMongoDuplicates extends Command
{
    protected $signature = 'fix:mongo-duplicates';
    protected $description = 'Arreglar referencias duplicadas de MongoMongo';

    public function handle()
    {
        $this->info('🔧 Arreglando referencias duplicadas MongoMongo...');

        $fixes = [
            'MongoMongoMongo' => 'Mongo',
            'MongoMongo' => 'Mongo',
            'UserMongoMongo' => 'UserMongo',
            'ProductoMongoMongo' => 'ProductoMongo',
            'CategoriaMongoMongo' => 'CategoriaMongo',
            'PedidoMongoMongo' => 'PedidoMongo',
            'ComisionMongoMongo' => 'ComisionMongo',
            'NotificacionMongoMongo' => 'NotificacionMongo',
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