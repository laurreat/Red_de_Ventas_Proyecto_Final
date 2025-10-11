<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pedido;
use App\Models\User;
use App\Models\Producto;
use App\Services\NotificationService;
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

        // Log temporal para debug
        $totalPedidos = Pedido::count();
        \Log::info('Cargando índice de pedidos', [
            'total_pedidos_db' => $totalPedidos,
            'filtros_aplicados' => $request->all()
        ]);

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
            'ingresos_mes' => to_float(Pedido::whereMonth('created_at', now()->month)
                                   ->whereYear('created_at', now()->year)
                                   ->sum('total_final')),
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
        // Pre-cargar productos para los detalles
        $productosDetalles = [];
        $detalles = $pedido->getRawDetalles();
        if (!empty($detalles)) {
            $productosIds = array_column($detalles, 'producto_id');
            $productos = \App\Models\Producto::whereIn('_id', $productosIds)->get();
            $productosDetalles = $productos->keyBy('_id');
        }

        return view('admin.pedidos.show', compact('pedido', 'productosDetalles'));
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
                'cliente_id' => 'required|string|exists:users,_id',
                'vendedor_id' => 'nullable|string|exists:users,_id',
                'productos' => 'required|array|min:1|max:50',
                'productos.*.id' => 'required|string|exists:productos,_id',
                'productos.*.cantidad' => 'required|integer|min:1|max:1000',
                'descuento' => 'nullable|numeric|min:0|max:999999',
                'observaciones' => 'nullable|string|max:500'
            ], [
                'cliente_id.required' => 'Debe seleccionar un cliente.',
                'productos.required' => 'Debe agregar al menos un producto.',
                'productos.max' => 'No puedes agregar más de 50 productos por pedido.',
                'productos.*.cantidad.max' => 'La cantidad máxima por producto es 1000.'
            ]);

        $cliente = User::find($request->cliente_id);
        $vendedor = $request->vendedor_id ? User::find($request->vendedor_id) : null;

        $pedido = new Pedido([
            'numero_pedido' => $this->generarNumeroPedido(),
            'user_id' => $request->cliente_id,
            'vendedor_id' => $request->vendedor_id,
            'estado' => 'pendiente',
            'total' => 0, // Se calculará después (cambié de 'subtotal' a 'total')
            'descuento' => $request->descuento ?? 0.00,
            'total_final' => 0, // Se calculará después
            'notas' => $request->observaciones,
            'stock_devuelto' => false, // Inicializar en false
            'cliente_data' => [
                '_id' => $cliente->_id,
                'name' => $cliente->name,
                'apellidos' => $cliente->apellidos ?? '',
                'email' => $cliente->email,
                'telefono' => $cliente->telefono ?? '',
                'cedula' => $cliente->cedula
            ]
        ]);

        if ($vendedor) {
            $pedido->vendedor_data = [
                '_id' => $vendedor->_id,
                'name' => $vendedor->name,
                'apellidos' => $vendedor->apellidos ?? '',
                'email' => $vendedor->email,
                'telefono' => $vendedor->telefono ?? ''
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

            // Convertir stock a número si es string
            $stockActual = is_numeric($producto->stock) ? (int)$producto->stock : 0;

            // Verificar stock disponible
            if ($stockActual < $cantidad) {
                throw new \Exception("Stock insuficiente para '{$producto->nombre}'. Stock disponible: {$stockActual}");
            }

            $precioUnitario = $producto->precio;
            $subtotal = $cantidad * $precioUnitario;

            $detalles[] = [
                'producto_id' => $producto->_id,
                'producto_nombre' => $producto->nombre,
                'producto_data' => [
                    '_id' => $producto->_id,
                    'nombre' => $producto->nombre,
                    'precio' => $producto->precio,
                    'imagen' => $producto->imagen ?? null
                ],
                'cantidad' => $cantidad,
                'precio_unitario' => $precioUnitario,
                'total' => $subtotal,
                'fecha_agregado' => now()
            ];

            $total += $subtotal;

            // Actualizar stock del producto (convertir a número y guardar)
            $nuevoStock = $stockActual - $cantidad;
            $producto->update(['stock' => $nuevoStock]);
        }

        \Log::info('Antes de guardar pedido', [
            'cantidad_detalles' => count($detalles),
            'detalles' => $detalles,
            'total' => $total
        ]);

        // Asignar valores directamente
        $pedido->total = $total;
        $pedido->total_final = $total - $pedido->descuento;

        // Asignar detalles directamente al array de attributes
        $pedido->setAttribute('detalles', $detalles);

        // Guardar pedido
        if (!$pedido->save()) {
            throw new \Exception('No se pudo guardar el pedido en la base de datos.');
        }

        // Recargar el pedido desde la base de datos para verificar
        $pedidoGuardado = Pedido::find($pedido->_id);

        // Verificar que los detalles se guardaron
        $detallesGuardados = [];
        if ($pedidoGuardado) {
            $detallesGuardados = $pedidoGuardado->getRawDetalles();
        }

        \Log::info('Pedido creado exitosamente', [
            'pedido_id' => $pedido->_id,
            'numero_pedido' => $pedido->numero_pedido,
            'total' => $pedido->total_final,
            'detalles_guardados' => is_array($detallesGuardados) ? count($detallesGuardados) : 0,
            'primer_detalle' => (is_array($detallesGuardados) && count($detallesGuardados) > 0) ? $detallesGuardados[0] : null
        ]);

        // 🔔 Enviar notificaciones automáticas
        try {
            // Recargar relaciones para notificaciones
            $pedidoCompleto = Pedido::with(['cliente', 'vendedor'])->find($pedido->_id);

            // Notificar nuevo pedido a administradores y líderes
            NotificationService::nuevoPedido($pedidoCompleto);

            // Si hay vendedor, notificar nueva venta
            if ($pedidoCompleto->vendedor) {
                NotificationService::nuevaVenta($pedidoCompleto);
            }

            // Notificar al cliente
            if ($pedidoCompleto->cliente) {
                NotificationService::crear(
                    $pedidoCompleto->cliente->id,
                    'pedido',
                    "Pedido #{$pedidoCompleto->numero_pedido} Creado",
                    "Tu pedido ha sido creado exitosamente por un monto de $" . number_format($pedidoCompleto->total_final, 2),
                    [
                        'pedido_id' => $pedidoCompleto->_id,
                        'numero_pedido' => $pedidoCompleto->numero_pedido,
                        'total' => $pedidoCompleto->total_final
                    ],
                    'alta'
                );
            }

            \Log::info('Notificaciones enviadas para pedido', ['pedido_id' => $pedido->_id]);
        } catch (\Exception $e) {
            \Log::error('Error al enviar notificaciones: ' . $e->getMessage());
        }

        return redirect()->route('admin.pedidos.index')
            ->with('success', 'Pedido creado exitosamente.');

        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()
                           ->withErrors($e->validator)
                           ->withInput();
        } catch (\Exception $e) {
            \Log::error('Error al crear pedido: ' . $e->getMessage());
            return redirect()->back()
                           ->with('error', $e->getMessage())
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

        // Para pedidos antiguos sin el campo stock_devuelto, asumimos false si el estado no es 'cancelado'
        $stockDevuelto = $pedido->stock_devuelto ?? ($estadoAnterior === 'cancelado');

        // Leer detalles directamente de MongoDB
        $detalles = $pedido->getRawDetalles() ?? [];

        \Log::info('=== INICIO updateStatus ===', [
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
                    // Convertir stock a número si es string
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
            // Marcar que el stock fue devuelto
            $pedido->stock_devuelto = true;
            \Log::info('Stock devuelto, marcando bandera stock_devuelto = true');
        }

        // Si se reactiva un pedido que estaba cancelado, restar stock
        if ($estadoAnterior === 'cancelado' && $request->estado !== 'cancelado' && $stockDevuelto) {
            \Log::info('>>> Iniciando resta de stock por reactivación');

            foreach ($detalles as $detalle) {
                $producto = Producto::find($detalle['producto_id']);
                if ($producto) {
                    // Convertir stock a número si es string
                    $stockActual = is_numeric($producto->stock) ? (int)$producto->stock : 0;
                    $nuevoStock = $stockActual - (int)$detalle['cantidad'];

                    \Log::info('Restando stock por reactivación', [
                        'producto' => $producto->nombre,
                        'stock_antes' => $stockActual,
                        'cantidad_restada' => $detalle['cantidad'],
                        'stock_despues' => $nuevoStock
                    ]);

                    $producto->update(['stock' => max(0, $nuevoStock)]); // No permitir stock negativo
                }
            }
            // Marcar que el stock fue restado nuevamente
            $pedido->stock_devuelto = false;
            \Log::info('Stock restado, marcando bandera stock_devuelto = false');
        }

        // Actualizar el estado del pedido
        $pedido->update([
            'estado' => $request->estado,
            'stock_devuelto' => $pedido->stock_devuelto ?? false
        ]);

        // 🔔 Enviar notificación de cambio de estado
        try {
            $pedidoCompleto = Pedido::with(['cliente', 'vendedor'])->find($pedido->_id);
            if ($pedidoCompleto) {
                NotificationService::cambioEstadoPedido($pedidoCompleto, $estadoAnterior);

                // Notificar también al vendedor si existe
                if ($pedidoCompleto->vendedor && $request->estado === 'entregado') {
                    NotificationService::crear(
                        $pedidoCompleto->vendedor->id,
                        'venta',
                        "Pedido Entregado #{$pedidoCompleto->numero_pedido}",
                        "El pedido ha sido entregado exitosamente. ¡Venta completada!",
                        [
                            'pedido_id' => $pedidoCompleto->_id,
                            'numero_pedido' => $pedidoCompleto->numero_pedido,
                            'total' => $pedidoCompleto->total_final
                        ],
                        'alta'
                    );
                }
            }
        } catch (\Exception $e) {
            \Log::error('Error al enviar notificaciones de cambio de estado: ' . $e->getMessage());
        }

        \Log::info('=== FIN updateStatus ===');

        return redirect()->back()
            ->with('success', 'Estado del pedido actualizado exitosamente.');
    }

    public function destroy(Pedido $pedido)
    {
        // Para pedidos antiguos sin el campo stock_devuelto, asumimos false si el estado no es 'cancelado'
        $stockDevuelto = $pedido->stock_devuelto ?? ($pedido->estado === 'cancelado');

        // Leer detalles directamente de MongoDB
        $detalles = $pedido->getRawDetalles() ?? [];

        \Log::info('=== INICIO destroy ===', [
            'pedido_id' => $pedido->_id,
            'numero_pedido' => $pedido->numero_pedido,
            'estado' => $pedido->estado,
            'stock_devuelto' => $stockDevuelto,
            'cantidad_detalles' => count($detalles)
        ]);

        if (in_array($pedido->estado, ['entregado'])) {
            \Log::warning('No se puede eliminar: pedido entregado');
            return redirect()->back()
                ->with('error', 'No se pueden eliminar pedidos entregados.');
        }

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
                    // Convertir stock a número si es string
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

        $pedido->delete();
        \Log::info('Pedido eliminado exitosamente');
        \Log::info('=== FIN destroy ===');

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

            // Buscar usuario con rol vendedor, líder o administrador por cédula
            $vendedor = User::where('cedula', $cedula)
                           ->whereIn('rol', ['vendedor', 'lider', 'administrador'])
                           ->first();

            if ($vendedor) {
                return response()->json([
                    'success' => true,
                    'user' => [
                        'id' => $vendedor->id,
                        'name' => $vendedor->name,
                        'email' => $vendedor->email,
                        'cedula' => $vendedor->cedula,
                        'rol' => $vendedor->rol
                    ],
                    'message' => 'Usuario encontrado correctamente'
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'No se encontró ningún usuario (vendedor, líder o administrador) con la cédula: ' . $cedula
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