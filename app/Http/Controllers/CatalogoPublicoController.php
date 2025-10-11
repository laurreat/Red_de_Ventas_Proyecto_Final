<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use App\Models\Categoria;
use Illuminate\Http\Request;

class CatalogoPublicoController extends Controller
{
    public function index(Request $request)
    {
        // Consulta más robusta que incluye productos sin campo activo definido
        $query = Producto::where(function($q) {
            $q->where('activo', true)
              ->orWhereNull('activo'); // Incluir productos sin campo activo definido
        })->with('categoria');

        // Filtros
        if ($request->has('categoria') && $request->categoria != '') {
            $query->where('categoria_id', $request->categoria);
        }

        if ($request->has('buscar') && $request->buscar != '') {
            $query->where(function($q) use ($request) {
                $q->where('nombre', 'like', '%' . $request->buscar . '%')
                  ->orWhere('descripcion', 'like', '%' . $request->buscar . '%');
            });
        }

        $productos = $query->orderBy('nombre')->paginate(12);

        // Cargar categorías activas o sin campo activo definido
        $categorias = Categoria::where(function($q) {
            $q->where('activo', true)
              ->orWhereNull('activo');
        })->orderBy('nombre')->get();

        // Calcular el contador de productos por categoría (incluyendo productos sin activo definido)
        foreach ($categorias as $categoria) {
            $categoria->productos_count = Producto::where(function($q) {
                $q->where('activo', true)
                  ->orWhereNull('activo');
            })->where('categoria_id', $categoria->id)->count();
        }

        // Estadísticas generales
        $stats = [
            'total_productos' => Producto::where(function($q) {
                $q->where('activo', true)
                  ->orWhereNull('activo');
            })->count(),
            'total_categorias' => $categorias->count(),
            'productos_filtrados' => $productos->total()
        ];

        return view('catalogo-publico.index', compact('productos', 'categorias', 'stats'));
    }

    public function show(Producto $producto)
    {
        // Verificar que el producto esté activo o no tenga el campo activo definido (asumiendo activo)
        if (isset($producto->activo) && !$producto->activo) {
            abort(404);
        }

        $producto->load('categoria');

        // Productos relacionados (activos o sin campo activo definido)
        $productosRelacionados = Producto::where('categoria_id', $producto->categoria_id)
            ->where('id', '!=', $producto->id)
            ->where(function($q) {
                $q->where('activo', true)
                  ->orWhereNull('activo');
            })
            ->limit(4)
            ->get();

        // Cargar categorías para el sidebar
        $categorias = Categoria::where(function($q) {
            $q->where('activo', true)
              ->orWhereNull('activo');
        })->orderBy('nombre')->get();

        // Calcular el contador de productos por categoría
        foreach ($categorias as $categoria) {
            $categoria->productos_count = Producto::where(function($q) {
                $q->where('activo', true)
                  ->orWhereNull('activo');
            })->where('categoria_id', $categoria->id)->count();
        }

        return view('catalogo-publico.show', compact('producto', 'productosRelacionados', 'categorias'));
    }
}