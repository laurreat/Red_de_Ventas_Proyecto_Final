<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class Role extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'roles';

    protected $fillable = [
        'name',
        'display_name',
        'description',
        'permissions',
        'active',
        'system_role'
    ];

    protected $casts = [
        'permissions' => 'array',
        'active' => 'boolean',
        'system_role' => 'boolean'
    ];

    // Relaciones
    public function users()
    {
        return $this->hasMany(User::class, 'role_id');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('active', true);
    }

    public function scopeSystem($query)
    {
        return $query->where('system_role', true);
    }

    public function scopeCustom($query)
    {
        return $query->where('system_role', false);
    }

    // Métodos auxiliares
    public function hasPermission($permission): bool
    {
        if (!$this->active) {
            return false;
        }

        $permissions = $this->permissions ?? [];
        return in_array($permission, $permissions);
    }

    public function addPermission($permission): bool
    {
        $permissions = $this->permissions ?? [];

        if (!in_array($permission, $permissions)) {
            $permissions[] = $permission;
            $this->permissions = $permissions;
            return $this->save();
        }

        return false;
    }

    public function removePermission($permission): bool
    {
        $permissions = $this->permissions ?? [];

        if (($key = array_search($permission, $permissions)) !== false) {
            unset($permissions[$key]);
            $this->permissions = array_values($permissions);
            return $this->save();
        }

        return false;
    }

    public function syncPermissions(array $permissions): bool
    {
        $this->permissions = $permissions;
        return $this->save();
    }

    public function getUsersCount(): int
    {
        return $this->users()->count();
    }

    public function canBeDeleted(): bool
    {
        return !$this->system_role && $this->getUsersCount() === 0;
    }

    // Métodos estáticos para roles del sistema
    public static function getSystemRoles(): array
    {
        return [
            'administrador' => [
                'name' => 'administrador',
                'display_name' => 'Administrador',
                'description' => 'Acceso completo al sistema',
                'permissions' => [
                    'admin.dashboard',
                    'admin.users.view',
                    'admin.users.create',
                    'admin.users.edit',
                    'admin.users.delete',
                    'admin.products.view',
                    'admin.products.create',
                    'admin.products.edit',
                    'admin.products.delete',
                    'admin.orders.view',
                    'admin.orders.create',
                    'admin.orders.edit',
                    'admin.orders.delete',
                    'admin.roles.view',
                    'admin.roles.create',
                    'admin.roles.edit',
                    'admin.roles.delete',
                    'admin.reports.view',
                    'admin.settings.view',
                    'admin.settings.edit'
                ]
            ],
            'lider' => [
                'name' => 'lider',
                'display_name' => 'Líder',
                'description' => 'Gestión de equipos y ventas',
                'permissions' => [
                    'dashboard.view',
                    'orders.view',
                    'orders.create',
                    'orders.edit',
                    'products.view',
                    'team.view',
                    'team.reports',
                    'commissions.view'
                ]
            ],
            'vendedor' => [
                'name' => 'vendedor',
                'display_name' => 'Vendedor',
                'description' => 'Gestión de ventas y clientes',
                'permissions' => [
                    'dashboard.view',
                    'orders.view',
                    'orders.create',
                    'products.view',
                    'clients.view',
                    'clients.create',
                    'commissions.view'
                ]
            ],
            'cliente' => [
                'name' => 'cliente',
                'display_name' => 'Cliente',
                'description' => 'Acceso a catálogo y pedidos',
                'permissions' => [
                    'catalog.view',
                    'orders.view',
                    'profile.edit'
                ]
            ]
        ];
    }
}