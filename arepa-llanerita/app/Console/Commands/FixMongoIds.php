<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class FixMongoIds extends Command
{
    protected $signature = 'fix:mongo-ids';
    protected $description = 'Arreglar referencias de id por _id en consultas MongoDB';

    public function handle()
    {
        $this->info('🔧 Arreglando referencias de id por _id en MongoDB...');

        $fixes = [
            // Cambiar ->id por ->_id en consultas where
            '$vendedor->id' => '$vendedor->_id',
            '$user->id' => '$user->_id',
            '$cliente->id' => '$cliente->_id',
            '$producto->id' => '$producto->_id',
            '$pedido->id' => '$pedido->_id',
            '$comision->id' => '$comision->_id',

            // Otros casos comunes
            '->where(\'vendedor_id\', $vendedor->id)' => '->where(\'vendedor_id\', $vendedor->_id)',
            '->where(\'user_id\', $vendedor->id)' => '->where(\'user_id\', $vendedor->_id)',
            '->where(\'cliente_id\', $cliente->id)' => '->where(\'cliente_id\', $cliente->_id)',
            '->where(\'referido_por\', $vendedor->id)' => '->where(\'referido_por\', $vendedor->_id)',
            '\'vendedor_id\' => $vendedor->id' => '\'vendedor_id\' => $vendedor->_id',
            '\'user_id\' => $vendedor->id' => '\'user_id\' => $vendedor->_id',
            '\'cliente_id\' => $cliente->id' => '\'cliente_id\' => $cliente->_id',
            '\'referido_por\' => $vendedor->id' => '\'referido_por\' => $vendedor->_id',

            // Correcciones en where con variables
            '->where(\'id\', $id)' => '->where(\'_id\', $id)',
            '->where(\'id\', $pedido->id)' => '->where(\'_id\', $pedido->_id)',
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