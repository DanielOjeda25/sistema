<?php

namespace App\Support;

use App\Models\Proyecto;
use Illuminate\Support\Collection;

/**
 * Arma la línea de tiempo del portal del Cliente: hitos y sprints ordenados
 * por fecha, cada uno con su estado visible y su detalle redactado en
 * lenguaje del cliente (sin jerga interna ni contadores triviales).
 *
 * Reglas que concentra en un solo lugar:
 *   - Un sprint queda CERRADO ("Completado") cuando terminó con todas sus
 *     actividades listas, o cuando su fecha fin pasó sin tareas cargadas.
 *   - Un sprint que terminó con actividades sin completar queda ATRASADO.
 *   - El "Estamos acá" es el primer punto sin terminar que no esté atrasado.
 *   - Con más de dos etapas completadas el historial colapsa en un único
 *     nodo-resumen; el detalle completo viaja dentro del nodo para el pop-up.
 */
class LineaDeTiempo
{
    /**
     * Colección de puntos listos para la vista (cliente/proyecto.blade.php):
     * fecha, titulo, detalle, estado y estado_clase por cada hito/sprint.
     */
    public function construir(Proyecto $proyecto): Collection
    {
        $linea = $this->hitos($proyecto)
            ->concat($this->sprints($proyecto))
            ->sortBy(fn (array $item) => $item['fecha'])
            ->values();

        $linea = $this->marcarEstados($linea);

        return $this->colapsarHistorial($linea);
    }

    /** Puntos por hito: hecho si está completado, atrasado si la fecha pasó. */
    private function hitos(Proyecto $proyecto): Collection
    {
        return $proyecto->hitos->map(fn ($hito) => [
            'fecha' => $hito->fecha_objetivo,
            'fecha_texto' => $hito->fecha_objetivo?->format('d/m/Y'),
            'tipo' => 'hito',
            'titulo' => $hito->nombre,
            'detalle' => $hito->descripcion,
            'descripcion' => $hito->descripcion,
            'hecho' => (bool) $hito->completado,
            'vencido' => ! $hito->completado && $hito->fecha_objetivo->isPast(),
        ]);
    }

    /**
     * Puntos por sprint: el detalle explica la situación en lenguaje del
     * cliente y el avance (0-100) alimenta la mini barra de la cabecera.
     */
    private function sprints(Proyecto $proyecto): Collection
    {
        return $proyecto->sprints->map(function ($sprint) {
            $total = $sprint->tareas->count();
            $hechas = $sprint->tareas->where('estado', 'completada')->count();
            $fin = $sprint->fecha_fin?->format('d/m/Y');
            $avance = $total > 0 ? (int) round($hechas * 100 / $total) : 0;
            $completo = $total > 0 && $hechas === $total;
            $termino = $sprint->fecha_fin?->isPast() ?? false;
            // Un sprint que ya terminó sin actividades cargadas no deja
            // nada pendiente: para el cliente es una etapa cerrada.
            $cerrado = $completo || ($termino && $total === 0);

            // Detalle en lenguaje del cliente: sin contadores triviales
            // ("1 de 1") ni la palabra "tareas"; el rango de fechas ya se
            // muestra aparte en la línea de tiempo.
            if ($completo) {
                $detalle = $total === 1 ? 'La actividad está lista' : "Las {$total} actividades están listas";
            } elseif ($termino && $total > 0) {
                $detalle = "Finalizó el {$fin} con actividades sin completar";
            } elseif ($termino) {
                $detalle = "Cerró el {$fin} sin actividades registradas";
            } elseif ($sprint->fecha_inicio?->isFuture()) {
                $detalle = match (true) {
                    $total === 0 => 'Actividades por definir',
                    $total === 1 => '1 actividad planificada',
                    default => "{$total} actividades planificadas",
                };
            } else {
                $detalle = match (true) {
                    $total === 0 => 'Actividades por definir',
                    $total === 1 => '1 actividad en camino',
                    default => "{$total} actividades en camino",
                };
            }

            return [
                'fecha' => $sprint->fecha_inicio,
                'fecha_texto' => $sprint->fecha_inicio?->format('d/m/Y'),
                'fecha_fin_texto' => $fin,
                'tipo' => 'sprint',
                'titulo' => $sprint->nombre,
                'detalle' => $detalle,
                'descripcion' => $sprint->descripcion,
                'resumen_ia' => $sprint->resumen_ia,
                'hecho' => $cerrado,
                // Un sprint que ya terminó con actividad pendiente también
                // queda atrasado: así el "Estamos acá" no cae en un punto vencido.
                'vencido' => ! $completo && $termino && $total > 0,
                'avance' => $avance,
            ];
        });
    }

    /**
     * Estado visible de cada punto: Completado / Atrasado / En curso
     * (el primer punto sin terminar) / Pendiente. Lo consumen la línea
     * de tiempo y el pop-up de detalle, así ambos muestran lo mismo.
     */
    private function marcarEstados(Collection $linea): Collection
    {
        $enCursoMarcado = false;

        return $linea->map(function (array $item) use (&$enCursoMarcado) {
            if ($item['hecho']) {
                $item['estado'] = 'Completado';
                $item['estado_clase'] = 'bg-emerald-100 text-emerald-700';
            } elseif ($item['vencido']) {
                $item['estado'] = 'Atrasado';
                $item['estado_clase'] = 'bg-red-100 text-red-700';
            } elseif (! $enCursoMarcado) {
                $enCursoMarcado = true;
                $item['estado'] = 'En curso';
                $item['estado_clase'] = 'bg-[#00d99a]/20 text-[#00795a]';
            } else {
                $item['estado'] = 'Pendiente';
                $item['estado_clase'] = 'bg-gray-100 text-gray-500';
            }

            return $item;
        });
    }

    /**
     * Con muchos puntos la línea se vuelve ilegible: el pasado completado
     * se colapsa en un único nodo-resumen y queda visible lo relevante
     * (el último completado como contexto, lo atrasado, lo en curso y lo
     * pendiente). El historial viaja en el nodo para listarlo en el pop-up.
     */
    private function colapsarHistorial(Collection $linea): Collection
    {
        $completados = $linea->filter(fn ($item) => $item['hecho'])->sortBy('fecha');

        if ($completados->count() <= 2) {
            return $linea;
        }

        $corte = $completados->last()['fecha'];

        $historial = $linea
            ->filter(fn ($item) => $item['hecho'] && $item['fecha']->lt($corte))
            ->values();

        return collect([
            [
                'fecha' => $historial->first()['fecha'],
                'fecha_texto' => null,
                'tipo' => 'resumen',
                'titulo' => $historial->count() === 1
                    ? 'Etapa completada'
                    : 'Etapas completadas ('.$historial->count().')',
                'detalle' => 'Toca para ver el historial',
                'hecho' => true,
                'vencido' => false,
                'estado' => 'Completado',
                'estado_clase' => 'bg-emerald-100 text-emerald-700',
                'historial' => $historial->all(),
            ],
        ])->merge(
            $linea->filter(fn ($item) => ! $item['hecho'] || ! $item['fecha']->lt($corte))->values()
        )->values();
    }
}
