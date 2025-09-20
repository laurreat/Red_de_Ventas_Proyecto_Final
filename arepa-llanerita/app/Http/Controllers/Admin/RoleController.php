<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\Permission;
use App\Models\User;
use Illuminate\Http\Request;

class RoleController extends Controller
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
        $query = Role::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('display_name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($request->filled('active')) {
            $query->where('active', $request->active == '1');
        }

        if ($request->filled('type')) {
            if ($request->type === 'system') {
                $query->where('system_role', true);
            } else {
                $query->where('system_role', false);
            }
        }

        $roles = $query->orderBy('name')->paginate(15);

        $stats = [
            'total' => Role::count(),
            'active' => Role::where('active', true)->count(),
            'system' => Role::where('system_role', true)->count(),
            'custom' => Role::where('system_role', false)->count()
        ];

        return view('admin.roles.index', compact('roles', 'stats'));
    }

    public function create()
    {
        $permissions = Permission::getSystemPermissions();
        $categories = Permission::getCategories();

        return view('admin.roles.create', compact('permissions', 'categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:roles,name',
            'display_name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'permissions' => 'array',
            'permissions.*' => 'string',
            'active' => 'boolean'
        ]);

        Role::create([
            'name' => $request->name,
            'display_name' => $request->display_name,
            'description' => $request->description,
            'permissions' => $request->permissions ?? [],
            'active' => $request->boolean('active', true),
            'system_role' => false
        ]);

        return redirect()->route('admin.roles.index')
                        ->with('success', 'Rol creado exitosamente.');
    }

    public function show(Role $role)
    {
        $role->load('users');
        $permissions = Permission::getSystemPermissions();
        $categories = Permission::getCategories();

        return view('admin.roles.show', compact('role', 'permissions', 'categories'));
    }

    public function edit(Role $role)
    {
        $permissions = Permission::getSystemPermissions();
        $categories = Permission::getCategories();

        return view('admin.roles.edit', compact('role', 'permissions', 'categories'));
    }

    public function update(Request $request, Role $role)
    {
        $rules = [
            'display_name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'permissions' => 'array',
            'permissions.*' => 'string',
            'active' => 'boolean'
        ];

        if (!$role->system_role) {
            $rules['name'] = 'required|string|max:255|unique:roles,name,' . $role->_id;
        }

        $request->validate($rules);

        $data = [
            'display_name' => $request->display_name,
            'description' => $request->description,
            'permissions' => $request->permissions ?? [],
            'active' => $request->boolean('active', true)
        ];

        if (!$role->system_role) {
            $data['name'] = $request->name;
        }

        $role->update($data);

        return redirect()->route('admin.roles.index')
                        ->with('success', 'Rol actualizado exitosamente.');
    }

    public function destroy(Role $role)
    {
        if ($role->system_role) {
            return redirect()->back()
                            ->with('error', 'No se puede eliminar un rol del sistema.');
        }

        if ($role->getUsersCount() > 0) {
            return redirect()->back()
                            ->with('error', 'No se puede eliminar un rol que tiene usuarios asignados.');
        }

        $role->delete();

        return redirect()->route('admin.roles.index')
                        ->with('success', 'Rol eliminado exitosamente.');
    }

    public function toggleActive(Role $role)
    {
        $role->update(['active' => !$role->active]);

        $estado = $role->active ? 'activado' : 'desactivado';

        return redirect()->back()
                        ->with('success', "Rol {$estado} exitosamente.");
    }

    public function permissions()
    {
        $permissions = Permission::getSystemPermissions();
        $categories = Permission::getCategories();

        $groupedPermissions = [];
        foreach ($permissions as $permission) {
            $groupedPermissions[$permission['category']][] = $permission;
        }

        return view('admin.roles.permissions', compact('permissions', 'categories', 'groupedPermissions'));
    }

    public function initializeSystemRoles()
    {
        $systemRoles = Role::getSystemRoles();

        foreach ($systemRoles as $roleData) {
            Role::updateOrCreate(
                ['name' => $roleData['name']],
                array_merge($roleData, [
                    'active' => true,
                    'system_role' => true
                ])
            );
        }

        return redirect()->route('admin.roles.index')
                        ->with('success', 'Roles del sistema inicializados exitosamente.');
    }

    public function assignUsers(Role $role)
    {
        $users = User::where('rol', '!=', 'administrador')
                    ->orderBy('name')
                    ->get();

        return view('admin.roles.assign-users', compact('role', 'users'));
    }

    public function updateUserRoles(Request $request, Role $role)
    {
        $request->validate([
            'users' => 'array',
            'users.*' => 'exists:users,_id'
        ]);

        $userIds = $request->users ?? [];

        User::where('role_id', $role->_id)->update(['role_id' => null]);

        if (!empty($userIds)) {
            User::whereIn('_id', $userIds)->update(['role_id' => $role->_id]);
        }

        return redirect()->route('admin.roles.show', $role)
                        ->with('success', 'Usuarios asignados al rol exitosamente.');
    }
}