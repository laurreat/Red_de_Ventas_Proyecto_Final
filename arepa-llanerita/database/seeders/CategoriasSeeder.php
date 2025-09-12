<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Categoria;

class CategoriasSeeder extends Seeder
{
    public function run(): void
    {
        $categorias = [
            [
                'nombre' => 'Arepas Tradicionales',
                'descripcion' => 'Las arepas clásicas de la tradición llanera',
                'activo' => true,
            ],
            [
                'nombre' => 'Arepas con Carne',
                'descripcion' => 'Arepas rellenas con diferentes tipos de carne',
                'activo' => true,
            ],
            [
                'nombre' => 'Arepas con Pollo',
                'descripcion' => 'Arepas rellenas con pollo en sus diversas preparaciones',
                'activo' => true,
            ],
            [
                'nombre' => 'Arepas Especiales',
                'descripcion' => 'Arepas con ingredientes únicos y especiales',
                'activo' => true,
            ],
            [
                'nombre' => 'Arepas Combinadas',
                'descripcion' => 'Arepas con múltiples ingredientes combinados',
                'activo' => true,
            ],
            [
                'nombre' => 'Bebidas Tradicionales',
                'descripcion' => 'Bebidas típicas de los llanos orientales',
                'activo' => true,
            ],
            [
                'nombre' => 'Bebidas Frías',
                'descripcion' => 'Refrescos y bebidas frías para acompañar',
                'activo' => true,
            ],
            [
                'nombre' => 'Postres',
                'descripcion' => 'Dulces y postres tradicionales llaneros',
                'activo' => true,
            ],
        ];

        foreach ($categorias as $categoria) {
            Categoria::create($categoria);
        }
    }
}