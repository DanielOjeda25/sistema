<?php

namespace App\Notifications;

use App\Models\SolicitudCambio;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class SolicitudCambioCreada extends Notification
{
    use Queueable;

    public function __construct(public SolicitudCambio $solicitud) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'titulo' => $this->solicitud->titulo,
            'proyecto' => $this->solicitud->proyecto->nombre,
            'url' => route('solicitudes-cambio.show', $this->solicitud),
        ];
    }
}
