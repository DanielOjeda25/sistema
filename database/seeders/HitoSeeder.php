<?php

namespace Database\Seeders;

use App\Models\Hito;
use App\Models\Proyecto;
use Illuminate\Database\Seeder;

class HitoSeeder extends Seeder
{
    public function run(): void
    {
        $obraLR = Proyecto::where('nombre', 'Sistema de obra L&R')->first();
        $gestor = Proyecto::where('nombre', 'Gestor de expedientes Gimenez')->first();
        $portal = Proyecto::where('nombre', 'Portal del socio — Cooperativa Union')->first();

        $hitos = [
            ['nombre' => 'Kick-off y firma de contrato',  'descripcion' => null,                                'fecha_objetivo' => '2026-03-05', 'completado' => true,  'proyecto_id' => $obraLR->id],
            ['nombre' => 'Entrega del modulo de avance',  'descripcion' => 'Visualizacion de etapas por obra.', 'fecha_objetivo' => '2026-06-15', 'completado' => false, 'proyecto_id' => $obraLR->id],
            ['nombre' => 'Demo final L&R',                'descripcion' => 'Presentacion al cliente.',           'fecha_objetivo' => '2026-09-25', 'completado' => false, 'proyecto_id' => $obraLR->id],

            ['nombre' => 'Diseno de flujo de expedientes','descripcion' => null,                                'fecha_objetivo' => '2026-05-15', 'completado' => true,  'proyecto_id' => $gestor->id],
            ['nombre' => 'Sprint de integracion email',   'descripcion' => 'SMTP del estudio.',                  'fecha_objetivo' => '2026-07-10', 'completado' => false, 'proyecto_id' => $gestor->id],

            ['nombre' => 'Lanzamiento portal',            'descripcion' => 'Entrega final, ya en produccion.',   'fecha_objetivo' => '2026-05-18', 'completado' => true,  'proyecto_id' => $portal->id],
        ];

        foreach ($hitos as $datos) {
            Hito::firstOrCreate(
                ['nombre' => $datos['nombre'], 'proyecto_id' => $datos['proyecto_id']],
                $datos
            );
        }
    }
}
