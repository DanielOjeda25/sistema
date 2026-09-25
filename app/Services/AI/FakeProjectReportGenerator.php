<?php

namespace App\Services\AI;

use App\Contracts\ProjectReportGenerator;

/**
 * Generador de informes en modo demo (AI_PROVIDER=fake).
 *
 * En lugar de tecnicismos ("estado en_progreso", "avance calculado"),
 * escribe como le contaria a un cliente: que se termino, que se esta
 * haciendo ahora mismo con nombres propios, y si algo atrasa.
 */
class FakeProjectReportGenerator implements ProjectReportGenerator
{
    public function generate(array $context): string
    {
        $proyecto = $context['proyecto'];
        $progreso = $context['progreso'];
        $lineas = [];

        $lineas[] = "Hola, les contamos como viene «{$proyecto['nombre']}»:";

        // El avance en criollo: de cada 10 cosas, cuantas van listas.
        $deCadaDiez = (int) round(($progreso['porcentaje'] ?? 0) / 10);
        $lineas[] = $deCadaDiez > 0
            ? "Vamos por buen camino: ya tenemos listas aproximadamente {$deCadaDiez} de cada 10 cosas del proyecto."
            : 'Recien estamos arrancando: todavia no hay nada terminado, pero el trabajo ya esta en marcha.';

        // Que se esta haciendo HOY, con nombres de las tareas.
        $enMarcha = collect($context['tareas'] ?? [])->where('estado', 'en_progreso')->pluck('titulo');
        if ($enMarcha->isNotEmpty()) {
            $lineas[] = 'Ahora mismo estamos trabajando en: '.$enMarcha->map(fn ($t) => "«{$t}»")->implode(', ').'.';
        }

        // Lo que ya quedo listo.
        $terminadas = collect($context['tareas'] ?? [])->where('estado', 'completada')->pluck('titulo');
        if ($terminadas->isNotEmpty()) {
            $lineas[] = 'Ya quedo terminado: '.$terminadas->map(fn ($t) => "«{$t}»")->implode(', ').'.';
        }

        // Hitos: los puntos de control, dichos en criollo.
        $hitosTotales = $progreso['hitos_total'] ?? 0;
        if ($hitosTotales > 0) {
            $lineas[] = "De los {$hitosTotales} acuerdos que teniamos como metas, ya cumplimos {$progreso['hitos_completados']}.";
        }

        // Novedades que el equipo marco para el cliente.
        $novedades = collect($context['actualizaciones'] ?? []);
        if ($novedades->isNotEmpty()) {
            $ultima = $novedades->last();
            $lineas[] = "La ultima novedad del equipo ({$ultima['titulo']}): {$ultima['descripcion']}";
        }

        // Si algo atrasa, decirlo sin maquillar pero sin asustar.
        $vencidas = $progreso['tareas_vencidas'] ?? 0;
        if ($vencidas > 0) {
            $lineas[] = "Para ser transparentes: hay {$vencidas} ".($vencidas === 1 ? 'cosa' : 'cosas')." que se atrasaron respecto a la fecha pactada. El equipo ya lo esta resolviendo y lo vamos a seguir informando.";
        }

        $lineas[] = 'Cualquier duda, respondemos por aca. Seguimos avisando como va todo.';

        return implode("\n\n", $lineas)
            ."\n\n(Borrador automatico: el equipo lo revisa y ajusta antes de publicarlo al cliente.)";
    }
}
