<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Producto;
use App\Models\Categoria;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductoController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            if (!auth()->user()->esAdmin()) {
                abort(403, 'Acceso denegado. Solo administradores.');
            }
            return $next($request);
        });
    }

    public function index(Request $request)
    {
        $query = Producto::with('categoria');

        // Filtros
        if ($request->has('categoria') && $request->categoria != '') {
            $query->where('categoria_id', $request->categoria);
        }

        if ($request->has('estado') && $request->estado != '') {
            $query->where('activo', $request->estado == 'activo');
        }

        if ($request->has('buscar') && $request->buscar != '') {
            $query->where(function($q) use ($request) {
                $q->where('nombre', 'like', '%' . $request->buscar . '%')
                  ->orWhere('descripcion', 'like', '%' . $request->buscar . '%');
            });
        }

        $productos = $query->orderBy('nombre')->paginate(15);
        $categorias = Categoria::orderBy('nombre')->get();

        // Estadísticas
        $stats = [
            'total_productos' => Producto::count(),
            'productos_activos' => Producto::where('activo', true)->count(),
            'productos_stock_bajo' => Producto::where('stock', '<=', 5)->count(),
            'total_categorias' => Categoria::count(),
        ];

        return view('admin.productos.index', compact('productos', 'categorias', 'stats'));
    }

    public function create()
    {
        $categorias = Categoria::orderBy('nombre')->get();
        return view('admin.productos.create', compact('categorias'));
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'nombre' => 'required|string|max:255|min:2',
                'descripcion' => 'nullable|string|max:1000',
                'precio' => 'required|numeric|min:0.01|max:999999.99',
                'categoria_id' => 'required|string|exists:categorias,_id',
                'stock' => 'required|integer|min:0|max:999999',
                'imagen' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120|dimensions:min_width=200,min_height=200,max_width=2000,max_height=2000'
            ], [
                'nombre.min' => 'El nombre debe tener al menos 2 caracteres.',
                'precio.min' => 'El precio debe ser mayor a 0.',
                'precio.max' => 'El precio no puede ser mayor a $999,999.99.',
                'categoria_id.exists' => 'La categoría seleccionada no existe.',
                'stock.max' => 'El stock no puede ser mayor a 999,999 unidades.',
                'imagen.max' => 'La imagen no puede pesar más de 5MB.',
                'imagen.dimensions' => 'La imagen debe tener entre 200x200px y 2000x2000px.',
            ]);

            \DB::beginTransaction();

            $data = [
                'nombre' => trim($validated['nombre']),
                'descripcion' => $validated['descripcion'] ? trim($validated['descripcion']) : null,
                'precio' => round($validated['precio'], 2),
                'categoria_id' => $validated['categoria_id'],
                'stock' => $validated['stock'],
                'activo' => $request->boolean('activo', true),
                'created_at' => now(),
                'updated_at' => now(),
            ];

            // Manejar imagen con validaciones adicionales
            if ($request->hasFile('imagen')) {
                $imagen = $request->file('imagen');

                // Verificar que el archivo sea válido
                if ($imagen->isValid()) {
                    // Generar nombre único y seguro
                    $extension = $imagen->getClientOriginalExtension();
                    $nombreSeguro = preg_replace('/[^a-zA-Z0-9]/', '_', pathinfo($imagen->getClientOriginalName(), PATHINFO_FILENAME));
                    $nombreImagen = time() . '_' . $nombreSeguro . '.' . $extension;

                    // Almacenar imagen
                    $rutaImagen = $imagen->storeAs('productos', $nombreImagen, 'public');

                    if ($rutaImagen) {
                        $data['imagen'] = $rutaImagen;
                    } else {
                        throw new \Exception('Error al almacenar la imagen.');
                    }
                }
            }

            $producto = Producto::create($data);

            \DB::commit();

            return redirect()->route('admin.productos.index')
                ->with('success', 'Producto creado exitosamente.');

        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()
                           ->withErrors($e->validator)
                           ->withInput();
        } catch (\Exception $e) {
            \DB::rollBack();
            \Log::error('Error al crear producto: ' . $e->getMessage());
            return redirect()->back()
                           ->with('error', 'Error al crear el producto: ' . $e->getMessage())
                           ->withInput();
        }
    }

    public function show(Producto $producto)
    {
        $producto->load('categoria');
        return view('admin.productos.show', compact('producto'));
    }

    public function edit(Producto $producto)
    {
        $categorias = Categoria::orderBy('nombre')->get();
        return view('admin.productos.edit', compact('producto', 'categorias'));
    }

    public function update(Request $request, Producto $producto)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'precio' => 'required|numeric|min:0',
            'categoria_id' => 'required|string',
            'stock' => 'required|integer|min:0',
            'imagen' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $data = $request->all();
        $data['activo'] = $request->has('activo');

        // Manejar imagen
        if ($request->hasFile('imagen')) {
            // Eliminar imagen anterior si existe
            if ($producto->imagen) {
                Storage::disk('public')->delete($producto->imagen);
            }

            $imagen = $request->file('imagen');
            $nombreImagen = time() . '_' . $imagen->getClientOriginalName();
            $data['imagen'] = $imagen->storeAs('productos', $nombreImagen, 'public');
        }

        $producto->update($data);

        return redirect()->route('admin.productos.index')
            ->with('success', 'Producto actualizado exitosamente.');
    }

    public function destroy(Producto $producto)
    {
        // Eliminar imagen si existe
        if ($producto->imagen) {
            Storage::disk('public')->delete($producto->imagen);
        }

        $producto->delete();

        return redirect()->route('admin.productos.index')
            ->with('success', 'Producto eliminado exitosamente.');
    }

    public function toggleStatus(Producto $producto)
    {
        $producto->update(['activo' => !$producto->activo]);

        $estado = $producto->activo ? 'activado' : 'desactivado';
        return redirect()->back()
            ->with('success', "Producto {$estado} exitosamente.");
    }
}