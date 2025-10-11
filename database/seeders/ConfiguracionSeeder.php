<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Configuracion;

class ConfiguracionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $configuraciones = [
            // Configuración General
            [
                'clave' => 'nombre_empresa',
                'valor' => ['valor' => 'Arepa la Llanerita'],
                'tipo' => 'string',
                'categoria' => 'general',
                'descripcion' => 'Nombre de la empresa',
                'es_publica' => true,
                'es_editable' => true,
                'grupo' => 'general',
                'orden' => 1
            ],
            [
                'clave' => 'email_empresa',
                'valor' => ['valor' => 'admin@arepa-llanerita.com'],
                'tipo' => 'string',
                'categoria' => 'general',
                'descripcion' => 'Email de contacto de la empresa',
                'es_publica' => true,
                'es_editable' => true,
                'grupo' => 'general',
                'orden' => 2
            ],
            [
                'clave' => 'telefono_empresa',
                'valor' => ['valor' => '(57) 300 123 4567'],
                'tipo' => 'string',
                'categoria' => 'general',
                'descripcion' => 'Teléfono de la empresa',
                'es_publica' => true,
                'es_editable' => true,
                'grupo' => 'general',
                'orden' => 3
            ],
            [
                'clave' => 'direccion_empresa',
                'valor' => ['valor' => 'Calle 123 #45-67, Bogotá, Colombia'],
                'tipo' => 'string',
                'categoria' => 'general',
                'descripcion' => 'Dirección de la empresa',
                'es_publica' => true,
                'es_editable' => true,
                'grupo' => 'general',
                'orden' => 4
            ],

            // Configuración MLM
            [
                'clave' => 'comision_directa',
                'valor' => ['valor' => 10.0],
                'tipo' => 'float',
                'categoria' => 'mlm',
                'descripcion' => 'Porcentaje de comisión directa',
                'es_publica' => false,
                'es_editable' => true,
                'grupo' => 'mlm',
                'orden' => 1
            ],
            [
                'clave' => 'comision_referido',
                'valor' => ['valor' => 3.0],
                'tipo' => 'float',
                'categoria' => 'mlm',
                'descripcion' => 'Porcentaje de comisión por referido',
                'es_publica' => false,
                'es_editable' => true,
                'grupo' => 'mlm',
                'orden' => 2
            ],
            [
                'clave' => 'comision_lider',
                'valor' => ['valor' => 2.0],
                'tipo' => 'float',
                'categoria' => 'mlm',
                'descripcion' => 'Porcentaje de comisión para líderes',
                'es_publica' => false,
                'es_editable' => true,
                'grupo' => 'mlm',
                'orden' => 3
            ],
            [
                'clave' => 'niveles_maximos',
                'valor' => ['valor' => 5],
                'tipo' => 'integer',
                'categoria' => 'mlm',
                'descripcion' => 'Niveles máximos de la red MLM',
                'es_publica' => false,
                'es_editable' => true,
                'grupo' => 'mlm',
                'orden' => 4
            ],
            [
                'clave' => 'bonificacion_lider',
                'valor' => ['valor' => 5.0],
                'tipo' => 'float',
                'categoria' => 'mlm',
                'descripcion' => 'Porcentaje de bonificación para líderes',
                'es_publica' => false,
                'es_editable' => true,
                'grupo' => 'mlm',
                'orden' => 5
            ],
            [
                'clave' => 'minimo_ventas_mes',
                'valor' => ['valor' => 100000],
                'tipo' => 'integer',
                'categoria' => 'mlm',
                'descripcion' => 'Mínimo de ventas mensuales requerido',
                'es_publica' => false,
                'es_editable' => true,
                'grupo' => 'mlm',
                'orden' => 6
            ],

            // Configuración de Pedidos
            [
                'clave' => 'tiempo_preparacion',
                'valor' => ['valor' => 30],
                'tipo' => 'integer',
                'categoria' => 'pedidos',
                'descripcion' => 'Tiempo de preparación en minutos',
                'es_publica' => true,
                'es_editable' => true,
                'grupo' => 'pedidos',
                'orden' => 1
            ],
            [
                'clave' => 'costo_envio',
                'valor' => ['valor' => 5000],
                'tipo' => 'integer',
                'categoria' => 'pedidos',
                'descripcion' => 'Costo de envío en COP',
                'es_publica' => true,
                'es_editable' => true,
                'grupo' => 'pedidos',
                'orden' => 2
            ],
            [
                'clave' => 'envio_gratis_desde',
                'valor' => ['valor' => 50000],
                'tipo' => 'integer',
                'categoria' => 'pedidos',
                'descripcion' => 'Monto mínimo para envío gratis en COP',
                'es_publica' => true,
                'es_editable' => true,
                'grupo' => 'pedidos',
                'orden' => 3
            ],
        ];

        foreach ($configuraciones as $config) {
            Configuracion::updateOrCreate(
                ['clave' => $config['clave']],
                $config
            );
        }

        $this->command->info('Configuraciones inicializadas correctamente.');
    }
}
