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
        try {
            $query = User::query();

            // Filtros con validación
            if ($request->filled('rol') && in_array($request->rol, ['administrador', 'lider', 'vendedor', 'cliente'])) {
                $query->where('rol', $request->rol);
            }

            if ($request->filled('activo') && in_array($request->activo, ['0', '1'])) {
                $query->where('activo', $request->activo == '1');
            }

            if ($request->filled('search')) {
                $search = trim($request->search);
                if (strlen($search) >= 2) { // Mínimo 2 caracteres para buscar
                    $query->where(function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%")
                          ->orWhere('apellidos', 'like', "%{$search}%")
                          ->orWhere('email', 'like', "%{$search}%")
                          ->orWhere('cedula', 'like', "%{$search}%");
                    });
                }
            }

            // Ordenamiento con opción de personalizar
            $orderBy = $request->get('order_by', 'created_at');
            $orderDirection = $request->get('order_direction', 'desc');

            $allowedOrderBy = ['name', 'created_at', 'rol', 'activo'];
            if (!in_array($orderBy, $allowedOrderBy)) {
                $orderBy = 'created_at';
            }

            if (!in_array($orderDirection, ['asc', 'desc'])) {
                $orderDirection = 'desc';
            }

            $usuarios = $query->orderBy($orderBy, $orderDirection)
                             ->paginate(20)
                             ->withQueryString();

            // Estadísticas en tiempo real sin cache
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

        } catch (\Exception $e) {
            \Log::error('Error en UserController@index: ' . $e->getMessage());
            return redirect()->back()
                           ->with('error', 'Error al cargar los usuarios. Inténtalo de nuevo.');
        }
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
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255|regex:/^[\pL\s\-]+$/u',
                'apellidos' => 'required|string|max:255|regex:/^[\pL\s\-]+$/u',
                'cedula' => 'required|string|max:20|unique:users|regex:/^[0-9]+$/',
                'email' => 'required|string|email:rfc,dns|max:255|unique:users',
                'password' => 'required|string|min:8|confirmed|regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$/',
                'telefono' => 'required|string|max:20|regex:/^[\+]?[0-9\s\-\(\)]+$/',
                'direccion' => 'nullable|string|max:500',
                'ciudad' => 'required|string|max:100',
                'departamento' => 'required|string|max:100',
                'fecha_nacimiento' => 'required|date|before:today|after:1900-01-01',
                'rol' => 'required|in:administrador,lider,vendedor,cliente',
                'activo' => 'boolean',
                'referido_por' => 'nullable|exists:users,_id',
                'meta_mensual' => 'nullable|numeric|min:0|max:999999',
            ], [
                'name.regex' => 'El nombre solo puede contener letras, espacios y guiones.',
                'apellidos.regex' => 'Los apellidos solo pueden contener letras, espacios y guiones.',
                'cedula.regex' => 'La cédula solo puede contener números.',
                'cedula.unique' => 'Ya existe un usuario con esta cédula.',
                'email.unique' => 'Ya existe un usuario con este email.',
                'password.regex' => 'La contraseña debe contener al menos una minúscula, una mayúscula y un número.',
                'telefono.regex' => 'El teléfono no tiene un formato válido.',
                'fecha_nacimiento.before' => 'La fecha de nacimiento debe ser anterior a hoy.',
                'fecha_nacimiento.after' => 'La fecha de nacimiento no puede ser anterior a 1900.',
            ]);

            // Verificar que el referidor puede tener referidos
            if ($validated['referido_por']) {
                $referidor = User::find($validated['referido_por']);
                if ($referidor && !in_array($referidor->rol, ['administrador', 'lider', 'vendedor'])) {
                    throw new \Exception('El usuario seleccionado no puede ser referidor.');
                }
            }

            $usuario = User::create([
                'name' => $validated['name'],
                'apellidos' => $validated['apellidos'],
                'cedula' => $validated['cedula'],
                'email' => strtolower($validated['email']),
                'password' => Hash::make($validated['password']),
                'telefono' => $validated['telefono'],
                'direccion' => $validated['direccion'],
                'ciudad' => $validated['ciudad'],
                'departamento' => $validated['departamento'],
                'fecha_nacimiento' => $validated['fecha_nacimiento'],
                'rol' => $validated['rol'],
                'activo' => $request->boolean('activo', true),
                'referido_por' => $validated['referido_por'],
                'meta_mensual' => $validated['meta_mensual'] ?? 0.00,
                'codigo_referido' => $this->generarCodigoReferido(),
                'comisiones_ganadas' => 0.00,
                'comisiones_disponibles' => 0.00,
                'ventas_mes_actual' => 0.00,
                'total_referidos' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Actualizar contador de referidos si tiene referidor
            if ($usuario->referido_por) {
                User::where('_id', $usuario->referido_por)
                    ->increment('total_referidos');
            }

            return redirect()->route('admin.users.index')
                            ->with('success', 'Usuario creado exitosamente.');

        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()
                           ->withErrors($e->validator)
                           ->withInput();
        } catch (\Exception $e) {
            \Log::error('Error al crear usuario: ' . $e->getMessage());
            return redirect()->back()
                           ->with('error', 'Error al crear el usuario: ' . $e->getMessage())
                           ->withInput();
        }
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
        $posibles_referidores = User::where('_id', '!=', $user->_id)
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
            'referido_por' => 'nullable|exists:users,_id',
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
                User::where('_id', $referidor_anterior)->decrement('total_referidos');
            }
            // Incrementar contador del nuevo referidor
            if ($request->referido_por) {
                User::where('_id', $request->referido_por)->increment('total_referidos');
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
            User::where('_id', $user->referido_por)->decrement('total_referidos');
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