<?php

namespace App\Http\Controllers\Cliente;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;
use App\Models\Pedido;
use App\Models\Producto;
use App\Models\User;

class PedidoClienteController extends Controller
{
    /**
     * Constructor - Middleware de autenticación
     */
    public function __construct()
    {
        $this->middleware(['auth', 'verified']);
    }

    /**
     * Mostrar listado de pedidos del cliente
     * 
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        
        // Construir query base
        $query = Pedido::where('user_id', $user->_id)
            ->orderBy('created_at', 'desc');
        
        // Aplicar filtros
        if ($request->has('estado') && $request->estado != '') {
            $query->where('estado', $request->estado);
        }
        
        if ($request->has('fecha') && $request->fecha != '') {
            $query->whereDate('created_at', $request->fecha);
        }
        
        if ($request->has('busqueda') && $request->busqueda != '') {
            $busqueda = $request->busqueda;
            $query->where(function($q) use ($busqueda) {
                $q->where('numero_pedido', 'like', "%{$busqueda}%")
                  ->orWhere('direccion_entrega', 'like', "%{$busqueda}%");
            });
        }
        
        // Paginación
        $pedidos = $query->paginate(10);
        
        // Estadísticas del cliente
        $stats = $this->getClienteStats($user);
        
        return view('cliente.pedidos.index', compact('pedidos', 'stats'));
    }

    /**
     * Mostrar detalles de un pedido específico
     * 
     * @param string $id
     * @return \Illuminate\View\View
     */
    public function show($id)
    {
        $user = Auth::user();
        
        $pedido = Pedido::where('_id', $id)
            ->where('user_id', $user->_id)
            ->firstOrFail();
        
        return view('cliente.pedidos.show', compact('pedido'));
    }

