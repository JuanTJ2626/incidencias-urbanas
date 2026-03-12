<x-mail::message>
{{-- Header Headline --}}
<div style="text-align: center; border-bottom: 2px solid #f5f5f7; padding-bottom: 25px; margin-bottom: 30px;">
    <h1 style="color: #1D1D1F; margin: 0; font-size: 26px; font-weight: 900; letter-spacing: -0.8px;">Actualización de Estatus</h1>
    <p style="color: #86868B; margin: 5px 0 0 0; font-size: 14px; font-weight: 600; text-transform: uppercase; letter-spacing: 1.2px;">Plataforma de Atención Ciudadana</p>
</div>

Hola **{{ $nombreCiudadano }}**,

Gracias por tu colaboración. Te informamos que tu reporte ha sido actualizado y actualmente se encuentra en el siguiente estado:

{{-- Status Card Container --}}
<div style="background-color: #f5f5f7; border-radius: 24px; padding: 35px; text-align: center; margin: 25px 0; border: 1px solid #e8e8ed; box-shadow: inset 0 2px 4px rgba(0,0,0,0.02);">
    <span style="font-size: 11px; font-weight: 800; color: #86868B; text-transform: uppercase; letter-spacing: 2px; display: block; margin-bottom: 15px;">Estado de la Solicitud</span>

    <div style="display: inline-block; background-color: {{ $colorEstatus }}; color: white; padding: 12px 32px; border-radius: 16px; font-weight: 900; font-size: 18px; text-transform: uppercase; box-shadow: 0 8px 20px {{ $colorEstatus }}50; margin-bottom: 20px;">
        {{ $iconoEstatus }} {{ $estatusNuevo }}
    </div>

    <p style="color: #1D1D1F; font-size: 16px; line-height: 1.8; margin: 0 auto; max-width: 460px; font-weight: 500;">
        {{ $mensajeDetalle }}
    </p>
</div>

{{-- Details Table --}}
### Resumen del Reporte
<x-mail::table>
| Información | Detalle registrado |
|:---|:---|
| **Número de Folio** | <strong style="color: #1D1D1F; font-family: monospace;">#{{ $folio }}</strong> |
| **Tipo de Incidencia** | {{ $tipoIncidencia }} |
| **Dirección** | {{ $direccion }} |
| **Fecha de Registro** | {{ $fechaReporte }} |
</x-mail::table>

@if($estatusNuevo !== 'rechazado')
<x-mail::button url="https://reporteurbano.site/" color="primary">
Consultar Seguimiento
</x-mail::button>
@endif

<div style="margin-top: 45px; padding: 30px; background-color: #fcfcfd; border-radius: 20px; border-left: 5px solid #1D1D1F; box-shadow: 0 4px 15px rgba(0,0,0,0.03);">
<p style="margin: 0; font-size: 14px; color: #424245; line-height: 1.6;">
    <strong>Compromiso Municipal:</strong> Estamos trabajando diariamente para resolver los reportes ciudadanos lo antes posible. Tu participación es clave para mantener nuestra ciudad en óptimas condiciones.
</p>
</div>

Atentamente,<br>
**Gobierno Municipal**<br>
<span style="color: #86868B; font-size: 12px;">Sistema de Gestión Integral de Incidencias Urbanas</span>
</x-mail::message>
