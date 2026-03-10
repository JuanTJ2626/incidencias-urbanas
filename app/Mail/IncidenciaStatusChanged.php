<?php

namespace App\Mail;

use App\Models\Incidencias;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class IncidenciaStatusChanged extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public string $nombreCiudadano;
    public string $tipoIncidencia;
    public string $direccion;
    public string $estatusAnterior;
    public string $estatusNuevo;
    public string $folio;
    public string $fechaReporte;
    public string $mensajeDetalle;
    public string $colorEstatus;
    public string $iconoEstatus;

    /**
     * Create a new message instance.
     */
    public function __construct(
        Incidencias $incidencia,
        string $estatusAnterior,
        string $estatusNuevo
    ) {
        $this->nombreCiudadano = $incidencia->nombre_ciudadano;
        $this->tipoIncidencia  = $incidencia->tipo_incidencia;
        $this->direccion       = $incidencia->direccion;
        $this->estatusAnterior = $estatusAnterior;
        $this->estatusNuevo    = $estatusNuevo;
        $this->folio           = str_pad($incidencia->id, 6, '0', STR_PAD_LEFT);
        $this->fechaReporte    = $incidencia->created_at?->format('d/m/Y H:i') ?? 'N/A';

        // Mensaje personalizado según el cambio de estatus
        $this->mensajeDetalle = $this->generarMensaje($estatusNuevo);
        $this->colorEstatus   = $this->colorPorEstatus($estatusNuevo);
        $this->iconoEstatus   = $this->iconoPorEstatus($estatusNuevo);
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Actualización de tu reporte #{$this->folio} — {$this->estatusNuevo}",
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
     * Genera un mensaje descriptivo según el nuevo estatus.
     */
    private function generarMensaje(string $estatus): string
    {
        return match ($estatus) {
            'en proceso'  => 'Tu reporte ha sido revisado y aceptado por nuestro equipo. Un trabajador ha sido asignado y está en camino para atender la situación. Te notificaremos cuando se resuelva.',
            'en revisión' => 'El trabajador asignado ha enviado evidencia de la atención de tu reporte. Nuestro equipo de administración está revisando la evidencia para confirmar que el problema fue resuelto correctamente.',
            'resuelto'    => '¡Excelente noticia! Tu reporte ha sido resuelto satisfactoriamente. La evidencia fue revisada y aprobada por nuestro equipo. Agradecemos tu participación ciudadana para mejorar nuestra comunidad.',
            'rechazado'   => 'Después de una revisión, tu reporte no pudo ser procesado. Esto puede deberse a que el reporte no cumple con los criterios necesarios o la información proporcionada no fue suficiente. Te invitamos a crear un nuevo reporte con más detalles.',
            default       => "El estatus de tu reporte ha sido actualizado a \"{$estatus}\".",
        };
    }

    /**
     * Devuelve el color hexadecimal según el estatus.
     */
    private function colorPorEstatus(string $estatus): string
    {
        return match ($estatus) {
            'pendiente'   => '#6B7280',
            'en proceso'  => '#0EA5E9',
            'en revisión' => '#F59E0B',
            'resuelto'    => '#10B981',
            'rechazado'   => '#EF4444',
            default       => '#6B7280',
        };
    }

    /**
     * Devuelve un emoji/icono según el estatus.
     */
    private function iconoPorEstatus(string $estatus): string
    {
        return match ($estatus) {
            'pendiente'   => '🕐',
            'en proceso'  => '🔧',
            'en revisión' => '🔍',
            'resuelto'    => '✅',
            'rechazado'   => '❌',
            default       => '📋',
        };
    }
}
