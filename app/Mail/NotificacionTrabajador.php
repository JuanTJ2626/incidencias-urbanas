<?php

namespace App\Mail;

use App\Models\Incidencias;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NotificacionTrabajador extends Mailable
{
    use Queueable, SerializesModels;

    public $incidencia;
    public $tipoNotificacion; // 'asignacion' o 'rechazo'
    public $notaAdmin;

    /**
     * Create a new message instance.
     */
    public function __construct(Incidencias $incidencia, string $tipoNotificacion, ?string $notaAdmin = null)
    {
        $this->incidencia = $incidencia;
        $this->tipoNotificacion = $tipoNotificacion;
        $this->notaAdmin = $notaAdmin;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $subject = $this->tipoNotificacion === 'asignacion' 
            ? "Nueva Tarea Asignada (#{$this->incidencia->id})" 
            : "Evidencia Rechazada (#{$this->incidencia->id}) - Acción Requerida";

        return new Envelope(
            subject: $subject,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.notificacion-trabajador',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
