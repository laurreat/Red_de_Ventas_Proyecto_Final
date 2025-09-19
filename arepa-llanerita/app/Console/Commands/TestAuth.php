<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\UserMongo;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class TestAuth extends Command
{
    protected $signature = 'test:auth';
    protected $description = 'Probar autenticación con MongoDB';

    public function handle()
    {
        $this->info('🔐 Probando autenticación con MongoDB...');

        try {
            // Verificar usuarios en MongoDB
            $this->info('\n👥 Usuarios disponibles en MongoDB:');
            $users = UserMongo::all();

            foreach ($users as $user) {
                $this->info("   📧 {$user->email} - {$user->rol} - Activo: " . ($user->activo ? 'Sí' : 'No'));
            }

            // Probar autenticación manual
            $adminUser = UserMongo::where('email', 'admin@arepallanerita.com')->first();

            if ($adminUser) {
                $this->info("\n🔍 Probando usuario admin:");
                $this->info("   ID: {$adminUser->_id}");
                $this->info("   Email: {$adminUser->email}");
                $this->info("   Nombre: {$adminUser->name} {$adminUser->apellidos}");
                $this->info("   Rol: {$adminUser->rol}");

                // Verificar password
                if (Hash::check('password123', $adminUser->password)) {
                    $this->info("   ✅ Password correcto");
                } else {
                    $this->error("   ❌ Password incorrecto");
                }

                // Probar login programático
                $this->info("\n🔓 Probando login programático:");
                if (Auth::attempt(['email' => 'admin@arepallanerita.com', 'password' => 'password123'])) {
                    $loggedUser = Auth::user();
                    $this->info("   ✅ Login exitoso");
                    $this->info("   👤 Usuario logueado: {$loggedUser->name}");
                    $this->info("   🏷️ Rol: {$loggedUser->rol}");

                    Auth::logout();
                    $this->info("   ✅ Logout exitoso");
                } else {
                    $this->error("   ❌ Login falló");
                }
            } else {
                $this->error("❌ No se encontró usuario admin");
            }

            // Probar crear nuevo usuario
            $this->info("\n➕ Probando creación de usuario:");
            $newUser = UserMongo::create([
                'name' => 'Test',
                'apellidos' => 'User',
                'cedula' => '12345678901',
                'email' => 'test@test.com',
                'password' => Hash::make('test123'),
                'telefono' => '3000000000',
                'ciudad' => 'Test City',
                'departamento' => 'Test Dep',
                'rol' => 'cliente',
                'activo' => true,
                'codigo_referido' => 'TEST123'
            ]);

            if ($newUser) {
                $this->info("   ✅ Usuario creado: {$newUser->_id}");

                // Probar login con nuevo usuario
                if (Auth::attempt(['email' => 'test@test.com', 'password' => 'test123'])) {
                    $this->info("   ✅ Login con nuevo usuario exitoso");
                    Auth::logout();
                } else {
                    $this->error("   ❌ Login con nuevo usuario falló");
                }

                // Eliminar usuario de prueba
                $newUser->delete();
                $this->info("   ✅ Usuario de prueba eliminado");
            }

            $this->info("\n🎉 Pruebas de autenticación completadas!");

        } catch (\Exception $e) {
            $this->error('❌ Error en pruebas de autenticación:');
            $this->error($e->getMessage());
            $this->error("Archivo: {$e->getFile()}");
            $this->error("Línea: {$e->getLine()}");
        }
    }
}