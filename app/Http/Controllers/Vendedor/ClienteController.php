<?php

namespace App\Http\Controllers\Vendedor;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Pedido;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class ClienteController extends Controller
{
    public function index(Request $request)
    {
        $vendedor = Auth::user();

        // Obtener clientes que han comprado al vendedor
        $query = User::where('rol', 'cliente')
                    ->whereHas('pedidos', function($q) use ($vendedor) {
                        $q->where('vendedor_id', $vendedor->_id);
                    })
                    ->withCount(['pedidos' => function($q) use ($vendedor) {
                        $q->where('vendedor_id', $vendedor->_id);
                    }])
                    ->withSum(['pedidos as total_compras' => function($q) use ($vendedor) {
                        $q->where('vendedor_id', $vendedor->_id);
                    }], 'total_final');

        // Filtros
        if ($request->filled('buscar')) {
            $buscar = $request->buscar;
            $query->where(function($q) use ($buscar) {
                $q->where('name', 'like', '%' . $buscar . '%')
                  ->orWhere('email', 'like', '%' . $buscar . '%')
                  ->orWhere('telefono', 'like', '%' . $buscar . '%');
            });
        }

        if ($request->filled('estado')) {
            if ($request->estado === 'activo') {
                $query->whereHas('pedidos', function($q) use ($vendedor) {
                    $q->where('vendedor_id', $vendedor->_id)
                      ->where('created_at', '>=', Carbon::now()->subDays(30));
                });
            } elseif ($request->estado === 'inactivo') {
                $query->whereDoesntHave('pedidos', function($q) use ($vendedor) {
                    $q->where('vendedor_id', $vendedor->_id)
                      ->where('created_at', '>=', Carbon::now()->subDays(30));
                });
            }
        }

        $clientes = $query->orderBy('name')->paginate(15);

        // Estadísticas
        $stats = [
            'total_clientes' => User::where('rol', 'cliente')
                                   ->whereHas('pedidos', function($q) use ($vendedor) {
                                       $q->where('vendedor_id', $vendedor->_id);
                                   })->count(),
            'clientes_activos' => User::where('rol', 'cliente')
                                     ->whereHas('pedidos', function($q) use ($vendedor) {
                                         $q->where('vendedor_id', $vendedor->_id)
                                           ->where('created_at', '>=', Carbon::now()->subDays(30));
                                     })->count(),
            'nuevos_mes' => User::where('rol', 'cliente')
                               ->whereHas('pedidos', function($q) use ($vendedor) {
                                   $q->where('vendedor_id', $vendedor->_id)
                                     ->where('created_at', '>=', Carbon::now()->startOfMonth());
                               })->count(),
            'promedio_compra' => Pedido::where('vendedor_id', $vendedor->id)->avg('total_final') ?? 0
        ];

        return view('vendedor.clientes.index', compact('clientes', 'stats'));
    }

    public function show($id)
    {
        $vendedor = Auth::user();
        $cliente = User::where('rol', 'cliente')
                      ->whereHas('pedidos', function($q) use ($vendedor) {
                          $q->where('vendedor_id', $vendedor->_id);
                      })
                      ->findOrFail($id);

        // Pedidos del cliente con este vendedor
        $pedidos = Pedido::where('cliente_id', $cliente->id)
                        ->where('vendedor_id', $vendedor->_id)
                        ->orderBy('created_at', 'desc')
                        ->paginate(10);

        // Estadísticas del cliente
        $estadisticas = [
            'total_pedidos' => Pedido::where('cliente_id', $cliente->id)
                                    ->where('vendedor_id', $vendedor->_id)
                                    ->count(),
            'total_gastado' => Pedido::where('cliente_id', $cliente->id)
                                    ->where('vendedor_id', $vendedor->_id)
                                    ->sum('total_final'),
            'promedio_pedido' => Pedido::where('cliente_id', $cliente->id)
                                      ->where('vendedor_id', $vendedor->_id)
                                      ->avg('total_final') ?? 0,
            'ultimo_pedido' => Pedido::where('cliente_id', $cliente->id)
                                    ->where('vendedor_id', $vendedor->_id)
                                    ->orderBy('created_at', 'desc')
                                    ->first()?->created_at,
            'pedidos_mes' => Pedido::where('cliente_id', $cliente->id)
                                  ->where('vendedor_id', $vendedor->_id)
                                  ->where('created_at', '>=', Carbon::now()->startOfMonth())
                                  ->count()
        ];

        return view('vendedor.clientes.show', compact('cliente', 'pedidos', 'estadisticas'));
    }

    public function create()
    {
        return view('vendedor.clientes.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'telefono' => 'nullable|string|max:20',
            'direccion' => 'nullable|string|max:500',
            'ciudad' => 'nullable|string|max:100',
            'notas' => 'nullable|string|max:1000'
        ]);

        $vendedor = Auth::user();

