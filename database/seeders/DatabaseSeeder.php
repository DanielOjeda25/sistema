<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            UserSeeder::class,
            ClienteSeeder::class,
            ProyectoSeeder::class,
            HitoSeeder::class,
            SolicitudCambioSeeder::class,
            TareaSeeder::class,
            EntregableIASeeder::class,
            FacturaSeeder::class,
        ]);
    }
}
