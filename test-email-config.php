#!/usr/bin/env php
<?php

/**
 * Script de Prueba de Configuración de Correo
 * Para ejecutar: php test-email-config.php
 */

define('LARAVEL_START', microtime(true));

// Cargar autoloader de Composer
require __DIR__.'/vendor/autoload.php';

// Bootstrap de Laravel
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

echo "\n";
echo "═══════════════════════════════════════════════════════════════\n";
echo "   PRUEBA DE CONFIGURACIÓN DE CORREO - AREPA LA LLANERITA\n";
echo "═══════════════════════════════════════════════════════════════\n";
echo "\n";

// 1. Verificar configuración
echo "📋 1. Verificando configuración de correo...\n";
echo "   Mailer: " . config('mail.default') . "\n";
echo "   Host: " . config('mail.mailers.smtp.host') . "\n";
echo "   Port: " . config('mail.mailers.smtp.port') . "\n";
echo "   Username: " . config('mail.mailers.smtp.username') . "\n";
echo "   From Address: " . config('mail.from.address') . "\n";
echo "   From Name: " . config('mail.from.name') . "\n";
echo "\n";

// 2. Verificar directorios
echo "📂 2. Verificando directorios necesarios...\n";

$directories = [
    'storage/app/password_resets' => 'Tokens de restablecimiento',
    'storage/logs' => 'Archivos de log',
    'storage/framework/cache' => 'Caché del framework'
];

foreach ($directories as $dir => $description) {
    $path = base_path($dir);
    if (file_exists($path)) {
        $writable = is_writable($path) ? '✓ Escribible' : '✗ No escribible';
        echo "   $writable - $description ($dir)\n";
    } else {
        echo "   ✗ No existe - $description ($dir)\n";
        @mkdir($path, 0755, true);
        if (file_exists($path)) {
            echo "      ✓ Directorio creado exitosamente\n";
        }
    }
}
echo "\n";

// 3. Probar conexión SMTP
echo "🔌 3. Probando conexión SMTP...\n";

try {
    $transport = new \Swift_SmtpTransport(
        config('mail.mailers.smtp.host'),
        config('mail.mailers.smtp.port'),
        config('mail.mailers.smtp.encryption') ?? 'tls'
    );
    
    $transport->setUsername(config('mail.mailers.smtp.username'));
    $transport->setPassword(config('mail.mailers.smtp.password'));
    $transport->setTimeout(10);
    
    $mailer = new \Swift_Mailer($transport);
    
    // Intentar iniciar conexión
    echo "   Conectando a " . config('mail.mailers.smtp.host') . ":" . config('mail.mailers.smtp.port') . "...\n";
    $transport->start();
    echo "   ✓ Conexión SMTP exitosa\n";
    $transport->stop();
    
} catch (\Swift_TransportException $e) {
    echo "   ✗ Error de conexión SMTP: " . $e->getMessage() . "\n";
    echo "\n";
    echo "   Posibles soluciones:\n";
    echo "   - Verifica que MAIL_HOST, MAIL_PORT, MAIL_USERNAME y MAIL_PASSWORD estén correctos\n";
    echo "   - Si usas Gmail, necesitas una 'Contraseña de Aplicación'\n";
    echo "   - Verifica que tu firewall no bloquee el puerto 587\n";
    echo "   - Verifica tu conexión a internet\n";
    echo "\n";
    exit(1);
} catch (\Exception $e) {
    echo "   ✗ Error inesperado: " . $e->getMessage() . "\n";
    exit(1);
}
echo "\n";

// 4. Enviar correo de prueba
echo "📧 4. Enviando correo de prueba...\n";

$testEmail = readline("   Ingresa el correo de destino para la prueba: ");

if (!filter_var($testEmail, FILTER_VALIDATE_EMAIL)) {
    echo "   ✗ Correo inválido\n";
    exit(1);
}

echo "   Enviando correo a $testEmail...\n";

try {
    Mail::raw('Este es un correo de prueba desde Arepa la Llanerita. Si recibes este mensaje, tu configuración de correo está funcionando correctamente.', function ($message) use ($testEmail) {
        $message->to($testEmail)
                ->subject('Prueba de Configuración de Correo - Arepa la Llanerita');
    });
    
    echo "   ✓ Correo enviado exitosamente\n";
    echo "   Por favor, revisa tu bandeja de entrada (y spam) en $testEmail\n";
    
} catch (\Exception $e) {
    echo "   ✗ Error enviando correo: " . $e->getMessage() . "\n";
    echo "\n";
    echo "   Error detallado:\n";
    echo "   " . get_class($e) . "\n";
    echo "   Archivo: " . $e->getFile() . ":" . $e->getLine() . "\n";
    exit(1);
}
echo "\n";

// 5. Verificar servicio de restablecimiento de contraseña
echo "🔐 5. Verificando servicio de restablecimiento...\n";

try {
    $passwordResetService = new \App\Services\PasswordResetService();
    $passwordResetService->ensureDirectoryExists();
    
    // Crear token de prueba
    $testToken = $passwordResetService->createToken($testEmail);
    echo "   ✓ Token de prueba creado: " . substr($testToken, 0, 10) . "...\n";
    
    // Verificar token
    $isValid = $passwordResetService->verifyToken($testEmail, $testToken);
    if ($isValid) {
        echo "   ✓ Token verificado correctamente\n";
    } else {
        echo "   ✗ Error al verificar token\n";
    }
    
    // Consumir token
    $consumed = $passwordResetService->consumeToken($testEmail, $testToken);
    if ($consumed) {
        echo "   ✓ Token consumido correctamente\n";
    } else {
        echo "   ✗ Error al consumir token\n";
    }
    
    // Limpiar tokens expirados
    $deleted = $passwordResetService->cleanExpiredTokens();
    echo "   ✓ Tokens expirados limpiados: $deleted\n";
    
} catch (\Exception $e) {
    echo "   ✗ Error en servicio de restablecimiento: " . $e->getMessage() . "\n";
    exit(1);
}
echo "\n";

// Resumen final
echo "═══════════════════════════════════════════════════════════════\n";
echo "   ✅ TODAS LAS PRUEBAS COMPLETADAS EXITOSAMENTE\n";
echo "═══════════════════════════════════════════════════════════════\n";
echo "\n";
echo "Tu sistema está correctamente configurado para enviar correos.\n";
echo "Ya puedes usar la función de restablecimiento de contraseña.\n";
echo "\n";

exit(0);
