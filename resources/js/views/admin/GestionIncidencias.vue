<template>
  <div class="animate-fade-in py-6 pl-4 pr-6">
    <PageHeader
      title="Gestión de Incidencias"
      subtitle="Valida, aprueba, rechaza y asigna órdenes de trabajo a los trabajadores."
    >
      <template #actions>
        <div class="flex flex-col md:flex-row items-center gap-3 w-full md:w-auto">
          <div class="relative w-full md:w-64 group">
            <i class="pi pi-search absolute left-4 top-1/2 -translate-y-1/2 text-[#86868B] dark:text-[#A1A1A6]"></i>
            <InputText
              v-model="searchQuery"
              placeholder="Filtrar incidencias..."
              class="!w-full !rounded-2xl !border-transparent !bg-app-secondary dark:!bg-white/5 !pl-11 !py-2.5 !text-xs !font-bold focus:!bg-white dark:focus:!bg-white/10 focus:!border-app-border transition-all"
            />
          </div>
          <button
            @click="crearVisible = true"
            class="w-full md:w-auto flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl bg-brand-gradient text-white text-sm font-black hover:scale-105 active:scale-95 transition-all shadow-xl shadow-brand-red/20"
          ><i class="pi pi-plus"></i> Nueva IncidenciaAA</button>
        </div>
      </template>
    </PageHeader>

    <StatsGrid :data="localData" groupBy="estatus" gap="gap-4" mb="mb-6" />

    <!-- Tabla Genérica Premium -->
    <GenericPremiumTable
      title="Listado de Reportes"
      :subtitle="`${filteredData.length} registros encontrados`"
      icon="pi pi-list"
      :columns="[
        { key: 'id', label: 'ID', width: '80px' },
        { key: 'tipo_incidencia', label: 'Incidencia' },
        { key: 'direccion', label: 'Ubicación' },
        { key: 'trabajador_nombre', label: 'Asignado' },
        { key: 'estatus', label: 'Estado', width: '130px' },
        { key: 'actions', label: 'Acciones', width: '220px' }
      ]"
      :data="filteredData"
      :loading="loading"
      selectable
      v-model:selection="selectedIncidencias"
    >
      <!-- Eliminadas acciones masivas de borrado por flujo de rechazo -->
      <!-- Slot ID -->
      <template #cell-id="{ value }">
        <span class="text-xs font-black text-[#86868B] dark:text-[#A1A1A6]">#{{ value }}</span>
      </template>

      <!-- Slot Incidencia -->
      <template #cell-tipo_incidencia="{ row }">
        <div class="flex items-center gap-3">
          <div class="w-8 h-8 rounded-lg bg-app-secondary flex items-center justify-center shrink-0">
            <i :class="[iconoCategoria(row.tipo_incidencia), 'text-brand-red text-xs']"></i>
          </div>
          <div class="flex flex-col">
            <span class="font-bold text-[#1D1D1F] dark:text-white capitalize leading-tight">{{ row.tipo_incidencia }}</span>
            <span class="text-[10px] text-[#86868B] dark:text-[#A1A1A6] font-medium">{{ row.nombre_ciudadano }}</span>
          </div>
        </div>
      </template>

      <!-- Slot Ubicación -->
      <template #cell-direccion="{ value }">
        <div class="max-w-[200px] truncate text-xs font-medium text-[#4A4A4D] dark:text-[#A1A1A6]" :title="value">
           <i class="pi pi-map-marker text-[10px] mr-1 text-brand-red"></i>{{ value }}
        </div>
      </template>

      <!-- Slot Asignado -->
      <template #cell-trabajador_nombre="{ value }">
        <div v-if="value" class="flex items-center gap-2 text-xs font-bold text-sky-600 dark:text-sky-400 bg-sky-50 dark:bg-sky-500/10 px-2 py-1 rounded-lg border border-sky-100 dark:border-sky-500/20 w-fit">
           <i class="pi pi-user text-[10px]"></i> {{ value }}
        </div>
        <span v-else class="text-[10px] text-gray-400 dark:text-gray-500 italic font-medium">Esperando...</span>
      </template>

      <!-- Slot Estado -->
      <template #cell-estatus="{ value }">
         <span :class="['text-[10px] font-black uppercase tracking-widest px-2.5 py-1 rounded-full border shadow-sm', badgeClass(value)]">
            {{ value }}
         </span>
      </template>

      <!-- Slot Acciones -->
      <template #cell-actions="{ row }">
        <div class="flex items-center gap-1.5">
          <!-- DETALLE -->
          <button @click="abrirDetalle(row)" :disabled="loadingId === row.id" class="w-8 h-8 rounded-lg bg-app-secondary hover:bg-app-border dark:hover:bg-white/10 transition-all flex items-center justify-center text-[#1D1D1F] dark:text-white" title="Ver Detalle">
             <i class="pi pi-eye text-xs"></i>
          </button>
          
          <!-- APROBAR (Solo en revisión) -->
          <button 
            v-if="row.estatus === 'en revisión'"
            @click="handleAprobarCierre(row.id)" 
            :disabled="loadingId === row.id" 
            class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 border border-emerald-100 dark:bg-emerald-500/10 dark:text-emerald-400 dark:border-emerald-500/20 hover:bg-emerald-500 hover:text-white transition-all flex items-center justify-center" 
            title="Aprobar Reporte"
          >
            <i v-if="loadingId === row.id" class="pi pi-spinner pi-spin text-[10px]"></i>
            <i v-else class="pi pi-check text-xs"></i>
          </button>

          <!-- ASIGNAR (Solo pendiente o en proceso) -->
          <button 
            v-if="['pendiente', 'en proceso'].includes(row.estatus)"
            @click="asignarInc = row; asignarVisible = true" 
            :disabled="loadingId === row.id" 
            class="w-8 h-8 rounded-lg bg-amber-50 text-amber-600 border border-amber-100 dark:bg-amber-500/10 dark:text-amber-400 dark:border-amber-500/20 hover:bg-amber-100 transition-all flex items-center justify-center" 
            title="Asignar"
          >
            <i v-if="loadingId === row.id" class="pi pi-spinner pi-spin text-[10px]"></i>
            <i v-else class="pi pi-user-plus text-xs"></i>
          </button>

          <!-- EDITAR -->
          <button @click="abrirEditar(row)" :disabled="loadingId === row.id" class="w-8 h-8 rounded-lg bg-app-secondary hover:bg-app-border dark:hover:bg-white/10 transition-all flex items-center justify-center text-[#1D1D1F] dark:text-white" title="Editar">
             <i class="pi pi-pencil text-xs"></i>
          </button>
        </div>
      </template>
    </GenericPremiumTable>

    <!-- ─── Dialogs ──────────────────────────────────────────── -->
    <DetalleIncidenciaDialog
      v-model="detalleVisible"
      :inc="detalleInc"
      @cambiar-estatus="(id, est) => { handleCambiarEstatus(id, est); detalleVisible = false }"
      @asignar="(inc) => { asignarInc = inc; asignarVisible = true; detalleVisible = false }"
    />

    <RechazoEvidenciaDialog
      v-model="rechazoVisible"
      :inc="rechazoInc"
      @confirmed="handleRechazoConfirmado"
    />

    <AsignarTrabajadorDialog
      v-model="asignarVisible"
      :inc="asignarInc"
      :trabajadores="trabajadores"
      @confirmed="handleAsignarConfirmado"
    />

    <!-- CRUD modals -->
    <IncidenciaFormModal
      v-model:visible="crearVisible"
      mode="create"
      @saved="onIncidenciaCreada"
    />
    <IncidenciaFormModal
      v-model:visible="editarVisible"
      mode="edit"
      :incidencia="editarInc"
      @saved="onIncidenciaEditada"
    />

    <Toast />
  </div>
