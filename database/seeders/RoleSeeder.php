<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $permisoEditarRoles = Permission::firstOrCreate(['name' => 'editar_roles']);

        $jefe        = Role::firstOrCreate(['name' => 'Jefe']);
        $pm          = Role::firstOrCreate(['name' => 'PM']);
        $po          = Role::firstOrCreate(['name' => 'PO']);
        $programador = Role::firstOrCreate(['name' => 'Programador']);
        $cliente     = Role::firstOrCreate(['name' => 'Cliente']);

        $jefe->givePermissionTo($permisoEditarRoles);
    }
}
