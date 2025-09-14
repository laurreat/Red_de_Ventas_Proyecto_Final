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
            CategoriasSeeder::class,    // Categorías de productos
            ProductosSeeder::class,     // Catálogo de productos
            UserSeeder::class,          // Solo administrador, sin datos demo
            // PedidosSeeder::class,    // Comentado - no hay usuarios demo para pedidos
        ]);
    }
}
