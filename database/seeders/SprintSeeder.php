<?php

namespace Database\Seeders;

use App\Models\Proyecto;
use App\Models\Sprint;
use App\Models\Tarea;
use Illuminate\Database\Seeder;

class SprintSeeder extends Seeder
{
    public function run(): void
    {
        $obraLR = Proyecto::where('nombre', 'Sistema de obra L&R')->first();
        $gestor = Proyecto::where('nombre', 'Gestor de expedientes Gimenez')->first();

        $sprints = [
            ['proyecto_id' => $obraLR->id, 'nombre' => 'Sprint 1', 'fecha_inicio' => '2026-03-02', 'fecha_fin' => '2026-03-13'],
            ['proyecto_id' => $obraLR->id, 'nombre' => 'Sprint 2', 'fecha_inicio' => '2026-03-16', 'fecha_fin' => '2026-03-27'],
            ['proyecto_id' => $gestor->id, 'nombre' => 'Sprint 1', 'fecha_inicio' => '2026-04-06', 'fecha_fin' => '2026-04-17'],
        ];

        foreach ($sprints as $data) {
            Sprint::firstOrCreate($data);
        }

        // Reparte las tareas existentes de cada proyecto entre sus sprints.
        $porSprint = [
            'Sistema de obra L&R|Sprint 1' => ['Disenar pantalla de avance por obra', 'Implementar listado de obras'],
            'Sistema de obra L&R|Sprint 2' => ['PDF reporte mensual de horas'],
            'Gestor de expedientes Gimenez|Sprint 1' => ['Crear modelo Expediente'],
        ];

        foreach ($porSprint as $clave => $titulos) {
            [$proyectoNombre, $sprintNombre] = explode('|', $clave);
            $sprintId = Sprint::where('nombre', $sprintNombre)
                ->whereHas('proyecto', fn ($q) => $q->where('nombre', $proyectoNombre))
                ->value('id');

            Tarea::whereIn('titulo', $titulos)->update(['sprint_id' => $sprintId]);
        }
    }
}
