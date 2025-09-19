<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class UpdateControllersToMongoDB extends Command
{
    protected $signature = 'controllers:update-to-mongodb';
    protected $description = 'Actualizar todos los controladores para usar modelos MongoDB';

    protected $modelMappings = [
        'App\\Models\\User' => 'App\\Models\\UserMongo',
        'App\\Models\\Producto' => 'App\\Models\\ProductoMongo',
        'App\\Models\\Categoria' => 'App\\Models\\CategoriaMongo',
        'App\\Models\\Pedido' => 'App\\Models\\PedidoMongo',
        'App\\Models\\Comision' => 'App\\Models\\ComisionMongo',
        'App\\Models\\Notificacion' => 'App\\Models\\NotificacionMongo',
        'User::' => 'UserMongo::',
        'Producto::' => 'ProductoMongo::',
        'Categoria::' => 'CategoriaMongo::',
        'Pedido::' => 'PedidoMongo::',
        'Comision::' => 'ComisionMongo::',
        'Notificacion::' => 'NotificacionMongo::'
    ];

    protected $useStatements = [
        'use App\Models\User;' => 'use App\Models\UserMongo as User;',
        'use App\Models\Producto;' => 'use App\Models\ProductoMongo as Producto;',
        'use App\Models\Categoria;' => 'use App\Models\CategoriaMongo as Categoria;',
        'use App\Models\Pedido;' => 'use App\Models\PedidoMongo as Pedido;',
        'use App\Models\Comision;' => 'use App\Models\ComisionMongo as Comision;',
        'use App\Models\Notificacion;' => 'use App\Models\NotificacionMongo as Notificacion;'
    ];

    public function handle()
    {
        $this->info('🔄 Actualizando controladores para usar modelos MongoDB...');

        $controllersPath = app_path('Http/Controllers');
        $controllers = $this->getAllControllers($controllersPath);

        $updatedCount = 0;

        foreach ($controllers as $controller) {
            if ($this->updateController($controller)) {
                $updatedCount++;
                $relativePath = str_replace(base_path() . '/', '', $controller);
                $this->info("✅ Actualizado: {$relativePath}");
            }
        }

        $this->info("\n🎉 Actualización completada!");
        $this->info("📊 Controladores actualizados: {$updatedCount}");
        $this->info("🚀 Los controladores ahora usan modelos MongoDB");
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

    protected function updateController($filePath)
    {
        $content = File::get($filePath);
        $originalContent = $content;
        $updated = false;

        // Actualizar use statements
        foreach ($this->useStatements as $old => $new) {
            if (strpos($content, $old) !== false) {
                $content = str_replace($old, $new, $content);
                $updated = true;
            }
        }

        // Actualizar referencias a modelos sin use statements
        foreach ($this->modelMappings as $old => $new) {
            if (strpos($content, $old) !== false) {
                $content = str_replace($old, $new, $content);
                $updated = true;
            }
        }

        // Actualizar métodos específicos problemáticos para MongoDB
        $mongoUpdates = [
            // Cambiar ->id por ->_id para referencias MongoDB
            "->where('user_id', \$user->id)" => "->where('user_id', \$user->_id)",
            "->where('vendedor_id', \$vendedor->id)" => "->where('vendedor_id', \$vendedor->_id)",
            "->where('cliente_id', \$cliente->id)" => "->where('cliente_id', \$cliente->_id)",

            // Actualizar validaciones
            "'required|exists:users,id'" => "'required|string'",
            "'required|exists:productos,id'" => "'required|string'",
            "'required|exists:categorias,id'" => "'required|string'",

            // Actualizar relaciones que no existen en MongoDB
            "->with(['cliente', 'productos'])" => "// MongoDB: datos embebidos, no necesita with",
            "->with(['usuario', 'pedido'])" => "// MongoDB: datos embebidos, no necesita with",
            "->with('categoria')" => "// MongoDB: categoría embebida",

            // Simplificar where clauses
            "whereHas('cliente'" => "where('cliente_data.name'",
            "whereHas('usuario'" => "where('user_data.name'",

            // Actualizar attach/detach para MongoDB
            "->attach(" => "// MongoDB: usar arrays embebidos //->attach(",
            "->detach(" => "// MongoDB: usar arrays embebidos //->detach(",
            "->sync(" => "// MongoDB: usar arrays embebidos //->sync("
        ];

        foreach ($mongoUpdates as $old => $new) {
            if (strpos($content, $old) !== false) {
                $content = str_replace($old, $new, $content);
                $updated = true;
            }
        }

        // Solo escribir si hubo cambios
        if ($updated && $content !== $originalContent) {
            File::put($filePath, $content);
            return true;
        }

        return false;
    }
}