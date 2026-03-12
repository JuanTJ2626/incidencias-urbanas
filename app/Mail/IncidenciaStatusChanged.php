<?php

namespace App\Mail;

use App\Models\Incidencias;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class IncidenciaStatusChanged extends Mailable
{
    use Queueable, SerializesModels;

    public $incidencia;
    public $estatusAnterior;
    public $estatusNuevo;

    public $folio;
    public $nombreCiudadano;
    public $tipoIncidencia;
    public $direccion;
    public $fechaReporte;
    public $colorEstatus;
    public $iconoEstatus;
    public $mensajeDetalle;

    /**
     * Create a new message instance.
     */
    public function __construct(Incidencias $incidencia, string $estatusAnterior, string $estatusNuevo)
    {
        $this->incidencia = $incidencia;
        $this->estatusAnterior = $estatusAnterior;
        $this->estatusNuevo = $estatusNuevo;

        // Mapeo de datos para la vista
        $this->folio           = $incidencia->id;
        $this->nombreCiudadano = $incidencia->nombre_ciudadano;
        $this->tipoIncidencia  = $incidencia->tipo_incidencia;
        $this->direccion       = $incidencia->direccion;
        $this->fechaReporte    = $incidencia->created_at->format('d/m/Y');

        // Configuración visual según el estatus
        $this->setVisualConfig($estatusNuevo);
    }

    private function setVisualConfig($estatus)
    {
        $config = [
            'pendiente' => [
                'color' => '#86868B',
                'icono' => '📝',
                'msg'   => 'Tu reporte ha sido recibido y está en espera de ser revisado por nuestro equipo.'
            ],
            'en proceso' => [
                'color' => '#0071E3',
                'icono' => '🔧',
                'msg'   => 'Tu reporte ha sido revisado y aceptado. Un trabajador ha sido asignado para atender la situación.'
            ],
            'en revisión' => [
                'color' => '#F5A623',
                'icono' => '👀',
                'msg'   => 'El trabajo ha sido realizado y está siendo verificado por el administrador.'
            ],
            'resuelto' => [
                'color' => '#28A745',
                'icono' => '✅',
                'msg'   => '¡Buenas noticias! Tu reporte ha sido resuelto satisfactoriamente.'
            ],
            'rechazado' => [
                'color' => '#FF3B30',
                'icono' => '❌',
                'msg'   => 'Tu reporte no pudo ser procesado en este momento. Por favor verifica la información.'
            ],
        ];

        $res = $config[$estatus] ?? $config['pendiente'];
        $this->colorEstatus   = $res['color'];
        $this->iconoEstatus   = $res['icono'];
        $this->mensajeDetalle = $res['msg'];
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Actualización de tu reporte #{$this->folio} - {$this->estatusNuevo}",
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.incidencia-status',
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
