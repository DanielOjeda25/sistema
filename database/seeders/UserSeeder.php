<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $jefe = User::firstOrCreate(
            ['email' => 'jefe@example.com'],
            ['name' => 'Roberto', 'apellido' => 'Acosta', 'password' => bcrypt('1234')]
        );
        $jefe->syncRoles(['Jefe']);

        $pm = User::firstOrCreate(
            ['email' => 'pm@example.com'],
            ['name' => 'Laura', 'apellido' => 'Mendez', 'password' => bcrypt('1234')]
        );
        $pm->syncRoles(['PM']);

        $po = User::firstOrCreate(
            ['email' => 'po@example.com'],
            ['name' => 'Diego', 'apellido' => 'Sosa', 'password' => bcrypt('1234')]
        );
        $po->syncRoles(['PO']);

        $programador = User::firstOrCreate(
            ['email' => 'dev@example.com'],
            ['name' => 'Sofia', 'apellido' => 'Ruiz', 'password' => bcrypt('1234')]
        );
        $programador->syncRoles(['Programador']);

        $cliente = User::firstOrCreate(
            ['email' => 'cliente@example.com'],
            ['name' => 'Juan', 'apellido' => 'Perez', 'password' => bcrypt('1234')]
        );
        $cliente->syncRoles(['Cliente']);
    }
}
