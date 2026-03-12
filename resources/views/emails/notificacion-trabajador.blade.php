<x-mail::message>
{{-- Header Headline --}}
<div style="text-align: center; border-bottom: 2px solid #f5f5f7; padding-bottom: 20px; margin-bottom: 30px;">
<h1 style="color: #1D1D1F; margin: 0; font-size: 24px; font-weight: 900; letter-spacing: -0.7px;">
    {{ $tipoNotificacion === 'asignacion' ? 'Nueva Tarea Asignada' : 'Atención: Tarea Rechazada' }}
</h1>
<p style="color: #86868B; margin: 4px 0 0 0; font-size: 13px; font-weight: 600; text-transform: uppercase; letter-spacing: 1px;">Gestión Operativa de Incidencias</p>
</div>

Hola **{{ $incidencia->trabajador->name }}**,

{{ $tipoNotificacion === 'asignacion' 
    ? 'Se te ha asignado una nueva orden de trabajo que requiere tu atención.' 
    : 'La evidencia que enviaste para la siguiente tarea ha sido rechazada por el administrador.' }}

{{-- Notification Card --}}
<div style="background-color: #f5f5f7; border-radius: 20px; padding: 30px; text-align: center; margin: 25px 0; border: 1px solid #e8e8ed;">
<span style="font-size: 10px; font-weight: 800; color: #86868B; text-transform: uppercase; letter-spacing: 2px; display: block; margin-bottom: 12px;">Acción Requerida</span>

<div style="display: inline-block; background-color: {{ $tipoNotificacion === 'asignacion' ? '#0071E3' : '#FF3B30' }}; color: white; padding: 10px 28px; border-radius: 14px; font-weight: 900; font-size: 17px; text-transform: uppercase; box-shadow: 0 6px 15px {{ $tipoNotificacion === 'asignacion' ? '#0071E3' : '#FF3B30' }}40;">
    {{ $tipoNotificacion === 'asignacion' ? '📌 Revisar Tarea' : '⚠️ Corregir Evidencia' }}
</div>

@if($notaAdmin || $incidencia->motivo_rechazo)
<p style="color: #1D1D1F; font-size: 15px; line-height: 1.7; margin: 20px auto 0 auto; max-width: 440px; font-weight: 600; border-left: 3px solid {{ $tipoNotificacion === 'asignacion' ? '#0071E3' : '#FF3B30' }}; padding-left: 15px; text-align: left;">
    <strong>Nota del Admin:</strong><br>
    {{ $tipoNotificacion === 'asignacion' ? $notaAdmin : $incidencia->motivo_rechazo }}
</p>
@endif
</div>

### Detalles de la Incidencia
<x-mail::table>
| Información | Detalle registrado |
|:---|:---|
| **# Folio** | <strong style="color: #1D1D1F;">#{{ $incidencia->id }}</strong> |
| **Categoría** | {{ $incidencia->tipo_incidencia }} |
| **Ubicación** | {{ $incidencia->direccion }} |
| **Prioridad** | Alta |
</x-mail::table>

<x-mail::button url="https://incidencias-urbanas-production.up.railway.app/login" color="primary">
Ver en mi Tablero de Tareas
</x-mail::button>

<div style="margin-top: 40px; padding: 25px; background-color: #fcfcfd; border-radius: 16px; border-left: 4px solid #1D1D1F;">
<p style="margin: 0; font-size: 13px; color: #424245; line-height: 1.5;">
<strong>Instrucciones:</strong> Recuerda que debes confirmar tu ubicación al llegar al sitio y tomar una fotografía clara del trabajo finalizado para cerrar la orden.
</p>
</div>

Atentamente,<br>
**Panel de Administración**<br>
<span style="color: #86868B; font-size: 11px;">Sistema de Gestión de Infraestructura Urbana</span>
</x-mail::message>
