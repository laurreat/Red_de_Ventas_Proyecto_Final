<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ComisionMongo;
use App\Models\UserMongo;

class TestComision extends Command
{
    protected $signature = 'test:comision';
    protected $description = 'Probar creación de comisiones en MongoDB';

    public function handle()
    {
        $this->info('🧪 Probando ComisionMongo...');

        try {
            // Test crear comisión simple
            $this->info('1. Creando comisión simple...');
            $comision = ComisionMongo::create([
                'tipo' => 'test',
                'monto' => 100,
                'porcentaje' => 10,
                'estado' => 'pendiente'
            ]);

            if ($comision) {
                $this->info("✅ Comisión creada: {$comision->_id}");
            }

            // Verificar que se puede leer
            $count = ComisionMongo::count();
            $this->info("📊 Total comisiones: {$count}");

            // Buscar un usuario para asociar
            $user = UserMongo::first();
            if ($user) {
                $this->info("2. Creando comisión con usuario...");
                $comisionConUser = ComisionMongo::create([
                    'user_id' => $user->_id,
                    'user_data' => [
                        '_id' => $user->_id,
                        'name' => $user->name,
                        'email' => $user->email
                    ],
                    'tipo' => 'venta_directa',
                    'monto' => 250,
                    'porcentaje' => 15,
                    'estado' => 'aprobada'
                ]);

                if ($comisionConUser) {
                    $this->info("✅ Comisión con usuario creada: {$comisionConUser->_id}");
                }
            }

            $finalCount = ComisionMongo::count();
            $this->info("📊 Total final: {$finalCount}");

            // Mostrar primera comisión
            $primera = ComisionMongo::first();
            if ($primera) {
                $this->info("💰 Primera comisión:");
                $this->info("   ID: {$primera->_id}");
                $this->info("   Tipo: {$primera->tipo}");
                $this->info("   Monto: {$primera->monto}");
            }

        } catch (\Exception $e) {
            $this->error('❌ Error:');
            $this->error($e->getMessage());
            $this->error("Archivo: {$e->getFile()}");
            $this->error("Línea: {$e->getLine()}");
        }
    }
}