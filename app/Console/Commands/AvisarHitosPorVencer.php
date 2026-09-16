<?php

namespace App\Console\Commands;

use App\Models\Hito;
use App\Notifications\HitoPorVencer;
use Illuminate\Console\Command;

class AvisarHitosPorVencer extends Command
{
    protected $signature = 'avisos:hitos-por-vencer';

    protected $description = 'Notifica al PM de cada proyecto los hitos sin completar que vencen dentro de 7 días (o ya vencidos)';

    public function handle(): int
    {
        // Hitos sin completar dentro de la ventana de aviso (7 días hacia
        // atrás incluidos los ya vencidos, para avisar también los atrasados).
        $hitos = Hito::with('proyecto.pm')
            ->where('completado', false)
            ->whereDate('fecha_objetivo', '<=', today()->addDays(7))
            ->get();

        $enviados = 0;

        foreach ($hitos as $hito) {
            $pm = $hito->proyecto?->pm;
            if (! $pm) {
                continue;
            }

            $diasRestantes = (int) today()->diffInDays($hito->fecha_objetivo, false);

            // Sin duplicados: si ya existe una notificación para este hito,
            // no se vuelve a avisar aunque el usuario la haya leído.
            $yaNotificado = $pm->notifications()
                ->where('data->hito_id', $hito->id)
                ->where('type', HitoPorVencer::class)
                ->exists();

            if ($yaNotificado) {
                continue;
            }

            $pm->notify(new HitoPorVencer($hito, $diasRestantes));
            $enviados++;
            $this->line("Aviso enviado a {$pm->name}: {$hito->nombre}");
        }

        $this->info("Notificaciones enviadas: {$enviados} de {$hitos->count()} hito(s) revisado(s).");

        return self::SUCCESS;
    }
}
