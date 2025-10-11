<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Crear usuario administrador
        $admin = User::create([
            'name' => 'Administrador',
            'apellidos' => 'Principal',
            'cedula' => '12345678',
            'email' => 'admin@arepallanerita.com',
            'password' => Hash::make('admin123'),
            'telefono' => '3001234567',
            'direccion' => 'Oficina Principal - Arepa la Llanerita',
            'ciudad' => 'Villavicencio',
            'departamento' => 'Meta',
            'fecha_nacimiento' => '1980-01-01',
            'rol' => 'administrador',
            'activo' => true,
            'referido_por' => null,
            'codigo_referido' => 'ADMIN001',
            'total_referidos' => 0,
            'comisiones_ganadas' => 0.00,
            'comisiones_disponibles' => 0.00,
            'meta_mensual' => 1000000.00,
            'ventas_mes_actual' => 450000.00,
            'nivel_vendedor' => 0,
            'zonas_asignadas' => null,
            'email_verified_at' => now(),
        ]);

        // Crear líder de ventas
        $lider = User::create([
            'name' => 'Carlos',
            'apellidos' => 'Rodriguez',
            'cedula' => '87654321',
            'email' => 'carlos.rodriguez@arepallanerita.com',
            'password' => Hash::make('lider123'),
            'telefono' => '3009876543',
            'direccion' => 'Calle 15 #10-25',
            'ciudad' => 'Villavicencio',
            'departamento' => 'Meta',
            'fecha_nacimiento' => '1985-05-15',
            'rol' => 'lider',
            'activo' => true,
            'referido_por' => $admin->id,
            'codigo_referido' => 'LIDER001',
            'total_referidos' => 3,
            'comisiones_ganadas' => 250000.00,
            'comisiones_disponibles' => 50000.00,
            'meta_mensual' => 500000.00,
            'ventas_mes_actual' => 320000.00,
            'nivel_vendedor' => 2,
            'zonas_asignadas' => 'Norte, Centro',
            'email_verified_at' => now(),
        ]);

        // Crear vendedores
        $vendedor1 = User::create([
            'name' => 'Ana',
            'apellidos' => 'Lopez',
            'cedula' => '11223344',
            'email' => 'ana.lopez@arepallanerita.com',
            'password' => Hash::make('vendedor123'),
            'telefono' => '3011234567',
            'direccion' => 'Carrera 20 #8-15',
            'ciudad' => 'Villavicencio',
            'departamento' => 'Meta',
            'fecha_nacimiento' => '1990-08-20',
            'rol' => 'vendedor',
            'activo' => true,
            'referido_por' => $lider->id,
            'codigo_referido' => 'VEND001',
            'total_referidos' => 5,
            'comisiones_ganadas' => 180000.00,
            'comisiones_disponibles' => 30000.00,
            'meta_mensual' => 200000.00,
            'ventas_mes_actual' => 150000.00,
            'nivel_vendedor' => 1,
            'zonas_asignadas' => 'Norte',
            'email_verified_at' => now(),
        ]);

        User::create([
            'name' => 'Miguel',
            'apellidos' => 'Torres',
            'cedula' => '55667788',
            'email' => 'miguel.torres@arepallanerita.com',
            'password' => Hash::make('vendedor123'),
            'telefono' => '3022345678',
            'direccion' => 'Avenida 40 #25-10',
            'ciudad' => 'Villavicencio',
            'departamento' => 'Meta',
            'fecha_nacimiento' => '1988-12-10',
            'rol' => 'vendedor',
            'activo' => true,
            'referido_por' => $lider->id,
            'codigo_referido' => 'VEND002',
            'total_referidos' => 2,
            'comisiones_ganadas' => 120000.00,
            'comisiones_disponibles' => 20000.00,
            'meta_mensual' => 180000.00,
            'ventas_mes_actual' => 95000.00,
            'nivel_vendedor' => 1,
            'zonas_asignadas' => 'Centro',
            'email_verified_at' => now(),
        ]);

        // Crear clientes
        User::create([
            'name' => 'Maria',
            'apellidos' => 'Gonzalez',
            'cedula' => '99887766',
            'email' => 'maria.gonzalez@email.com',
            'password' => Hash::make('cliente123'),
            'telefono' => '3033456789',
            'direccion' => 'Barrio La Esperanza, Calle 8 #12-34',
            'ciudad' => 'Villavicencio',
            'departamento' => 'Meta',
            'fecha_nacimiento' => '1992-03-25',
            'rol' => 'cliente',
            'activo' => true,
            'referido_por' => $vendedor1->id,
            'codigo_referido' => 'CLI001',
            'total_referidos' => 1,
            'comisiones_ganadas' => 0.00,
            'comisiones_disponibles' => 0.00,
            'meta_mensual' => 0.00,
            'ventas_mes_actual' => 0.00,
            'nivel_vendedor' => 0,
            'zonas_asignadas' => null,
            'email_verified_at' => now(),
        ]);

        User::create([
            'name' => 'Pedro',
            'apellidos' => 'Ramirez',
            'cedula' => '44332211',
            'email' => 'pedro.ramirez@email.com',
            'password' => Hash::make('cliente123'),
            'telefono' => '3044567890',
            'direccion' => 'Barrio Los Pinos, Carrera 15 #20-45',
            'ciudad' => 'Villavicencio',
            'departamento' => 'Meta',
            'fecha_nacimiento' => '1987-11-30',
            'rol' => 'cliente',
            'activo' => true,
            'referido_por' => $vendedor1->id,
            'codigo_referido' => 'CLI002',
            'total_referidos' => 0,
            'comisiones_ganadas' => 0.00,
            'comisiones_disponibles' => 0.00,
            'meta_mensual' => 0.00,
            'ventas_mes_actual' => 0.00,
            'nivel_vendedor' => 0,
            'zonas_asignadas' => null,
            'email_verified_at' => now(),
        ]);
    }
}
