<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\Permission;

class RolesSeeder extends Seeder
{
    public function run()
    {
        // Crear permisos del sistema
        $permissions = Permission::getSystemPermissions();
        foreach ($permissions as $permission) {
            Permission::updateOrCreate(
                ['name' => $permission['name']],
                $permission + ['active' => true]
            );
        }

        // Crear roles del sistema
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

        $this->command->info('Roles y permisos del sistema creados exitosamente.');
    }
}