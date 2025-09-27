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
                'categoria_id' => 'required|string',
                'stock' => 'required|integer|min:0|max:999999',
                'imagen' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120|dimensions:min_width=200,min_height=200,max_width=2000,max_height=2000'
            ], [
                'nombre.required' => 'El nombre del producto es obligatorio.',
                'nombre.min' => 'El nombre debe tener al menos 2 caracteres.',
                'nombre.max' => 'El nombre no puede tener más de 255 caracteres.',
                'descripcion.max' => 'La descripción no puede tener más de 1000 caracteres.',
                'precio.required' => 'El precio es obligatorio.',
                'precio.numeric' => 'El precio debe ser un número válido.',
                'precio.min' => 'El precio debe ser mayor a 0.',
                'precio.max' => 'El precio no puede ser mayor a $999,999.99.',
                'categoria_id.required' => 'Debe seleccionar una categoría.',
                'stock.required' => 'El stock es obligatorio.',
                'stock.integer' => 'El stock debe ser un número entero.',
                'stock.min' => 'El stock no puede ser negativo.',
                'stock.max' => 'El stock no puede ser mayor a 999,999 unidades.',
                'imagen.image' => 'El archivo debe ser una imagen válida.',
                'imagen.mimes' => 'La imagen debe ser de tipo: jpeg, png, jpg, gif o webp.',
                'imagen.max' => 'La imagen no puede pesar más de 5MB.',
                'imagen.dimensions' => 'La imagen debe tener entre 200x200px y 2000x2000px.',
            ]);

            // Validar que la categoría existe
            $categoria = Categoria::find($validated['categoria_id']);
            if (!$categoria) {
                return redirect()->back()
                    ->withErrors(['categoria_id' => 'La categoría seleccionada no existe.'])
                    ->withInput();
            }

            // MongoDB standalone no soporta transacciones, comentamos por ahora
            // \DB::beginTransaction();

            $data = [
                'nombre' => trim($validated['nombre']),
                'descripcion' => $validated['descripcion'] ? trim($validated['descripcion']) : null,
                'precio' => round($validated['precio'], 2),
                'categoria_id' => $validated['categoria_id'],
                'categoria_data' => [
                    '_id' => $categoria->_id,
                    'nombre' => $categoria->nombre,
                    'slug' => $categoria->slug ?? null,
                    'activo' => $categoria->activo ?? true
                ],
                'stock' => $validated['stock'],
                'stock_minimo' => 5, // Stock mínimo por defecto
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

            // \DB::commit();

            return redirect()->route('admin.productos.index')
                ->with('success', "Producto '{$producto->nombre}' creado exitosamente y agregado al catálogo.");

        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()
                           ->withErrors($e->validator)
                           ->withInput();
        } catch (\Exception $e) {
            // \DB::rollBack();
            \Log::error('Error al crear producto: ' . $e->getMessage(), [
                'user_id' => auth()->id(),
                'request_data' => $request->all(),
                'error_trace' => $e->getTraceAsString()
            ]);

            $errorMessage = 'Ha ocurrido un error inesperado al crear el producto.';
            if (app()->environment('local')) {
                $errorMessage .= ' Detalles: ' . $e->getMessage();
            }

            return redirect()->back()
                           ->with('error', $errorMessage)
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
        try {
            $validated = $request->validate([
                'nombre' => 'required|string|max:255|min:2',
                'descripcion' => 'nullable|string|max:1000',
                'precio' => 'required|numeric|min:0.01|max:999999.99',
                'categoria_id' => 'required|string',
                'stock' => 'required|integer|min:0|max:999999',
                'imagen' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120|dimensions:min_width=200,min_height=200,max_width=2000,max_height=2000'
            ], [
                'nombre.required' => 'El nombre del producto es obligatorio.',
                'nombre.min' => 'El nombre debe tener al menos 2 caracteres.',
                'nombre.max' => 'El nombre no puede tener más de 255 caracteres.',
                'descripcion.max' => 'La descripción no puede tener más de 1000 caracteres.',
                'precio.required' => 'El precio es obligatorio.',
                'precio.numeric' => 'El precio debe ser un número válido.',
                'precio.min' => 'El precio debe ser mayor a 0.',
                'precio.max' => 'El precio no puede ser mayor a $999,999.99.',
                'categoria_id.required' => 'Debe seleccionar una categoría.',
                'stock.required' => 'El stock es obligatorio.',
                'stock.integer' => 'El stock debe ser un número entero.',
                'stock.min' => 'El stock no puede ser negativo.',
                'stock.max' => 'El stock no puede ser mayor a 999,999 unidades.',
                'imagen.image' => 'El archivo debe ser una imagen válida.',
                'imagen.mimes' => 'La imagen debe ser de tipo: jpeg, png, jpg, gif o webp.',
                'imagen.max' => 'La imagen no puede pesar más de 5MB.',
                'imagen.dimensions' => 'La imagen debe tener entre 200x200px y 2000x2000px.',
            ]);

            // Validar que la categoría existe
            $categoria = Categoria::find($validated['categoria_id']);
            if (!$categoria) {
                return redirect()->back()
                    ->withErrors(['categoria_id' => 'La categoría seleccionada no existe.'])
                    ->withInput();
            }

            $data = [
                'nombre' => trim($validated['nombre']),
                'descripcion' => $validated['descripcion'] ? trim($validated['descripcion']) : null,
                'precio' => round($validated['precio'], 2),
                'categoria_id' => $validated['categoria_id'],
                'categoria_data' => [
                    '_id' => $categoria->_id,
                    'nombre' => $categoria->nombre,
                    'slug' => $categoria->slug ?? null,
                    'activo' => $categoria->activo ?? true
                ],
                'stock' => $validated['stock'],
                'activo' => $request->boolean('activo', true),
                'updated_at' => now(),
            ];

            // Manejar imagen
            if ($request->hasFile('imagen')) {
                $imagen = $request->file('imagen');

                // Verificar que el archivo sea válido
                if ($imagen->isValid()) {
                    // Eliminar imagen anterior si existe
                    if ($producto->imagen) {
                        Storage::disk('public')->delete($producto->imagen);
                    }

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

            $producto->update($data);

            return redirect()->route('admin.productos.index')
                ->with('success', "Producto '{$producto->nombre}' actualizado exitosamente.");

        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()
                           ->withErrors($e->validator)
                           ->withInput();
        } catch (\Exception $e) {
            \Log::error('Error al actualizar producto: ' . $e->getMessage(), [
                'producto_id' => $producto->_id,
                'user_id' => auth()->id(),
                'request_data' => $request->all(),
                'error_trace' => $e->getTraceAsString()
            ]);

            $errorMessage = 'Ha ocurrido un error inesperado al actualizar el producto.';
            if (app()->environment('local')) {
                $errorMessage .= ' Detalles: ' . $e->getMessage();
            }

            return redirect()->back()
                           ->with('error', $errorMessage)
                           ->withInput();
        }
    }

    public function destroy(Producto $producto)
    {
        try {
            $nombreProducto = $producto->nombre;

            // \DB::beginTransaction();

            // Verificar si el producto tiene pedidos asociados
            $tienePedidos = $producto->detallesPedidos()->count() > 0;

            if ($tienePedidos) {
                return redirect()->back()
                    ->with('warning', "No se puede eliminar '{$nombreProducto}' porque tiene pedidos asociados. Puede desactivarlo en su lugar.");
            }

            // Eliminar imagen si existe
            if ($producto->imagen) {
                Storage::disk('public')->delete($producto->imagen);
            }

            $producto->delete();

            // \DB::commit();

            return redirect()->route('admin.productos.index')
                ->with('success', "Producto '{$nombreProducto}' eliminado exitosamente del sistema.");

        } catch (\Exception $e) {
            // \DB::rollBack();
            \Log::error('Error al eliminar producto: ' . $e->getMessage(), [
                'producto_id' => $producto->_id,
                'user_id' => auth()->id(),
                'error_trace' => $e->getTraceAsString()
            ]);

            return redirect()->back()
                ->with('error', 'Error al eliminar el producto. Por favor, intente nuevamente.');
        }
    }

    public function toggleStatus(Producto $producto)
    {
        try {
            $nuevoEstado = !$producto->activo;
            $producto->update(['activo' => $nuevoEstado]);

            $accion = $nuevoEstado ? 'activado' : 'desactivado';
            $mensaje = $nuevoEstado
                ? "Producto '{$producto->nombre}' activado. Ahora está visible en el catálogo."
                : "Producto '{$producto->nombre}' desactivado. Ya no aparece en el catálogo.";

            return redirect()->back()
                ->with('success', $mensaje);

        } catch (\Exception $e) {
            \Log::error('Error al cambiar estado del producto: ' . $e->getMessage(), [
                'producto_id' => $producto->_id,
                'user_id' => auth()->id(),
                'error_trace' => $e->getTraceAsString()
            ]);

            return redirect()->back()
                ->with('error', 'Error al cambiar el estado del producto. Por favor, intente nuevamente.');
        }
    }
}