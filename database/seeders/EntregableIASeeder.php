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
        $po     = User::where('email', 'po@example.com')->first();

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
        ];

        foreach ($entregables as $datos) {
            EntregableIA::firstOrCreate(
                ['titulo' => $datos['titulo'], 'proyecto_id' => $datos['proyecto_id']],
                $datos
            );
        }
    }
}
