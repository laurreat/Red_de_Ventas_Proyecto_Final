<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Support\Str;

class ArepaLlaneritaSeeder extends Seeder
{
    public function run(): void
    {
        // Usuario Administrador
        User::create([
            'name' => 'Carlos',
            'apellidos' => 'Administrador López',
            'cedula' => '12345678',
            'email' => 'admin@arepallanerita.com',
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
            'telefono' => '3001234567',
            'direccion' => 'Calle 123 #45-67',
            'ciudad' => 'Villavicencio',
            'departamento' => 'Meta',
            'fecha_nacimiento' => '1985-05-15',
            'rol' => 'administrador',
            'activo' => true,
            'codigo_referido' => 'ADMIN001',
            'nivel_vendedor' => 5,
        ]);

        // Usuario Líder
        User::create([
            'name' => 'María',
            'apellidos' => 'Líder González',
            'cedula' => '23456789',
            'email' => 'lider@arepallanerita.com',
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
            'telefono' => '3009876543',
            'direccion' => 'Carrera 45 #67-89',
            'ciudad' => 'Villavicencio',
            'departamento' => 'Meta',
            'fecha_nacimiento' => '1988-08-20',
            'rol' => 'lider',
            'activo' => true,
            'codigo_referido' => 'LIDER001',
            'meta_mensual' => 5000000,
            'nivel_vendedor' => 4,
            'comisiones_ganadas' => 850000,
            'comisiones_disponibles' => 250000,
        ]);

        // Usuario Vendedor 1
        User::create([
            'name' => 'Juan',
            'apellidos' => 'Vendedor Pérez',
            'cedula' => '34567890',
            'email' => 'vendedor@arepallanerita.com',
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
            'telefono' => '3001112233',
            'direccion' => 'Avenida 67 #89-01',
            'ciudad' => 'Villavicencio',
            'departamento' => 'Meta',
            'fecha_nacimiento' => '1990-03-10',
            'rol' => 'vendedor',
            'activo' => true,
            'referido_por' => 2, // Referido por el líder
            'codigo_referido' => 'VEND001',
            'total_referidos' => 15,
            'meta_mensual' => 2000000,
            'ventas_mes_actual' => 1350000,
            'nivel_vendedor' => 3,
            'comisiones_ganadas' => 450000,
            'comisiones_disponibles' => 120000,
        ]);

        // Usuario Vendedor 2
        User::create([
            'name' => 'Ana',
            'apellidos' => 'Vendedor Ramírez',
            'cedula' => '45678901',
            'email' => 'ana.vendedor@arepallanerita.com',
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
            'telefono' => '3004445566',
            'direccion' => 'Calle 78 #90-12',
            'ciudad' => 'Acacías',
            'departamento' => 'Meta',
            'fecha_nacimiento' => '1992-11-25',
            'rol' => 'vendedor',
            'activo' => true,
            'referido_por' => 3, // Referido por Juan
            'codigo_referido' => 'VEND002',
            'total_referidos' => 8,
            'meta_mensual' => 1500000,
            'ventas_mes_actual' => 980000,
            'nivel_vendedor' => 2,
            'comisiones_ganadas' => 180000,
            'comisiones_disponibles' => 75000,
        ]);

        // Usuarios Clientes
        $clientes = [
            [
                'name' => 'Pedro',
                'apellidos' => 'Cliente Martínez',
                'cedula' => '56789012',
                'email' => 'pedro@gmail.com',
                'telefono' => '3005556677',
                'ciudad' => 'Villavicencio',
                'referido_por' => 3,
            ],
            [
                'name' => 'Laura',
                'apellidos' => 'Cliente Rodríguez',
                'cedula' => '67890123',
                'email' => 'laura@hotmail.com',
                'telefono' => '3006667788',
                'ciudad' => 'Granada',
                'referido_por' => 4,
            ],
            [
                'name' => 'Diego',
                'apellidos' => 'Cliente Hernández',
                'cedula' => '78901234',
                'email' => 'diego@yahoo.com',
                'telefono' => '3007778899',
                'ciudad' => 'Villavicencio',
                'referido_por' => 3,
            ]
        ];

        foreach ($clientes as $cliente) {
            User::create([
                'name' => $cliente['name'],
                'apellidos' => $cliente['apellidos'],
                'cedula' => $cliente['cedula'],
                'email' => $cliente['email'],
                'email_verified_at' => now(),
                'password' => Hash::make('password'),
                'telefono' => $cliente['telefono'],
                'direccion' => 'Dirección ejemplo',
                'ciudad' => $cliente['ciudad'],
                'departamento' => 'Meta',
                'fecha_nacimiento' => '1995-01-01',
                'rol' => 'cliente',
                'activo' => true,
                'referido_por' => $cliente['referido_por'],
            ]);
        }

        // Actualizar contadores de referidos
        User::where('id', 2)->update(['total_referidos' => 1]); // Líder
        User::where('id', 3)->update(['total_referidos' => 3]); // Juan
        User::where('id', 4)->update(['total_referidos' => 1]); // Ana

        $this->command->info('✅ Usuarios de prueba creados correctamente:');
        $this->command->info('📧 Admin: admin@arepallanerita.com - password: password');
        $this->command->info('📧 Líder: lider@arepallanerita.com - password: password');
        $this->command->info('📧 Vendedor: vendedor@arepallanerita.com - password: password');
        $this->command->info('📧 Vendedor 2: ana.vendedor@arepallanerita.com - password: password');
        $this->command->info('📧 Cliente: pedro@gmail.com - password: password');
    }
}