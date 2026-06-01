<?php

namespace Database\Seeders;

use App\Models\Proyecto;
use App\Models\SolicitudCambio;
use App\Models\Tarea;
use App\Models\User;
use Illuminate\Database\Seeder;

class TareaSeeder extends Seeder
{
    public function run(): void
    {
        $obraLR = Proyecto::where('nombre', 'Sistema de obra L&R')->first();
        $gestor = Proyecto::where('nombre', 'Gestor de expedientes Gimenez')->first();
        $portal = Proyecto::where('nombre', 'Portal del socio — Cooperativa Union')->first();
        $dev    = User::where('email', 'dev@example.com')->first();
        $pm     = User::where('email', 'pm@example.com')->first();

        $solicitudReporte = SolicitudCambio::where('titulo', 'Agregar reporte de horas por obra')->first();

        $tareas = [
            [
                'titulo' => 'Disenar pantalla de avance por obra',
                'descripcion' => 'Mockup en Figma + revision con cliente.',
                'estado' => 'completada',
                'prioridad' => 'alta',
                'fecha_limite' => '2026-04-10',
                'proyecto_id' => $obraLR->id,
                'asignado_a' => $pm->id,
                'solicitud_cambio_id' => null,
            ],
            [
                'titulo' => 'Implementar listado de obras',
                'descripcion' => 'Endpoint + vista index.',
                'estado' => 'en_progreso',
                'prioridad' => 'alta',
                'fecha_limite' => '2026-05-20',
                'proyecto_id' => $obraLR->id,
                'asignado_a' => $dev->id,
                'solicitud_cambio_id' => null,
            ],
            [
                'titulo' => 'PDF reporte mensual de horas',
                'descripcion' => 'Originado por solicitud de cambio del cliente.',
                'estado' => 'pendiente',
                'prioridad' => 'alta',
                'fecha_limite' => '2026-07-15',
                'proyecto_id' => $obraLR->id,
                'asignado_a' => $dev->id,
                'solicitud_cambio_id' => $solicitudReporte?->id,
            ],
            [
                'titulo' => 'Crear modelo Expediente',
                'descripcion' => null,
                'estado' => 'completada',
                'prioridad' => 'media',
                'fecha_limite' => '2026-05-05',
                'proyecto_id' => $gestor->id,
                'asignado_a' => $dev->id,
                'solicitud_cambio_id' => null,
            ],
            [
                'titulo' => 'Login con dos factores',
                'descripcion' => 'Requerimiento legal.',
                'estado' => 'pendiente',
                'prioridad' => 'alta',
                'fecha_limite' => '2026-06-20',
                'proyecto_id' => $gestor->id,
                'asignado_a' => $dev->id,
                'solicitud_cambio_id' => null,
            ],
            [
                'titulo' => 'Migrar datos legacy a produccion',
                'descripcion' => 'Importacion de socios historica.',
                'estado' => 'completada',
                'prioridad' => 'alta',
                'fecha_limite' => '2026-05-10',
                'proyecto_id' => $portal->id,
                'asignado_a' => $dev->id,
                'solicitud_cambio_id' => null,
            ],
        ];

        foreach ($tareas as $datos) {
            Tarea::firstOrCreate(
                ['titulo' => $datos['titulo'], 'proyecto_id' => $datos['proyecto_id']],
                $datos
            );
        }
    }
}
