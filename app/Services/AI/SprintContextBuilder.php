<?php

namespace App\Services\AI;

use App\Models\Sprint;

class SprintContextBuilder
{
    /**
     * Construye el contexto mínimo que se envía a OpenRouter.
     *
     * No incluye IDs, timestamps, usuarios ni otros metadatos de la base.
     *
     * @return array<string, mixed>
     */
    public function build(Sprint $sprint): array
    {
        $sprint->loadMissing([
            'proyecto:id,nombre',
            'tareas:id,sprint_id,titulo,descripcion,estado',
        ]);

        $tareas = $sprint->tareas->map(fn ($tarea) => [
            'titulo' => $tarea->titulo,
            'descripcion' => $tarea->descripcion,
            'estado' => $tarea->estado,
        ])->values()->all();

        return [
            'sprint' => [
                'nombre' => $sprint->nombre,
                'descripcion' => $sprint->descripcion,
                'estado' => $sprint->estado,
                'fecha_inicio' => $sprint->fecha_inicio?->toDateString(),
                'fecha_fin' => $sprint->fecha_fin?->toDateString(),
                'proyecto' => $sprint->proyecto?->nombre,
            ],
            'totales' => [
                'tarjetas' => count($tareas),
                'completadas' => $sprint->tareas->where('estado', 'completada')->count(),
                'en_progreso' => $sprint->tareas->where('estado', 'en_progreso')->count(),
                'pendientes' => $sprint->tareas->where('estado', 'pendiente')->count(),
                'canceladas' => $sprint->tareas->where('estado', 'cancelada')->count(),
            ],
            'tarjetas' => $tareas,
        ];
    }
}
