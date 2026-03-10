<?php

namespace App\Services;

use App\Mail\IncidenciaStatusChanged;
use App\Models\Incidencias;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class IncidenciaService
{
    // ─── Constantes del flujo de estados ─────────────────────────────────────

    /**
     * Transiciones válidas que puede hacer el ADMIN.
     * Formato: 'estatus_actual' => ['estatus_destino', ...]
     */
    const TRANSICIONES_ADMIN = [
        'pendiente' => ['en proceso', 'rechazado'],
    ];

    /**
     * Estados que bloquean la asignación de trabajador.
     */
    const BLOQUEADOS_ASIGNACION = ['rechazado', 'resuelto', 'en revisión'];

    /**
     * Estados finales: no se pueden modificar más.
     */
    const ESTADOS_FINALES = ['rechazado', 'resuelto'];

    // ─── Formateo ─────────────────────────────────────────────────────────────

    /**
     * Formatea una incidencia para enviar al frontend vía Inertia.
     */
    public function formatear(Incidencias $inc): array
    {
        return [
            'id'               => $inc->id,
            'nombre_ciudadano' => $inc->nombre_ciudadano,
            'email'            => $inc->email,
            'direccion'        => $inc->direccion,
            'tipo_incidencia'  => $inc->tipo_incidencia,
            'descripcion'      => $inc->descripcion,
            'estatus'          => $inc->estatus,
            'foto'             => $inc->foto,
            'latitud'          => $inc->latitud  ? (float) $inc->latitud  : null,
            'longitud'         => $inc->longitud ? (float) $inc->longitud : null,
            'asignado_a'       => $inc->asignado_a,
            'trabajador_nombre'=> $inc->trabajador?->name,
            'created_at'       => $inc->created_at?->format('d/m/Y H:i'),
        ];
    }

    /**
     * Devuelve todas las incidencias formateadas (con relación trabajador).
     */
    public function listar(): \Illuminate\Support\Collection
    {
        return Incidencias::with('trabajador')
            ->latest()
            ->get()
            ->map(fn($inc) => $this->formatear($inc));
    }

    /**
     * Devuelve los trabajadores activos disponibles para asignar.
     */
    public function trabajadoresDisponibles(): \Illuminate\Database\Eloquent\Collection
    {
        return User::whereIn('rol', ['trabajador', 'contratista', 'worker'])
            ->where('activo', true)
            ->select('id', 'name', 'email', 'rol')
            ->get();
    }

    // ─── CRUD ─────────────────────────────────────────────────────────────────

    /**
     * Crea una nueva incidencia. Guarda la foto si se proporciona.
     */
    public function crear(array $datos, ?UploadedFile $foto = null): Incidencias
    {
        if ($foto) {
            $datos['foto'] = $foto->store('incidencias', 'public');
        }

        return Incidencias::create($datos);
    }

    /**
     * Actualiza una incidencia. Reemplaza la foto si se proporciona una nueva.
     */
    public function actualizar(int $id, array $datos, ?UploadedFile $foto = null): Incidencias
    {
        $inc = Incidencias::findOrFail($id);

        if ($foto) {
            // Borrar foto anterior si existe
            if ($inc->foto) {
                Storage::disk('public')->delete($inc->foto);
            }
            $datos['foto'] = $foto->store('incidencias', 'public');
        } else {
            unset($datos['foto']);
        }

        $inc->update($datos);

        return $inc;
    }

    /**
     * Elimina una incidencia y sus archivos asociados del storage.
     */
    public function eliminar(int $id): void
    {
        $inc = Incidencias::findOrFail($id);

        if ($inc->foto) {
            Storage::disk('public')->delete($inc->foto);
        }
        if ($inc->foto_despues) {
            Storage::disk('public')->delete($inc->foto_despues);
        }

        $inc->delete();
    }

    // ─── Lógica de negocio: flujo de estados ──────────────────────────────────

    /**
     * Cambia el estatus de una incidencia respetando el flujo de negocio.
     *
     * Reglas:
     *  - pendiente → en proceso  : admin activa la incidencia
     *  - pendiente → rechazado   : admin rechaza el reporte; la incidencia se ELIMINA
     *  - cualquier otro caso     : lanza excepción con mensaje descriptivo
     *
     * @throws \RuntimeException con código HTTP sugerido
     * @return array ['message', 'deleted' => bool]
     */
    public function cambiarEstatus(int $id, string $nuevo): array
    {
        $inc    = Incidencias::findOrFail($id);
        $actual = $inc->estatus;

        // Verificar transición permitida
        $permitida = isset(self::TRANSICIONES_ADMIN[$actual])
            && in_array($nuevo, self::TRANSICIONES_ADMIN[$actual]);

        if (! $permitida) {
            throw new \RuntimeException($this->mensajeTransicionInvalida($actual, $nuevo), 422);
        }

        // Rechazar → eliminar completamente del sistema
        if ($nuevo === 'rechazado') {
            // Notificar ANTES de eliminar (necesitamos los datos)
            $this->notificarCambioEstatus($inc, $actual, 'rechazado');

            if ($inc->foto) {
                Storage::disk('public')->delete($inc->foto);
            }
            $inc->delete();

            return [
                'message' => 'Incidencia rechazada y eliminada del sistema.',
                'deleted' => true,
            ];
        }

        // Cualquier otra transición permitida
        $inc->update(['estatus' => $nuevo]);

        // Notificar al ciudadano
        $this->notificarCambioEstatus($inc, $actual, $nuevo);

        return [
            'message' => "Estatus actualizado a \"$nuevo\".",
            'deleted' => false,
        ];
    }

    /**
     * Revisa la evidencia de cierre enviada por el trabajador.
     *
     * Solo aplicable en estatus 'en revisión'.
     *  - aprobar  → resuelto
     *  - rechazar → en proceso (trabajador debe corregir)
     *
     * @throws \RuntimeException
     */
    public function revisarCierre(int $id, string $accion, ?string $motivoRechazo = null): array
    {
        $inc = Incidencias::findOrFail($id);

        if ($inc->estatus !== 'en revisión') {
            throw new \RuntimeException(
                'Solo se puede revisar una incidencia en estado "en revisión". ' .
                'El trabajador debe haber enviado su evidencia primero.',
                422
            );
        }

        $estatusAnterior = $inc->estatus;

        if ($accion === 'aprobar') {
            $inc->update([
                'estatus'        => 'resuelto',
                'motivo_rechazo' => null,
            ]);

            // Notificar al ciudadano que su reporte fue resuelto
            $this->notificarCambioEstatus($inc, $estatusAnterior, 'resuelto');

            return ['message' => 'Orden aprobada y marcada como resuelta.'];
        }

        // rechazar evidencia → vuelve a en proceso
        $inc->update([
            'estatus'        => 'en proceso',
            'motivo_rechazo' => $motivoRechazo,
            'foto_despues'   => null,
            'cerrado_en'     => null,
        ]);

        // Notificar al ciudadano que sigue en proceso
        $this->notificarCambioEstatus($inc, $estatusAnterior, 'en proceso');

        return ['message' => 'Evidencia rechazada. El trabajador deberá corregir y reenviar.'];
    }

    /**
     * Asigna un trabajador a una incidencia y la pone en proceso.
     *
     * Reglas:
     *  - No se permite asignar si está en: rechazado, resuelto, en revisión
     *  - El trabajador debe estar activo
     *
     * @throws \RuntimeException
     */
    public function asignarTrabajador(int $id, int $trabajadorId): array
    {
        $inc = Incidencias::findOrFail($id);
        $estatusAnterior = $inc->estatus;

        if (in_array($inc->estatus, self::BLOQUEADOS_ASIGNACION)) {
            throw new \RuntimeException(
                "No se puede asignar un trabajador a una incidencia con estatus \"{$inc->estatus}\".",
                422
            );
        }

        $trabajador = User::where('id', $trabajadorId)
            ->where('activo', true)
            ->first();

        if (! $trabajador) {
            throw new \RuntimeException(
                'El trabajador seleccionado no existe o está inactivo.',
                422
            );
        }

        $inc->update([
            'asignado_a' => $trabajador->id,
            'estatus'    => 'en proceso',
        ]);

        // Notificar al ciudadano que su reporte fue aceptado y asignado
        $this->notificarCambioEstatus($inc, $estatusAnterior, 'en proceso');

        return [
            'message'           => "Trabajador \"{$trabajador->name}\" asignado correctamente.",
            'trabajador_nombre' => $trabajador->name,
            'estatus'           => 'en proceso',
        ];
    }

    // ─── Helpers privados ─────────────────────────────────────────────────────

    /**
     * Envía un correo electrónico al ciudadano cuando cambia el estatus.
     * Solo envía si la incidencia tiene un email válido.
     */
    private function notificarCambioEstatus(Incidencias $inc, string $anterior, string $nuevo): void
    {
        // Solo enviar si hay email del ciudadano
        if (empty($inc->email)) {
            return;
        }

        try {
            Mail::to($inc->email)->send(
                new IncidenciaStatusChanged($inc, $anterior, $nuevo)
            );
        } catch (\Throwable $e) {
            // Log del error pero NO detenemos el flujo principal
            Log::warning('No se pudo enviar correo de cambio de estatus', [
                'incidencia_id' => $inc->id,
                'email'         => $inc->email,
                'error'         => $e->getMessage(),
            ]);
        }
    }

    /**
     * Genera un mensaje de error descriptivo según el estatus actual.
     */
    private function mensajeTransicionInvalida(string $actual, string $nuevo): string
    {
        if (in_array($actual, self::ESTADOS_FINALES)) {
            return "Una incidencia con estatus \"$actual\" es un estado final y no puede modificarse.";
        }

        if ($actual === 'en proceso') {
            return 'Una incidencia en proceso solo puede cerrarse por el trabajador asignado.';
        }

        if ($actual === 'en revisión') {
            return 'Esta incidencia está pendiente de revisión de evidencia. Usa "Aprobar" o "Rechazar evidencia".';
        }

        return "La transición de \"$actual\" a \"$nuevo\" no está permitida.";
    }
}
