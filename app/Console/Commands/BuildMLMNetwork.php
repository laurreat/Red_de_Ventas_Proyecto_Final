<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Pedido;
use Database\Seeders\RedMLMSeeder;

class BuildMLMNetwork extends Command
{
    protected $signature = 'mlm:build {--fresh : Limpiar usuarios existentes antes de crear la red}';
    protected $description = 'Construir una red MLM completa con TODAS las categorías de leyenda, productos reales y métricas completas';

    public function handle()
    {
        $this->info("╔═══════════════════════════════════════════════════════╗");
        $this->info("║   🌟 Constructor de Red MLM - Arepa la Llanerita     ║");
        $this->info("╚═══════════════════════════════════════════════════════╝");
        $this->newLine();

        // Verificar si se debe limpiar
        if ($this->option('fresh')) {
            if ($this->confirm('⚠️  ¿Estás seguro de que deseas ELIMINAR datos calculables y regenerar la red?', false)) {
                $this->warn('🗑️  Eliminando datos calculables...');
                
                // Guardar el admin si existe
                $admin = User::where('email', 'admin@arepallanerita.com')->first();
                $adminId = $admin ? $admin->_id : null;
                
                // ELIMINAR DATOS CALCULABLES (se regeneran)
                // Usuarios (excepto admin)
                $deletedUsers = User::when($adminId, function($query) use ($adminId) {
                    return $query->where('_id', '!=', $adminId);
                })->delete();
                $this->info("   ✅ Eliminados {$deletedUsers} usuarios (preservado admin)");
                
                // Pedidos (incluye detalles embebidos)
                try {
                    $deletedPedidos = \App\Models\Pedido::query()->delete();
                    $this->info("   ✅ Eliminados {$deletedPedidos} pedidos");
                } catch (\Exception $e) {
                    $this->warn("   ⚠️  Error al eliminar pedidos: " . $e->getMessage());
                }
                
                // Detalles de pedidos (colección separada si existe)
                try {
                    $deletedDetalles = \App\Models\DetallePedido::query()->delete();
                    $this->info("   ✅ Eliminados {$deletedDetalles} detalles de pedidos");
                } catch (\Exception $e) {
                    // Si la colección no existe o hay error, continuar
                    $this->info("   ℹ️  Detalles de pedidos: colección vacía o no existe");
                }
                
                // Comisiones
                try {
                    $deletedComisiones = \App\Models\Comision::query()->delete();
                    $this->info("   ✅ Eliminadas {$deletedComisiones} comisiones");
                } catch (\Exception $e) {
                    $this->info("   ℹ️  Comisiones: colección vacía o no existe");
                }
                
                // Referidos (relaciones - si existe colección separada)
                try {
                    $deletedReferidos = \App\Models\Referido::query()->delete();
                    $this->info("   ✅ Eliminadas {$deletedReferidos} relaciones de referidos");
                } catch (\Exception $e) {
                    $this->info("   ℹ️  Referidos: colección vacía o no existe");
                }
                
                // Notificaciones (son calculables/regenerables)
                try {
                    $deletedNotificaciones = \App\Models\Notificacion::query()->delete();
                    $this->info("   ✅ Eliminadas {$deletedNotificaciones} notificaciones");
                } catch (\Exception $e) {
                    $this->info("   ℹ️  Notificaciones: colección vacía o no existe");
                }
                
                // Mensajes de líder (son calculables)
                try {
                    $deletedMensajes = \App\Models\MensajeLider::query()->delete();
                    $this->info("   ✅ Eliminados {$deletedMensajes} mensajes de líder");
                } catch (\Exception $e) {
                    $this->info("   ℹ️  Mensajes de líder: colección vacía o no existe");
                }
                
                // Movimientos de inventario relacionados con pedidos
                try {
                    $deletedMovimientos = \App\Models\MovimientoInventario::where('tipo', 'salida')
                        ->whereNotNull('pedido_id')
                        ->delete();
                    $this->info("   ✅ Eliminados {$deletedMovimientos} movimientos de inventario de pedidos");
                } catch (\Exception $e) {
                    $this->info("   ℹ️  Movimientos de inventario: colección vacía o no existe");
                }
                
                $this->newLine();
                $this->info('📦 PRESERVADO (NO se elimina):');
                $this->info('   ✓ Productos y Categorías');
                $this->info('   ✓ Capacitaciones');
                $this->info('   ✓ Configuraciones del sistema');
                $this->info('   ✓ Cupones');
                $this->info('   ✓ Zonas de entrega');
                $this->info('   ✓ Roles y permisos');
                $this->info('   ✓ Auditorías');
                $this->newLine();
            } else {
                $this->error('Operación cancelada.');
                return 0;
            }
        }

        // Verificar usuarios existentes
        $usuariosExistentes = User::whereIn('rol', ['administrador', 'lider', 'vendedor'])->count();
        
        if ($usuariosExistentes > 50 && !$this->option('fresh')) {
            $this->warn("⚠️  Ya existen {$usuariosExistentes} usuarios en la base de datos.");
            
            if (!$this->confirm('¿Deseas continuar y agregar más usuarios a la red existente?', true)) {
                $this->error('Operación cancelada. Usa --fresh para empezar desde cero.');
                return 0;
            }
        }

        // Ejecutar el seeder
        $this->newLine();
        $this->info('🚀 Construyendo red MLM...');
        $this->newLine();
        
        $bar = $this->output->createProgressBar(6);
        $bar->start();

        try {
            $seeder = new RedMLMSeeder();
            $seeder->setCommand($this);
            $seeder->run();
            
            $bar->finish();
            $this->newLine(2);
            
            $this->info('✨ ¡Red MLM construida exitosamente!');
            $this->newLine();
            
            // Mostrar instrucciones
            $this->showInstructions();
            
            return 0;
            
        } catch (\Exception $e) {
            $bar->finish();
            $this->newLine(2);
            $this->error('❌ Error al construir la red: ' . $e->getMessage());
            $this->error('Traza: ' . $e->getTraceAsString());
            return 1;
        }
    }

