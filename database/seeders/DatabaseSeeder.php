<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RolesSeeder::class,           // Roles y permisos
            UserSeeder::class,             // Usuarios del sistema
            ProductosSeeder::class,        // Productos
            ConfiguracionSeeder::class,    // Configuraciones
            CapacitacionSeeder::class,     // Módulos de capacitación para líderes
        ]);
    }
}
