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
        $query = Pedido::with(['cliente', 'vendedor', 'detalles.producto']);

        // Filtros
        if ($request->has('estado') && $request->estado != '') {
            $query->where('estado', $request->estado);
        }

        if ($request->has('vendedor') && $request->vendedor != '') {
            $query->where('vendedor_id', $request->vendedor);
        }

        if ($request->has('fecha_desde') && $request->fecha_desde != '') {
            $query->whereDate('created_at', '>=', $request->fecha_desde);
        }

        if ($request->has('fecha_hasta') && $request->fecha_hasta != '') {
            $query->whereDate('created_at', '<=', $request->fecha_hasta);
        }

        if ($request->has('buscar') && $request->buscar != '') {
            $query->where(function($q) use ($request) {
                $q->where('numero_pedido', 'like', '%' . $request->buscar . '%')
                  ->orWhereHas('cliente', function($clienteQuery) use ($request) {
                      $clienteQuery->where('name', 'like', '%' . $request->buscar . '%')
                                   ->orWhere('email', 'like', '%' . $request->buscar . '%');
                  });
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

    public function show(Pedido $pedido)
    {
        $pedido->load(['cliente', 'vendedor', 'detalles.producto.categoria']);
        return view('admin.pedidos.show', compact('pedido'));
    }

    public function create()
    {
        $clientes = User::clientes()->orderBy('name')->get();
        $vendedores = User::vendedores()->orderBy('name')->get();
        $productos = Producto::where('activo', true)->with('categoria')->orderBy('nombre')->get();

        return view('admin.pedidos.create', compact('clientes', 'vendedores', 'productos'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'cliente_id' => 'required|exists:users,id',
            'vendedor_id' => 'nullable|exists:users,id',
            'productos' => 'required|array|min:1',
            'productos.*.id' => 'required|exists:productos,id',
            'productos.*.cantidad' => 'required|integer|min:1',
            'descuento' => 'nullable|numeric|min:0',
            'observaciones' => 'nullable|string|max:500'
        ]);

        $numeroPedido = 'PED-' . str_pad(Pedido::count() + 1, 6, '0', STR_PAD_LEFT);

        $pedido = Pedido::create([
            'numero_pedido' => $numeroPedido,
            'cliente_id' => $request->cliente_id,
            'vendedor_id' => $request->vendedor_id,
            'estado' => 'pendiente',
            'observaciones' => $request->observaciones,
            'descuento' => $request->descuento ?? 0,
        ]);

        $subtotal = 0;

        foreach ($request->productos as $productoData) {
            $producto = Producto::find($productoData['id']);
            $cantidad = $productoData['cantidad'];
            $precio = $producto->precio;
            $total = $precio * $cantidad;

            $pedido->detalles()->create([
                'producto_id' => $producto->id,
                'cantidad' => $cantidad,
                'precio_unitario' => $precio,
                'total' => $total
            ]);

            $subtotal += $total;

            // Actualizar stock del producto
            $producto->decrement('stock', $cantidad);
        }

        $pedido->update([
            'subtotal' => $subtotal,
            'total_final' => $subtotal - $pedido->descuento
        ]);

        return redirect()->route('admin.pedidos.index')
            ->with('success', 'Pedido creado exitosamente.');
    }

    public function edit(Pedido $pedido)
    {
        if (in_array($pedido->estado, ['entregado', 'cancelado'])) {
            return redirect()->back()
                ->with('error', 'No se pueden editar pedidos entregados o cancelados.');
        }

        $clientes = User::clientes()->orderBy('name')->get();
        $vendedores = User::vendedores()->orderBy('name')->get();
        $productos = Producto::where('activo', true)->with('categoria')->orderBy('nombre')->get();
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
}