    private function showInstructions()
    {
        $this->info("╔═══════════════════════════════════════════════════════╗");
        $this->info("║              📝 INSTRUCCIONES DE USO                  ║");
        $this->info("╚═══════════════════════════════════════════════════════╝");
        $this->newLine();
        
        $this->line("1. Accede a la red MLM:");
        $this->line("   🌐 http://127.0.0.1:8000/admin/referidos");
        $this->newLine();
        
        $this->line("2. Credenciales de acceso:");
        $this->line("   📧 Email: admin@arepallanerita.com");
        $this->line("   🔑 Password: admin123");
        $this->newLine();
        
        $this->line("3. Funcionalidades disponibles:");
        $this->line("   • Ver red completa en visualización interactiva");
        $this->line("   • Click en cualquier nodo para ver detalles");
        $this->line("   • Buscar usuarios por cédula");
        $this->line("   • Filtrar por tipo (líder/vendedor/cliente)");
        $this->line("   • Ver estadísticas en tiempo real");
        $this->line("   • Leyenda con 7 categorías de colores");
        $this->line("   • Pedidos completos con productos y clientes");
        $this->line("   • Métricas reales de ventas");
        $this->newLine();
        
        $this->line("4. Categorías de colores:");
        $this->line("   🏆 Top Ventas: >$5M en ventas (Rojo intenso #DC143C)");
        $this->line("   ⭐ Red Grande: >20 referidos (Rojo oscuro #8B0000)");
        $this->line("   💰 Ventas Altas: $2M-$5M (Dorado #B8860B)");
        $this->line("   ✅ Red Activa: 5-10 referidos (Vino rosado #A8556A)");
        $this->line("   👑 Líder: Rol de líder (Vino tinto #722F37)");
        $this->line("   👤 Vendedor: 1-4 referidos (Rosa claro #C89FA6)");
        $this->line("   🛒 Cliente: Con referidos (Visible en red)");
        $this->line("   ❌ Inactivo: 0 referidos (Rosa pálido #E8D5D9)");
        $this->newLine();

        $this->line("5. Datos generados:");
        $this->line("   ✓ Usuarios con roles diferenciados");
        $this->line("   ✓ TODAS las categorías de leyenda implementadas");
        $this->line("   ✓ Usuarios TOP con ventas >$5M garantizados");
        $this->line("   ✓ Usuarios con >20 referidos garantizados");
        $this->line("   ✓ Productos organizados por categorías");
        $this->line("   ✓ Pedidos completos con detalles de productos");
        $this->line("   ✓ Clientes reales asociados a pedidos");
        $this->line("   ✓ Clientes integrados en red de referidos");
        $this->line("   ✓ Métricas y estadísticas reales");
        $this->newLine();

        $this->info("═══════════════════════════════════════════════════════");
        $this->newLine();
    }
}
