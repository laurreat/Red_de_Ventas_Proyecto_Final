<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
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

    /**
     * Mostrar lista de usuarios
     */
    public function index(Request $request)
    {
        $query = User::query();

        // Filtros
        if ($request->filled('rol')) {
            $query->where('rol', $request->rol);
        }

        if ($request->filled('activo')) {
            $query->where('activo', $request->activo == '1');
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('apellidos', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('cedula', 'like', "%{$search}%");
            });
        }

        $usuarios = $query->orderBy('created_at', 'desc')
                         ->paginate(20);

        $stats = [
            'total' => User::count(),
            'administradores' => User::where('rol', 'administrador')->count(),
            'lideres' => User::where('rol', 'lider')->count(),
            'vendedores' => User::where('rol', 'vendedor')->count(),
            'clientes' => User::where('rol', 'cliente')->count(),
            'activos' => User::where('activo', true)->count(),
            'inactivos' => User::where('activo', false)->count(),
        ];

        return view('admin.users.index', compact('usuarios', 'stats'));
    }

    /**
     * Mostrar formulario de creación
     */
    public function create()
    {
        return view('admin.users.create');
    }

    /**
     * Crear nuevo usuario
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'apellidos' => 'required|string|max:255',
            'cedula' => 'required|string|max:20|unique:users',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'telefono' => 'required|string|max:20',
            'direccion' => 'nullable|string',
            'ciudad' => 'required|string|max:100',
            'departamento' => 'required|string|max:100',
            'fecha_nacimiento' => 'required|date|before:today',
            'rol' => 'required|in:administrador,lider,vendedor,cliente',
            'activo' => 'boolean',
            'referido_por' => 'nullable|exists:users,id',
            'meta_mensual' => 'nullable|numeric|min:0',
        ]);

        $usuario = User::create([
            'name' => $request->name,
            'apellidos' => $request->apellidos,
            'cedula' => $request->cedula,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'telefono' => $request->telefono,
            'direccion' => $request->direccion,
            'ciudad' => $request->ciudad,
            'departamento' => $request->departamento,
            'fecha_nacimiento' => $request->fecha_nacimiento,
            'rol' => $request->rol,
            'activo' => $request->boolean('activo', true),
            'referido_por' => $request->referido_por,
            'meta_mensual' => $request->meta_mensual ?? 0.00,
            'codigo_referido' => $this->generarCodigoReferido(),
            'comisiones_ganadas' => 0.00,
            'comisiones_disponibles' => 0.00,
            'ventas_mes_actual' => 0.00,
            'total_referidos' => 0,
        ]);

        // Actualizar contador de referidos si tiene referidor
        if ($usuario->referido_por) {
            User::where('id', $usuario->referido_por)
                ->increment('total_referidos');
        }

        return redirect()->route('admin.users.index')
                        ->with('success', 'Usuario creado exitosamente.');
    }

    /**
     * Ver detalles del usuario
     */
    public function show(User $user)
    {
        $user->load(['referidor', 'referidos', 'pedidosComoCliente', 'pedidosComoVendedor', 'comisiones']);

        $stats = [
            'pedidos_como_cliente' => $user->pedidosComoCliente()->count(),
            'pedidos_como_vendedor' => $user->pedidosComoVendedor()->count(),
            'total_gastado' => $user->pedidosComoCliente()->sum('total_final'),
            'total_vendido' => $user->pedidosComoVendedor()->sum('total_final'),
            'comisiones_totales' => $user->comisiones()->sum('monto'),
        ];

        return view('admin.users.show', compact('user', 'stats'));
    }

    /**
     * Mostrar formulario de edición
     */
    public function edit(User $user)
    {
        $posibles_referidores = User::where('id', '!=', $user->id)
                                   ->whereIn('rol', ['administrador', 'lider', 'vendedor'])
                                   ->get();

        return view('admin.users.edit', compact('user', 'posibles_referidores'));
    }

    /**
     * Actualizar usuario
     */
    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'apellidos' => 'required|string|max:255',
            'cedula' => ['required', 'string', 'max:20', Rule::unique('users')->ignore($user)],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user)],
            'password' => 'nullable|string|min:8|confirmed',
            'telefono' => 'required|string|max:20',
            'direccion' => 'nullable|string',
            'ciudad' => 'required|string|max:100',
            'departamento' => 'required|string|max:100',
            'fecha_nacimiento' => 'required|date|before:today',
            'rol' => 'required|in:administrador,lider,vendedor,cliente',
            'activo' => 'boolean',
            'referido_por' => 'nullable|exists:users,id',
            'meta_mensual' => 'nullable|numeric|min:0',
            'ventas_mes_actual' => 'nullable|numeric|min:0',
            'comisiones_ganadas' => 'nullable|numeric|min:0',
            'comisiones_disponibles' => 'nullable|numeric|min:0',
        ]);

        $data = $request->except(['password', 'password_confirmation']);

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        // Actualizar referidos si cambió el referidor
        $referidor_anterior = $user->referido_por;
        if ($referidor_anterior != $request->referido_por) {
            // Decrementar contador del referidor anterior
            if ($referidor_anterior) {
                User::where('id', $referidor_anterior)->decrement('total_referidos');
            }
            // Incrementar contador del nuevo referidor
            if ($request->referido_por) {
                User::where('id', $request->referido_por)->increment('total_referidos');
            }
        }

        $user->update($data);

        return redirect()->route('admin.users.index')
                        ->with('success', 'Usuario actualizado exitosamente.');
    }

    /**
     * Activar/Desactivar usuario
     */
    public function toggleActive(User $user)
    {
        $user->update(['activo' => !$user->activo]);

        $estado = $user->activo ? 'activado' : 'desactivado';

        return redirect()->back()
                        ->with('success', "Usuario {$estado} exitosamente.");
    }

    /**
     * Eliminar usuario (soft delete)
     */
    public function destroy(User $user)
    {
        // Prevenir eliminación del admin principal
        if ($user->esAdmin() && User::where('rol', 'administrador')->count() <= 1) {
            return redirect()->back()
                            ->with('error', 'No se puede eliminar el último administrador.');
        }

        // Actualizar contador de referidos del referidor
        if ($user->referido_por) {
            User::where('id', $user->referido_por)->decrement('total_referidos');
        }

        $user->delete();

        return redirect()->route('admin.users.index')
                        ->with('success', 'Usuario eliminado exitosamente.');
    }

    /**
     * Generar código de referido único
     */
    private function generarCodigoReferido(): string
    {
        do {
            $codigo = 'REF' . str_pad(random_int(1, 9999), 4, '0', STR_PAD_LEFT);
        } while (User::where('codigo_referido', $codigo)->exists());

        return $codigo;
    }
}