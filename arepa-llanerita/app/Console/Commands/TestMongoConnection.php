<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\TestMongoDB;
use Illuminate\Support\Facades\DB;

class TestMongoConnection extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:mongodb';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Probar conexión a MongoDB';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔄 Probando conexión a MongoDB...');

        try {
            // Probar conexión básica
            $connection = DB::connection('mongodb');
            $this->info('✅ Conexión a MongoDB establecida exitosamente!');

            // Probar inserción de documento
            $test = TestMongoDB::create([
                'nombre' => 'Prueba de conexión desde Laravel',
                'timestamp' => now(),
                'estado' => 'exitoso',
                'detalles' => [
                    'php_version' => PHP_VERSION,
                    'laravel_version' => app()->version(),
                    'fecha_prueba' => now()->format('Y-m-d H:i:s')
                ]
            ]);

            $this->info('✅ Documento insertado en MongoDB exitosamente!');
            $this->info('📍 ID del documento: ' . $test->_id);

            // Probar lectura
            $count = TestMongoDB::count();
            $this->info('📊 Total de documentos en la colección: ' . $count);

            $this->info('🎉 MongoDB está funcionando correctamente con Laravel!');

        } catch (\Exception $e) {
            $this->error('❌ Error al conectar con MongoDB:');
            $this->error($e->getMessage());
            $this->error('📍 Archivo: ' . $e->getFile());
            $this->error('📍 Línea: ' . $e->getLine());
        }
    }
}
