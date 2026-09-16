<?php

namespace App\Notifications;

use App\Models\Hito;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class HitoPorVencer extends Notification
{
    use Queueable;

    public function __construct(public Hito $hito, public int $diasRestantes) {}

    // Canal database: la campanita del layout la lee de unreadNotifications.
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $vencido = $this->diasRestantes < 0;

        return [
            'hito_id' => $this->hito->id,
            'tipo' => 'hito_por_vencer',
            'titulo' => $vencido
                ? "Hito vencido: {$this->hito->nombre}"
                : "Hito por vencer: {$this->hito->nombre}",
            'detalle' => $vencido
                ? "Venció el {$this->hito->fecha_objetivo->format('d/m/Y')} · {$this->hito->proyecto?->nombre}"
                : "Vence el {$this->hito->fecha_objetivo->format('d/m/Y')} (en {$this->diasRestantes} día(s)) · {$this->hito->proyecto?->nombre}",
            'url' => route('hitos.index'),
        ];
    }
}
