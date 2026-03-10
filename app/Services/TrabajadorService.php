<?php

namespace App\Services;

use App\Mail\IncidenciaStatusChanged;
use App\Models\Incidencias;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class TrabajadorService
{
    // ─── Consultas ────────────────────────────────────────────────────────────

    /**
     * Devuelve todas las incidencias asignadas al trabajador,
     * ordenadas por prioridad de estatus (en proceso primero).
     */
    public function misIncidencias(int $userId): \Illuminate\Support\Collection
    {
        return Incidencias::where('asignado_a', $userId)
            ->orderByRaw("CASE estatus
                WHEN 'en proceso'  THEN 1
                WHEN 'en revisión' THEN 2
                WHEN 'pendiente'   THEN 3
                WHEN 'resuelto'    THEN 4
                ELSE 5 END")
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(fn($inc) => array_merge($inc->toArray(), [
                'foto_url'         => $inc->foto         ? Storage::url($inc->foto)         : null,
                'foto_despues_url' => $inc->foto_despues ? Storage::url($inc->foto_despues) : null,
            ]));
    }

    /**
     * Devuelve las incidencias cerradas (resueltas) del trabajador
     * para mostrar en la vista de reportes.
     */
    public function cerradas(int $userId): \Illuminate\Support\Collection
    {
        return Incidencias::where('asignado_a', $userId)
            ->where('estatus', 'resuelto')
            ->orderBy('cerrado_en', 'desc')
            ->get()
            ->map(fn($inc) => array_merge($inc->toArray(), [
                'foto_url'         => $inc->foto         ? Storage::url($inc->foto)         : null,
                'foto_despues_url' => $inc->foto_despues ? Storage::url($inc->foto_despues) : null,
            ]));
    }

    // ─── Lógica de negocio ────────────────────────────────────────────────────

    /**
     * Cierra una orden: sube la foto de evidencia, guarda coordenadas
     * y cambia el estatus a 'en revisión' para que el admin lo apruebe.
     *
     * Reglas:
     *  - Solo el trabajador asignado puede cerrar la orden.
     *  - La orden debe estar en 'en proceso'.
     *  - Si había un rechazo previo se limpia al reenviar evidencia.
     *
     * @throws \RuntimeException con código HTTP sugerido
     */
    public function cerrarOrden(
        int          $id,
        int          $userId,
        UploadedFile $foto,
        float        $latCierre,
        float        $lngCierre,
        ?string      $notasCierre = null
    ): array {
        // Verificar pertenencia al trabajador
        $inc = Incidencias::where('id', $id)
            ->where('asignado_a', $userId)
            ->firstOrFail();

        // Solo se puede cerrar si está en proceso
        if ($inc->estatus !== 'en proceso') {
            throw new \RuntimeException(
                "Solo puedes cerrar órdenes que estén en proceso. Esta orden está en \"{$inc->estatus}\".",
                422
            );
        }

        $estatusAnterior = $inc->estatus;

        // Borrar foto anterior de cierre si existía (p.ej. evidencia rechazada)
        if ($inc->foto_despues) {
            Storage::disk('public')->delete($inc->foto_despues);
        }

        $path = $foto->store('incidencias/cierres', 'public');

        $inc->update([
            'foto_despues'   => $path,
            'lat_cierre'     => $latCierre,
            'lng_cierre'     => $lngCierre,
            'notas_cierre'   => $notasCierre,
            'estatus'        => 'en revisión',
            'cerrado_en'     => now(),
            'motivo_rechazo' => null,   // limpia rechazo anterior si había
        ]);

        // Notificar al ciudadano que su reporte está en revisión
        $this->notificarCambioEstatus($inc, $estatusAnterior, 'en revisión');

        return ['message' => 'Evidencia enviada. Esperando revisión del administrador.'];
    }

    // ─── Helpers privados ─────────────────────────────────────────────────────

    /**
     * Envía un correo al ciudadano cuando cambia el estatus.
     */
    private function notificarCambioEstatus(Incidencias $inc, string $anterior, string $nuevo): void
    {
        if (empty($inc->email)) {
            return;
        }

        try {
            Mail::to($inc->email)->send(
                new IncidenciaStatusChanged($inc, $anterior, $nuevo)
            );
        } catch (\Throwable $e) {
            Log::warning('No se pudo enviar correo de cambio de estatus (trabajador)', [
                'incidencia_id' => $inc->id,
                'email'         => $inc->email,
                'error'         => $e->getMessage(),
            ]);
        }
    }
}

