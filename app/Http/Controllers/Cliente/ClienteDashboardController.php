<?php

namespace App\Http\Controllers\Cliente;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use App\Models\Pedido;
use App\Models\Producto;
use App\Models\User;

class ClienteDashboardController extends Controller
{
    /**
     * Constructor - Middleware de autenticación
     */
    public function __construct()
    {
        $this->middleware(['auth', 'verified']);
    }

    /**
     * Mostrar dashboard del cliente
     * 
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $user = Auth::user();
        
        // Cache de estadísticas por 5 minutos
        $cacheKey = "cliente_stats_{$user->_id}";
        
        $stats = Cache::remember($cacheKey, 300, function () use ($user) {
            return $this->getClienteStats($user);
        });

        // Obtener pedidos recientes (últimos 5)
        $pedidos_recientes = Pedido::where('cliente_id', $user->_id)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Obtener productos favoritos del cliente
        $productos_favoritos = $this->getProductosFavoritos($user);

        // Obtener productos disponibles del catálogo (cache por 10 minutos)
        $productos_catalogo = Cache::remember('productos_catalogo', 600, function () {
            return Producto::where('activo', true)
                ->where('stock', '>', 0)
                ->orderBy('destacado', 'desc')
                ->orderBy('veces_vendido', 'desc')
                ->orderBy('nombre', 'asc')
                ->get();
        });

        // Agrupar productos por categoría
        $productos_por_categoria = $productos_catalogo->groupBy(function ($producto) {
            return $producto->categoria_data['nombre'] ?? 'Sin Categoría';
        });

        // Obtener categorías únicas
        $categorias = $productos_por_categoria->keys()->sort()->values();
        return view('cliente.dashboard', compact(
            'stats',
            'pedidos_recientes',
            'productos_favoritos',
            'productos_catalogo',
            'productos_por_categoria',
            'categorias'
        ));
    }

    /**
     * Obtener estadísticas del cliente
     * 
     * @param User $user
     * @return array
     */
    private function getClienteStats(User $user): array
    {
        // Total de pedidos
        $total_pedidos = Pedido::where('cliente_id', $user->_id)->count();

        // Total gastado
        $total_gastado = Pedido::where('cliente_id', $user->_id)
            ->where('estado', '!=', 'cancelado')
            ->sum('total_final') ?? 0;

        // Total de referidos
        $total_referidos = User::where('referido_por', $user->_id)->count();

        // Pedidos completados
        $pedidos_completados = Pedido::where('cliente_id', $user->_id)
            ->where('estado', 'entregado')
            ->count();

        // Productos más pedidos (para recomendaciones)
        $productos_frecuentes = $this->getProductosFrecuentes($user);

        return [
            'total_pedidos' => $total_pedidos,
            'total_gastado' => $total_gastado,
            'total_referidos' => $total_referidos,
            'pedidos_completados' => $pedidos_completados,
            'productos_frecuentes' => $productos_frecuentes,
            'referidos_realizados' => $total_referidos
        ];
    }

    /**
     * Obtener productos favoritos del cliente
     * 
     * @param User $user
     * @return \Illuminate\Database\Eloquent\Collection
     */
    private function getProductosFavoritos(User $user)
    {
        // Si el usuario tiene campo favoritos en MongoDB
        if (isset($user->favoritos) && is_array($user->favoritos)) {
            return Producto::whereIn('_id', $user->favoritos)
                ->where('activo', true)
                ->get();
        }

        return collect();
    }

    /**
     * Obtener productos más pedidos por el cliente
     * 
     * @param User $user
     * @return array
     */
    private function getProductosFrecuentes(User $user): array
    {
        $pedidos = Pedido::where('cliente_id', $user->_id)
            ->where('estado', '!=', 'cancelado')
            ->get();

        $productos_count = [];

        foreach ($pedidos as $pedido) {
            if (isset($pedido->items) && is_array($pedido->items)) {
                foreach ($pedido->items as $item) {
                    $producto_id = $item['producto_id'] ?? null;
                    if ($producto_id) {
                        if (!isset($productos_count[$producto_id])) {
                            $productos_count[$producto_id] = 0;
                        }
                        $productos_count[$producto_id]++;
                    }
                }
            }
        }

        // Ordenar por frecuencia y retornar top 5
        arsort($productos_count);
        return array_slice($productos_count, 0, 5, true);
    }

