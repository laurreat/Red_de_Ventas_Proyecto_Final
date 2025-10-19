<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\PasswordResetService;
use Illuminate\Support\Facades\Storage;

class CleanPasswordResetTokens extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'password:clean-tokens {--all : Eliminar todos los tokens, no solo los expirados}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Limpiar tokens de restablecimiento de contraseña expirados o todos';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('');
        $this->info('🧹 Limpieza de Tokens de Restablecimiento de Contraseña');
        $this->info('═══════════════════════════════════════════════════════');
        $this->info('');

        $passwordResetService = new PasswordResetService();
        $passwordResetService->ensureDirectoryExists();

        // Mostrar tokens actuales
        $this->info('📊 Estado actual:');
        $files = Storage::files('password_resets');
        $this->line("   Total de tokens: " . count($files));
        $this->info('');

        if (count($files) > 0) {
            $this->info('📋 Tokens existentes:');
            foreach ($files as $file) {
                try {
                    $data = json_decode(Storage::get($file), true);
                    if ($data) {
                        $expiresAt = \Carbon\Carbon::parse($data['expires_at']);
                        $isExpired = $expiresAt->isPast();
                        $status = $isExpired ? '<fg=red>EXPIRADO</>' : '<fg=green>VÁLIDO</>';
                        
                        $this->line("   {$status} - {$data['email']} - Expira: {$expiresAt->format('Y-m-d H:i:s')}");
                    }
                } catch (\Exception $e) {
                    $this->line("   <fg=yellow>⚠️ CORRUPTO</> - " . basename($file));
                }
            }
            $this->info('');
        }

        if ($this->option('all')) {
            if ($this->confirm('¿Estás seguro de eliminar TODOS los tokens?', false)) {
                $deleted = 0;
                foreach ($files as $file) {
                    Storage::delete($file);
                    $deleted++;
                }
                $this->info("✅ {$deleted} tokens eliminados");
            } else {
                $this->info('❌ Operación cancelada');
            }
        } else {
            $deleted = $passwordResetService->cleanExpiredTokens();
            if ($deleted > 0) {
                $this->info("✅ {$deleted} tokens expirados eliminados");
            } else {
                $this->info("✅ No hay tokens expirados para eliminar");
            }
        }

        // Mostrar estado final
        $this->info('');
        $filesAfter = Storage::files('password_resets');
        $this->info('📊 Estado final:');
        $this->line("   Total de tokens: " . count($filesAfter));
        $this->info('');

        return 0;
    }
}
