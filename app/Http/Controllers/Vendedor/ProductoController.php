<?php

namespace App\Http\Controllers\Vendedor;

use App\Http\Controllers\Controller;
use App\Models\Producto;
use App\Models\Categoria;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProductoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Producto::query();

        // Filtros
        if ($request->filled('buscar')) {
            $query->where(function($q) use ($request) {
                $q->where('nombre', 'like', '%' . $request->buscar . '%')
                  ->orWhere('descripcion', 'like', '%' . $request->buscar . '%');
            });
        }

        if ($request->filled('categoria')) {
            $query->where('categoria_id', $request->categoria);
        }

        if ($request->filled('estado')) {
            $query->where('activo', $request->estado === 'activo');
        }

        if ($request->filled('stock')) {
            if ($request->stock === 'bajo') {
                // MongoDB: comparar campos usando $expr
                $query->whereRaw(['$expr' => ['$lte' => ['$stock', '$stock_minimo']]]);
            } elseif ($request->stock === 'sin_stock') {
                $query->where('stock', 0);
            }
        }

        // Ordenamiento
        $sort = $request->get('sort', 'created_at');
        $direction = $request->get('direction', 'desc');
        $query->orderBy($sort, $direction);

        $productos = $query->paginate(12);
        
        // Stats
        $stats = [
            'total' => Producto::count(),
            'activos' => Producto::where('activo', true)->count(),
            'inactivos' => Producto::where('activo', false)->count(),
            'bajo_stock' => Producto::whereRaw(['$expr' => ['$lte' => ['$stock', '$stock_minimo']]])->count(),
            'sin_stock' => Producto::where('stock', 0)->count(),
        ];

        $categorias = Categoria::where('activo', true)->get();

        return view('vendedor.productos.index', compact('productos', 'stats', 'categorias'));
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $producto = Producto::findOrFail($id);
        
        // Cargar categoría si existe
        if ($producto->categoria_id) {
            $categoria = Categoria::find($producto->categoria_id);
            if ($categoria) {
                $producto->categoria_data = $categoria->toArray();
            }
        }

        return view('vendedor.productos.show', compact('producto'));
    }

    /**
     * Search products for autocomplete
     */
    public function buscar(Request $request)
    {
        $query = $request->get('q', '');
        
        $productos = Producto::where('activo', true)
            ->where(function($q) use ($query) {
                $q->where('nombre', 'like', '%' . $query . '%')
                  ->orWhere('descripcion', 'like', '%' . $query . '%');
            })
            ->where('stock', '>', 0)
            ->limit(10)
            ->get(['_id', 'nombre', 'precio', 'stock', 'imagen']);

        return response()->json($productos);
    }

    /**
     * Get product details
     */
    public function detalle(string $id)
    {
        $producto = Producto::findOrFail($id);
        
        return response()->json([
            '_id' => $producto->_id,
            'nombre' => $producto->nombre,
            'descripcion' => $producto->descripcion,
            'precio' => to_float($producto->precio),
            'stock' => $producto->stock,
            'imagen' => $producto->imagen,
            'categoria' => $producto->categoria_data['nombre'] ?? 'Sin categoría',
        ]);
    }
}
