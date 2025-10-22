<?php

/**
 * Script para verificar la red completa de referidos
 * Ejecutar con: php verificar-red-completa.php
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;

echo "=== VERIFICACIÓN DE RED DE REFERIDOS COMPLETA ===\n\n";

// 1. Contar todos los usuarios por rol
$totalVendedores = User::where('rol', 'vendedor')->count();
$totalLideres = User::where('rol', 'lider')->count();
$totalClientes = User::where('rol', 'cliente')->count();
$totalAdmins = User::where('rol', 'administrador')->count();

echo "📊 USUARIOS POR ROL:\n";
echo "   - Vendedores: {$totalVendedores}\n";
echo "   - Líderes: {$totalLideres}\n";
echo "   - Clientes: {$totalClientes}\n";
echo "   - Administradores: {$totalAdmins}\n";
echo "   - TOTAL: " . ($totalVendedores + $totalLideres + $totalClientes + $totalAdmins) . "\n\n";

// 2. Usuarios en la red MLM (vendedores, líderes, clientes)
$usuariosRed = User::whereIn('rol', ['vendedor', 'lider', 'cliente'])->get();
echo "🌐 USUARIOS EN LA RED MLM: {$usuariosRed->count()}\n\n";

// 3. Usuarios raíz (sin referidor)
$usuariosRaiz = User::whereNull('referido_por')
    ->whereIn('rol', ['vendedor', 'lider', 'cliente'])
    ->get();

echo "🌳 USUARIOS RAÍZ (sin referidor): {$usuariosRaiz->count()}\n";
foreach ($usuariosRaiz as $raiz) {
    $referidos = User::where('referido_por', $raiz->_id)->count();
    echo "   - {$raiz->name} ({$raiz->rol}) - {$referidos} referidos directos\n";
}
echo "\n";

// 4. Función recursiva para contar todos los nodos
function contarNodosRecursivo($usuario, &$contador = 0, $nivel = 0) {
    $contador++;
    $referidos = User::where('referido_por', $usuario->_id)->get();
    
    foreach ($referidos as $referido) {
        contarNodosRecursivo($referido, $contador, $nivel + 1);
    }
    
    return $contador;
}

// 5. Contar nodos totales en la jerarquía
$totalNodos = 0;
$maxProfundidad = 0;

foreach ($usuariosRaiz as $raiz) {
    $nodosEnRama = 0;
    $profundidad = contarProfundidad($raiz, 0);
    contarNodosRecursivo($raiz, $nodosEnRama, 0);
    
    echo "📍 Rama de {$raiz->name}:\n";
    echo "   - Nodos totales: {$nodosEnRama}\n";
    echo "   - Profundidad máxima: {$profundidad}\n\n";
    
    $totalNodos += $nodosEnRama;
    if ($profundidad > $maxProfundidad) {
        $maxProfundidad = $profundidad;
    }
}

function contarProfundidad($usuario, $nivelActual) {
    $referidos = User::where('referido_por', $usuario->_id)->get();
    
    if ($referidos->count() === 0) {
        return $nivelActual;
    }
    
    $maxProfundidad = $nivelActual;
    foreach ($referidos as $referido) {
        $profundidad = contarProfundidad($referido, $nivelActual + 1);
        if ($profundidad > $maxProfundidad) {
            $maxProfundidad = $profundidad;
        }
    }
    
    return $maxProfundidad;
}

echo "✅ RESUMEN:\n";
echo "   - Total de nodos en la red: {$totalNodos}\n";
echo "   - Profundidad máxima: {$maxProfundidad} niveles\n";
echo "   - Usuarios raíz: {$usuariosRaiz->count()}\n\n";

// 6. Verificar usuarios con referido_por pero el referidor no existe
echo "🔍 VERIFICANDO INTEGRIDAD DE REFERENCIAS:\n";
$usuariosConReferidor = User::whereNotNull('referido_por')
    ->whereIn('rol', ['vendedor', 'lider', 'cliente'])
    ->get();

$referenciasRotas = 0;
foreach ($usuariosConReferidor as $usuario) {
    $referidor = User::find($usuario->referido_por);
    if (!$referidor) {
        echo "   ⚠️  {$usuario->name} ({$usuario->email}) tiene referido_por pero el referidor no existe\n";
        $referenciasRotas++;
    }
}

if ($referenciasRotas === 0) {
    echo "   ✅ Todas las referencias están correctas\n";
} else {
    echo "   ⚠️  Se encontraron {$referenciasRotas} referencias rotas\n";
}

echo "\n=== FIN DE VERIFICACIÓN ===\n";
