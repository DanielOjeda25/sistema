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
        $pmJefe = User::where('email', 'jefe@example.com')->first();

        $clienteLR = Cliente::where('email', 'mariana@constructoraLR.com')->first();
        $clienteAbog = Cliente::where('email', 'fede@gimenezabog.com.py')->first();
        $clienteCoop = Cliente::where('email', 'patri@cooperativaUnion.com')->first();
        $clienteLucia = Cliente::where('email', 'lucia.f@gmail.com')->first();

        // Fechas relativas a hoy: la demo siempre muestra un calendario coherente
        // sin importar cuando se siembre.
        $proyectos = [
            [
                // Proyecto principal de la demo: activo, con sprint en curso.
                'nombre' => 'Sistema de obra L&R',
                'descripcion' => 'Plataforma interna para seguimiento de obra: avance, presupuesto, materiales.',
                'fecha_inicio' => now()->subMonths(5)->toDateString(),
                'fecha_fin_estimada' => now()->addMonths(2)->toDateString(),
                'estado' => 'en_progreso',
                'cliente_id' => $clienteLR->id,
                'pm_id' => $pmLaura->id,
            ],
            [
                'nombre' => 'Gestor de expedientes Gimenez',
                'descripcion' => 'CRM legal con flujo de aprobacion y notificaciones.',
                'fecha_inicio' => now()->subMonths(3)->toDateString(),
                'fecha_fin_estimada' => now()->addMonths(2)->toDateString(),
                'estado' => 'en_progreso',
                'cliente_id' => $clienteAbog->id,
                'pm_id' => $pmLaura->id,
            ],
            [
                'nombre' => 'Portal del socio — Cooperativa Union',
                'descripcion' => 'Sitio publico para que los socios consulten saldos y soliciten creditos.',
                'fecha_inicio' => now()->subMonths(9)->toDateString(),
                'fecha_fin_estimada' => now()->subMonths(4)->toDateString(),
                'estado' => 'completado',
                'cliente_id' => $clienteCoop->id,
                'pm_id' => $pmJefe->id,
            ],
            [
                'nombre' => 'Landing personal — Lucia',
                'descripcion' => 'Pagina de presentacion profesional con CV y portfolio.',
                'fecha_inicio' => now()->addWeek()->toDateString(),
                'fecha_fin_estimada' => now()->addMonths(2)->toDateString(),
                'estado' => 'pendiente',
                'cliente_id' => $clienteLucia->id,
                'pm_id' => $pmLaura->id,
            ],
        ];

        foreach ($proyectos as $datos) {
            Proyecto::updateOrCreate(['nombre' => $datos['nombre']], $datos);
        }
    }
}
