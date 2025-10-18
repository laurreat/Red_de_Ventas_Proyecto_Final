<?php

namespace App\Http\Controllers\Vendedor;

use App\Http\Controllers\Controller;
use App\Models\Venta;
use App\Models\User;
use App\Models\Producto;
use App\Models\Cliente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use MongoDB\BSON\UTCDateTime;

class VentaController extends Controller
{
    /**
     * Mostrar historial de ventas del vendedor
     */
    public function index(Request $request)
    {
        $vendedor = Auth::user();
        $query = Venta::where('vendedor_id', $vendedor->_id);

        // Filtros
        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        if ($request->filled('metodo_pago')) {
            $query->where('metodo_pago', $request->metodo_pago);
        }

        if ($request->filled('fecha_desde')) {
            $query->where('created_at', '>=', new UTCDateTime(strtotime($request->fecha_desde) * 1000));
        }

        if ($request->filled('fecha_hasta')) {
            $query->where('created_at', '<=', new UTCDateTime(strtotime($request->fecha_hasta . ' 23:59:59') * 1000));
        }

        if ($request->filled('cliente')) {
            $query->where(function($q) use ($request) {
                $q->where('cliente_data.name', 'like', '%' . $request->cliente . '%')
                  ->orWhere('cliente_data.email', 'like', '%' . $request->cliente . '%');
            });
        }

        $ventas = $query->orderBy('created_at', 'desc')->paginate(15);

        // Estadísticas
        $stats = [
            'total' => Venta::where('vendedor_id', $vendedor->_id)->count(),
            'completadas' => Venta::where('vendedor_id', $vendedor->_id)->where('estado', 'completada')->count(),
            'pendientes' => Venta::where('vendedor_id', $vendedor->_id)->where('estado', 'pendiente')->count(),
            'canceladas' => Venta::where('vendedor_id', $vendedor->_id)->where('estado', 'cancelada')->count(),
            'total_ventas' => Venta::where('vendedor_id', $vendedor->_id)->where('estado', 'completada')->sum('total_final'),
            'comisiones_totales' => Venta::where('vendedor_id', $vendedor->_id)->where('estado', 'completada')->sum('comision_vendedor'),
            'ventas_mes' => Venta::where('vendedor_id', $vendedor->_id)->whereMonth('created_at', now()->month)->count(),
            'total_mes' => Venta::where('vendedor_id', $vendedor->_id)->whereMonth('created_at', now()->month)->sum('total_final')
        ];

        return view('vendedor.ventas.index', compact('ventas', 'stats'));
    }

    /**
     * Mostrar detalles de una venta
     */
    public function show($id)
    {
        $vendedor = Auth::user();
        $venta = Venta::where('vendedor_id', $vendedor->_id)
                     ->where('_id', $id)
                     ->firstOrFail();

        return view('vendedor.ventas.show', compact('venta'));
    }

    /**
     * Formulario para crear nueva venta
     */
    public function create()
    {
        $clientes = Cliente::where('activo', true)->get();

        // Si no hay modelo Cliente, usar User con rol cliente
        if ($clientes->isEmpty()) {
            $clientes = User::where('rol', 'cliente')->where('activo', true)->get();
        }

        $productos = Producto::where('estado', 'activo')
                             ->where('stock', '>', 0)
                             ->get();

        return view('vendedor.ventas.create', compact('clientes', 'productos'));
    }

