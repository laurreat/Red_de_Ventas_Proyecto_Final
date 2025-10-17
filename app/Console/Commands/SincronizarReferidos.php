<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;

class SincronizarReferidos extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'usuarios:sincronizar-referidos {--force : Forzar sincronización sin confirmación}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sincroniza el conteo de referidos de todos los usuarios con los datos reales de la base de datos';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔄 Iniciando sincronización de referidos...');
        $this->newLine();

        // Obtener todos los usuarios
        $users = User::all();
        $this->info("📊 Total de usuarios a revisar: {$users->count()}");
        $this->newLine();

        // Analizar diferencias
        $inconsistencias = [];
        foreach ($users as $user) {
            $realCount = User::where('referido_por', $user->_id)->count();
            $storedCount = $user->total_referidos ?? 0;

            if ($realCount != $storedCount) {
                $inconsistencias[] = [
                    'user' => $user,
                    'stored' => $storedCount,
                    'real' => $realCount,
                    'diff' => $realCount - $storedCount
                ];
            }
        }

        if (empty($inconsistencias)) {
            $this->info('✅ No se encontraron inconsistencias. Todos los conteos están correctos.');
            return Command::SUCCESS;
        }

        // Mostrar inconsistencias
        $totalInconsistencias = count($inconsistencias);
        $this->warn("⚠️  Se encontraron {$totalInconsistencias} usuarios con inconsistencias:");
        $this->newLine();

        $this->table(
            ['ID', 'Nombre', 'Rol', 'Guardado', 'Real', 'Diferencia'],
            collect($inconsistencias)->map(fn($item) => [
                substr($item['user']->_id, -8),
                $item['user']->name . ' ' . ($item['user']->apellidos ?? ''),
                $item['user']->rol,
                $item['stored'],
                $item['real'],
                ($item['diff'] > 0 ? '+' : '') . $item['diff']
            ])
        );

        $this->newLine();

        // Confirmar sincronización
        if (!$this->option('force')) {
            if (!$this->confirm('¿Deseas sincronizar estos conteos?', true)) {
                $this->warn('❌ Sincronización cancelada.');
                return Command::FAILURE;
            }
        }

        // Sincronizar
        $this->info('🔄 Sincronizando conteos...');
        $progress = $this->output->createProgressBar(count($inconsistencias));
        $progress->start();

        $sincronizados = 0;
        foreach ($inconsistencias as $item) {
            try {
                $item['user']->update([
                    'total_referidos' => $item['real']
                ]);
                $sincronizados++;
            } catch (\Exception $e) {
                $this->error("\n❌ Error al sincronizar usuario {$item['user']->_id}: {$e->getMessage()}");
            }
            $progress->advance();
        }

        $progress->finish();
        $this->newLine(2);

        // Resultado final
        if ($sincronizados === count($inconsistencias)) {
            $this->info("✅ Sincronización completada exitosamente.");
            $this->info("   {$sincronizados} usuarios actualizados.");
        } else {
            $this->warn("⚠️  Sincronización completada con errores.");
            $this->warn("   {$sincronizados} de " . count($inconsistencias) . " usuarios actualizados.");
        }

        return Command::SUCCESS;
    }
}
