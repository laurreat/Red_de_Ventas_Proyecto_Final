<?php

namespace App\Http\Controllers\Vendedor;

use App\Http\Controllers\Controller;
use App\Models\Pedido;
use App\Models\User;
use App\Models\Producto;
use App\Services\ComisionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PedidoController extends Controller
{
    public function index(Request $request)
    {
        $vendedor = Auth::user();
        $query = Pedido::where('vendedor_id', $vendedor->_id);

        // Filtros
        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        if ($request->filled('fecha_desde')) {
            $query->where('created_at', '>=', new \MongoDB\BSON\UTCDateTime(strtotime($request->fecha_desde) * 1000));
        }

        if ($request->filled('fecha_hasta')) {
            $query->where('created_at', '<=', new \MongoDB\BSON\UTCDateTime(strtotime($request->fecha_hasta . ' 23:59:59') * 1000));
        }

        if ($request->filled('cliente')) {
            $query->where('cliente_data.name', 'like', '%' . $request->cliente . '%');
        }

        $pedidos = $query->orderBy('created_at', 'desc')->paginate(15);

        // Garantizar que cada pedido tenga productos como array
        foreach ($pedidos as $pedido) {
            if (!isset($pedido->productos) || !is_array($pedido->productos)) {
                $pedido->productos = $pedido->detalles ?? [];
            }
        }

        // Estadísticas para el dashboard de pedidos (usando agregaciones de MongoDB)
        $stats = [
            'total' => Pedido::where('vendedor_id', $vendedor->_id)->count(),
            'pendientes' => Pedido::where('vendedor_id', $vendedor->_id)->where('estado', 'pendiente')->count(),
            'confirmados' => Pedido::where('vendedor_id', $vendedor->_id)->where('estado', 'confirmado')->count(),
            'entregados' => Pedido::where('vendedor_id', $vendedor->_id)->where('estado', 'entregado')->count(),
            'total_ventas' => Pedido::where('vendedor_id', $vendedor->_id)->sum('total_final')
        ];

        return view('vendedor.pedidos.index', compact('pedidos', 'stats'));
    }

    public function show($id)
    {
        $vendedor = Auth::user();
        $pedido = Pedido::where('vendedor_id', $vendedor->_id)
                       ->where('_id', $id)
                       ->firstOrFail();

        // Garantizar que productos sea un array con múltiples intentos
        if (!isset($pedido->productos) || !is_array($pedido->productos) || empty($pedido->productos)) {
            // Intento 1: usar detalles (estructura estándar de MongoDB)
            if (isset($pedido->detalles) && is_array($pedido->detalles) && !empty($pedido->detalles)) {
                $pedido->productos = $pedido->detalles;
            } else {
                // Intento 2: acceso directo a attributes
                $rawDetalles = $pedido->getRawOriginal('detalles');
                $rawProductos = $pedido->getRawOriginal('productos');
                
                if (is_array($rawDetalles) && !empty($rawDetalles)) {
                    $pedido->productos = $rawDetalles;
                } elseif (is_array($rawProductos) && !empty($rawProductos)) {
                    $pedido->productos = $rawProductos;
                } else {
                    // Intento 3: desde attributes directamente
                    if (isset($pedido->attributes['detalles']) && is_array($pedido->attributes['detalles'])) {
                        $pedido->productos = $pedido->attributes['detalles'];
                    } elseif (isset($pedido->attributes['productos']) && is_array($pedido->attributes['productos'])) {
                        $pedido->productos = $pedido->attributes['productos'];
                    } else {
                        $pedido->productos = [];
                    }
                }
            }
        }

        // Log para debugging
        \Log::info('Pedido Show - Productos cargados', [
            'pedido_id' => $pedido->_id,
            'cantidad_productos' => count($pedido->productos),
            'productos' => $pedido->productos
        ]);

        return view('vendedor.pedidos.show', compact('pedido'));
    }

    public function create()
    {
        $clientes = User::where('rol', 'cliente')->get();
        $productos = Producto::where('activo', true)
                            ->orderBy('nombre', 'asc')
                            ->get();

        return view('vendedor.pedidos.create', compact('clientes', 'productos'));
    }

    public function store(Request $request)
    {
        // Normalizar y filtrar productos válidos antes de validar
        $productosFiltrados = collect($request->input('productos', []))
            ->filter(function ($p) {
                return isset($p['id']) && $p['id'] !== '' && isset($p['cantidad']) && (int) $p['cantidad'] > 0;
            })
            ->map(function ($p) {
                $p['precio'] = isset($p['precio']) ? (float) $p['precio'] : 0;
                return $p;
            })
            ->values()
            ->all();
        $request->merge(['productos' => $productosFiltrados]);

        $request->validate([
            'cliente_id' => 'required|string',
            'productos' => 'required|array|min:1',
            'productos.*.id' => 'required|string',
            'productos.*.cantidad' => 'required|integer|min:1',
            'productos.*.precio' => 'required|numeric|min:0',
            'direccion_entrega' => 'nullable|string|max:500',
            'telefono_entrega' => 'nullable|string|max:20',
            'notas' => 'nullable|string|max:500',
            'descuento' => 'nullable|numeric|min:0'
        ]);

        $vendedor = Auth::user();

        try {
            // Obtener datos del cliente
            $cliente = User::findOrFail($request->cliente_id);

            // Generar número de pedido único
            $numeroPedido = 'PED-' . str_pad(Pedido::count() + 1, 6, '0', STR_PAD_LEFT);

            // Calcular totales y preparar productos
            $subtotal = 0;
            $productosData = [];

            foreach ($request->productos as $productoData) {
                $producto = Producto::findOrFail($productoData['id']);
                $cantidad = $productoData['cantidad'];
                $precio = $productoData['precio'];

                // Verificar stock disponible
                $stockActual = is_numeric($producto->stock) ? (int)$producto->stock : 0;

                $total = $precio * $cantidad;
                $subtotal += $total;

                $productosData[] = [
                    'producto_id' => $producto->_id,
                    'codigo' => $producto->codigo ?? 'N/A',
                    'nombre' => $producto->nombre,
                    'cantidad' => $cantidad,
                    'precio' => $precio,
                    'subtotal' => $total
                ];

                // Descontar stock
                $nuevoStock = $stockActual - $cantidad;
                $producto->update(['stock' => $nuevoStock]);
            }

            $descuento = $request->descuento ?? 0;
            $totalFinal = $subtotal - $descuento;

            // Crear el pedido con productos embebidos
            $pedido = Pedido::create([
                'numero_pedido' => $numeroPedido,
                'cliente_id' => $cliente->_id,
                'cliente_data' => [
                    '_id' => $cliente->_id,
                    'name' => $cliente->name,
                    'email' => $cliente->email,
                    'telefono' => $cliente->telefono ?? 'N/A',
                ],
                'vendedor_id' => $vendedor->_id,
                'vendedor_data' => [
                    '_id' => $vendedor->_id,
                    'name' => $vendedor->name,
                    'email' => $vendedor->email,
                ],
                'productos' => $productosData, // Array embebido
                'detalles' => $productosData, // Alias para compatibilidad
                'subtotal' => $subtotal,
                'descuento' => $descuento,
                'total_final' => $totalFinal,
                'estado' => 'pendiente',
                'direccion_entrega' => $request->direccion_entrega,
                'telefono_entrega' => $request->telefono_entrega,
                'notas' => $request->notas,
                'stock_devuelto' => false
            ]);
            // La comisión se crea automáticamente mediante el Observer

            return redirect()->route('vendedor.pedidos.show', $pedido->_id)
                           ->with('success', 'Pedido creado exitosamente.');

        } catch (\Exception $e) {
            return redirect()->back()
                           ->with('error', 'Error al crear el pedido: ' . $e->getMessage())
                           ->withInput();
        }
    }

    public function edit($id)
    {
        $vendedor = Auth::user();
        $pedido = Pedido::where('vendedor_id', $vendedor->_id)
                       ->where('_id', $id)
                       ->where('estado', 'pendiente') // Solo pedidos pendientes se pueden editar
                       ->firstOrFail();

        // Garantizar que productos sea un array con múltiples intentos
        if (!isset($pedido->productos) || !is_array($pedido->productos) || empty($pedido->productos)) {
            // Intento 1: usar detalles (estructura estándar de MongoDB)
            if (isset($pedido->detalles) && is_array($pedido->detalles) && !empty($pedido->detalles)) {
                $pedido->productos = $pedido->detalles;
            } else {
                // Intento 2: acceso directo a attributes
                $rawDetalles = $pedido->getRawOriginal('detalles');
                $rawProductos = $pedido->getRawOriginal('productos');
                
                if (is_array($rawDetalles) && !empty($rawDetalles)) {
                    $pedido->productos = $rawDetalles;
                } elseif (is_array($rawProductos) && !empty($rawProductos)) {
                    $pedido->productos = $rawProductos;
                } else {
                    $pedido->productos = [];
                }
            }
        }

        $clientes = User::where('rol', 'cliente')->get();
        // Cambiar 'estado' por 'activo' que es el campo correcto
        $productos = Producto::where('activo', true)
                            ->orderBy('nombre', 'asc')
                            ->get();

        return view('vendedor.pedidos.edit', compact('pedido', 'clientes', 'productos'));
    }

    public function update(Request $request, $id)
    {
        // Normalizar y filtrar productos válidos antes de validar
        $productosFiltrados = collect($request->input('productos', []))
            ->filter(function ($p) {
                return isset($p['id']) && $p['id'] !== '' && isset($p['cantidad']) && (int) $p['cantidad'] > 0;
            })
            ->map(function ($p) {
                $p['precio'] = isset($p['precio']) ? (float) $p['precio'] : 0;
                return $p;
            })
            ->values()
            ->all();
        $request->merge(['productos' => $productosFiltrados]);

        $request->validate([
            'cliente_id' => 'required|string',
            'productos' => 'required|array|min:1',
            'productos.*.id' => 'required|string',
            'productos.*.cantidad' => 'required|integer|min:1',
            'productos.*.precio' => 'required|numeric|min:0',
            'direccion_entrega' => 'nullable|string|max:500',
            'telefono_entrega' => 'nullable|string|max:20',
            'notas' => 'nullable|string|max:500',
            'descuento' => 'nullable|numeric|min:0'
        ]);

        $vendedor = Auth::user();
        $pedido = Pedido::where('vendedor_id', $vendedor->_id)
                       ->where('_id', $id)
                       ->where('estado', 'pendiente')
                       ->firstOrFail();

        try {
            // Obtener datos del cliente
            $cliente = User::findOrFail($request->cliente_id);

            // Crear un mapa de productos actuales para comparar
            $productosAnteriores = [];
            $productosData = $pedido->productos ?? $pedido->detalles ?? [];
            
            foreach ($productosData as $prod) {
                $prodId = $prod['producto_id'] ?? null;
                if ($prodId) {
                    $productosAnteriores[(string) $prodId] = $prod['cantidad'] ?? 0;
                }
            }

            // Calcular nuevos totales y preparar productos
            $subtotal = 0;
            $productosNuevos = [];
            $productosNuevosIds = [];

            foreach ($request->productos as $productoData) {
                $producto = Producto::findOrFail($productoData['id']);
                $cantidad = (int)$productoData['cantidad'];
                $precio = $productoData['precio'];

                // Calcular diferencia de stock
                $cantidadAnterior = $productosAnteriores[(string) $producto->_id] ?? 0;
                $diferencia = $cantidad - $cantidadAnterior;

                // Solo verificar stock si se aumenta la cantidad
                if ($diferencia > 0) {
                    $stockActual = is_numeric($producto->stock) ? (int)$producto->stock : 0;
                    // Descontar solo la diferencia (sin validar stock)
                    $nuevoStock = $stockActual - $diferencia;
                    $producto->update(['stock' => $nuevoStock]);
                } elseif ($diferencia < 0) {
                    // Si se reduce la cantidad, devolver stock
                    $stockActual = is_numeric($producto->stock) ? (int)$producto->stock : 0;
                    $nuevoStock = $stockActual + abs($diferencia);
                    $producto->update(['stock' => $nuevoStock]);
                }

                $total = $precio * $cantidad;
                $subtotal += $total;

                $productosNuevos[] = [
                    'producto_id' => $producto->_id,
                    'codigo' => $producto->codigo ?? 'N/A',
                    'nombre' => $producto->nombre,
                    'cantidad' => $cantidad,
                    'precio' => $precio,
                    'subtotal' => $total
                ];
                
                $productosNuevosIds[] = (string) $producto->_id;
            }

            // Devolver stock de productos que fueron eliminados del pedido
            foreach ($productosAnteriores as $prodId => $cantidadAnterior) {
                if (!in_array($prodId, $productosNuevosIds)) {
                    $producto = Producto::find($prodId);
                    if ($producto) {
                        $stockActual = is_numeric($producto->stock) ? (int)$producto->stock : 0;
                        $nuevoStock = $stockActual + $cantidadAnterior;
                        $producto->update(['stock' => $nuevoStock]);
                    }
                }
            }

            $descuento = $request->descuento ?? 0;
            $totalFinal = $subtotal - $descuento;

            // Actualizar el pedido
            $pedido->update([
                'cliente_id' => $cliente->_id,
                'cliente_data' => [
                    '_id' => $cliente->_id,
                    'name' => $cliente->name,
                    'email' => $cliente->email,
                    'telefono' => $cliente->telefono ?? 'N/A',
                ],
                'productos' => $productosNuevos,
                'detalles' => $productosNuevos,
                'subtotal' => $subtotal,
                'descuento' => $descuento,
                'total_final' => $totalFinal,
                'direccion_entrega' => $request->direccion_entrega,
                'telefono_entrega' => $request->telefono_entrega,
                'notas' => $request->notas
            ]);
            // La comisión se actualiza automáticamente mediante el Observer

            return redirect()->route('vendedor.pedidos.show', $pedido->_id)
                           ->with('success', 'Pedido actualizado exitosamente.');

        } catch (\Exception $e) {
            return redirect()->back()
                           ->with('error', 'Error al actualizar el pedido: ' . $e->getMessage())
                           ->withInput();
        }
    }

    public function updateEstado(Request $request, $id)
    {
        $request->validate([
            'estado' => 'required|in:pendiente,confirmado,en_preparacion,listo,en_camino,entregado,cancelado',
            'motivo_cancelacion' => 'nullable|string|max:255'
        ]);

        $vendedor = Auth::user();
        $pedido = Pedido::where('vendedor_id', $vendedor->_id)
                       ->where('_id', $id)
                       ->firstOrFail();

        $estadoAnterior = $pedido->estado;
        $stockDevuelto = $pedido->stock_devuelto ?? ($estadoAnterior === 'cancelado');

        // Leer detalles directamente de MongoDB
        $detalles = $pedido->getRawDetalles() ?? [];

        \Log::info('=== INICIO updateEstado (Vendedor) ===', [
            'pedido_id' => $pedido->_id,
            'numero_pedido' => $pedido->numero_pedido,
            'estado_anterior' => $estadoAnterior,
            'estado_nuevo' => $request->estado,
            'stock_devuelto_actual' => $stockDevuelto,
            'cantidad_detalles' => count($detalles)
        ]);

        // Si se cancela el pedido y NO se ha devuelto el stock anteriormente
        if ($request->estado === 'cancelado' && $estadoAnterior !== 'cancelado' && !$stockDevuelto) {
            \Log::info('>>> Iniciando devolución de stock por cancelación');

            foreach ($detalles as $detalle) {
                \Log::info('Procesando detalle', [
                    'producto_id' => $detalle['producto_id'] ?? 'no definido',
                    'cantidad' => $detalle['cantidad'] ?? 'no definido'
                ]);

                $producto = Producto::find($detalle['producto_id']);
                if ($producto) {
                    $stockActual = is_numeric($producto->stock) ? (int)$producto->stock : 0;
                    $nuevoStock = $stockActual + (int)$detalle['cantidad'];

                    \Log::info('Devolviendo stock', [
                        'producto' => $producto->nombre,
                        'stock_antes' => $stockActual,
                        'cantidad_devuelta' => $detalle['cantidad'],
                        'stock_despues' => $nuevoStock
                    ]);

                    $producto->update(['stock' => $nuevoStock]);
                } else {
                    \Log::warning('Producto no encontrado', ['producto_id' => $detalle['producto_id']]);
                }
            }
            $pedido->stock_devuelto = true;
            \Log::info('Stock devuelto, marcando bandera stock_devuelto = true');
        }

        // Si se reactiva un pedido que estaba cancelado, restar stock
        if ($estadoAnterior === 'cancelado' && $request->estado !== 'cancelado' && $stockDevuelto) {
            \Log::info('>>> Iniciando resta de stock por reactivación');

            foreach ($detalles as $detalle) {
                $producto = Producto::find($detalle['producto_id']);
                if ($producto) {
                    $stockActual = is_numeric($producto->stock) ? (int)$producto->stock : 0;
                    $nuevoStock = $stockActual - (int)$detalle['cantidad'];

                    \Log::info('Restando stock por reactivación', [
                        'producto' => $producto->nombre,
                        'stock_antes' => $stockActual,
                        'cantidad_restada' => $detalle['cantidad'],
                        'stock_despues' => $nuevoStock
                    ]);

                    $producto->update(['stock' => max(0, $nuevoStock)]);
                }
            }
            $pedido->stock_devuelto = false;
            \Log::info('Stock restado, marcando bandera stock_devuelto = false');
        }

        $pedido->update([
            'estado' => $request->estado,
            'motivo_cancelacion' => $request->motivo_cancelacion,
            'stock_devuelto' => $pedido->stock_devuelto ?? false
        ]);
        // Las comisiones se gestionan automáticamente mediante el Observer

        \Log::info('=== FIN updateEstado (Vendedor) ===');

        return redirect()->back()->with('success', 'Estado del pedido actualizado exitosamente.');
    }

    public function destroy($id)
    {
        $vendedor = Auth::user();
        
        // Primero buscar el pedido sin restricción de estado
        $pedido = Pedido::where('vendedor_id', $vendedor->_id)
                       ->where('_id', $id)
                       ->first();
        
        if (!$pedido) {
            return redirect()->back()
                           ->with('error', 'Pedido no encontrado o no tienes permisos para eliminarlo.');
        }
        
        // Verificar si el pedido puede ser eliminado
        if ($pedido->estado === 'entregado') {
            return redirect()->back()
                           ->with('error', 'No se pueden eliminar pedidos que ya fueron entregados.');
        }

        // Para pedidos antiguos sin el campo stock_devuelto, asumimos false si el estado no es 'cancelado'
        $stockDevuelto = $pedido->stock_devuelto ?? ($pedido->estado === 'cancelado');

        // Leer detalles directamente de MongoDB
        $detalles = $pedido->getRawDetalles() ?? [];

        \Log::info('=== INICIO destroy (Vendedor) ===', [
            'pedido_id' => $pedido->_id,
            'numero_pedido' => $pedido->numero_pedido,
            'estado' => $pedido->estado,
            'stock_devuelto' => $stockDevuelto,
            'cantidad_detalles' => count($detalles)
        ]);

        try {
            // Devolver stock solo si NO se devolvió anteriormente (por cancelación)
            \Log::info('Verificando si devolver stock', ['stock_devuelto' => $stockDevuelto]);

            if (!$stockDevuelto) {
                \Log::info('>>> Iniciando devolución de stock por eliminación');

                foreach ($detalles as $detalle) {
                    \Log::info('Procesando detalle para devolución', [
                        'producto_id' => $detalle['producto_id'] ?? 'no definido',
                        'cantidad' => $detalle['cantidad'] ?? 'no definido'
                    ]);

                    $producto = Producto::find($detalle['producto_id']);
                    if ($producto) {
                        $stockActual = is_numeric($producto->stock) ? (int)$producto->stock : 0;
                        $nuevoStock = $stockActual + (int)$detalle['cantidad'];

                        \Log::info('Devolviendo stock por eliminación', [
                            'producto' => $producto->nombre,
                            'stock_antes' => $stockActual,
                            'cantidad_devuelta' => $detalle['cantidad'],
                            'stock_despues' => $nuevoStock
                        ]);

                        $producto->update(['stock' => $nuevoStock]);
                    } else {
                        \Log::warning('Producto no encontrado al eliminar', ['producto_id' => $detalle['producto_id']]);
                    }
                }
            } else {
                \Log::info('No se devuelve stock porque ya fue devuelto anteriormente (stock_devuelto = true)');
            }

            // Las comisiones se eliminan automáticamente mediante el Observer
            $pedido->delete();
            
            \Log::info('Pedido eliminado exitosamente');
            \Log::info('=== FIN destroy (Vendedor) ===');

            return redirect()->route('vendedor.pedidos.index')
                           ->with('success', 'Pedido eliminado exitosamente. El stock ha sido devuelto al inventario.');

        } catch (\Exception $e) {
            \Log::error('Error al eliminar pedido', [
                'pedido_id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()->back()
                           ->with('error', 'Error al eliminar el pedido: ' . $e->getMessage());
        }
    }

    public function exportar(Request $request)
    {
        $vendedor = Auth::user();
        $query = Pedido::where('vendedor_id', $vendedor->id);

        if ($request->filled('fecha_desde')) {
            $query->whereDate('created_at', '>=', $request->fecha_desde);
        }

        if ($request->filled('fecha_hasta')) {
            $query->whereDate('created_at', '<=', $request->fecha_hasta);
        }

        // MongoDB: datos embebidos, no necesita with
        $pedidos = $query->get();

        // Aquí implementarías la lógica de exportación (CSV, Excel, PDF)
        // Por ahora retornamos un JSON
        return response()->json($pedidos);
    }

    /**
     * Buscar cliente por cédula
     */
    public function searchCliente(Request $request)
    {
        $request->validate([
            'cedula' => 'required|string'
        ]);

        try {
            $cedula = $request->cedula;

            // Buscar usuario con rol cliente por cédula
            $cliente = User::where('cedula', $cedula)
                          ->where('rol', 'cliente')
                          ->first();

            if ($cliente) {
                return response()->json([
                    'success' => true,
                    'user' => [
                        'id' => $cliente->_id,
                        'name' => $cliente->name,
                        'email' => $cliente->email,
                        'cedula' => $cliente->cedula
                    ],
                    'message' => 'Cliente encontrado correctamente'
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'No se encontró un cliente con esa cédula'
                ], 404);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al buscar el cliente: ' . $e->getMessage()
            ], 500);
        }
    }
}