    /**
     * Mostrar formulario de crear pedido
     * 
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $user = Auth::user();
        
        // Obtener productos activos con stock
        $productos = Cache::remember('productos_disponibles', 600, function () {
            return Producto::where('activo', true)
                ->where('stock', '>', 0)
                ->orderBy('nombre', 'asc')
                ->get();
        });
        
        // Agrupar por categoría
        $productosPorCategoria = $productos->groupBy(function ($producto) {
            return $producto->categoria_data['nombre'] ?? 'Sin Categoría';
        });
        
        return view('cliente.pedidos.create', compact('productosPorCategoria', 'user'));
    }

    /**
     * Guardar nuevo pedido con seguridad para MongoDB
     * 
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        
        // Validar datos con reglas específicas para MongoDB
        $validator = Validator::make($request->all(), [
            'productos' => 'required|array|min:1',
            'productos.*.producto_id' => 'required|string|size:24', // MongoDB ObjectId tiene 24 caracteres
            'productos.*.cantidad' => 'required|integer|min:1|max:100',
            'direccion_entrega' => 'required|string|max:500',
            'telefono_entrega' => 'required|string|max:20',
            'notas' => 'nullable|string|max:1000',
            'metodo_pago' => 'required|in:efectivo,transferencia,tarjeta',
        ], [
            'productos.required' => 'Debes seleccionar al menos un producto',
            'productos.*.producto_id.required' => 'ID de producto requerido',
            'productos.*.producto_id.size' => 'ID de producto inválido',
            'productos.*.cantidad.min' => 'La cantidad mínima es 1',
            'productos.*.cantidad.max' => 'La cantidad máxima es 100',
            'direccion_entrega.required' => 'La dirección de entrega es requerida',
            'telefono_entrega.required' => 'El teléfono de contacto es requerido',
            'metodo_pago.required' => 'Debes seleccionar un método de pago',
            'metodo_pago.in' => 'Método de pago inválido',
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput()
                ->with('error', 'Por favor corrige los errores en el formulario.');
        }
        
        try {
            // Verificar límite de pedidos pendientes (prevención de spam)
            $pedidosPendientes = Pedido::where('user_id', $user->_id)
                ->where('estado', 'pendiente')
                ->count();
            
            if ($pedidosPendientes >= 5) {
                return redirect()->back()
                    ->with('error', 'Tienes demasiados pedidos pendientes. Por favor espera a que sean procesados.')
                    ->withInput();
            }
            
            // Crear pedido con protección XSS
            $pedido = new Pedido();
            $pedido->numero_pedido = $this->generarNumeroPedido();
            $pedido->user_id = $user->_id;
            $pedido->cliente_data = [
                '_id' => $user->_id,
                'name' => htmlspecialchars($user->name, ENT_QUOTES, 'UTF-8'),
                'apellidos' => htmlspecialchars($user->apellidos ?? '', ENT_QUOTES, 'UTF-8'),
                'email' => filter_var($user->email, FILTER_SANITIZE_EMAIL),
                'telefono' => htmlspecialchars($user->telefono ?? '', ENT_QUOTES, 'UTF-8'),
                'cedula' => htmlspecialchars($user->cedula ?? '', ENT_QUOTES, 'UTF-8')
            ];
            
            // Vendedor asignado (si existe)
            if ($user->vendedor_id) {
                $vendedor = User::find($user->vendedor_id);
                if ($vendedor) {
                    $pedido->vendedor_id = $vendedor->_id;
                    $pedido->vendedor_data = [
                        '_id' => $vendedor->_id,
                        'name' => htmlspecialchars($vendedor->name, ENT_QUOTES, 'UTF-8'),
                        'apellidos' => htmlspecialchars($vendedor->apellidos ?? '', ENT_QUOTES, 'UTF-8'),
                        'email' => filter_var($vendedor->email, FILTER_SANITIZE_EMAIL),
                        'telefono' => htmlspecialchars($vendedor->telefono ?? '', ENT_QUOTES, 'UTF-8')
                    ];
                }
            }
            
            $pedido->estado = 'pendiente';
            $pedido->direccion_entrega = htmlspecialchars($request->direccion_entrega, ENT_QUOTES, 'UTF-8');
            $pedido->telefono_entrega = htmlspecialchars($request->telefono_entrega, ENT_QUOTES, 'UTF-8');
            $pedido->notas = $request->notas ? htmlspecialchars($request->notas, ENT_QUOTES, 'UTF-8') : null;
            $pedido->metodo_pago = $request->metodo_pago;
            
            // Procesar detalles del pedido con validación de stock
            $detalles = [];
            $total = 0;
            $productosIds = [];
            
            foreach ($request->productos as $item) {
                // Validar que el producto_id sea un ObjectId válido
                if (!preg_match('/^[a-f0-9]{24}$/i', $item['producto_id'])) {
                    continue;
                }
                
                // Prevenir duplicados
                if (in_array($item['producto_id'], $productosIds)) {
                    continue;
                }
                $productosIds[] = $item['producto_id'];
                
                $producto = Producto::find($item['producto_id']);
                
                if (!$producto || !$producto->activo) {
                    continue;
                }
                
                // Validar cantidad contra stock real
                $cantidadSolicitada = min((int)$item['cantidad'], 100); // Máximo 100 unidades
                
                if ($producto->stock < $cantidadSolicitada) {
                    return redirect()->back()
                        ->with('error', "El producto {$producto->nombre} no tiene suficiente stock. Disponible: {$producto->stock}")
                        ->withInput();
                }
                
                $subtotal = $producto->precio * $cantidadSolicitada;
                $total += $subtotal;
                
                // Proteger datos embebidos
                $detalles[] = [
                    'producto_id' => $producto->_id,
                    'producto_data' => [
                        '_id' => $producto->_id,
                        'nombre' => htmlspecialchars($producto->nombre, ENT_QUOTES, 'UTF-8'),
                        'descripcion' => htmlspecialchars($producto->descripcion ?? '', ENT_QUOTES, 'UTF-8'),
                        'precio' => (float)$producto->precio,
                        'imagen' => $producto->imagen_principal ?? null,
                        'categoria_data' => $producto->categoria_data ?? []
                    ],
                    'cantidad' => $cantidadSolicitada,
                    'precio_unitario' => (float)$producto->precio,
                    'subtotal' => (float)$subtotal
                ];
                
                // Actualizar stock de forma atómica (importante para MongoDB)
                $producto->decrement('stock', $cantidadSolicitada);
                $producto->increment('veces_vendido', $cantidadSolicitada);
            }
            
            // Validar que se hayan agregado productos
            if (empty($detalles)) {
                return redirect()->back()
                    ->with('error', 'No se pudo procesar ningún producto. Verifica que estén disponibles.')
                    ->withInput();
            }
            
            $pedido->detalles = $detalles;
            $pedido->total = (float)$total;
            $pedido->descuento = 0;
            $pedido->total_final = (float)$total;
            
            // Fecha estimada de entrega (3 días hábiles)
            $pedido->fecha_entrega_estimada = now()->addDays(3);
            
            // Historial de estados con información del usuario
            $pedido->historial_estados = [[
                'estado' => 'pendiente',
                'fecha' => now(),
                'usuario_id' => $user->_id,
                'usuario_nombre' => $user->name,
                'motivo' => 'Pedido creado por el cliente',
                'ip' => $request->ip()
            ]];
            
            // Campos de auditoría
            $pedido->ip_creacion = $request->ip();
            $pedido->user_agent = $request->userAgent();
            
            $pedido->save();
            
            // Limpiar cache de estadísticas
            Cache::forget("cliente_stats_{$user->_id}");
            
            // Log de auditoría
            \Log::info("Pedido creado exitosamente", [
                'pedido_id' => $pedido->_id,
                'numero_pedido' => $pedido->numero_pedido,
                'user_id' => $user->_id,
                'total' => $pedido->total_final,
                'productos' => count($detalles)
            ]);
            
            return redirect()->route('cliente.pedidos.show', $pedido->_id)
                ->with('success', '¡Pedido creado exitosamente! Número de pedido: ' . $pedido->numero_pedido);
                
        } catch (\Exception $e) {
            \Log::error('Error al crear pedido: ' . $e->getMessage(), [
                'user_id' => $user->_id,
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()->back()
                ->with('error', 'Ocurrió un error al crear el pedido. Por favor intenta nuevamente.')
                ->withInput();
        }
    }

    /**
     * Cancelar un pedido
     * 
     * @param Request $request
     * @param string $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function cancel(Request $request, $id)
    {
        $user = Auth::user();
        
        $pedido = Pedido::where('_id', $id)
            ->where('user_id', $user->_id)
            ->firstOrFail();
        
        // Verificar si puede ser cancelado
        if (!$pedido->puedeSerCancelado()) {
            return response()->json([
                'success' => false,
                'message' => 'Este pedido no puede ser cancelado en su estado actual.'
            ], 400);
        }
        
        try {
            // Devolver stock
            if (!$pedido->stock_devuelto) {
                foreach ($pedido->detalles as $detalle) {
                    $producto = Producto::find($detalle['producto_id']);
                    if ($producto) {
                        $producto->stock += $detalle['cantidad'];
                        $producto->veces_vendido -= $detalle['cantidad'];
                        $producto->save();
                    }
                }
                $pedido->stock_devuelto = true;
            }
            
            // Cambiar estado
            $pedido->cambiarEstado('cancelado', $request->motivo ?? 'Cancelado por el cliente', $user->_id);
            
            // Limpiar cache
            Cache::forget("cliente_stats_{$user->_id}");
            
            return response()->json([
                'success' => true,
                'message' => 'Pedido cancelado exitosamente.'
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Error al cancelar pedido: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Ocurrió un error al cancelar el pedido.'
            ], 500);
        }
    }

    /**
     * Obtener estadísticas del cliente con caché
     * Optimizado para MongoDB y PWA
     * 
     * @param User $user
     * @return array
     */
    private function getClienteStats($user)
    {
        return Cache::remember("cliente_stats_{$user->_id}", 600, function () use ($user) {
            try {
                // Usar agregación de MongoDB para mejor rendimiento
                $userId = $user->_id;
                
                return [
                    'total_pedidos' => Pedido::where('user_id', $userId)->count(),
                    'pedidos_pendientes' => Pedido::where('user_id', $userId)->where('estado', 'pendiente')->count(),
                    'pedidos_confirmados' => Pedido::where('user_id', $userId)->whereIn('estado', ['confirmado', 'en_preparacion'])->count(),
                    'pedidos_entregados' => Pedido::where('user_id', $userId)->where('estado', 'entregado')->count(),
                    'pedidos_cancelados' => Pedido::where('user_id', $userId)->where('estado', 'cancelado')->count(),
                    'total_gastado' => Pedido::where('user_id', $userId)
                        ->whereIn('estado', ['confirmado', 'enviado', 'entregado'])
                        ->sum('total_final') ?? 0,
                    'promedio_pedido' => Pedido::where('user_id', $userId)
                        ->whereIn('estado', ['confirmado', 'enviado', 'entregado'])
                        ->avg('total_final') ?? 0,
                ];
            } catch (\Exception $e) {
                \Log::error('Error al obtener estadísticas del cliente: ' . $e->getMessage());
                return [
                    'total_pedidos' => 0,
                    'pedidos_pendientes' => 0,
                    'pedidos_confirmados' => 0,
                    'pedidos_entregados' => 0,
                    'pedidos_cancelados' => 0,
                    'total_gastado' => 0,
                    'promedio_pedido' => 0,
                ];
            }
        });
    }

    /**
     * Generar número de pedido consecutivo
     * 
     * @return string
     */
    private function generarNumeroPedido()
    {
        $año = date('Y');
        $ultimoNumero = Pedido::whereYear('created_at', $año)->count() + 1;
        return 'ARE-' . $año . '-' . str_pad($ultimoNumero, 5, '0', STR_PAD_LEFT);
    }
}
