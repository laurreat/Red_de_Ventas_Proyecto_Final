<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Producto;
use App\Models\Categoria;

class ProductosSeeder extends Seeder
{
    public function run(): void
    {
        // Crear categorías si no existen
        $categorias = [
            ['nombre' => 'Arepas Tradicionales', 'descripcion' => 'Arepas tradicionales llaneras', 'activo' => true],
            ['nombre' => 'Arepas con Carne', 'descripcion' => 'Arepas con diferentes tipos de carne', 'activo' => true],
            ['nombre' => 'Arepas con Pollo', 'descripcion' => 'Arepas con pollo en diferentes preparaciones', 'activo' => true],
            ['nombre' => 'Arepas Especiales', 'descripcion' => 'Arepas con preparaciones especiales', 'activo' => true],
            ['nombre' => 'Arepas Combinadas', 'descripcion' => 'Arepas con múltiples ingredientes', 'activo' => true],
            ['nombre' => 'Bebidas Tradicionales', 'descripcion' => 'Bebidas típicas de los llanos', 'activo' => true],
            ['nombre' => 'Bebidas Frías', 'descripcion' => 'Jugos y bebidas refrescantes', 'activo' => true],
            ['nombre' => 'Postres', 'descripcion' => 'Postres tradicionales llaneros', 'activo' => true],
        ];

        foreach ($categorias as $categoria) {
            Categoria::firstOrCreate(
                ['nombre' => $categoria['nombre']],
                $categoria
            );
        }

        // Obtener las categorías
        $arepasTradicionales = Categoria::where('nombre', 'Arepas Tradicionales')->first();
        $arepasCarne = Categoria::where('nombre', 'Arepas con Carne')->first();
        $arepasPollo = Categoria::where('nombre', 'Arepas con Pollo')->first();
        $arepasEspeciales = Categoria::where('nombre', 'Arepas Especiales')->first();
        $arepasCombinadas = Categoria::where('nombre', 'Arepas Combinadas')->first();
        $bebidasTradicionales = Categoria::where('nombre', 'Bebidas Tradicionales')->first();
        $bebidasFrias = Categoria::where('nombre', 'Bebidas Frías')->first();
        $postres = Categoria::where('nombre', 'Postres')->first();

        $productos = [
            // Arepas Tradicionales
            [
                'nombre' => 'Arepa de Queso Llanero',
                'descripcion' => 'Arepa tradicional rellena con queso fresco llanero, cremosa y deliciosa',
                'categoria_id' => $arepasTradicionales->id,
                'precio' => 18000.00,
                'stock' => 50,
                'stock_minimo' => 10,
                'activo' => true,
                'imagen' => 'arepa-queso-llanero.jpg',
            ],
            [
                'nombre' => 'Arepa de Cuajada',
                'descripcion' => 'Arepa con cuajada fresca, suave y tradicional',
                'categoria_id' => $arepasTradicionales->id,
                'precio' => 16000.00,
                'stock' => 40,
                'stock_minimo' => 8,
                'activo' => true,
                'imagen' => 'arepa-cuajada.jpg',
            ],
            [
                'nombre' => 'Arepa Sola',
                'descripcion' => 'Arepa tradicional sin relleno, perfecta para acompañar',
                'categoria_id' => $arepasTradicionales->id,
                'precio' => 8000.00,
                'stock' => 100,
                'stock_minimo' => 20,
                'activo' => true,
                'imagen' => 'arepa-sola.jpg',
            ],

            // Arepas con Carne
            [
                'nombre' => 'Arepa de Carne Mechada',
                'descripcion' => 'Arepa rellena con carne mechada jugosa y bien condimentada',
                'categoria_id' => $arepasCarne->id,
                'precio' => 25000.00,
                'stock' => 30,
                'stock_minimo' => 5,
                'activo' => true,
                'imagen' => 'arepa-carne-mechada.jpg',
            ],
            [
                'nombre' => 'Arepa de Carne Asada',
                'descripcion' => 'Arepa con carne asada a la parrilla, sabor único',
                'categoria_id' => $arepasCarne->id,
                'precio' => 28000.00,
                'stock' => 25,
                'stock_minimo' => 5,
                'activo' => true,
                'imagen' => 'arepa-carne-asada.jpg',
            ],
            [
                'nombre' => 'Arepa de Chicharrón',
                'descripcion' => 'Arepa rellena con chicharrón crujiente y sabroso',
                'categoria_id' => $arepasCarne->id,
                'precio' => 22000.00,
                'stock' => 35,
                'stock_minimo' => 7,
                'activo' => true,
                'imagen' => 'arepa-chicharron.jpg',
            ],

            // Arepas con Pollo
            [
                'nombre' => 'Arepa de Pollo Desmechado',
                'descripcion' => 'Arepa con pollo desmechado en salsa especial',
                'categoria_id' => $arepasPollo->id,
                'precio' => 23000.00,
                'stock' => 40,
                'stock_minimo' => 8,
                'activo' => true,
                'imagen' => 'arepa-pollo-desmechado.jpg',
            ],
            [
                'nombre' => 'Arepa de Pollo Guisado',
                'descripcion' => 'Arepa con pollo guisado con vegetales frescos',
                'categoria_id' => $arepasPollo->id,
                'precio' => 24000.00,
                'stock' => 30,
                'stock_minimo' => 6,
                'activo' => true,
                'imagen' => 'arepa-pollo-guisado.jpg',
            ],

            // Arepas Combinadas
            [
                'nombre' => 'Arepa Mixta (Queso + Carne)',
                'descripcion' => 'La combinación perfecta: queso llanero y carne mechada',
                'categoria_id' => $arepasCombinadas->id,
                'precio' => 32000.00,
                'stock' => 20,
                'stock_minimo' => 4,
                'activo' => true,
                'imagen' => 'arepa-mixta-queso-carne.jpg',
            ],
            [
                'nombre' => 'Arepa Llanera Especial',
                'descripcion' => 'Arepa con queso, carne mechada, aguacate y suero costeño',
                'categoria_id' => $arepasCombinadas->id,
                'precio' => 35000.00,
                'stock' => 15,
                'stock_minimo' => 3,
                'activo' => true,
                'imagen' => 'arepa-llanera-especial.jpg',
            ],

            // Arepas Especiales
            [
                'nombre' => 'Arepa de Huevo Perico',
                'descripcion' => 'Arepa rellena con huevo perico tradicional',
                'categoria_id' => $arepasEspeciales->id,
                'precio' => 20000.00,
                'stock' => 25,
                'stock_minimo' => 5,
                'activo' => true,
                'imagen' => 'arepa-huevo-perico.jpg',
            ],
            [
                'nombre' => 'Arepa Vegetariana',
                'descripcion' => 'Arepa con aguacate, tomate, cebolla y queso',
                'categoria_id' => $arepasEspeciales->id,
                'precio' => 19000.00,
                'stock' => 20,
                'stock_minimo' => 4,
                'activo' => true,
                'imagen' => 'arepa-vegetariana.jpg',
            ],

            // Bebidas Tradicionales
            [
                'nombre' => 'Chicha Llanera',
                'descripcion' => 'Bebida tradicional de arroz dulce con canela',
                'categoria_id' => $bebidasTradicionales->id,
                'precio' => 12000.00,
                'stock' => 60,
                'stock_minimo' => 15,
                'activo' => true,
                'imagen' => 'chicha-llanera.jpg',
            ],
            [
                'nombre' => 'Guarapo de Caña',
                'descripcion' => 'Jugo fresco de caña de azúcar natural',
                'categoria_id' => $bebidasTradicionales->id,
                'precio' => 10000.00,
                'stock' => 40,
                'stock_minimo' => 10,
                'activo' => true,
                'imagen' => 'guarapo-cana.jpg',
            ],

            // Bebidas Frías
            [
                'nombre' => 'Jugo de Maracuyá',
                'descripcion' => 'Refrescante jugo natural de maracuyá',
                'categoria_id' => $bebidasFrias->id,
                'precio' => 8000.00,
                'stock' => 50,
                'stock_minimo' => 12,
                'activo' => true,
                'imagen' => 'jugo-maracuya.jpg',
            ],
            [
                'nombre' => 'Limonada Natural',
                'descripcion' => 'Limonada fresca con limón natural y panela',
                'categoria_id' => $bebidasFrias->id,
                'precio' => 7000.00,
                'stock' => 60,
                'stock_minimo' => 15,
                'activo' => true,
                'imagen' => 'limonada-natural.jpg',
            ],

            // Postres
            [
                'nombre' => 'Quesillo Llanero',
                'descripcion' => 'Postre tradicional de leche y caramelo',
                'categoria_id' => $postres->id,
                'precio' => 15000.00,
                'stock' => 20,
                'stock_minimo' => 5,
                'activo' => true,
                'imagen' => 'quesillo-llanero.jpg',
            ],
            [
                'nombre' => 'Dulce de Lechosa',
                'descripcion' => 'Dulce tradicional de papaya en almíbar',
                'categoria_id' => $postres->id,
                'precio' => 12000.00,
                'stock' => 15,
                'stock_minimo' => 3,
                'activo' => true,
                'imagen' => 'dulce-lechosa.jpg',
            ],
        ];

        foreach ($productos as $producto) {
            Producto::firstOrCreate(
                ['nombre' => $producto['nombre']],
                $producto
            );
        }

        $this->command->info('✓ Productos y categorías creados/actualizados exitosamente');
    }
}