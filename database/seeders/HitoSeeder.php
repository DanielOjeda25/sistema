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

        // Un hito vence en 5 dias (dispara el aviso diario al PM), otro ya esta
        // vencido (aparece en el filtro de vencidos) y el resto da historia.
        $hitos = [
            ['nombre' => 'Kick-off y firma de contrato', 'descripcion' => null, 'fecha_objetivo' => now()->subMonths(5)->toDateString(), 'completado' => true, 'proyecto_id' => $obraLR->id],
            ['nombre' => 'Entrega del modulo de avance', 'descripcion' => 'Visualizacion de etapas por obra.', 'fecha_objetivo' => now()->addDays(5)->toDateString(), 'completado' => false, 'proyecto_id' => $obraLR->id],
            ['nombre' => 'Demo final L&R', 'descripcion' => 'Presentacion al cliente.', 'fecha_objetivo' => now()->addWeeks(8)->toDateString(), 'completado' => false, 'proyecto_id' => $obraLR->id],

            ['nombre' => 'Diseno de flujo de expedientes', 'descripcion' => null, 'fecha_objetivo' => now()->subMonths(2)->toDateString(), 'completado' => true, 'proyecto_id' => $gestor->id],
            ['nombre' => 'Sprint de integracion email', 'descripcion' => 'SMTP del estudio.', 'fecha_objetivo' => now()->subDays(4)->toDateString(), 'completado' => false, 'proyecto_id' => $gestor->id],

            ['nombre' => 'Lanzamiento portal', 'descripcion' => 'Entrega final, ya en produccion.', 'fecha_objetivo' => now()->subMonths(4)->toDateString(), 'completado' => true, 'proyecto_id' => $portal->id],
        ];

        foreach ($hitos as $datos) {
            Hito::updateOrCreate(
                ['nombre' => $datos['nombre'], 'proyecto_id' => $datos['proyecto_id']],
                $datos
            );
        }
    }
}
