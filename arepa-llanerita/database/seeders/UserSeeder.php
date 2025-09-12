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
        // Crear usuarios de prueba
        User::create([
            'name' => 'Admin',
            'apellidos' => 'Principal',
            'cedula' => '12345678',
            'email' => 'admin@arepallanerita.com',
            'password' => Hash::make('admin123'),
            'telefono' => '3001234567',
            'direccion' => 'Calle 123 #45-67',
            'ciudad' => 'Bogotá',
            'departamento' => 'Cundinamarca',
            'rol' => 'administrador',
            'activo' => true,
            'codigo_referido' => 'ADMIN001',
            'email_verified_at' => now(),
        ]);

        User::create([
            'name' => 'Juan Carlos',
            'apellidos' => 'Pérez López',
            'cedula' => '87654321',
            'email' => 'lider@arepallanerita.com',
            'password' => Hash::make('lider123'),
            'telefono' => '3009876543',
            'direccion' => 'Carrera 456 #78-90',
            'ciudad' => 'Bogotá',
            'departamento' => 'Cundinamarca',
            'rol' => 'lider',
            'activo' => true,
            'codigo_referido' => 'LIDER001',
            'meta_mensual' => 5000000,
            'email_verified_at' => now(),
        ]);

        User::create([
            'name' => 'María Elena',
            'apellidos' => 'González Ruiz',
            'cedula' => '11223344',
            'email' => 'vendedor@arepallanerita.com',
            'password' => Hash::make('vendedor123'),
            'telefono' => '3001122334',
            'direccion' => 'Avenida 789 #12-34',
            'ciudad' => 'Bogotá',
            'departamento' => 'Cundinamarca',
            'rol' => 'vendedor',
            'activo' => true,
            'codigo_referido' => 'VEND001',
            'referido_por' => 2,
            'meta_mensual' => 1000000,
            'email_verified_at' => now(),
        ]);

        User::create([
            'name' => 'Carlos Alberto',
            'apellidos' => 'Rodríguez Mora',
            'cedula' => '99887766',
            'email' => 'cliente@test.com',
            'password' => Hash::make('cliente123'),
            'telefono' => '3009988776',
            'direccion' => 'Calle Cliente #56-78',
            'ciudad' => 'Bogotá',
            'departamento' => 'Cundinamarca',
            'rol' => 'cliente',
            'activo' => true,
            'codigo_referido' => 'CLIENTE001',
            'referido_por' => 3,
            'email_verified_at' => now(),
        ]);
    }
}
