<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class Permission extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'permissions';

    protected $fillable = [
        'name',
        'display_name',
        'description',
        'category',
        'active'
    ];

    protected $casts = [
        'active' => 'boolean'
    ];

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('active', true);
    }

    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    // Métodos estáticos para obtener permisos del sistema
    public static function getSystemPermissions(): array
    {
        return [
            // Administración
            'admin.dashboard' => [
                'name' => 'admin.dashboard',
                'display_name' => 'Dashboard Administrativo',
                'description' => 'Acceso al dashboard administrativo',
                'category' => 'admin'
            ],
            'admin.users.view' => [
                'name' => 'admin.users.view',
                'display_name' => 'Ver Usuarios',
                'description' => 'Ver listado de usuarios',
                'category' => 'admin'
            ],
            'admin.users.create' => [
                'name' => 'admin.users.create',
                'display_name' => 'Crear Usuarios',
                'description' => 'Crear nuevos usuarios',
                'category' => 'admin'
            ],
            'admin.users.edit' => [
                'name' => 'admin.users.edit',
                'display_name' => 'Editar Usuarios',
                'description' => 'Editar usuarios existentes',
                'category' => 'admin'
            ],
            'admin.users.delete' => [
                'name' => 'admin.users.delete',
                'display_name' => 'Eliminar Usuarios',
                'description' => 'Eliminar usuarios',
                'category' => 'admin'
            ],

            // Productos
            'admin.products.view' => [
                'name' => 'admin.products.view',
                'display_name' => 'Ver Productos (Admin)',
                'description' => 'Ver gestión de productos en admin',
                'category' => 'products'
            ],
            'admin.products.create' => [
                'name' => 'admin.products.create',
                'display_name' => 'Crear Productos',
                'description' => 'Crear nuevos productos',
                'category' => 'products'
            ],
            'admin.products.edit' => [
                'name' => 'admin.products.edit',
                'display_name' => 'Editar Productos',
                'description' => 'Editar productos existentes',
                'category' => 'products'
            ],
            'admin.products.delete' => [
                'name' => 'admin.products.delete',
                'display_name' => 'Eliminar Productos',
                'description' => 'Eliminar productos',
                'category' => 'products'
            ],

            // Pedidos
            'admin.orders.view' => [
                'name' => 'admin.orders.view',
                'display_name' => 'Ver Pedidos (Admin)',
                'description' => 'Ver gestión de pedidos en admin',
                'category' => 'orders'
            ],
            'admin.orders.create' => [
                'name' => 'admin.orders.create',
                'display_name' => 'Crear Pedidos (Admin)',
                'description' => 'Crear pedidos desde admin',
                'category' => 'orders'
            ],
            'admin.orders.edit' => [
                'name' => 'admin.orders.edit',
                'display_name' => 'Editar Pedidos',
                'description' => 'Editar pedidos existentes',
                'category' => 'orders'
            ],
            'admin.orders.delete' => [
                'name' => 'admin.orders.delete',
                'display_name' => 'Eliminar Pedidos',
                'description' => 'Eliminar pedidos',
                'category' => 'orders'
            ],

            // Roles y Permisos
            'admin.roles.view' => [
                'name' => 'admin.roles.view',
                'display_name' => 'Ver Roles',
                'description' => 'Ver gestión de roles',
                'category' => 'roles'
            ],
            'admin.roles.create' => [
                'name' => 'admin.roles.create',
                'display_name' => 'Crear Roles',
                'description' => 'Crear nuevos roles',
                'category' => 'roles'
            ],
            'admin.roles.edit' => [
                'name' => 'admin.roles.edit',
                'display_name' => 'Editar Roles',
                'description' => 'Editar roles existentes',
                'category' => 'roles'
            ],
            'admin.roles.delete' => [
                'name' => 'admin.roles.delete',
                'display_name' => 'Eliminar Roles',
                'description' => 'Eliminar roles personalizados',
                'category' => 'roles'
            ],

            // Reportes
            'admin.reports.view' => [
                'name' => 'admin.reports.view',
                'display_name' => 'Ver Reportes',
                'description' => 'Acceso a reportes administrativos',
                'category' => 'reports'
            ],

            // Configuración
            'admin.settings.view' => [
                'name' => 'admin.settings.view',
                'display_name' => 'Ver Configuración',
                'description' => 'Ver configuración del sistema',
                'category' => 'settings'
            ],
            'admin.settings.edit' => [
                'name' => 'admin.settings.edit',
                'display_name' => 'Editar Configuración',
                'description' => 'Editar configuración del sistema',
                'category' => 'settings'
            ],

            // Dashboard General
            'dashboard.view' => [
                'name' => 'dashboard.view',
                'display_name' => 'Dashboard General',
                'description' => 'Acceso al dashboard general',
                'category' => 'general'
            ],

            // Pedidos Generales
            'orders.view' => [
                'name' => 'orders.view',
                'display_name' => 'Ver Pedidos',
                'description' => 'Ver pedidos propios',
                'category' => 'orders'
            ],
            'orders.create' => [
                'name' => 'orders.create',
                'display_name' => 'Crear Pedidos',
                'description' => 'Crear nuevos pedidos',
                'category' => 'orders'
            ],
            'orders.edit' => [
                'name' => 'orders.edit',
                'display_name' => 'Editar Pedidos Propios',
                'description' => 'Editar pedidos propios',
                'category' => 'orders'
            ],

            // Productos Generales
            'products.view' => [
                'name' => 'products.view',
                'display_name' => 'Ver Catálogo',
                'description' => 'Ver catálogo de productos',
                'category' => 'products'
            ],

            // Clientes
            'clients.view' => [
                'name' => 'clients.view',
                'display_name' => 'Ver Clientes',
                'description' => 'Ver listado de clientes',
                'category' => 'clients'
            ],
            'clients.create' => [
                'name' => 'clients.create',
                'display_name' => 'Crear Clientes',
                'description' => 'Registrar nuevos clientes',
                'category' => 'clients'
            ],

            // Equipo
            'team.view' => [
                'name' => 'team.view',
                'display_name' => 'Ver Equipo',
                'description' => 'Ver miembros del equipo',
                'category' => 'team'
            ],
            'team.reports' => [
                'name' => 'team.reports',
                'display_name' => 'Reportes de Equipo',
                'description' => 'Ver reportes del equipo',
                'category' => 'team'
            ],

            // Comisiones
            'commissions.view' => [
                'name' => 'commissions.view',
                'display_name' => 'Ver Comisiones',
                'description' => 'Ver comisiones propias',
                'category' => 'commissions'
            ],

            // Catálogo
            'catalog.view' => [
                'name' => 'catalog.view',
                'display_name' => 'Ver Catálogo',
                'description' => 'Ver catálogo de productos',
                'category' => 'catalog'
            ],

            // Perfil
            'profile.edit' => [
                'name' => 'profile.edit',
                'display_name' => 'Editar Perfil',
                'description' => 'Editar perfil personal',
                'category' => 'profile'
            ]
        ];
    }

    public static function getCategories(): array
    {
        return [
            'admin' => 'Administración',
            'products' => 'Productos',
            'orders' => 'Pedidos',
            'roles' => 'Roles y Permisos',
            'reports' => 'Reportes',
            'settings' => 'Configuración',
            'general' => 'General',
            'clients' => 'Clientes',
            'team' => 'Equipo',
            'commissions' => 'Comisiones',
            'catalog' => 'Catálogo',
            'profile' => 'Perfil'
        ];
    }
}