    /**
     * Guardar nueva venta
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'cliente_id' => 'required|string',
            'productos' => 'required|array|min:1',
            'productos.*.id' => 'required|string',
            'productos.*.cantidad' => 'required|integer|min:1',
            'productos.*.precio' => 'required|numeric|min:0',
            'metodo_pago' => 'required|in:efectivo,transferencia,tarjeta,credito',
            'descuento' => 'nullable|numeric|min:0',
            'notas' => 'nullable|string|max:500',
            'direccion_entrega' => 'nullable|string|max:255',
            'telefono_entrega' => 'nullable|string|max:20'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                           ->withErrors($validator)
                           ->withInput();
        }

        try {
            $vendedor = Auth::user();

            // Verificar stock y calcular totales
            $subtotal = 0;
            $productosData = [];

            foreach ($request->productos as $productoData) {
                $producto = Producto::findOrFail($productoData['id']);
                $cantidad = (int) $productoData['cantidad'];
                $precio = (float) $productoData['precio'];

                // Verificar stock
                $stockActual = is_numeric($producto->stock) ? (int)$producto->stock : 0;
                if ($stockActual < $cantidad) {
                    throw new \Exception("Stock insuficiente para '{$producto->nombre}'. Stock disponible: {$stockActual}");
                }

                $total = $precio * $cantidad;
                $subtotal += $total;

                $productosData[] = [
                    'producto_id' => $producto->_id,
                    'nombre' => $producto->nombre,
                    'codigo' => $producto->codigo ?? '',
                    'cantidad' => $cantidad,
                    'precio_unitario' => $precio,
                    'subtotal' => $total,
                    'fecha_agregado' => now()
                ];

                // Descontar stock
                $nuevoStock = $stockActual - $cantidad;
                $producto->update(['stock' => $nuevoStock]);
            }

            $descuento = (float) ($request->descuento ?? 0);
            $iva = ($subtotal - $descuento) * 0.19;
            $totalFinal = $subtotal - $descuento + $iva;

            // Crear la venta
            $venta = new Venta();
            $venta->numero_venta = $venta->generarNumeroConsecutivo();
            $venta->vendedor_id = $vendedor->_id;
            $venta->cliente_id = $request->cliente_id;
            $venta->productos = $productosData;
            $venta->subtotal = $subtotal;
            $venta->descuento = $descuento;
            $venta->iva = $iva;
            $venta->total_final = $totalFinal;
            $venta->metodo_pago = $request->metodo_pago;
            $venta->estado = 'completada';
            $venta->estado_pago = $request->metodo_pago === 'credito' ? 'pendiente' : 'pagado';
            $venta->notas = $request->notas;
            $venta->fecha_venta = now();

            if ($request->filled('direccion_entrega') || $request->filled('telefono_entrega')) {
                $venta->datos_entrega = [
                    'direccion' => $request->direccion_entrega,
                    'telefono' => $request->telefono_entrega
                ];
            }

            $venta->save();

            // Embeber datos
            $venta->asignarDatosEmbebidos();

            // Calcular comisiones
            $venta->calcularComisiones();

            return redirect()->route('vendedor.ventas.show', $venta->_id)
                           ->with('success', 'Venta registrada exitosamente. Número: ' . $venta->numero_venta);

        } catch (\Exception $e) {
            return redirect()->back()
                           ->with('error', 'Error al registrar la venta: ' . $e->getMessage())
                           ->withInput();
        }
    }

    /**
     * Exportar ventas a Excel/CSV
     */
    public function exportar(Request $request)
    {
        $vendedor = Auth::user();
        $query = Venta::where('vendedor_id', $vendedor->_id);

        if ($request->filled('fecha_desde')) {
            $query->where('created_at', '>=', new UTCDateTime(strtotime($request->fecha_desde) * 1000));
        }

        if ($request->filled('fecha_hasta')) {
            $query->where('created_at', '<=', new UTCDateTime(strtotime($request->fecha_hasta . ' 23:59:59') * 1000));
        }

        $ventas = $query->get();

        // Por ahora retornamos JSON, se puede implementar CSV/Excel
        return response()->json($ventas, 200, [], JSON_PRETTY_PRINT);
    }

    /**
     * Buscar cliente por nombre o email (AJAX)
     */
    public function buscarCliente(Request $request)
    {
        $termino = $request->get('q', '');

        // Intentar buscar en modelo Cliente primero
        $clientes = Cliente::where('activo', true)
                          ->where(function($query) use ($termino) {
                              $query->where('nombre', 'like', "%{$termino}%")
                                    ->orWhere('email', 'like', "%{$termino}%")
                                    ->orWhere('telefono', 'like', "%{$termino}%");
                          })
                          ->limit(10)
                          ->get();

        // Si no hay modelo Cliente, buscar en User
        if ($clientes->isEmpty()) {
            $clientes = User::where('rol', 'cliente')
                          ->where('activo', true)
                          ->where(function($query) use ($termino) {
                              $query->where('name', 'like', "%{$termino}%")
                                    ->orWhere('email', 'like', "%{$termino}%")
                                    ->orWhere('telefono', 'like', "%{$termino}%");
                          })
                          ->limit(10)
                          ->get();
        }

        return response()->json($clientes);
    }

    /**
     * Buscar producto por nombre o código (AJAX)
     */
    public function buscarProducto(Request $request)
    {
        $termino = $request->get('q', '');

        $productos = Producto::where('estado', 'activo')
                            ->where('stock', '>', 0)
                            ->where(function($query) use ($termino) {
                                $query->where('nombre', 'like', "%{$termino}%")
                                      ->orWhere('codigo', 'like', "%{$termino}%");
                            })
                            ->limit(10)
                            ->get();

        return response()->json($productos);
    }
}