    /**
     * Agregar producto a favoritos
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function agregarFavorito(Request $request)
    {
        $request->validate([
            'producto_id' => 'required|string'
        ]);

        $user = Auth::user();
        $producto_id = $request->producto_id;

        // Verificar que el producto existe
        $producto = Producto::find($producto_id);
        if (!$producto) {
            return response()->json([
                'success' => false,
                'message' => 'Producto no encontrado'
            ], 404);
        }

        // Inicializar favoritos si no existe
        $favoritos = $user->favoritos ?? [];

        // Verificar si ya está en favoritos
        if (in_array($producto_id, $favoritos)) {
            return response()->json([
                'success' => false,
                'message' => 'El producto ya está en favoritos'
            ], 400);
        }

        // Agregar a favoritos
        $favoritos[] = $producto_id;
        $user->favoritos = $favoritos;
        $user->save();

        // Limpiar cache
        Cache::forget("cliente_stats_{$user->_id}");

        return response()->json([
            'success' => true,
            'message' => 'Producto agregado a favoritos',
            'total_favoritos' => count($favoritos)
        ]);
    }

    /**
     * Eliminar producto de favoritos
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function eliminarFavorito(Request $request)
    {
        $request->validate([
            'producto_id' => 'required|string'
        ]);

        $user = Auth::user();
        $producto_id = $request->producto_id;

        // Inicializar favoritos si no existe
        $favoritos = $user->favoritos ?? [];

        // Buscar y eliminar
        $key = array_search($producto_id, $favoritos);
        if ($key !== false) {
            unset($favoritos[$key]);
            $favoritos = array_values($favoritos); // Reindexar array
            
            $user->favoritos = $favoritos;
            $user->save();

            // Limpiar cache
            Cache::forget("cliente_stats_{$user->_id}");

            return response()->json([
                'success' => true,
                'message' => 'Producto eliminado de favoritos',
                'total_favoritos' => count($favoritos)
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'El producto no está en favoritos'
        ], 400);
    }

    /**
     * Actualizar perfil del cliente
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function actualizarPerfil(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'apellidos' => 'sometimes|string|max:255',
            'telefono' => 'sometimes|string|max:20',
            'direccion' => 'sometimes|string|max:500',
            'ciudad' => 'sometimes|string|max:100'
        ]);

        // Actualizar solo campos enviados
        foreach ($validated as $key => $value) {
            $user->$key = $value;
        }

        $user->save();

        // Limpiar cache
        Cache::forget("cliente_stats_{$user->_id}");

        return response()->json([
            'success' => true,
            'message' => 'Perfil actualizado correctamente',
            'user' => $user
        ]);
    }

    /**
     * Obtener productos recomendados
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function productosRecomendados()
    {
        $user = Auth::user();

        // Cache de recomendaciones por 1 hora
        $cacheKey = "recomendaciones_{$user->_id}";

        $productos = Cache::remember($cacheKey, 3600, function () use ($user) {
            // Obtener productos más pedidos
            $frecuentes = $this->getProductosFrecuentes($user);
            $ids_frecuentes = array_keys($frecuentes);

            // Obtener productos similares
            $productos_recomendados = Producto::where('activo', true)
                ->whereNotIn('_id', $ids_frecuentes)
                ->inRandomOrder()
                ->limit(6)
                ->get();

            return $productos_recomendados;
        });

        return response()->json([
            'success' => true,
            'productos' => $productos
        ]);
    }

    /**
     * Crear pedido desde el carrito
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function crearPedido(Request $request)
    {
        $validated = $request->validate([
            'items' => 'required|array|min:1',
            'items.*.producto_id' => 'required|string',
            'items.*.cantidad' => 'required|integer|min:1',
            'items.*.precio' => 'required|numeric|min:0',
            'direccion_entrega' => 'sometimes|string',
            'observaciones' => 'sometimes|string|max:500'
        ]);

        $user = Auth::user();

        try {
            // Calcular totales
            $subtotal = 0;
            $items_procesados = [];

            foreach ($validated['items'] as $item) {
                $producto = Producto::find($item['producto_id']);
                
                if (!$producto || !$producto->activo) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Uno o más productos no están disponibles'
                    ], 400);
                }

                $precio_unitario = $producto->precio;
                $cantidad = $item['cantidad'];
                $subtotal_item = $precio_unitario * $cantidad;

                $items_procesados[] = [
                    'producto_id' => $producto->_id,
                    'nombre' => $producto->nombre,
                    'cantidad' => $cantidad,
                    'precio_unitario' => $precio_unitario,
                    'subtotal' => $subtotal_item
                ];

                $subtotal += $subtotal_item;
            }

            // Calcular impuestos y total
            $impuestos = $subtotal * 0.08; // 8% de impuesto ejemplo
            $total_final = $subtotal + $impuestos;

            // Generar número de pedido único
            $numero_pedido = 'PED-' . strtoupper(substr(uniqid(), -8));

            // Crear pedido
            $pedido = new Pedido();
            $pedido->numero_pedido = $numero_pedido;
            $pedido->cliente_id = $user->_id;
            $pedido->cliente_nombre = $user->name;
            $pedido->cliente_email = $user->email;
            $pedido->cliente_telefono = $user->telefono ?? '';
            $pedido->items = $items_procesados;
            $pedido->subtotal = $subtotal;
            $pedido->impuestos = $impuestos;
            $pedido->total_final = $total_final;
            $pedido->estado = 'pendiente';
            $pedido->direccion_entrega = $validated['direccion_entrega'] ?? $user->direccion ?? '';
            $pedido->observaciones = $validated['observaciones'] ?? '';
            $pedido->metodo_pago = 'pendiente';
            $pedido->save();

            // Limpiar cache
            Cache::forget("cliente_stats_{$user->_id}");

            // TODO: Enviar email de confirmación
            // TODO: Notificar al admin

            return response()->json([
                'success' => true,
                'message' => 'Pedido creado exitosamente',
                'pedido' => [
                    'numero_pedido' => $numero_pedido,
                    'total' => $total_final,
                    'estado' => 'pendiente'
                ]
            ], 201);

        } catch (\Exception $e) {
            \Log::error('Error al crear pedido: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error al procesar el pedido. Intente nuevamente.'
            ], 500);
        }
    }

    /**
     * Obtener historial de pedidos
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function historialPedidos(Request $request)
    {
        $user = Auth::user();
        
        $page = $request->get('page', 1);
        $per_page = $request->get('per_page', 10);

        $pedidos = Pedido::where('cliente_id', $user->_id)
            ->orderBy('created_at', 'desc')
            ->paginate($per_page);

        return response()->json([
            'success' => true,
            'pedidos' => $pedidos
        ]);
    }

    /**
     * Ver detalle de un pedido
     * 
     * @param string $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function verPedido($id)
    {
        $user = Auth::user();

        $pedido = Pedido::where('_id', $id)
            ->where('cliente_id', $user->_id)
            ->first();

        if (!$pedido) {
            return response()->json([
                'success' => false,
                'message' => 'Pedido no encontrado'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'pedido' => $pedido
        ]);
    }

    /**
     * Cancelar pedido (solo si está en estado pendiente)
     * 
     * @param string $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function cancelarPedido($id)
    {
        $user = Auth::user();

        $pedido = Pedido::where('_id', $id)
            ->where('cliente_id', $user->_id)
            ->first();

        if (!$pedido) {
            return response()->json([
                'success' => false,
                'message' => 'Pedido no encontrado'
            ], 404);
        }

        // Solo se puede cancelar si está pendiente o confirmado
        if (!in_array($pedido->estado, ['pendiente', 'confirmado'])) {
            return response()->json([
                'success' => false,
                'message' => 'No se puede cancelar este pedido'
            ], 400);
        }

        $pedido->estado = 'cancelado';
        $pedido->cancelado_at = now();
        $pedido->save();

        // Limpiar cache
        Cache::forget("cliente_stats_{$user->_id}");

        return response()->json([
            'success' => true,
            'message' => 'Pedido cancelado exitosamente'
        ]);
    }
}