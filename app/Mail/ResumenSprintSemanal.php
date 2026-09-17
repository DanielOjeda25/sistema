<?php

namespace App\Mail;

use App\Models\Sprint;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ResumenSprintSemanal extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Sprint $sprint,
        public string $cuerpo,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Resumen semanal: {$this->sprint->nombre}",
        );
    }

    public function content(): Content
    {
        return new Content(
            text: 'emails.resumen-sprint-semanal',
        );
    }
}
