<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\PasswordResetService;

class CleanExpiredPasswordResets extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'auth:clean-password-resets';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Limpiar tokens de restablecimiento de contraseña expirados';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Limpiando tokens de restablecimiento de contraseña expirados...');

        $passwordResetService = new PasswordResetService();
        $deletedCount = $passwordResetService->cleanExpiredTokens();

        if ($deletedCount > 0) {
            $this->info("Se eliminaron {$deletedCount} tokens expirados.");
        } else {
            $this->info('No se encontraron tokens expirados para eliminar.');
        }

        return Command::SUCCESS;
    }
}