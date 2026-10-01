<?php

namespace Database\Seeders;

use App\Models\Factura;
use App\Models\Proyecto;
use App\Models\User;
use Illuminate\Database\Seeder;

class FacturaSeeder extends Seeder
{
    public function run(): void
    {
        $obraLR = Proyecto::where('nombre', 'Sistema de obra L&R')->first();
        $gestor = Proyecto::where('nombre', 'Gestor de expedientes Gimenez')->first();
        $portal = Proyecto::where('nombre', 'Portal del socio — Cooperativa Union')->first();
        $jefe = User::where('email', 'jefe@example.com')->first();

        // Los tres estados presentes: pagada (historia), pendiente (vence pronto)
        // y vencida (para el filtro y la senal roja).
        $facturas = [
            [
                'numero' => 'F-2026-0001',
                'monto' => 4500000.00,
                'fecha_emision' => now()->subMonths(5)->toDateString(),
                'fecha_vencimiento' => now()->subMonths(4)->toDateString(),
                'estado' => 'pagada',
                'detalle' => 'Anticipo del 30% del proyecto Sistema de obra L&R.',
                'proyecto_id' => $obraLR->id,
                'emitida_por' => $jefe->id,
            ],
            [
                'numero' => 'F-2026-0002',
                'monto' => 7500000.00,
                'fecha_emision' => now()->subMonths(2)->toDateString(),
                'fecha_vencimiento' => now()->subMonth()->toDateString(),
                'estado' => 'pagada',
                'detalle' => 'Avance del 50% — proyecto Sistema de obra L&R.',
                'proyecto_id' => $obraLR->id,
                'emitida_por' => $jefe->id,
            ],
            [
                'numero' => 'F-2026-0003',
                'monto' => 5200000.00,
                'fecha_emision' => now()->subWeeks(3)->toDateString(),
                'fecha_vencimiento' => now()->addDays(10)->toDateString(),
                'estado' => 'pendiente',
                'detalle' => 'Avance del 70% — proyecto Sistema de obra L&R.',
                'proyecto_id' => $obraLR->id,
                'emitida_por' => $jefe->id,
            ],
            [
                'numero' => 'F-2026-0004',
                'monto' => 3200000.00,
                'fecha_emision' => now()->subMonths(2)->toDateString(),
                'fecha_vencimiento' => now()->subDays(12)->toDateString(),
                'estado' => 'vencida',
                'detalle' => 'Anticipo — Gestor de expedientes Gimenez.',
                'proyecto_id' => $gestor->id,
                'emitida_por' => $jefe->id,
            ],
            [
                'numero' => 'F-2026-0005',
                'monto' => 9000000.00,
                'fecha_emision' => now()->subMonths(5)->toDateString(),
                'fecha_vencimiento' => now()->subMonths(4)->toDateString(),
                'estado' => 'pagada',
                'detalle' => 'Facturacion final — Portal del socio.',
                'proyecto_id' => $portal->id,
                'emitida_por' => $jefe->id,
            ],
        ];

        foreach ($facturas as $datos) {
            Factura::updateOrCreate(['numero' => $datos['numero']], $datos);
        }
    }
}
