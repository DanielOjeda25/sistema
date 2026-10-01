<?php

namespace Database\Seeders;

use App\Models\ActualizacionProyecto;
use App\Models\Proyecto;
use App\Models\User;
use Illuminate\Database\Seeder;

class ActualizacionProyectoSeeder extends Seeder
{
    public function run(): void
    {
        $obraLR = Proyecto::where('nombre', 'Sistema de obra L&R')->first();
        $gestor = Proyecto::where('nombre', 'Gestor de expedientes Gimenez')->first();
        $portal = Proyecto::where('nombre', 'Portal del socio — Cooperativa Union')->first();
        $pm = User::where('email', 'pm@example.com')->first();
        $jefe = User::where('email', 'jefe@example.com')->first();

        // Feed de la ficha del proyecto: mezcla de avances visibles para el
        // cliente y notas internas.
        $actualizaciones = [
            [
                'proyecto_id' => $obraLR->id,
                'creado_por' => $pm->id,
                'titulo' => 'Reunion de kick-off realizada',
                'descripcion' => 'Se firmo el contrato y se acordaron los hitos del proyecto con el equipo de obra.',
                'tipo' => 'hito',
                'fecha' => now()->subMonths(5)->toDateString(),
                'visible_cliente' => true,
            ],
            [
                'proyecto_id' => $obraLR->id,
                'creado_por' => $pm->id,
                'titulo' => 'Listado de obras en preproduccion',
                'descripcion' => 'El modulo de listado ya se prueba con datos reales del cliente.',
                'tipo' => 'avance',
                'fecha' => now()->subWeeks(2)->toDateString(),
                'visible_cliente' => true,
            ],
            [
                'proyecto_id' => $obraLR->id,
                'creado_por' => $pm->id,
                'titulo' => 'Pendiente cotizar hosting',
                'descripcion' => 'Nota interna: pedir dos cotizaciones antes del siguiente avance de fase.',
                'tipo' => 'nota',
                'fecha' => now()->subDays(3)->toDateString(),
                'visible_cliente' => false,
            ],
            [
                'proyecto_id' => $gestor->id,
                'creado_por' => $pm->id,
                'titulo' => 'Modelo de datos aprobado',
                'descripcion' => 'El estudio valido el flujo de estados del expediente.',
                'tipo' => 'hito',
                'fecha' => now()->subMonths(2)->toDateString(),
                'visible_cliente' => true,
            ],
            [
                'proyecto_id' => $portal->id,
                'creado_por' => $jefe->id,
                'titulo' => 'Sitio publicado',
                'descripcion' => 'El portal quedo disponible para todos los socios de la cooperativa.',
                'tipo' => 'hito',
                'fecha' => now()->subMonths(4)->toDateString(),
                'visible_cliente' => true,
            ],
        ];

        foreach ($actualizaciones as $datos) {
            ActualizacionProyecto::updateOrCreate(
                ['proyecto_id' => $datos['proyecto_id'], 'titulo' => $datos['titulo']],
                $datos
            );
        }
    }
}
