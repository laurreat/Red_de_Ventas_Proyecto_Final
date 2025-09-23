<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Role;
use App\Models\Permission;
use App\Models\Configuracion;
use App\Models\Categoria;
use Illuminate\Support\Facades\Hash;

class SeedMongoData extends Command
{
    protected $signature = 'mongo:seed {--force : Force seeding even if data exists}';
    protected $description = 'Seed MongoDB with initial data';

    public function handle()
    {
        $force = $this->option('force');

        $this->info('Seeding MongoDB with initial data...');

        try {
            $this->seedRoles($force);
            $this->seedPermissions($force);
            $this->seedAdminUser($force);
            $this->seedCategorias($force);
            $this->seedConfiguraciones($force);

            $this->info('✅ Database seeded successfully!');
        } catch (\Exception $e) {
            $this->error('❌ Error seeding database: ' . $e->getMessage());
            return 1;
        }

        return 0;
    }

    private function seedRoles($force = false)
    {
        $roles = [
            ['name' => 'administrador', 'description' => 'Administrador del sistema'],
            ['name' => 'lider', 'description' => 'Líder de equipo de ventas'],
            ['name' => 'vendedor', 'description' => 'Vendedor individual'],
            ['name' => 'cliente', 'description' => 'Cliente del sistema']
        ];

        foreach ($roles as $roleData) {
            if ($force || !Role::where('name', $roleData['name'])->exists()) {
                Role::updateOrCreate(
                    ['name' => $roleData['name']],
                    $roleData
                );
                $this->info("✅ Role '{$roleData['name']}' created/updated");
            }
        }
    }

    private function seedPermissions($force = false)
    {
        $permissions = [
            // Usuarios
            ['name' => 'users.view', 'description' => 'Ver usuarios'],
            ['name' => 'users.create', 'description' => 'Crear usuarios'],
            ['name' => 'users.edit', 'description' => 'Editar usuarios'],
            ['name' => 'users.delete', 'description' => 'Eliminar usuarios'],

            // Productos
            ['name' => 'products.view', 'description' => 'Ver productos'],
            ['name' => 'products.create', 'description' => 'Crear productos'],
            ['name' => 'products.edit', 'description' => 'Editar productos'],
            ['name' => 'products.delete', 'description' => 'Eliminar productos'],

            // Pedidos
            ['name' => 'orders.view', 'description' => 'Ver pedidos'],
            ['name' => 'orders.create', 'description' => 'Crear pedidos'],
            ['name' => 'orders.edit', 'description' => 'Editar pedidos'],
            ['name' => 'orders.delete', 'description' => 'Eliminar pedidos'],

            // Comisiones
            ['name' => 'commissions.view', 'description' => 'Ver comisiones'],
            ['name' => 'commissions.calculate', 'description' => 'Calcular comisiones'],
            ['name' => 'commissions.export', 'description' => 'Exportar comisiones'],

            // Reportes
            ['name' => 'reports.view', 'description' => 'Ver reportes'],
            ['name' => 'reports.export', 'description' => 'Exportar reportes'],

            // Configuración
            ['name' => 'settings.view', 'description' => 'Ver configuración'],
            ['name' => 'settings.edit', 'description' => 'Editar configuración'],

            // Referidos
            ['name' => 'referrals.view', 'description' => 'Ver referidos'],
            ['name' => 'referrals.manage', 'description' => 'Gestionar referidos']
        ];

        foreach ($permissions as $permissionData) {
            if ($force || !Permission::where('name', $permissionData['name'])->exists()) {
                Permission::updateOrCreate(
                    ['name' => $permissionData['name']],
                    $permissionData
                );
            }
        }

        $this->info("✅ Permissions created/updated");
    }

    private function seedAdminUser($force = false)
    {
        $email = 'admin@arepallanerita.com';

        if ($force || !User::where('email', $email)->exists()) {
            $admin = User::updateOrCreate(
                ['email' => $email],
                [
                    'name' => 'Administrador Principal',
                    'email' => $email,
                    'password' => Hash::make('admin123456'),
                    'rol' => 'administrador',
                    'activo' => true,
                    'telefono' => '+58 424 000 0000',
                    'email_verified_at' => now(),
                    'comisiones_disponibles' => 0,
                    'total_referidos' => 0,
                    'ventas_totales' => 0,
                    'created_at' => now(),
                    'updated_at' => now()
                ]
            );

            $this->info("✅ Admin user created: {$email} / admin123456");
        }
    }

    private function seedCategorias($force = false)
    {
        $categorias = [
            ['nombre' => 'Arepas Tradicionales', 'descripcion' => 'Arepas clásicas venezolanas'],
            ['nombre' => 'Arepas Especiales', 'descripcion' => 'Arepas con ingredientes especiales'],
            ['nombre' => 'Bebidas', 'descripcion' => 'Bebidas para acompañar'],
            ['nombre' => 'Postres', 'descripcion' => 'Postres tradicionales'],
            ['nombre' => 'Complementos', 'descripcion' => 'Salsas y complementos']
        ];

        foreach ($categorias as $categoriaData) {
            if ($force || !Categoria::where('nombre', $categoriaData['nombre'])->exists()) {
                Categoria::updateOrCreate(
                    ['nombre' => $categoriaData['nombre']],
                    array_merge($categoriaData, [
                        'activo' => true,
                        'created_at' => now(),
                        'updated_at' => now()
                    ])
                );
            }
        }

        $this->info("✅ Categories created/updated");
    }

    private function seedConfiguraciones($force = false)
    {
        $configuraciones = [
            [
                'clave' => 'comision_vendedor',
                'valor' => '10',
                'tipo' => 'porcentaje',
                'descripcion' => 'Porcentaje de comisión para vendedores',
                'categoria' => 'ventas'
            ],
            [
                'clave' => 'comision_lider',
                'valor' => '5',
                'tipo' => 'porcentaje',
                'descripcion' => 'Porcentaje de comisión adicional para líderes',
                'categoria' => 'ventas'
            ],
            [
                'clave' => 'moneda',
                'valor' => 'VES',
                'tipo' => 'texto',
                'descripcion' => 'Moneda principal del sistema',
                'categoria' => 'general'
            ],
            [
                'clave' => 'tiempo_entrega_min',
                'valor' => '30',
                'tipo' => 'numero',
                'descripcion' => 'Tiempo mínimo de entrega en minutos',
                'categoria' => 'delivery'
            ],
            [
                'clave' => 'tiempo_entrega_max',
                'valor' => '60',
                'tipo' => 'numero',
                'descripcion' => 'Tiempo máximo de entrega en minutos',
                'categoria' => 'delivery'
            ],
            [
                'clave' => 'referidos_activos',
                'valor' => 'true',
                'tipo' => 'booleano',
                'descripcion' => 'Sistema de referidos activo',
                'categoria' => 'marketing'
            ],
            [
                'clave' => 'bono_referido',
                'valor' => '50000',
                'tipo' => 'numero',
                'descripcion' => 'Bono por referir nuevos vendedores',
                'categoria' => 'marketing'
            ]
        ];

        foreach ($configuraciones as $configData) {
            if ($force || !Configuracion::where('clave', $configData['clave'])->exists()) {
                Configuracion::updateOrCreate(
                    ['clave' => $configData['clave']],
                    array_merge($configData, [
                        'activo' => true,
                        'created_at' => now(),
                        'updated_at' => now()
                    ])
                );
            }
        }

        $this->info("✅ System configurations created/updated");
    }
}