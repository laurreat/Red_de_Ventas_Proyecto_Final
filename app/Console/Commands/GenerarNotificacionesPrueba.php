<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Services\NotificationService;

class GenerarNotificacionesPrueba extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notificaciones:generar-prueba {--rol=lider : Rol del usuario}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Genera notificaciones de prueba para usuarios líderes';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $rol = $this->option('rol');

        $this->info("🔔 Generando notificaciones de prueba para usuarios con rol: {$rol}");

        // Obtener usuarios con el rol especificado
        $usuarios = User::where('rol', $rol)
            ->where('activo', true)
            ->get();

        if ($usuarios->isEmpty()) {
            $this->error("❌ No se encontraron usuarios activos con rol: {$rol}");
            return 1;
        }

        $this->info("📋 Usuarios encontrados: {$usuarios->count()}");

        $totalNotificaciones = 0;

        foreach ($usuarios as $usuario) {
            $this->line("\n👤 Generando para: {$usuario->name} ({$usuario->email})");

            $count = NotificationService::crearNotificacionesPrueba($usuario->_id);

            $totalNotificaciones += $count;

            $this->info("   ✅ {$count} notificaciones creadas");
        }

        $this->newLine();
        $this->info("🎉 Total de notificaciones generadas: {$totalNotificaciones}");
        $this->info("💡 Puedes verlas en el dashboard del líder");

        return 0;
    }
}
