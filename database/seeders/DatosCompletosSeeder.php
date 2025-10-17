<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatosCompletosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('');
        $this->command->info('═══════════════════════════════════════════════════════');
        $this->command->info('  SEEDER MAESTRO - DATOS COMPLETOS DEL SISTEMA');
        $this->command->info('═══════════════════════════════════════════════════════');
        $this->command->info('');

        // 1. Pedidos (primero porque las comisiones dependen de ellos)
        $this->command->info('📦 PASO 1/2: Generando Pedidos...');
        $this->command->info('─────────────────────────────────────────────────────');
        $this->call(PedidosSeeder::class);
        $this->command->info('');

        // 2. Comisiones (usa los pedidos creados)
        $this->command->info('💰 PASO 2/2: Generando Comisiones...');
        $this->command->info('─────────────────────────────────────────────────────');
        $this->call(ComisionesSeeder::class);
        $this->command->info('');

        $this->command->info('═══════════════════════════════════════════════════════');
        $this->command->info('  ✅ SEEDER MAESTRO COMPLETADO EXITOSAMENTE');
        $this->command->info('═══════════════════════════════════════════════════════');
        $this->command->info('');
        $this->command->info('🎯 DATOS GENERADOS:');
        $this->command->info('   • Pedidos con clientes, vendedores y productos');
        $this->command->info('   • Comisiones vinculadas a pedidos reales');
        $this->command->info('   • Solicitudes de pago del líder');
        $this->command->info('');
        $this->command->info('📍 ACCESOS RÁPIDOS:');
        $this->command->info('   • Pedidos Admin: http://127.0.0.1:8000/admin/pedidos');
        $this->command->info('   • Comisiones Líder: http://127.0.0.1:8000/lider/comisiones');
        $this->command->info('');
    }
}
