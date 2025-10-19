<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use App\Services\PasswordResetService;

class TestEmailConfig extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:email {email? : Correo electrónico de destino para la prueba}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Prueba la configuración de correo y el sistema de restablecimiento de contraseña';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('');
        $this->info('═══════════════════════════════════════════════════════════════');
        $this->info('   PRUEBA DE CONFIGURACIÓN DE CORREO - AREPA LA LLANERITA');
        $this->info('═══════════════════════════════════════════════════════════════');
        $this->info('');

        // 1. Verificar configuración
        $this->info('📋 1. Verificando configuración de correo...');
        $this->line('   Mailer: ' . config('mail.default'));
        $this->line('   Host: ' . config('mail.mailers.smtp.host'));
        $this->line('   Port: ' . config('mail.mailers.smtp.port'));
        $this->line('   Username: ' . config('mail.mailers.smtp.username'));
        $this->line('   From Address: ' . config('mail.from.address'));
        $this->line('   From Name: ' . config('mail.from.name'));
        $this->info('');

        // 2. Verificar directorios
        $this->info('📂 2. Verificando directorios necesarios...');
        
        $directories = [
            'storage/app/password_resets' => 'Tokens de restablecimiento',
            'storage/logs' => 'Archivos de log',
            'storage/framework/cache' => 'Caché del framework'
        ];

        foreach ($directories as $dir => $description) {
            $path = base_path($dir);
            if (file_exists($path)) {
                $writable = is_writable($path);
                if ($writable) {
                    $this->line("   <fg=green>✓</> Escribible - $description");
                } else {
                    $this->line("   <fg=red>✗</> No escribible - $description");
                }
            } else {
                $this->line("   <fg=red>✗</> No existe - $description");
                @mkdir($path, 0755, true);
                if (file_exists($path)) {
                    $this->line("      <fg=green>✓</> Directorio creado exitosamente");
                }
            }
        }
        $this->info('');

        // 3. Probar conexión SMTP
        $this->info('🔌 3. Probando conexión SMTP...');
        
        try {
            $transport = new \Swift_SmtpTransport(
                config('mail.mailers.smtp.host'),
                config('mail.mailers.smtp.port'),
                config('mail.mailers.smtp.encryption') ?? 'tls'
            );
            
            $transport->setUsername(config('mail.mailers.smtp.username'));
            $transport->setPassword(config('mail.mailers.smtp.password'));
            $transport->setTimeout(10);
            
            $this->line('   Conectando a ' . config('mail.mailers.smtp.host') . ':' . config('mail.mailers.smtp.port') . '...');
            
            $transport->start();
            $this->line('   <fg=green>✓</> Conexión SMTP exitosa');
            $transport->stop();
            
        } catch (\Swift_TransportException $e) {
            $this->error('   ✗ Error de conexión SMTP: ' . $e->getMessage());
            $this->line('');
            $this->warn('   Posibles soluciones:');
            $this->line('   - Verifica que MAIL_HOST, MAIL_PORT, MAIL_USERNAME y MAIL_PASSWORD estén correctos');
            $this->line('   - Si usas Gmail, necesitas una "Contraseña de Aplicación"');
            $this->line('   - Verifica que tu firewall no bloquee el puerto 587');
            $this->line('   - Verifica tu conexión a internet');
            return 1;
        } catch (\Exception $e) {
            $this->error('   ✗ Error inesperado: ' . $e->getMessage());
            return 1;
        }
        $this->info('');

        // 4. Enviar correo de prueba
        $this->info('📧 4. Enviando correo de prueba...');
        
        $testEmail = $this->argument('email');
        if (!$testEmail) {
            $testEmail = $this->ask('   Ingresa el correo de destino para la prueba');
        }

        if (!filter_var($testEmail, FILTER_VALIDATE_EMAIL)) {
            $this->error('   ✗ Correo inválido');
            return 1;
        }

        $this->line("   Enviando correo a $testEmail...");

        try {
            Mail::raw(
                'Este es un correo de prueba desde Arepa la Llanerita. Si recibes este mensaje, tu configuración de correo está funcionando correctamente.',
                function ($message) use ($testEmail) {
                    $message->to($testEmail)
                            ->subject('Prueba de Configuración de Correo - Arepa la Llanerita');
                }
            );
            
            $this->line('   <fg=green>✓</> Correo enviado exitosamente');
            $this->line("   Por favor, revisa tu bandeja de entrada (y spam) en $testEmail");
            
        } catch (\Exception $e) {
            $this->error('   ✗ Error enviando correo: ' . $e->getMessage());
            $this->line('');
            $this->line('   Error detallado:');
            $this->line('   ' . get_class($e));
            $this->line('   Archivo: ' . $e->getFile() . ':' . $e->getLine());
            return 1;
        }
        $this->info('');

        // 5. Verificar servicio de restablecimiento de contraseña
        $this->info('🔐 5. Verificando servicio de restablecimiento...');

        try {
            $passwordResetService = new PasswordResetService();
            $passwordResetService->ensureDirectoryExists();
            
            // Crear token de prueba
            $testToken = $passwordResetService->createToken($testEmail);
            $this->line('   <fg=green>✓</> Token de prueba creado: ' . substr($testToken, 0, 10) . '...');
            
            // Verificar token
            $isValid = $passwordResetService->verifyToken($testEmail, $testToken);
            if ($isValid) {
                $this->line('   <fg=green>✓</> Token verificado correctamente');
            } else {
                $this->error('   ✗ Error al verificar token');
            }
            
            // Consumir token
            $consumed = $passwordResetService->consumeToken($testEmail, $testToken);
            if ($consumed) {
                $this->line('   <fg=green>✓</> Token consumido correctamente');
            } else {
                $this->error('   ✗ Error al consumir token');
            }
            
            // Limpiar tokens expirados
            $deleted = $passwordResetService->cleanExpiredTokens();
            $this->line("   <fg=green>✓</> Tokens expirados limpiados: $deleted");
            
        } catch (\Exception $e) {
            $this->error('   ✗ Error en servicio de restablecimiento: ' . $e->getMessage());
            return 1;
        }
        $this->info('');

        // Resumen final
        $this->info('═══════════════════════════════════════════════════════════════');
        $this->info('   ✅ TODAS LAS PRUEBAS COMPLETADAS EXITOSAMENTE');
        $this->info('═══════════════════════════════════════════════════════════════');
        $this->info('');
        $this->info('Tu sistema está correctamente configurado para enviar correos.');
        $this->info('Ya puedes usar la función de restablecimiento de contraseña.');
        $this->info('');

        return 0;
    }
}
