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

        // Sprints cerrados en el pasado y uno ACTIVO alrededor de hoy: es el que
        // se usa en la demo para generar el resumen IA en vivo.
        $sprints = [
            ['proyecto_id' => $obraLR->id, 'nombre' => 'Sprint 1 — Fundaciones', 'fecha_inicio' => now()->subWeeks(8)->toDateString(), 'fecha_fin' => now()->subWeeks(6)->toDateString(), 'estado' => 'finalizado'],
            ['proyecto_id' => $obraLR->id, 'nombre' => 'Sprint 2 — Listados', 'fecha_inicio' => now()->subWeeks(6)->toDateString(), 'fecha_fin' => now()->subWeeks(4)->toDateString(), 'estado' => 'finalizado'],
            ['proyecto_id' => $obraLR->id, 'nombre' => 'Sprint 3 — Nucleo de obras', 'fecha_inicio' => now()->subWeek()->toDateString(), 'fecha_fin' => now()->addWeek()->toDateString(), 'estado' => 'activo'],
            ['proyecto_id' => $gestor->id, 'nombre' => 'Sprint 1 — Modelo de datos', 'fecha_inicio' => now()->subWeeks(3)->toDateString(), 'fecha_fin' => now()->subWeek()->toDateString(), 'estado' => 'finalizado'],
            ['proyecto_id' => $gestor->id, 'nombre' => 'Sprint 2 — Seguridad', 'fecha_inicio' => now()->toDateString(), 'fecha_fin' => now()->addWeeks(2)->toDateString(), 'estado' => 'activo'],
        ];

        foreach ($sprints as $data) {
            Sprint::updateOrCreate(
                ['proyecto_id' => $data['proyecto_id'], 'nombre' => $data['nombre']],
                $data
            );
        }

        // Reparte las tareas existentes de cada proyecto entre sus sprints.
        $porSprint = [
            'Sistema de obra L&R|Sprint 1 — Fundaciones' => ['Disenar pantalla de avance por obra'],
            'Sistema de obra L&R|Sprint 2 — Listados' => ['Implementar listado de obras'],
            'Sistema de obra L&R|Sprint 3 — Nucleo de obras' => ['Ajustes de la reunion con el cliente', 'PDF reporte mensual de horas', 'Conectar API de presupuestos'],
            'Gestor de expedientes Gimenez|Sprint 1 — Modelo de datos' => ['Crear modelo Expediente'],
            'Gestor de expedientes Gimenez|Sprint 2 — Seguridad' => ['Importar expedientes historicos', 'Login con dos factores'],
        ];

        foreach ($porSprint as $clave => $titulos) {
            [$proyectoNombre, $sprintNombre] = explode('|', $clave);
            $sprintId = Sprint::where('nombre', $sprintNombre)
                ->whereHas('proyecto', fn ($q) => $q->where('nombre', $proyectoNombre))
                ->value('id');

            Tarea::whereIn('titulo', $titulos)->update(['sprint_id' => $sprintId]);
        }

        // El sprint ya cerrado guarda el resumen que dejo el job semanal: sirve de
        // plan B si la IA no responde el dia de la demo (el sprint activo se
        // resume en vivo).
        $sprintListados = Sprint::where('nombre', 'Sprint 2 — Listados')->first();
        $sprintListados?->update([
            'resumen_ia' => "Durante el sprint se completo la pantalla de diseno de avance y el listado de obras llego a un 60%. El equipo destaco la integracion temprana con el cliente. Como riesgo queda definido el formato del reporte mensual de horas, que se aborda en el sprint 3 con prioridad alta.",
        ]);
    }
}
