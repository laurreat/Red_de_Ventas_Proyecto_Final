<?php

namespace App\Http\Controllers\Vendedor;

use App\Http\Controllers\Controller;
use App\Models\Pedido;
use App\Models\User;
use App\Models\Producto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PedidoController extends Controller
{
    public function index(Request $request)
    {
        $vendedor = Auth::user();
        $query = Pedido::where('vendedor_id', $vendedor->id)
                      ->with(['cliente', 'productos']);

        // Filtros
        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        if ($request->filled('fecha_desde')) {
            $query->whereDate('created_at', '>=', $request->fecha_desde);
        }

        if ($request->filled('fecha_hasta')) {
            $query->whereDate('created_at', '<=', $request->fecha_hasta);
        }

        if ($request->filled('cliente')) {
            $query->whereHas('cliente', function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->cliente . '%');
            });
        }

        $pedidos = $query->orderBy('created_at', 'desc')->paginate(15);

        // Estadísticas para el dashboard de pedidos
        $stats = [
            'total' => Pedido::where('vendedor_id', $vendedor->id)->count(),
            'pendientes' => Pedido::where('vendedor_id', $vendedor->id)->where('estado', 'pendiente')->count(),
            'completados' => Pedido::where('vendedor_id', $vendedor->id)->where('estado', 'completado')->count(),
            'cancelados' => Pedido::where('vendedor_id', $vendedor->id)->where('estado', 'cancelado')->count(),
            'total_ventas' => Pedido::where('vendedor_id', $vendedor->id)->sum('total_final')
        ];

        return view('vendedor.pedidos.index', compact('pedidos', 'stats'));
    }

    public function show($id)
    {
        $vendedor = Auth::user();
        $pedido = Pedido::where('vendedor_id', $vendedor->id)
                       ->where('id', $id)
                       ->with(['cliente', 'productos'])
                       ->firstOrFail();

        return view('vendedor.pedidos.show', compact('pedido'));
    }

    public function create()
    {
        $clientes = User::where('rol', 'cliente')->get();
        $productos = Producto::where('estado', 'activo')->get();

        return view('vendedor.pedidos.create', compact('clientes', 'productos'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'cliente_id' => 'required|exists:users,id',
            'productos' => 'required|array|min:1',
            'productos.*.id' => 'required|exists:productos,id',
            'productos.*.cantidad' => 'required|integer|min:1',
            'notas' => 'nullable|string|max:500'
        ]);

        $vendedor = Auth::user();

        DB::beginTransaction();
        try {
            // Generar número de pedido único
            $numeroPedido = 'PED-' . str_pad(Pedido::count() + 1, 6, '0', STR_PAD_LEFT);

            // Calcular totales
            $subtotal = 0;
            $productosData = [];

            foreach ($request->productos as $productoData) {
                $producto = Producto::findOrFail($productoData['id']);
                $cantidad = $productoData['cantidad'];
                $precio = $producto->precio;
                $total = $precio * $cantidad;

                $subtotal += $total;

                $productosData[] = [
                    'producto_id' => $producto->id,
                    'cantidad' => $cantidad,
                    'precio_unitario' => $precio,
                    'total' => $total
                ];
            }

            $iva = $subtotal * 0.19; // Asumiendo IVA del 19%
            $totalFinal = $subtotal + $iva;

            // Crear el pedido
            $pedido = Pedido::create([
                'numero_pedido' => $numeroPedido,
                'cliente_id' => $request->cliente_id,
                'vendedor_id' => $vendedor->id,
                'subtotal' => $subtotal,
                'iva' => $iva,
                'total_final' => $totalFinal,
                'estado' => 'pendiente',
                'notas' => $request->notas
            ]);

            // Asociar productos al pedido
            foreach ($productosData as $productoData) {
                $pedido->productos()->attach($productoData['producto_id'], [
                    'cantidad' => $productoData['cantidad'],
                    'precio_unitario' => $productoData['precio_unitario'],
                    'total' => $productoData['total']
                ]);
            }

            DB::commit();

            return redirect()->route('vendedor.pedidos.show', $pedido->id)
                           ->with('success', 'Pedido creado exitosamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                           ->with('error', 'Error al crear el pedido: ' . $e->getMessage())
                           ->withInput();
        }
    }

    public function edit($id)
    {
        $vendedor = Auth::user();
        $pedido = Pedido::where('vendedor_id', $vendedor->id)
                       ->where('id', $id)
                       ->where('estado', 'pendiente') // Solo pedidos pendientes se pueden editar
                       ->with(['cliente', 'productos'])
                       ->firstOrFail();

        $clientes = User::where('rol', 'cliente')->get();
        $productos = Producto::where('estado', 'activo')->get();

        return view('vendedor.pedidos.edit', compact('pedido', 'clientes', 'productos'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'cliente_id' => 'required|exists:users,id',
            'productos' => 'required|array|min:1',
            'productos.*.id' => 'required|exists:productos,id',
            'productos.*.cantidad' => 'required|integer|min:1',
            'notas' => 'nullable|string|max:500'
        ]);

        $vendedor = Auth::user();
        $pedido = Pedido::where('vendedor_id', $vendedor->id)
                       ->where('id', $id)
                       ->where('estado', 'pendiente')
                       ->firstOrFail();

        DB::beginTransaction();
        try {
            // Calcular nuevos totales
            $subtotal = 0;
            $productosData = [];

            foreach ($request->productos as $productoData) {
                $producto = Producto::findOrFail($productoData['id']);
                $cantidad = $productoData['cantidad'];
                $precio = $producto->precio;
                $total = $precio * $cantidad;

                $subtotal += $total;

                $productosData[] = [
                    'producto_id' => $producto->id,
                    'cantidad' => $cantidad,
                    'precio_unitario' => $precio,
                    'total' => $total
                ];
            }

            $iva = $subtotal * 0.19;
            $totalFinal = $subtotal + $iva;

            // Actualizar el pedido
            $pedido->update([
                'cliente_id' => $request->cliente_id,
                'subtotal' => $subtotal,
                'iva' => $iva,
                'total_final' => $totalFinal,
                'notas' => $request->notas
            ]);

            // Actualizar productos del pedido
            $pedido->productos()->detach();
            foreach ($productosData as $productoData) {
                $pedido->productos()->attach($productoData['producto_id'], [
                    'cantidad' => $productoData['cantidad'],
                    'precio_unitario' => $productoData['precio_unitario'],
                    'total' => $productoData['total']
                ]);
            }

            DB::commit();

            return redirect()->route('vendedor.pedidos.show', $pedido->id)
                           ->with('success', 'Pedido actualizado exitosamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                           ->with('error', 'Error al actualizar el pedido: ' . $e->getMessage())
                           ->withInput();
        }
    }

    public function updateEstado(Request $request, $id)
    {
        $request->validate([
            'estado' => 'required|in:pendiente,procesando,completado,cancelado',
            'motivo_cancelacion' => 'required_if:estado,cancelado|string|max:255'
        ]);

        $vendedor = Auth::user();
        $pedido = Pedido::where('vendedor_id', $vendedor->id)
                       ->where('id', $id)
                       ->firstOrFail();

        $pedido->update([
            'estado' => $request->estado,
            'motivo_cancelacion' => $request->motivo_cancelacion
        ]);

        return redirect()->back()->with('success', 'Estado del pedido actualizado.');
    }

    public function destroy($id)
    {
        $vendedor = Auth::user();
        $pedido = Pedido::where('vendedor_id', $vendedor->id)
                       ->where('id', $id)
                       ->where('estado', 'pendiente') // Solo pendientes se pueden eliminar
                       ->firstOrFail();

        DB::beginTransaction();
        try {
            $pedido->productos()->detach();
            $pedido->delete();

            DB::commit();

            return redirect()->route('vendedor.pedidos.index')
                           ->with('success', 'Pedido eliminado exitosamente.');

        } catch (\Exception $e) {
            DB::rollBack();
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

        $pedidos = $query->with(['cliente', 'productos'])->get();

        // Aquí implementarías la lógica de exportación (CSV, Excel, PDF)
        // Por ahora retornamos un JSON
        return response()->json($pedidos);
    }
}