<?php

namespace Database\Seeders;

use App\Models\Cliente;
use App\Models\Proyecto;
use App\Models\User;
use Illuminate\Database\Seeder;

class ProyectoSeeder extends Seeder
{
    public function run(): void
    {
        $pmLaura = User::where('email', 'pm@example.com')->first();
        $pmJefe  = User::where('email', 'jefe@example.com')->first();

        $clienteLR    = Cliente::where('email', 'mariana@constructoraLR.com')->first();
        $clienteAbog  = Cliente::where('email', 'fede@gimenezabog.com.py')->first();
        $clienteCoop  = Cliente::where('email', 'patri@cooperativaUnion.com')->first();
        $clienteLucia = Cliente::where('email', 'lucia.f@gmail.com')->first();

        $proyectos = [
            [
                'nombre' => 'Sistema de obra L&R',
                'descripcion' => 'Plataforma interna para seguimiento de obra: avance, presupuesto, materiales.',
                'fecha_inicio' => '2026-03-01',
                'fecha_fin_estimada' => '2026-09-30',
                'estado' => 'en_progreso',
                'cliente_id' => $clienteLR->id,
                'pm_id' => $pmLaura->id,
            ],
            [
                'nombre' => 'Gestor de expedientes Gimenez',
                'descripcion' => 'CRM legal con flujo de aprobacion y notificaciones.',
                'fecha_inicio' => '2026-04-15',
                'fecha_fin_estimada' => '2026-08-15',
                'estado' => 'en_progreso',
                'cliente_id' => $clienteAbog->id,
                'pm_id' => $pmLaura->id,
            ],
            [
                'nombre' => 'Portal del socio — Cooperativa Union',
                'descripcion' => 'Sitio publico para que los socios consulten saldos y soliciten creditos.',
                'fecha_inicio' => '2026-01-10',
                'fecha_fin_estimada' => '2026-05-20',
                'estado' => 'completado',
                'cliente_id' => $clienteCoop->id,
                'pm_id' => $pmJefe->id,
            ],
            [
                'nombre' => 'Landing personal — Lucia',
                'descripcion' => 'Pagina de presentacion profesional con CV y portfolio.',
                'fecha_inicio' => '2026-05-20',
                'fecha_fin_estimada' => '2026-06-30',
                'estado' => 'pendiente',
                'cliente_id' => $clienteLucia->id,
                'pm_id' => $pmLaura->id,
            ],
        ];

        foreach ($proyectos as $datos) {
            Proyecto::firstOrCreate(['nombre' => $datos['nombre']], $datos);
        }
    }
}
