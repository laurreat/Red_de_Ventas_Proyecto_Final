<?php

/**
 * Script para encontrar las raíces reales de la red MLM
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;

echo "=== BUSCANDO RAÍCES REALES DE LA RED ===\n\n";

// Obtener TODOS los usuarios en la red
$todosUsuarios = User::whereIn('rol', ['vendedor', 'lider', 'cliente', 'administrador'])->get();

echo "📊 Total usuarios: {$todosUsuarios->count()}\n\n";

// Agrupar por referido_por
$usuariosPorReferidor = [];
$usuariosSinReferidor = [];

foreach ($todosUsuarios as $usuario) {
    if (!$usuario->referido_por) {
        $usuariosSinReferidor[] = $usuario;
    } else {
        if (!isset($usuariosPorReferidor[$usuario->referido_por])) {
            $usuariosPorReferidor[$usuario->referido_por] = [];
        }
        $usuariosPorReferidor[$usuario->referido_por][] = $usuario;
    }
}

echo "🌳 Usuarios SIN referidor (referido_por = null):\n";
echo "   Total: " . count($usuariosSinReferidor) . "\n\n";

foreach ($usuariosSinReferidor as $usuario) {
    echo "   - {$usuario->name} ({$usuario->rol}) - ID: {$usuario->_id}\n";
}

echo "\n📍 Top 20 Referidores (usuarios con más referidos directos):\n\n";

// Ordenar por cantidad de referidos
$referidoresOrdenados = [];
foreach ($usuariosPorReferidor as $referidorId => $referidos) {
    $referidor = User::find($referidorId);
    if ($referidor) {
        $referidoresOrdenados[] = [
            'id' => $referidorId,
            'usuario' => $referidor,
            'count' => count($referidos),
            'referidos' => $referidos
        ];
    }
}

usort($referidoresOrdenados, function($a, $b) {
    return $b['count'] - $a['count'];
});

foreach (array_slice($referidoresOrdenados, 0, 20) as $i => $data) {
    $referidor = $data['usuario'];
    $count = $data['count'];
    
    echo ($i + 1) . ". {$referidor->name} ({$referidor->rol}) - {$count} referidos directos\n";
    echo "   ID: {$referidor->_id}\n";
    echo "   Email: {$referidor->email}\n";
    echo "   Referido por: " . ($referidor->referido_por ?? 'NINGUNO (ES RAÍZ)') . "\n";
    
    // Mostrar primeros 3 referidos
    $primerosReferidos = array_slice($data['referidos'], 0, 3);
    foreach ($primerosReferidos as $ref) {
        echo "      → {$ref->name} ({$ref->rol})\n";
    }
    if ($count > 3) {
        echo "      → ... y " . ($count - 3) . " más\n";
    }
    echo "\n";
}

// Verificar si el administrador es la raíz
echo "\n🔍 VERIFICANDO ADMINISTRADOR:\n";
$admin = User::where('rol', 'administrador')->first();
if ($admin) {
    $referidosDelAdmin = User::where('referido_por', $admin->_id)->count();
    echo "   Administrador: {$admin->name}\n";
    echo "   ID: {$admin->_id}\n";
    echo "   Email: {$admin->email}\n";
    echo "   Referido por: " . ($admin->referido_por ?? 'NINGUNO (ES RAÍZ)') . "\n";
    echo "   Referidos directos: {$referidosDelAdmin}\n";
}

echo "\n=== RECOMENDACIÓN ===\n";
echo "Si quieres que la red muestre TODO desde el administrador,\n";
echo "asegúrate de que el administrador tenga referido_por = null\n";
echo "y que todos los líderes/vendedores principales tengan al\n";
echo "administrador como referido_por.\n\n";

echo "Para ver la red completa desde el administrador, busca por\n";
echo "cédula del administrador en la interfaz web.\n";
