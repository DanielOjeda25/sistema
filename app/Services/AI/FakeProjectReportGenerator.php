<?php

namespace App\Services\AI;

use App\Contracts\ProjectReportGenerator;

class FakeProjectReportGenerator implements ProjectReportGenerator
{
    public function generate(array $context): string
    {
        $proyecto = $context['proyecto'];
        $progreso = $context['progreso'];

        return implode("\n\n", [
            "Informe de avance: {$proyecto['nombre']}",
            "El proyecto se encuentra en estado {$proyecto['estado']} y registra un avance calculado del {$progreso['porcentaje']}%.",
            "Tareas: {$progreso['tareas_completadas']} completadas de {$progreso['tareas_total']}; {$progreso['tareas_en_progreso']} en progreso; {$progreso['tareas_pendientes']} pendientes; {$progreso['tareas_vencidas']} vencidas.",
            "Hitos: {$progreso['hitos_completados']} completados de {$progreso['hitos_total']}.",
            'BORRADOR AUTOMÁTICO: revisar y aprobar antes de publicarlo al Cliente.',
        ]);
    }
}
