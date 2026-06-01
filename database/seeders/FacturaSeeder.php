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
        $jefe   = User::where('email', 'jefe@example.com')->first();

        $facturas = [
            [
                'numero' => 'F-2026-0001',
                'monto' => 4500000.00,
                'fecha_emision' => '2026-03-15',
                'fecha_vencimiento' => '2026-04-15',
                'estado' => 'pagada',
                'detalle' => 'Anticipo del 30% del proyecto Sistema de obra L&R.',
                'proyecto_id' => $obraLR->id,
                'emitida_por' => $jefe->id,
            ],
            [
                'numero' => 'F-2026-0002',
                'monto' => 7500000.00,
                'fecha_emision' => '2026-06-01',
                'fecha_vencimiento' => '2026-07-01',
                'estado' => 'pendiente',
                'detalle' => 'Avance del 50% — proyecto Sistema de obra L&R.',
                'proyecto_id' => $obraLR->id,
                'emitida_por' => $jefe->id,
            ],
            [
                'numero' => 'F-2026-0003',
                'monto' => 3200000.00,
                'fecha_emision' => '2026-04-20',
                'fecha_vencimiento' => '2026-05-20',
                'estado' => 'pagada',
                'detalle' => 'Pago unico — Gestor expedientes (anticipo).',
                'proyecto_id' => $gestor->id,
                'emitida_por' => $jefe->id,
            ],
            [
                'numero' => 'F-2026-0004',
                'monto' => 9000000.00,
                'fecha_emision' => '2026-05-20',
                'fecha_vencimiento' => '2026-06-20',
                'estado' => 'pagada',
                'detalle' => 'Facturacion final — Portal del socio.',
                'proyecto_id' => $portal->id,
                'emitida_por' => $jefe->id,
            ],
        ];

        foreach ($facturas as $datos) {
            Factura::firstOrCreate(['numero' => $datos['numero']], $datos);
        }
    }
}
