<?php

namespace App\Services\AI;

use App\Models\Proyecto;

class ProjectContextBuilder
{
    /**
     * Construye exclusivamente contexto publicable para el Cliente.
     *
     * @return array<string, mixed>
     */
    public function build(Proyecto $proyecto): array
    {
        $proyecto->load([
            'cliente:id,nombre,apellido,empresa',
            'tareas:id,proyecto_id,titulo,descripcion,estado,prioridad,fecha_limite',
            'hitos:id,proyecto_id,nombre,descripcion,fecha_objetivo,completado',
            'actualizaciones' => fn ($query) => $query
                ->where('visible_cliente', true)
                ->with('autor:id,name')
                ->orderBy('fecha'),
        ]);

        $totalTareas = $proyecto->tareas->count();
        $tareasCompletadas = $proyecto->tareas->where('estado', 'completada')->count();
        $totalHitos = $proyecto->hitos->count();
        $hitosCompletados = $proyecto->hitos->where('completado', true)->count();
        $totalElementos = $totalTareas + $totalHitos;
        $elementosCompletados = $tareasCompletadas + $hitosCompletados;

        return [
            'proyecto' => [
                'id' => $proyecto->id,
                'nombre' => $proyecto->nombre,
                'descripcion' => $proyecto->descripcion,
                'estado' => $proyecto->estado,
                'fecha_inicio' => $proyecto->fecha_inicio?->toDateString(),
                'fecha_fin_estimada' => $proyecto->fecha_fin_estimada?->toDateString(),
                'cliente' => $proyecto->cliente?->empresa
                    ?: trim(($proyecto->cliente?->nombre ?? '').' '.($proyecto->cliente?->apellido ?? '')),
            ],
            'progreso' => [
                'porcentaje' => $totalElementos > 0
                    ? (int) round(($elementosCompletados / $totalElementos) * 100)
                    : 0,
                'criterio' => 'Tareas completadas e hitos completados, todos con el mismo peso.',
                'tareas_total' => $totalTareas,
                'tareas_completadas' => $tareasCompletadas,
                'tareas_pendientes' => $proyecto->tareas->where('estado', 'pendiente')->count(),
                'tareas_en_progreso' => $proyecto->tareas->where('estado', 'en_progreso')->count(),
                'tareas_vencidas' => $proyecto->tareas
                    ->filter(fn ($tarea) => $tarea->fecha_limite?->lt(today())
                        && ! in_array($tarea->estado, ['completada', 'cancelada'], true))
                    ->count(),
                'hitos_total' => $totalHitos,
                'hitos_completados' => $hitosCompletados,
            ],
            'tareas' => $proyecto->tareas->map(fn ($tarea) => [
                'titulo' => $tarea->titulo,
                'descripcion' => $tarea->descripcion,
                'estado' => $tarea->estado,
                'prioridad' => $tarea->prioridad,
                'fecha_limite' => $tarea->fecha_limite?->toDateString(),
            ])->values()->all(),
            'hitos' => $proyecto->hitos->map(fn ($hito) => [
                'nombre' => $hito->nombre,
                'descripcion' => $hito->descripcion,
                'fecha_objetivo' => $hito->fecha_objetivo?->toDateString(),
                'completado' => $hito->completado,
            ])->values()->all(),
            'actualizaciones' => $proyecto->actualizaciones->map(fn ($actualizacion) => [
                'titulo' => $actualizacion->titulo,
                'descripcion' => $actualizacion->descripcion,
                'tipo' => $actualizacion->tipo,
                'fecha' => $actualizacion->fecha->toDateString(),
                'autor' => $actualizacion->autor?->name,
            ])->values()->all(),
        ];
    }
}
