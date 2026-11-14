<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // Limpiar caché de permisos
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Permisos del sistema
        $permissions = [
            'users.manage' => 'Administrar usuarios y accesos',
            'system.maintenance' => 'Ejecutar migraciones y comandos del sistema',
            'evaluations.manage' => 'Crear y gestionar evaluaciones oftalmológicas',
            'evaluations.view' => 'Consultar evaluaciones oftalmológicas',
            'research.view' => 'Acceso a observatorio de datos NASA y motor SANS',
            'simulations.run' => 'Ejecutar simuladores de misiones espaciales',
        ];

        foreach ($permissions as $permission => $description) {
            Permission::findOrCreate($permission, 'web');
        }

        // Roles espaciales y base
        $roles = [
            'super-admin' => [
                'users.manage',
                'system.maintenance',
                'evaluations.manage',
                'evaluations.view',
                'research.view',
                'simulations.run',
            ],
            'admin' => [
                'users.manage',
                'system.maintenance',
                'evaluations.view',
                'research.view',
            ],
            'flight-surgeon' => [
                'evaluations.manage',
                'evaluations.view',
                'research.view',
                'simulations.run',
            ],
            'researcher' => [
                'evaluations.view',
                'research.view',
                'simulations.run',
            ],
            'astronaut' => [
                'evaluations.view',
            ],
            'user' => [
                'evaluations.view',
            ],
        ];

        foreach ($roles as $roleName => $rolePermissions) {
            $role = Role::findOrCreate($roleName, 'web');
            $role->syncPermissions($rolePermissions);
        }
    }
}

