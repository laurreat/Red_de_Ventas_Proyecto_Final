<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pedido;
use App\Models\User;
use App\Models\Producto;
use Illuminate\Http\Request;
use Carbon\Carbon;

class PedidoController extends Controller
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
        $query = Pedido::query();

        // Filtros
        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        if ($request->filled('vendedor')) {
            $query->where('vendedor_id', $request->vendedor);
        }

        if ($request->filled('fecha_desde')) {
            $query->whereDate('created_at', '>=', $request->fecha_desde);
        }

        if ($request->filled('fecha_hasta')) {
            $query->whereDate('created_at', '<=', $request->fecha_hasta);
        }

        if ($request->filled('buscar')) {
            $search = $request->buscar;
            $query->where(function($q) use ($search) {
                $q->where('numero_pedido', 'like', "%{$search}%")
                  ->orWhere('cliente_data.name', 'like', "%{$search}%")
                  ->orWhere('cliente_data.email', 'like', "%{$search}%")
                  ->orWhere('vendedor_data.name', 'like', "%{$search}%");
            });
        }

        $pedidos = $query->orderBy('created_at', 'desc')->paginate(15);

        // Estadísticas
        $hoy = Carbon::today();
        $stats = [
            'total_pedidos' => Pedido::count(),
            'pedidos_hoy' => Pedido::whereDate('created_at', $hoy)->count(),
            'pedidos_pendientes' => Pedido::where('estado', 'pendiente')->count(),
            'pedidos_entregados' => Pedido::where('estado', 'entregado')->count(),
            'pedidos_cancelados' => Pedido::where('estado', 'cancelado')->count(),
            'ingresos_mes' => Pedido::whereMonth('created_at', now()->month)
                                   ->whereYear('created_at', now()->year)
                                   ->sum('total_final'),
        ];

        // Para filtros
        $vendedores = User::vendedores()->orderBy('name')->get();
        $estados = [
            'pendiente' => 'Pendiente',
            'confirmado' => 'Confirmado',
            'en_preparacion' => 'En Preparación',
            'listo' => 'Listo',
            'en_camino' => 'En Camino',
            'entregado' => 'Entregado',
            'cancelado' => 'Cancelado'
        ];

        return view('admin.pedidos.index', compact('pedidos', 'stats', 'vendedores', 'estados'));
    }

    public function show(Request $request, Pedido $pedido)
    {
        // Si es una petición AJAX, devolver datos JSON para el modal
        if ($request->ajax()) {
            $data = [
                'id' => $pedido->id,
                'numero_pedido' => $pedido->numero_pedido,
                'cliente' => [
                    'name' => $pedido->cliente->name,
                    'email' => $pedido->cliente->email,
                    'telefono' => $pedido->cliente_data['telefono'] ?? 'No disponible'
                ],
                'vendedor' => $pedido->vendedor ? [
                    'name' => $pedido->vendedor->name,
                    'email' => $pedido->vendedor->email
                ] : null,
                'estado' => $pedido->estado,
                'estado_texto' => ucfirst(str_replace('_', ' ', $pedido->estado)),
                'total_productos' => $pedido->subtotal,
                'descuento' => $pedido->descuento,
                'total_final' => $pedido->total_final,
                'fecha_pedido' => $pedido->created_at->format('d/m/Y H:i'),
                'observaciones' => $pedido->observaciones,
                'detalles' => $pedido->detalles->map(function($detalle) {
                    return [
                        'producto_nombre' => $detalle->producto_nombre,
                        'cantidad' => $detalle->cantidad,
                        'precio_unitario' => $detalle->precio_unitario,
                        'total' => $detalle->total
                    ];
                })
            ];

            return response()->json($data);
        }

        // Si no es AJAX, devolver la vista normal
        return view('admin.pedidos.show', compact('pedido'));
    }

    public function create()
    {
        // Solo necesitamos los productos, ya que clientes y vendedores se buscan por cédula
        $productos = Producto::where('activo', true)->orderBy('nombre')->get();

        return view('admin.pedidos.create', compact('productos'));
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'user_id' => 'required|string|exists:users,_id',
                'vendedor_id' => 'nullable|string|exists:users,_id',
                'productos' => 'required|array|min:1|max:50',
                'productos.*.id' => 'required|string|exists:productos,_id',
                'productos.*.cantidad' => 'required|integer|min:1|max:1000',
                'descuento' => 'nullable|numeric|min:0|max:999999',
                'notas' => 'nullable|string|max:500',
                'direccion_entrega' => 'required|string|max:500',
                'telefono_entrega' => 'required|string|regex:/^[\+]?[0-9\s\-\(\)]+$/'
            ], [
                'productos.max' => 'No puedes agregar más de 50 productos por pedido.',
                'productos.*.cantidad.max' => 'La cantidad máxima por producto es 1000.',
                'telefono_entrega.regex' => 'El teléfono de entrega no tiene un formato válido.',
            ]);

            \DB::beginTransaction();

        $cliente = User::find($request->user_id);
        $vendedor = $request->vendedor_id ? User::find($request->vendedor_id) : null;

        $pedido = new Pedido([
            'numero_pedido' => $this->generarNumeroPedido(),
            'user_id' => $request->user_id,
            'vendedor_id' => $request->vendedor_id,
            'estado' => 'pendiente',
            'descuento' => $request->descuento ?? 0.00,
            'notas' => $request->notas,
            'direccion_entrega' => $request->direccion_entrega,
            'telefono_entrega' => $request->telefono_entrega,
            'cliente_data' => [
                '_id' => $cliente->_id,
                'name' => $cliente->name,
                'apellidos' => $cliente->apellidos,
                'email' => $cliente->email,
                'telefono' => $cliente->telefono,
                'cedula' => $cliente->cedula
            ]
        ]);

        if ($vendedor) {
            $pedido->vendedor_data = [
                '_id' => $vendedor->_id,
                'name' => $vendedor->name,
                'apellidos' => $vendedor->apellidos,
                'email' => $vendedor->email,
                'telefono' => $vendedor->telefono
            ];
        }

        $detalles = [];
        $total = 0;

        foreach ($request->productos as $productoData) {
            $producto = Producto::find($productoData['id']);
            if (!$producto) {
                throw new \Exception("Producto con ID {$productoData['id']} no encontrado");
            }

            if (!$producto->activo) {
                throw new \Exception("El producto '{$producto->nombre}' no está disponible");
            }

            $cantidad = $productoData['cantidad'];

            // Verificar stock disponible
            if ($producto->stock < $cantidad) {
                throw new \Exception("Stock insuficiente para '{$producto->nombre}'. Stock disponible: {$producto->stock}");
            }

            $precioUnitario = $producto->precio;
            $subtotal = $cantidad * $precioUnitario;

            $detalles[] = [
                'producto_id' => $producto->_id,
                'producto_data' => [
                    '_id' => $producto->_id,
                    'nombre' => $producto->nombre,
                    'precio' => $producto->precio,
                    'imagen' => $producto->imagen,
                    'categoria' => $producto->categoria ? $producto->categoria->nombre : null
                ],
                'cantidad' => $cantidad,
                'precio_unitario' => $precioUnitario,
                'subtotal' => $subtotal,
                'fecha_agregado' => now()
            ];

            $total += $subtotal;

            // Actualizar stock del producto
            $producto->decrement('stock', $cantidad);
        }

        $pedido->detalles = $detalles;
        $pedido->total = $total;
        $pedido->total_final = $total - $pedido->descuento;

            $pedido->save();

            \DB::commit();

            return redirect()->route('admin.pedidos.index')
                ->with('success', 'Pedido creado exitosamente.');

        } catch (\Illuminate\Validation\ValidationException $e) {
            \DB::rollBack();
            return redirect()->back()
                           ->withErrors($e->validator)
                           ->withInput();
        } catch (\Exception $e) {
            \DB::rollBack();
            \Log::error('Error al crear pedido: ' . $e->getMessage());
            return redirect()->back()
                           ->with('error', 'Error al crear el pedido: ' . $e->getMessage())
                           ->withInput();
        }
    }

    private function generarNumeroPedido(): string
    {
        $formato = 'ARE-{YYYY}-{NNNN}';
        $ultimoNumero = Pedido::whereYear('created_at', now()->year)->count() + 1;

        return str_replace(
            ['{YYYY}', '{NNNN}'],
            [now()->year, str_pad($ultimoNumero, 4, '0', STR_PAD_LEFT)],
            $formato
        );
    }

    public function edit(Pedido $pedido)
    {
        if (in_array($pedido->estado, ['entregado', 'cancelado'])) {
            return redirect()->back()
                ->with('error', 'No se pueden editar pedidos entregados o cancelados.');
        }

        $clientes = User::clientes()->orderBy('name')->get();
        $vendedores = User::vendedores()->orderBy('name')->get();
        $productos = Producto::where('activo', true)->orderBy('nombre')->get();
        $pedido->load(['detalles.producto']);

        return view('admin.pedidos.edit', compact('pedido', 'clientes', 'vendedores', 'productos'));
    }

    public function update(Request $request, Pedido $pedido)
    {
        if (in_array($pedido->estado, ['entregado', 'cancelado'])) {
            return redirect()->back()
                ->with('error', 'No se pueden editar pedidos entregados o cancelados.');
        }

        $request->validate([
            'estado' => 'required|in:pendiente,confirmado,en_preparacion,listo,en_camino,entregado,cancelado',
            'observaciones' => 'nullable|string|max:500'
        ]);

        $pedido->update([
            'estado' => $request->estado,
            'observaciones' => $request->observaciones
        ]);

        return redirect()->route('admin.pedidos.show', $pedido)
            ->with('success', 'Pedido actualizado exitosamente.');
    }

    public function updateStatus(Request $request, Pedido $pedido)
    {
        $request->validate([
            'estado' => 'required|in:pendiente,confirmado,en_preparacion,listo,en_camino,entregado,cancelado'
        ]);

        $estadoAnterior = $pedido->estado;
        $pedido->update(['estado' => $request->estado]);

        // Si se cancela el pedido, devolver stock
        if ($request->estado === 'cancelado' && $estadoAnterior !== 'cancelado') {
            foreach ($pedido->detalles as $detalle) {
                $detalle->producto->increment('stock', $detalle->cantidad);
            }
        }

        return redirect()->back()
            ->with('success', 'Estado del pedido actualizado exitosamente.');
    }

    public function destroy(Pedido $pedido)
    {
        if (in_array($pedido->estado, ['entregado'])) {
            return redirect()->back()
                ->with('error', 'No se pueden eliminar pedidos entregados.');
        }

        // Devolver stock si el pedido no estaba cancelado
        if ($pedido->estado !== 'cancelado') {
            foreach ($pedido->detalles as $detalle) {
                $detalle->producto->increment('stock', $detalle->cantidad);
            }
        }

        $pedido->delete();

        return redirect()->route('admin.pedidos.index')
            ->with('success', 'Pedido eliminado exitosamente.');
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
                        'id' => $cliente->id,
                        'name' => $cliente->name,
                        'email' => $cliente->email,
                        'cedula' => $cliente->cedula
                    ],
                    'message' => 'Cliente encontrado correctamente'
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'No se encontró ningún cliente con la cédula: ' . $cedula
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al buscar cliente: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Buscar vendedor por cédula
     */
    public function searchVendedor(Request $request)
    {
        $request->validate([
            'cedula' => 'required|string'
        ]);

        try {
            $cedula = $request->cedula;

            // Buscar usuario con rol vendedor por cédula
            $vendedor = User::where('cedula', $cedula)
                           ->where('rol', 'vendedor')
                           ->first();

            if ($vendedor) {
                return response()->json([
                    'success' => true,
                    'user' => [
                        'id' => $vendedor->id,
                        'name' => $vendedor->name,
                        'email' => $vendedor->email,
                        'cedula' => $vendedor->cedula
                    ],
                    'message' => 'Vendedor encontrado correctamente'
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'No se encontró ningún vendedor con la cédula: ' . $cedula
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al buscar vendedor: ' . $e->getMessage()
            ], 500);
        }
    }
}