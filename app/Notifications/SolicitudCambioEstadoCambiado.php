<?php

namespace App\Notifications;

use App\Models\SolicitudCambio;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class SolicitudCambioEstadoCambiado extends Notification
{
    use Queueable;

    public function __construct(
        public SolicitudCambio $solicitud,
        public string $estadoAnterior,
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'tipo' => 'solicitud_cambio_estado_cambiado',
            'titulo' => "Solicitud {$this->solicitud->titulo}: estado actualizado",
            'detalle' => "Pasó de {$this->estadoAnterior} a {$this->solicitud->estado} en {$this->solicitud->proyecto->nombre}.",
            'proyecto' => $this->solicitud->proyecto->nombre,
            'estado_anterior' => $this->estadoAnterior,
            'estado_nuevo' => $this->solicitud->estado,
            'url' => route('solicitudes-cambio.show', $this->solicitud),
        ];
    }
}
