<?php

namespace Database\Seeders;

use App\Models\Cliente;
use Illuminate\Database\Seeder;

class ClienteSeeder extends Seeder
{
    public function run(): void
    {
        $clientes = [
            [
                'nombre' => 'Mariana',
                'apellido' => 'Lopez',
                'email' => 'mariana@constructoraLR.com',
                'telefono' => '0981-111-222',
                'empresa' => 'Constructora L&R',
                'estado' => 'activo',
            ],
            [
                'nombre' => 'Federico',
                'apellido' => 'Gimenez',
                'email' => 'fede@gimenezabog.com.py',
                'telefono' => '0982-333-444',
                'empresa' => 'Gimenez Abogados',
                'estado' => 'activo',
            ],
            [
                'nombre' => 'Patricia',
                'apellido' => 'Martinez',
                'email' => 'patri@cooperativaUnion.com',
                'telefono' => '0983-555-666',
                'empresa' => 'Cooperativa Union',
                'estado' => 'activo',
            ],
            [
                'nombre' => 'Hernan',
                'apellido' => 'Vera',
                'email' => 'hvera@autopartesvera.com',
                'telefono' => null,
                'empresa' => 'Autopartes Vera SA',
                'estado' => 'inactivo',
            ],
            [
                'nombre' => 'Lucia',
                'apellido' => 'Fernandez',
                'email' => 'lucia.f@gmail.com',
                'telefono' => '0984-777-888',
                'empresa' => null,
                'estado' => 'activo',
            ],
        ];

        foreach ($clientes as $datos) {
            Cliente::firstOrCreate(['email' => $datos['email']], $datos);
        }
    }
}
