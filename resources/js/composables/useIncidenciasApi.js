/**
 * Composable que centraliza las llamadas a la API de incidencias
 * y la gestión del estado de carga para el módulo de administración.
 */
export function useIncidenciasApi(toast) {
    const headers = () => ({})

    /**
     * Cambia el estatus de una incidencia.
     * @returns {Promise<{ deleted?: boolean }>}
     */
    async function cambiarEstatus(id, estatus) {
        const res = await axios.patch(`/admin/incidencias/${id}/estatus`, { estatus }, { headers: headers() })
        return res.data
    }

    /**
     * Aprueba el cierre de una incidencia en revisión.
     */
    async function aprobarCierre(id) {
        await axios.patch(`/admin/incidencias/${id}/revisar`, { accion: 'aprobar' }, { headers: headers() })
    }

    /**
     * Rechaza evidencia de un trabajador con motivo.
     */
    async function rechazarCierre(id, motivoRechazo) {
        await axios.patch(`/admin/incidencias/${id}/revisar`, {
            accion: 'rechazar',
            motivo_rechazo: motivoRechazo,
        }, { headers: headers() })
    }

    /**
     * Asigna un trabajador a una incidencia.
     * @returns {{ trabajador_nombre?: string }}
     */
    async function asignarTrabajador(incId, trabajadorId, notaAdmin = null) {
        const res = await axios.patch(`/admin/incidencias/${incId}/asignar`, {
            asignado_a: trabajadorId,
            nota_admin: notaAdmin
        }, { headers: headers() })
        return res.data
    }

    /**
     * Elimina una incidencia.
     */
    async function eliminar(id) {
        await axios.delete(`/admin/incidencias/${id}`, { headers: headers() })
    }

    /**
     * Elimina múltiples incidencias.
     */
    async function eliminarMasivo(ids) {
        await axios.post(`/admin/incidencias/bulk-delete`, { ids }, { headers: headers() })
    }

    return {
        cambiarEstatus,
        aprobarCierre,
        rechazarCierre,
        asignarTrabajador,
        eliminar,
        eliminarMasivo,
    }
}