</template>

<script>
import StatsGrid                from '@/Components/StatsGrid.vue'
import GenericPremiumTable      from '@/Components/GenericPremiumTable.vue'
import IncidenciaFormModal      from '@/Components/IncidenciaFormModal.vue'
import DetalleIncidenciaDialog  from '@/Components/DetalleIncidenciaDialog.vue'
import RechazoEvidenciaDialog   from '@/Components/RechazoEvidenciaDialog.vue'
import AsignarTrabajadorDialog  from '@/Components/AsignarTrabajadorDialog.vue'
import { useIncidenciasApi }    from '@/composables/useIncidenciasApi.js'
import InputText                from 'primevue/inputtext'

const api = useIncidenciasApi()

export default {
  components: {
    GenericPremiumTable, StatsGrid, IncidenciaFormModal,
    DetalleIncidenciaDialog, RechazoEvidenciaDialog, AsignarTrabajadorDialog,
    InputText
  },

  props: {
    incidencias:  { type: Array, default: () => [] },
    trabajadores: { type: Array, default: () => [] },
  },

  data() {
    return {
      localData: [],
      loading: false,
      loadingId: null,
      // Detalle
      detalleVisible: false,
      detalleInc: null,
      // Rechazo
      rechazoVisible: false,
      rechazoInc: null,
      // Asignar
      asignarVisible: false,
      asignarInc: null,
      // Crear / Editar
      crearVisible: false,
      editarVisible: false,
      editarInc: null,
      searchQuery: '',
      selectedIncidencias: [],
    }
  },

  computed: {
    filteredData() {
      if (!this.searchQuery) return this.localData
      const q = this.searchQuery.toLowerCase()
      return this.localData.filter(i => 
        i.tipo_incidencia?.toLowerCase().includes(q) ||
        i.direccion?.toLowerCase().includes(q) ||
        i.nombre_ciudadano?.toLowerCase().includes(q) ||
        i.trabajador_nombre?.toLowerCase().includes(q) ||
        i.id.toString().includes(q)
      )
    },
  },

  mounted() {
    this.localData = [...this.incidencias]
  },

  methods: {
    // ─── UI helpers ─────────────────────────────────────────
    iconoCategoria(nombre) {
      const n = (nombre || '').toLowerCase()
      if (n.includes('bache') || n.includes('paviment')) return 'pi pi-exclamation-triangle'
      if (n.includes('alumbr') || n.includes('luz')) return 'pi pi-sun'
      if (n.includes('basura') || n.includes('residuo')) return 'pi pi-trash'
      if (n.includes('agua') || n.includes('fuga')) return 'pi pi-tint'
      if (n.includes('árbol') || n.includes('verde')) return 'pi pi-apple'
      if (n.includes('graffiti') || n.includes('vand')) return 'pi pi-palette'
      if (n.includes('ruido') || n.includes('sonido')) return 'pi pi-volume-up'
      return 'pi pi-map-marker'
    },

    badgeClass(estatus) {
      const m = {
        'pendiente':   'bg-gray-100 text-gray-600 border-gray-200 dark:bg-white/5 dark:text-gray-400 dark:border-white/10',
        'en proceso':  'bg-sky-50 text-sky-700 border-sky-200 dark:bg-sky-500/10 dark:text-sky-400 dark:border-sky-500/20',
        'en revisión': 'bg-amber-50 text-amber-700 border-amber-200 dark:bg-amber-500/10 dark:text-amber-400 dark:border-amber-500/20',
        'resuelto':    'bg-emerald-50 text-emerald-700 border-emerald-200 dark:bg-emerald-500/10 dark:text-emerald-400 dark:border-emerald-500/20',
        'rechazado':   'bg-rose-50 text-rose-700 border-rose-200 dark:bg-rose-500/10 dark:text-rose-400 dark:border-rose-500/20',
      }
      return m[estatus] || 'bg-gray-100 text-gray-600'
    },

    abrirDetalle(inc) {
      this.detalleInc = { ...inc }
      this.detalleVisible = true
    },

    abrirEditar(inc) {
      this.editarInc = { ...inc }
      this.editarVisible = true
    },

    actualizarLocal(id, campos) {
      const idx = this.localData.findIndex(i => i.id === id)
      if (idx !== -1) this.localData.splice(idx, 1, { ...this.localData[idx], ...campos })
    },

    // ─── API handlers ───────────────────────────────────────
    async handleCambiarEstatus(id, estatus) {
      if (!confirm(`¿Seguro que quieres cambiar el estatus a "${estatus}"?`)) return
      this.loadingId = id
      try {
        const data = await api.cambiarEstatus(id, estatus)
        if (data?.deleted) {
          this.localData = this.localData.filter(i => i.id !== id)
          this.$toast.add({ severity: 'info', summary: 'Rechazada y eliminada', detail: 'La incidencia fue rechazada y eliminada del sistema.', life: 4000 })
        } else {
          this.actualizarLocal(id, { estatus })
          this.$toast.add({ severity: 'success', summary: 'Listo', detail: `Estatus actualizado a "${estatus}"`, life: 3000 })
        }
      } catch (e) {
        const msg = e.response?.data?.message || 'No se pudo actualizar el estatus.'
        this.$toast.add({ severity: 'warn', summary: 'No permitido', detail: msg, life: 5000 })
      } finally {
        this.loadingId = null
      }
    },

    async handleAprobarCierre(id) {
      if (!confirm('¿Seguro que quieres marcar este reporte como "Resuelto"? Esta acción es definitiva.')) return
      this.loadingId = id
      try {
        await api.aprobarCierre(id)
        this.actualizarLocal(id, { estatus: 'resuelto' })
        this.$toast.add({ severity: 'success', summary: '¡Aprobado!', detail: 'Orden marcada como resuelta.', life: 3000 })
      } catch (e) {
        const msg = e.response?.data?.message || 'No se pudo aprobar el cierre.'
        this.$toast.add({ severity: 'warn', summary: 'No permitido', detail: msg, life: 5000 })
      } finally {
        this.loadingId = null
      }
    },

    async handleRechazoConfirmado({ incId, motivo, done }) {
      try {
        await api.rechazarCierre(incId, motivo)
        this.actualizarLocal(incId, { estatus: 'en proceso', motivo_rechazo: motivo })
        this.$toast.add({ severity: 'warn', summary: 'Evidencia rechazada', detail: 'El trabajador deberá corregir y reenviar.', life: 4000 })
        done()
      } catch (e) {
        done(e.response?.data?.message || 'Error al rechazar. Intenta de nuevo.')
      }
    },

    async handleAsignarConfirmado({ incId, trabajadorId, notaAdmin, done }) {
      try {
        const data = await api.asignarTrabajador(incId, trabajadorId, notaAdmin)
        const nombre = data?.trabajador_nombre
          ?? this.trabajadores.find(t => t.id === trabajadorId)?.name
          ?? '—'
        this.actualizarLocal(incId, { asignado_a: trabajadorId, trabajador_nombre: nombre, estatus: 'en proceso', nota_admin: notaAdmin })
        this.$toast.add({ severity: 'success', summary: 'Asignado', detail: `Trabajador: ${nombre}`, life: 3000 })
        done()
      } catch (e) {
        done(e.response?.data?.message || 'Error al asignar. Verifica que el trabajador esté activo.')
      }
    },

    async eliminarIncidencia(inc) {
      if (!confirm(`¿Eliminar la incidencia #${inc.id} "${inc.tipo_incidencia}"? Esta acción no se puede deshacer.`)) return
      this.loadingId = inc.id
      try {
        await api.eliminar(inc.id)
        this.localData = this.localData.filter(i => i.id !== inc.id)
        this.$toast.add({ severity: 'info', summary: 'Eliminada', detail: `Incidencia #${inc.id} eliminada.`, life: 3000 })
      } catch {
        this.$toast.add({ severity: 'error', summary: 'Error', detail: 'No se pudo eliminar.', life: 3000 })
      } finally {
        this.loadingId = null
      }
    },

    async bulkDeleteIncidencias(items) {
      if (!confirm(`¿Estás seguro de eliminar las ${items.length} incidencias seleccionadas?`)) return
      this.loading = true
      try {
        const ids = items.map(i => i.id)
        await api.eliminarMasivo(ids)
        this.localData = this.localData.filter(i => !ids.includes(i.id))
        this.selectedIncidencias = []
        this.$toast.add({ severity: 'success', summary: '¡Éxito!', detail: `${items.length} incidencias eliminadas.`, life: 4000 })
      } catch (e) {
        this.$toast.add({ severity: 'error', summary: 'Error', detail: 'No se pudo completar el borrado masivo.', life: 4000 })
      } finally {
        this.loading = false
      }
    },

    onIncidenciaCreada() {
      window.location.reload()
    },

    onIncidenciaEditada(inc) {
      if (inc) {
        this.actualizarLocal(inc.id, inc)
      } else {
        window.location.reload()
      }
    },
  },
}
</script>

<style scoped>
@keyframes fadeIn { from { opacity:0; transform:translateY(8px) } to { opacity:1; transform:translateY(0) } }
.animate-fade-in { animation: fadeIn .35s ease-out forwards }
</style>