        $cliente = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'telefono' => $request->telefono,
            'direccion' => $request->direccion,
            'ciudad' => $request->ciudad,
            'rol' => 'cliente',
            'password' => Hash::make('temporal123'), // Contraseña temporal
            'referido_por' => $vendedor->id // Marcar como referido del vendedor
        ]);

        // Agregar notas como información adicional del cliente
        if ($request->filled('notas')) {
            // Aquí podrías crear un modelo para notas de clientes si lo necesitas
            // Por ahora guardamos en un campo del usuario o creamos la relación
        }

        return redirect()->route('vendedor.clientes.show', $cliente->id)
                        ->with('success', 'Cliente creado exitosamente. Se envió un email con las credenciales de acceso.');
    }

    public function edit($id)
    {
        $vendedor = Auth::user();
        $cliente = User::where('rol', 'cliente')
                      ->whereHas('pedidos', function($q) use ($vendedor) {
                          $q->where('vendedor_id', $vendedor->_id);
                      })
                      ->findOrFail($id);

        return view('vendedor.clientes.edit', compact('cliente'));
    }

    public function update(Request $request, $id)
    {
        $vendedor = Auth::user();
        $cliente = User::where('rol', 'cliente')
                      ->whereHas('pedidos', function($q) use ($vendedor) {
                          $q->where('vendedor_id', $vendedor->_id);
                      })
                      ->findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $cliente->id,
            'telefono' => 'nullable|string|max:20',
            'direccion' => 'nullable|string|max:500',
            'ciudad' => 'nullable|string|max:100',
            'notas' => 'nullable|string|max:1000'
        ]);

        $cliente->update([
            'name' => $request->name,
            'email' => $request->email,
            'telefono' => $request->telefono,
            'direccion' => $request->direccion,
            'ciudad' => $request->ciudad
        ]);

        return redirect()->route('vendedor.clientes.show', $cliente->id)
                        ->with('success', 'Información del cliente actualizada exitosamente.');
    }

    public function seguimiento()
    {
        $vendedor = Auth::user();

        // Clientes que necesitan seguimiento (sin pedidos en los últimos 30 días)
        $clientesSeguimiento = User::where('rol', 'cliente')
                                  ->whereHas('pedidos', function($q) use ($vendedor) {
                                      $q->where('vendedor_id', $vendedor->_id);
                                  })
                                  ->whereDoesntHave('pedidos', function($q) use ($vendedor) {
                                      $q->where('vendedor_id', $vendedor->_id)
                                        ->where('created_at', '>=', Carbon::now()->subDays(30));
                                  })
                                  ->withCount(['pedidos' => function($q) use ($vendedor) {
                                      $q->where('vendedor_id', $vendedor->_id);
                                  }])
                                  ->withSum(['pedidos as total_compras' => function($q) use ($vendedor) {
                                      $q->where('vendedor_id', $vendedor->_id);
                                  }], 'total_final')
                                  ->with(['pedidos' => function($q) use ($vendedor) {
                                      $q->where('vendedor_id', $vendedor->_id)
                                        ->orderBy('created_at', 'desc')
                                        ->limit(1);
                                  }])
                                  ->orderBy('name')
                                  ->paginate(15);

        // Mejores clientes (más compras)
        $mejoresClientes = User::where('rol', 'cliente')
                              ->whereHas('pedidos', function($q) use ($vendedor) {
                                  $q->where('vendedor_id', $vendedor->_id);
                              })
                              ->withSum(['pedidos as total_compras' => function($q) use ($vendedor) {
                                  $q->where('vendedor_id', $vendedor->_id);
                              }], 'total_final')
                              ->orderBy('total_compras', 'desc')
                              ->limit(10)
                              ->get();

        return view('vendedor.clientes.seguimiento', compact('clientesSeguimiento', 'mejoresClientes'));
    }

    public function buscar(Request $request)
    {
        $vendedor = Auth::user();
        $buscar = $request->get('q');

        $clientes = User::where('rol', 'cliente')
                       ->where(function($q) use ($buscar) {
                           $q->where('name', 'like', '%' . $buscar . '%')
                             ->orWhere('email', 'like', '%' . $buscar . '%');
                       })
                       ->whereHas('pedidos', function($q) use ($vendedor) {
                           $q->where('vendedor_id', $vendedor->_id);
                       })
                       ->limit(10)
                       ->get();

        return response()->json($clientes);
    }

    public function exportar()
    {
        $vendedor = Auth::user();

        $clientes = User::where('rol', 'cliente')
                       ->whereHas('pedidos', function($q) use ($vendedor) {
                           $q->where('vendedor_id', $vendedor->_id);
                       })
                       ->withCount(['pedidos' => function($q) use ($vendedor) {
                           $q->where('vendedor_id', $vendedor->_id);
                       }])
                       ->withSum(['pedidos as total_compras' => function($q) use ($vendedor) {
                           $q->where('vendedor_id', $vendedor->_id);
                       }], 'total_final')
                       ->get();

        // Aquí implementarías la lógica de exportación
        return response()->json($clientes);
    }
}