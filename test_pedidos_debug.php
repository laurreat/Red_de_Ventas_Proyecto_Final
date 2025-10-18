<?php

// Script de debugging para verificar estructura de pedidos
require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Pedido;

echo "=== DEBUG DE PEDIDOS ===\n\n";

$pedidos = Pedido::limit(2)->get();

foreach ($pedidos as $pedido) {
    echo "Pedido ID: {$pedido->_id}\n";
    echo "Número: {$pedido->numero_pedido}\n";
    echo "---\n";
    
    echo "Atributos RAW:\n";
    print_r($pedido->getAttributes());
    echo "\n---\n";
    
    echo "Productos (accessor):\n";
    print_r($pedido->productos);
    echo "\n---\n";
    
    echo "Detalles (accessor):\n";
    print_r($pedido->detalles);
    echo "\n---\n";
    
    echo "Raw Original 'productos':\n";
    print_r($pedido->getRawOriginal('productos'));
    echo "\n---\n";
    
    echo "Raw Original 'detalles':\n";
    print_r($pedido->getRawOriginal('detalles'));
    echo "\n\n========================================\n\n";
}
