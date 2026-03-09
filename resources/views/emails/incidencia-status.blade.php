<x-mail::message>
{{-- Header con icono --}}
# {{ $iconoEstatus }} Actualización de tu Reporte

Hola **{{ $nombreCiudadano }}**,

Te informamos que el estatus de tu reporte ciudadano ha sido actualizado.

{{-- Tarjeta de estatus --}}
<x-mail::panel>
<div style="text-align: center;">
<span style="display: inline-block; background-color: {{ $colorEstatus }}; color: white; padding: 6px 20px; border-radius: 50px; font-weight: bold; font-size: 14px; text-transform: uppercase; letter-spacing: 1px;">
{{ $estatusNuevo }}
</span>
</div>
</x-mail::panel>

{{-- Detalles del reporte --}}
<x-mail::table>
| Dato | Detalle |
|:-----|:--------|
| **Folio** | #{{ $folio }} |
| **Tipo** | {{ $tipoIncidencia }} |
| **Ubicación** | {{ $direccion }} |
| **Fecha de reporte** | {{ $fechaReporte }} |
| **Estatus anterior** | {{ $estatusAnterior }} |
| **Nuevo estatus** | **{{ $estatusNuevo }}** |
</x-mail::table>

{{-- Mensaje personalizado --}}
### ¿Qué significa esto?

{{ $mensajeDetalle }}

{{-- Botón de acción --}}
@if($estatusNuevo !== 'rechazado')
<x-mail::button :url="config('app.url')" color="primary">
Ver mi reporte
</x-mail::button>
@endif

---

**Gracias por tu participación ciudadana.** Tu reporte ayuda a mejorar nuestra comunidad. Si tienes alguna duda, no dudes en contactarnos.

Con respeto,<br>
**{{ config('app.name') }}** — Sistema de Gestión de Incidencias Urbanas
</x-mail::message>
