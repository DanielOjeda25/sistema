<?php

namespace Database\Seeders;

use App\Models\EntregableIA;
use App\Models\Proyecto;
use App\Models\User;
use Illuminate\Database\Seeder;

class EntregableIASeeder extends Seeder
{
    public function run(): void
    {
        $obraLR = Proyecto::where('nombre', 'Sistema de obra L&R')->first();
        $gestor = Proyecto::where('nombre', 'Gestor de expedientes Gimenez')->first();
        $po = User::where('email', 'po@example.com')->first();
        $jefe = User::where('email', 'jefe@example.com')->first();

        $entregables = [
            [
                'titulo' => 'Borrador de manual de usuario',
                'contenido' => 'Generado por IA a partir de las pantallas de avance. Necesita revision humana.',
                'tipo' => 'documento',
                'estado' => 'borrador',
                'proyecto_id' => $obraLR->id,
                'generado_por' => $po->id,
            ],
            [
                'titulo' => 'Resumen de reunion 2026-05-22',
                'contenido' => 'Transcripcion procesada de la call con el cliente.',
                'tipo' => 'transcripcion',
                'estado' => 'revisado',
                'proyecto_id' => $obraLR->id,
                'generado_por' => $po->id,
            ],
            [
                'titulo' => 'Esquema legal del flujo de expedientes',
                'contenido' => 'Diagrama generado a partir de los requerimientos del estudio.',
                'tipo' => 'diagrama',
                'estado' => 'aprobado',
                'proyecto_id' => $gestor->id,
                'generado_por' => $po->id,
            ],
            // Informe IA ya publicado sobre el proyecto de la empresa de Juan:
            // es lo que ve el usuario Cliente en su vista.
            [
                'titulo' => 'Informe de avance mensual — Obra L&R',
                'contenido' => "Estimada Mariana: compartimos el avance del mes. El listado de obras ya se prueba con datos reales en preproduccion y los ajustes de la ultima reunion estan en desarrollo. El reporte mensual de horas solicitado entra en el sprint actual con prioridad alta. La entrega del modulo de avance se mantiene dentro del plazo acordado y queda programada la demostracion correspondiente. Seguimos trabajando para cumplir las fechas pactadas.",
                'tipo' => 'informe_avance',
                'estado' => 'aprobado',
                'proyecto_id' => $obraLR->id,
                'generado_por' => $po->id,
                'origen' => 'ia',
                'modelo_ia' => 'seed-demo',
                'generado_en' => now()->subDays(2),
                'visible_cliente' => true,
                'aprobado_por' => $jefe->id,
                'aprobado_en' => now()->subDays(2),
            ],
            // Informe IA en borrador: es el que se revisa y publica en vivo.
            [
                'titulo' => 'Informe de avance — Obra L&R',
                'contenido' => "Estimada Mariana: compartimos el avance del periodo. El listado de obras esta proximo a su cierre y los ajustes surgidos de la ultima reunion ya se encuentran en desarrollo. El reporte mensual de horas solicitado entra en el sprint en curso con prioridad alta. Como proximo hito, la entrega del modulo de avance esta prevista para dentro de una semana. Quedamos a disposicion para coordinar la demostracion.",
                'tipo' => 'informe_avance',
                'estado' => 'borrador',
                'proyecto_id' => $obraLR->id,
                'generado_por' => $po->id,
                'origen' => 'ia',
                'modelo_ia' => 'seed-demo',
                'generado_en' => now(),
                'visible_cliente' => false,
            ],
        ];

        foreach ($entregables as $datos) {
            EntregableIA::updateOrCreate(
                ['titulo' => $datos['titulo'], 'proyecto_id' => $datos['proyecto_id']],
                $datos
            );
        }
    }
}
