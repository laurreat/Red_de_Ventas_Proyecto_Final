<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UsersSeeder extends Seeder
{
    public function run(): void
    {
        // Administrador principal
        User::create([
            'name' => 'Administrador',
            'apellidos' => 'Sistema',
            'cedula' => '12345678',
            'email' => 'admin@arepallanerita.com',
            'password' => Hash::make('admin123'),
            'telefono' => '3001234567',
            'direccion' => 'Oficina Principal',
            'ciudad' => 'Villavicencio',
            'departamento' => 'Meta',
            'fecha_nacimiento' => '1985-01-01',
            'rol' => 'administrador',
            'activo' => true,
            'codigo_referido' => 'ADMIN001',
            'meta_mensual' => 0,
            'ventas_mes_actual' => 0,
            'nivel_vendedor' => 0,
            'zonas_asignadas' => ['todas'],
        ]);

        // Líder de ventas
        User::create([
            'name' => 'Carlos',
            'apellidos' => 'Rodríguez',
            'cedula' => '87654321',
            'email' => 'carlos.rodriguez@arepallanerita.com',
            'password' => Hash::make('lider123'),
            'telefono' => '3007654321',
            'direccion' => 'Calle 40 #25-30',
            'ciudad' => 'Villavicencio',
            'departamento' => 'Meta',
            'fecha_nacimiento' => '1982-05-15',
            'rol' => 'lider',
            'activo' => true,
            'codigo_referido' => 'LIDER001',
            'total_referidos' => 24,
            'comisiones_ganadas' => 195000.00,
            'comisiones_disponibles' => 125000.00,
            'meta_mensual' => 4000000.00,
            'ventas_mes_actual' => 3250000.00,
            'nivel_vendedor' => 3,
            'zonas_asignadas' => ['centro', 'norte'],
        ]);

        // Vendedores del equipo del líder
        $vendedores = [
            [
                'name' => 'Ana',
                'apellidos' => 'López',
                'cedula' => '11223344',
                'email' => 'ana.lopez@arepallanerita.com',
                'telefono' => '3011223344',
                'codigo_referido' => 'VEND001',
                'referido_por' => 2, // Carlos Rodríguez
                'total_referidos' => 12,
                'meta_mensual' => 800000.00,
                'ventas_mes_actual' => 720000.00,
                'comisiones_ganadas' => 72000.00,
                'comisiones_disponibles' => 45000.00,
                'nivel_vendedor' => 2,
                'zonas_asignadas' => ['centro'],
            ],
            [
                'name' => 'Pedro',
                'apellidos' => 'Castro',
                'cedula' => '22334455',
                'email' => 'pedro.castro@arepallanerita.com',
                'telefono' => '3022334455',
                'codigo_referido' => 'VEND002',
                'referido_por' => 2,
                'total_referidos' => 5,
                'meta_mensual' => 500000.00,
                'ventas_mes_actual' => 390000.00,
                'comisiones_ganadas' => 39000.00,
                'comisiones_disponibles' => 25000.00,
                'nivel_vendedor' => 1,
                'zonas_asignadas' => ['sur'],
            ],
            [
                'name' => 'Carmen',
                'apellidos' => 'Torres',
                'cedula' => '33445566',
                'email' => 'carmen.torres@arepallanerita.com',
                'telefono' => '3033445566',
                'codigo_referido' => 'VEND003',
                'referido_por' => 2,
                'total_referidos' => 15,
                'meta_mensual' => 900000.00,
                'ventas_mes_actual' => 840000.00,
                'comisiones_ganadas' => 84000.00,
                'comisiones_disponibles' => 60000.00,
                'nivel_vendedor' => 2,
                'zonas_asignadas' => ['norte'],
            ],
            [
                'name' => 'Miguel',
                'apellidos' => 'Vargas',
                'cedula' => '44556677',
                'email' => 'miguel.vargas@arepallanerita.com',
                'telefono' => '3044556677',
                'codigo_referido' => 'VEND004',
                'referido_por' => 2,
                'total_referidos' => 3,
                'meta_mensual' => 400000.00,
                'ventas_mes_actual' => 320000.00,
                'comisiones_ganadas' => 32000.00,
                'comisiones_disponibles' => 20000.00,
                'nivel_vendedor' => 1,
                'zonas_asignadas' => ['este'],
            ],
            [
                'name' => 'Lucía',
                'apellidos' => 'Herrera',
                'cedula' => '55667788',
                'email' => 'lucia.herrera@arepallanerita.com',
                'telefono' => '3055667788',
                'codigo_referido' => 'VEND005',
                'referido_por' => 2,
                'total_referidos' => 7,
                'meta_mensual' => 600000.00,
                'ventas_mes_actual' => 495000.00,
                'comisiones_ganadas' => 49500.00,
                'comisiones_disponibles' => 30000.00,
                'nivel_vendedor' => 1,
                'zonas_asignadas' => ['oeste'],
            ]
        ];

        foreach ($vendedores as $vendedor) {
            User::create(array_merge($vendedor, [
                'password' => Hash::make('vendedor123'),
                'direccion' => 'Dirección de ' . $vendedor['name'],
                'ciudad' => 'Villavicencio',
                'departamento' => 'Meta',
                'fecha_nacimiento' => '1990-01-01',
                'rol' => 'vendedor',
                'activo' => true,
            ]));
        }

        // Clientes
        $clientes = [
            [
                'name' => 'María',
                'apellidos' => 'González',
                'cedula' => '66778899',
                'email' => 'maria.gonzalez@email.com',
                'telefono' => '3066778899',
                'direccion' => 'Calle 45 #23-12',
                'referido_por' => 3, // Ana López
                'total_referidos' => 2,
            ],
            [
                'name' => 'José',
                'apellidos' => 'Martínez',
                'cedula' => '77889900',
                'email' => 'jose.martinez@email.com',
                'telefono' => '3077889900',
                'direccion' => 'Carrera 30 #15-45',
                'referido_por' => 3,
                'total_referidos' => 0,
            ],
            [
                'name' => 'Laura',
                'apellidos' => 'Silva',
                'cedula' => '88990011',
                'email' => 'laura.silva@email.com',
                'telefono' => '3088990011',
                'direccion' => 'Calle 20 #10-25',
                'referido_por' => 4, // Pedro Castro
                'total_referidos' => 1,
            ],
            [
                'name' => 'Roberto',
                'apellidos' => 'Díaz',
                'cedula' => '99001122',
                'email' => 'roberto.diaz@email.com',
                'telefono' => '3099001122',
                'direccion' => 'Carrera 25 #35-60',
                'referido_por' => 5, // Carmen Torres
                'total_referidos' => 0,
            ],
            [
                'name' => 'Sandra',
                'apellidos' => 'Jiménez',
                'cedula' => '10203040',
                'email' => 'sandra.jimenez@email.com',
                'telefono' => '3010203040',
                'direccion' => 'Calle 50 #40-15',
                'referido_por' => 6, // Miguel Vargas
                'total_referidos' => 3,
            ]
        ];

        foreach ($clientes as $cliente) {
            User::create(array_merge($cliente, [
                'password' => Hash::make('cliente123'),
                'ciudad' => 'Villavicencio',
                'departamento' => 'Meta',
                'fecha_nacimiento' => '1992-01-01',
                'rol' => 'cliente',
                'activo' => true,
                'codigo_referido' => 'CLIENTE' . str_pad(rand(1, 999), 3, '0', STR_PAD_LEFT),
                'meta_mensual' => 0,
                'ventas_mes_actual' => 0,
                'nivel_vendedor' => 0,
                'zonas_asignadas' => null,
            ]));
        }
    }
}