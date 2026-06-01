<?php

namespace Database\Seeders;

use App\Models\Proyecto;
use App\Models\SolicitudCambio;
use App\Models\User;
use Illuminate\Database\Seeder;

class SolicitudCambioSeeder extends Seeder
{
    public function run(): void
    {
        $obraLR  = Proyecto::where('nombre', 'Sistema de obra L&R')->first();
        $gestor  = Proyecto::where('nombre', 'Gestor de expedientes Gimenez')->first();
        $cliente = User::where('email', 'cliente@example.com')->first();
        $pm      = User::where('email', 'pm@example.com')->first();

        $solicitudes = [
            [
                'titulo' => 'Agregar reporte de horas por obra',
                'descripcion' => 'El cliente pide un PDF mensual con horas hombre por obra.',
                'estado' => 'aprobada',
                'prioridad' => 'alta',
                'proyecto_id' => $obraLR->id,
                'solicitado_por' => $cliente->id,
            ],
            [
                'titulo' => 'Cambiar paleta de colores',
                'descripcion' => 'El cliente quiere usar los colores corporativos.',
                'estado' => 'pendiente',
                'prioridad' => 'baja',
                'proyecto_id' => $obraLR->id,
                'solicitado_por' => $pm->id,
            ],
            [
                'titulo' => 'Notificaciones por WhatsApp',
                'descripcion' => 'Reemplazar email por WhatsApp para abogados senior.',
                'estado' => 'rechazada',
                'prioridad' => 'media',
                'proyecto_id' => $gestor->id,
                'solicitado_por' => $cliente->id,
            ],
        ];

        foreach ($solicitudes as $datos) {
            SolicitudCambio::firstOrCreate(
                ['titulo' => $datos['titulo'], 'proyecto_id' => $datos['proyecto_id']],
                $datos
            );
        }
    }
}
