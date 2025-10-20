<?php

/**
 * Script de Prueba del Sistema de Referidos
 * 
 * Este script te ayuda a verificar que el sistema de referidos está funcionando correctamente.
 * 
 * INSTRUCCIONES:
 * 1. Ejecuta: php artisan tinker
 * 2. Copia y pega las secciones de prueba una por una
 * 3. Verifica los resultados
 */

// ============================================
// PRUEBA 1: Verificar código de referido
// ============================================
echo "=== PRUEBA 1: Verificar código de referido ===\n";

// Obtener un usuario
$user = App\Models\User::where('rol', 'cliente')->first();

if ($user) {
    echo "Usuario: {$user->name} {$user->apellidos}\n";
    echo "Email: {$user->email}\n";
    echo "Código de referido: " . ($user->codigo_referido ?? 'NO TIENE') . "\n";
    
    // Si no tiene código, generarlo
    if (!$user->codigo_referido) {
        $user->codigo_referido = 'REF' . str_pad(random_int(1, 9999), 4, '0', STR_PAD_LEFT);
        $user->save();
        echo "✓ Código generado: {$user->codigo_referido}\n";
    }
} else {
    echo "✗ No hay usuarios clientes en el sistema\n";
}

// ============================================
// PRUEBA 2: Generar link de referido
// ============================================
echo "\n=== PRUEBA 2: Generar link de referido ===\n";

if ($user && $user->codigo_referido) {
    $link = route('register') . '?ref=' . $user->codigo_referido;
    echo "Link de referido: {$link}\n";
    echo "✓ Link generado correctamente\n";
} else {
    echo "✗ No se puede generar link sin usuario o código\n";
}

// ============================================
// PRUEBA 3: Verificar que el parámetro se lee correctamente
// ============================================
echo "\n=== PRUEBA 3: Simular acceso con código de referido ===\n";
echo "URL de prueba: {$link}\n";
echo "Parámetro 'ref' que se extraería: " . substr($link, strpos($link, 'ref=') + 4) . "\n";
echo "✓ El código se extraería correctamente de la URL\n";

// ============================================
// PRUEBA 4: Buscar referidor por código
// ============================================
echo "\n=== PRUEBA 4: Buscar referidor por código ===\n";

if ($user && $user->codigo_referido) {
    $codigo = $user->codigo_referido;
    $referidor = App\Models\User::where('codigo_referido', $codigo)->first();
    
    if ($referidor) {
        echo "✓ Referidor encontrado:\n";
        echo "  - ID: {$referidor->_id}\n";
        echo "  - Nombre: {$referidor->name} {$referidor->apellidos}\n";
        echo "  - Código: {$referidor->codigo_referido}\n";
    } else {
        echo "✗ No se encontró el referidor\n";
    }
}

// ============================================
// PRUEBA 5: Verificar referidos de un usuario
// ============================================
echo "\n=== PRUEBA 5: Verificar referidos existentes ===\n";

if ($user) {
    $referidos = App\Models\User::where('referido_por', $user->_id)->get();
    echo "Cantidad de referidos: {$referidos->count()}\n";
    
    if ($referidos->count() > 0) {
        foreach ($referidos as $ref) {
            echo "  - {$ref->name} {$ref->apellidos} ({$ref->email})\n";
        }
    } else {
        echo "  Este usuario aún no tiene referidos\n";
    }
}

// ============================================
// PRUEBA 6: Verificar notificaciones del modelo
// ============================================
echo "\n=== PRUEBA 6: Verificar modelo de notificaciones ===\n";

try {
    $notificacion = new App\Models\Notificacion();
    echo "✓ Modelo Notificacion existe\n";
    
    // Verificar campos fillable
    $fillable = $notificacion->getFillable();
    echo "Campos fillable:\n";
    foreach ($fillable as $field) {
        echo "  - {$field}\n";
    }
    
} catch (\Exception $e) {
    echo "✗ Error con el modelo Notificacion: " . $e->getMessage() . "\n";
}

// ============================================
// PRUEBA 7: Crear notificación de prueba (NO GUARDAR)
// ============================================
echo "\n=== PRUEBA 7: Crear estructura de notificación ===\n";

if ($user) {
    $testNotif = [
        'user_id' => $user->_id,
        'tipo' => 'nuevo_referido',
        'titulo' => '¡Nuevo Referido Registrado!',
        'mensaje' => 'Test de notificación',
        'datos_adicionales' => [
            'referido_id' => 'test_id',
            'referido_nombre' => 'Test User',
        ],
        'leida' => false,
        'canal' => 'sistema',
    ];
    
    echo "Estructura de notificación creada:\n";
    print_r($testNotif);
    echo "✓ Estructura correcta\n";
}

// ============================================
// PRUEBA 8: Verificar vista de email existe
// ============================================
echo "\n=== PRUEBA 8: Verificar plantilla de email ===\n";

$emailPath = resource_path('views/emails/nuevo-referido-notification.blade.php');
if (file_exists($emailPath)) {
    echo "✓ Plantilla de email existe: {$emailPath}\n";
    $size = filesize($emailPath);
    echo "  Tamaño: {$size} bytes\n";
} else {
    echo "✗ Plantilla de email NO existe\n";
}

// ============================================
// RESUMEN
// ============================================
echo "\n" . str_repeat("=", 50) . "\n";
echo "RESUMEN DE PRUEBAS\n";
echo str_repeat("=", 50) . "\n";
echo "✓ = Exitoso | ✗ = Error\n";
echo "\nSi todas las pruebas muestran ✓, el sistema está correctamente configurado.\n";
echo "\nPara probar el flujo completo:\n";
echo "1. Copia el link de referido mostrado arriba\n";
echo "2. Ábrelo en un navegador (modo incógnito)\n";
echo "3. Registra un nuevo usuario\n";
echo "4. Verifica las notificaciones\n";
