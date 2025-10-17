<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Comision;
use App\Models\Pedido;
use App\Models\SolicitudPago;
use Carbon\Carbon;

class ComisionesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * NOTA: Este seeder SOLO limpia las comisiones de prueba.
     * Las comisiones reales se calculan automáticamente cuando:
     * 1. Un pedido cambia a estado "entregado" o "confirmado"
     * 2. Un líder genera ventas directas
     * 3. Un vendedor referido realiza ventas
     */
    public function run(): void
    {
        $this->command->warn('⚠️  Este seeder limpiará TODAS las comisiones de prueba.');
        $this->command->info('Las comisiones se calcularán automáticamente desde los pedidos reales.');

        // Limpiar todas las comisiones existentes
        $countComisiones = Comision::count();

        if ($countComisiones > 0) {
            Comision::truncate();
            $this->command->info("✅ {$countComisiones} comisiones de prueba eliminadas");
        } else {
            $this->command->info("ℹ️  No hay comisiones para eliminar");
        }

        // Limpiar solicitudes de pago de prueba
        $countSolicitudes = SolicitudPago::count();

        if ($countSolicitudes > 0) {
            SolicitudPago::truncate();
            $this->command->info("✅ {$countSolicitudes} solicitudes de pago de prueba eliminadas");
        } else {
            $this->command->info("ℹ️  No hay solicitudes de pago para eliminar");
        }

        // Resetear campos de comisiones en usuarios
        User::whereIn('rol', ['lider', 'vendedor'])->update([
            'comisiones_ganadas' => 0,
            'comisiones_disponibles' => 0
        ]);

        $this->command->info("✅ Campos de comisiones reseteados en usuarios");
        $this->command->newLine();
        $this->command->info("━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━");
        $this->command->info("✅ Base de datos limpiada exitosamente");
        $this->command->info("━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━");
        $this->command->newLine();
        $this->command->info("📌 Las comisiones se calcularán automáticamente cuando:");
        $this->command->info("   1. Un pedido se marque como 'entregado' o 'confirmado'");
        $this->command->info("   2. Un líder realice ventas directas");
        $this->command->info("   3. Un vendedor referido complete una venta");
        $this->command->newLine();
    }

}
