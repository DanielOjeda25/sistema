<?php

namespace App\Console\Commands;

use App\Models\Factura;
use Illuminate\Console\Command;

class MarcarFacturasVencidas extends Command
{
    protected $signature = 'facturas:marcar-vencidas';

    protected $description = 'Pasa a "vencida" las facturas pendientes cuyo vencimiento ya paso';

    public function handle(): int
    {
        $this->info(Factura::marcarVencidas().' factura(s) marcadas como vencidas.');

        return self::SUCCESS;
    }
}
