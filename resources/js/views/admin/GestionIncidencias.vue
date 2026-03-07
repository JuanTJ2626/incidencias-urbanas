<template>
  <div class="animate-fade-in py-6 pl-4 pr-6">
    <PageHeader
      title="Gestión de Incidencias"
      subtitle="Valida, aprueba, rechaza y asigna órdenes de trabajo a los trabajadores."
    >
      <template #actions>
        <button
          @click="abrirCrear"
          class="flex items-center gap-2 px-4 py-2.5 rounded-xl bg-[#1D1D1F] text-white text-sm font-bold hover:bg-black transition shadow-sm"
        ><i class="pi pi-plus"></i> Nueva Incidencia</button>
      </template>
    </PageHeader>

    <!-- Stats KPI: auto-generadas desde los datos crudos, sin ningún hardcode -->
    <StatsGrid :data="localData" groupBy="estatus" gap="gap-4" mb="mb-6" />

    <!-- AutoTable: columnas auto-detectadas, sin hardcodear ningún campo -->
    <div class="bg-white border border-[#E8E8ED] rounded-2xl shadow-sm overflow-hidden">
      <AutoTable
        :value="localData"
        :rows="10"
        :loading="loading"
        :excludeColumns="['asignado_a', 'created_at', 'updated_at', 'latitud', 'longitud']"
        actionsHeader="Acciones"
        actionsWidth="320px"
      >
        <template #title>Listado de Reportes</template>

        <!-- Toolbar con contador + botón nueva incidencia -->
        <template #toolbar>
          <span class="text-xs font-semibold text-[#86868B] bg-gray-50 px-3 py-1.5 rounded-lg border border-gray-200">
            {{ localData.length }} registros
          </span>
        </template>

        <!-- Override celda: trabajador_nombre -->
        <template #col-trabajador_nombre="{ data }">
          <span v-if="data.trabajador_nombre" class="flex items-center gap-1.5 text-sm font-medium text-sky-700">
            <i class="pi pi-user text-xs"></i>{{ data.trabajador_nombre }}
          </span>
          <span v-else class="text-xs text-gray-400 italic">Sin asignar</span>
        </template>

        <!-- Columna de acciones inyectada vía slot -->
        <template #actions="{ data }">
          <div class="flex items-center flex-wrap gap-1.5">

            <!-- Ver detalles siempre visible -->
            <button
              @click="abrirDetalle(data)"
              class="px-2.5 py-1 rounded-lg text-xs font-bold bg-[#F5F5F7] text-[#1D1D1F] border border-[#E8E8ED] hover:bg-gray-200 transition flex items-center gap-1"
            ><i class="pi pi-eye"></i></button>

            <!-- Editar -->
            <button
              @click="abrirEditar(data)"
              class="px-2.5 py-1 rounded-lg text-xs font-bold bg-[#F5F5F7] text-[#1D1D1F] border border-[#E8E8ED] hover:bg-gray-200 transition flex items-center gap-1"
            ><i class="pi pi-pencil"></i></button>

            <!-- Eliminar -->
            <button
              @click="eliminarIncidencia(data)"
              :disabled="loadingId === data.id"
              class="px-2.5 py-1 rounded-lg text-xs font-bold bg-rose-50 text-rose-500 border border-rose-200 hover:bg-rose-100 transition disabled:opacity-50 flex items-center gap-1"
            ><i class="pi pi-trash"></i></button>

            <!-- EN REVISIÓN: admin decide si aprobar o rechazar -->
            <template v-if="data.estatus === 'en revisión'">
              <button
                @click="aprobarCierre(data.id)"
                :disabled="loadingId === data.id"
                class="px-2.5 py-1 rounded-lg text-xs font-bold bg-emerald-500 text-white border border-emerald-600 hover:bg-emerald-600 transition disabled:opacity-50 flex items-center gap-1"
              ><i class="pi pi-check-circle"></i> Aprobar</button>

              <button
                @click="abrirRechazo(data)"
                :disabled="loadingId === data.id"
                class="px-2.5 py-1 rounded-lg text-xs font-bold bg-rose-50 text-rose-600 border border-rose-200 hover:bg-rose-100 transition disabled:opacity-50 flex items-center gap-1"
              ><i class="pi pi-times-circle"></i> Rechazar</button>
            </template>

            <!-- OTROS ESTATUSES -->
            <template v-else>
              <button
                v-if="data.estatus === 'pendiente'"
                @click="cambiarEstatus(data.id, 'en proceso')"
                :disabled="loadingId === data.id"
                class="px-2.5 py-1 rounded-lg text-xs font-bold bg-sky-50 text-sky-600 border border-sky-200 hover:bg-sky-100 transition disabled:opacity-50"
              ><i class="pi pi-play mr-1"></i>En proceso</button>

              <button
                v-if="data.estatus !== 'rechazado'"
                @click="cambiarEstatus(data.id, 'rechazado')"
                :disabled="loadingId === data.id"
                class="px-2.5 py-1 rounded-lg text-xs font-bold bg-rose-50 text-rose-600 border border-rose-200 hover:bg-rose-100 transition disabled:opacity-50"
              ><i class="pi pi-times mr-1"></i>Rechazar</button>

              <button
                @click="abrirAsignar(data)"
                class="px-2.5 py-1 rounded-lg text-xs font-bold bg-amber-50 text-amber-600 border border-amber-200 hover:bg-amber-100 transition"
              ><i class="pi pi-user-plus mr-1"></i>Asignar</button>
            </template>
          </div>
        </template>
      </AutoTable>
    </div>

    <!-- Dialog: Ver detalles del reporte (foto + mapa OSM original) -->
    <Dialog
      v-model:visible="detalleVisible"
      :header="detalleInc ? `${detalleInc.tipo_incidencia} — #${detalleInc.id}` : 'Detalle'"
      :modal="true" :style="{ width: '780px', maxWidth: '96vw' }" :draggable="false"
    >
      <div v-if="detalleInc" class="flex flex-col gap-5 py-1">
        <!-- Ciudadano -->
        <div class="grid grid-cols-2 gap-4 text-sm">
          <div>
            <p class="text-[10px] font-black text-[#86868B] uppercase tracking-widest mb-1">Ciudadano</p>
            <p class="font-semibold text-[#1D1D1F]">{{ detalleInc.nombre_ciudadano }}</p>
            <p class="text-xs text-[#86868B]">{{ detalleInc.email }}</p>
          </div>
          <div>
            <p class="text-[10px] font-black text-[#86868B] uppercase tracking-widest mb-1">Fecha reporte</p>
            <p class="font-semibold text-[#1D1D1F]">{{ detalleInc.created_at }}</p>
            <span :class="['inline-block mt-1 text-[10px] font-black px-2.5 py-0.5 rounded-full uppercase', badgeClass(detalleInc.estatus)]">{{ detalleInc.estatus }}</span>
          </div>
        </div>

        <div>
          <p class="text-[10px] font-black text-[#86868B] uppercase tracking-widest mb-1">Dirección</p>
          <p class="text-sm font-semibold text-[#1D1D1F]">{{ detalleInc.direccion }}</p>
          <p class="text-xs text-[#86868B] mt-1">{{ detalleInc.descripcion }}</p>
        </div>

        <!-- Foto original + Mapa reporte -->
        <div class="grid grid-cols-2 gap-4">
          <div class="flex flex-col gap-2">
            <p class="text-[10px] font-black text-[#86868B] uppercase tracking-widest flex items-center gap-1">
              <i class="pi pi-image"></i> Foto del reporte
            </p>
            <div class="h-52 bg-[#F5F5F7] rounded-xl overflow-hidden border border-[#E8E8ED]">
              <img v-if="detalleInc.foto" :src="`/storage/${detalleInc.foto}`" class="w-full h-full object-cover" />
              <div v-else class="h-full flex flex-col items-center justify-center text-gray-300 gap-1">
                <i class="pi pi-image text-3xl"></i>
                <p class="text-xs">Sin foto</p>
              </div>
            </div>
          </div>

          <div class="flex flex-col gap-2">
            <p class="text-[10px] font-black text-[#86868B] uppercase tracking-widest flex items-center gap-1">
              <i class="pi pi-map-marker"></i> Ubicación reportada
            </p>
            <div class="h-52 rounded-xl overflow-hidden border border-[#E8E8ED] bg-[#F5F5F7]">
              <iframe
                v-if="detalleInc.latitud && detalleInc.longitud"
                :src="`https://www.openstreetmap.org/export/embed.html?bbox=${detalleInc.longitud-0.006},${detalleInc.latitud-0.006},${detalleInc.longitud+0.006},${detalleInc.latitud+0.006}&layer=mapnik&marker=${detalleInc.latitud},${detalleInc.longitud}`"
                width="100%" height="100%" style="border:0" loading="lazy"
              ></iframe>
              <div v-else class="h-full flex flex-col items-center justify-center text-gray-300 gap-1">
                <i class="pi pi-map text-3xl"></i>
                <p class="text-xs">Sin coordenadas</p>
              </div>
            </div>
          </div>
        </div>

        <!-- Acciones rápidas desde detalle -->
        <div class="border-t border-[#E8E8ED] pt-4 flex flex-wrap gap-2">
          <button
            v-if="detalleInc.estatus === 'pendiente'"
            @click="cambiarEstatus(detalleInc.id, 'en proceso'); detalleVisible = false"
            class="px-3 py-2 rounded-xl text-xs font-bold bg-sky-50 text-sky-600 border border-sky-200 hover:bg-sky-100 transition"
          ><i class="pi pi-play mr-1"></i>Poner en proceso</button>
          <button
            @click="abrirAsignar(detalleInc); detalleVisible = false"
            class="px-3 py-2 rounded-xl text-xs font-bold bg-amber-50 text-amber-600 border border-amber-200 hover:bg-amber-100 transition"
          ><i class="pi pi-user-plus mr-1"></i>Asignar trabajador</button>
          <button
            v-if="detalleInc.estatus !== 'rechazado'"
            @click="cambiarEstatus(detalleInc.id, 'rechazado'); detalleVisible = false"
            class="px-3 py-2 rounded-xl text-xs font-bold bg-rose-50 text-rose-600 border border-rose-200 hover:bg-rose-100 transition"
          ><i class="pi pi-times mr-1"></i>Rechazar reporte</button>
        </div>
      </div>
    </Dialog>

    <!-- Dialog: Rechazar cierre (con motivo) -->
    <Dialog
      v-model:visible="rechazoVisible"
      header="Rechazar evidencia del trabajador"
      :modal="true" :style="{ width: '480px', maxWidth: '95vw' }" :draggable="false"
      :closable="!rechazando"
    >
      <div class="flex flex-col gap-4 py-2">
        <!-- Vista previa evidencia -->
        <div v-if="rechazoInc" class="bg-[#F5F5F7] rounded-xl p-3">
          <p class="text-sm font-bold text-[#1D1D1F]">{{ rechazoInc.tipo_incidencia }} — #{{ rechazoInc.id }}</p>
          <p class="text-xs text-[#86868B]">{{ rechazoInc.direccion }}</p>
          <div v-if="rechazoInc.foto_despues" class="mt-2">
            <p class="text-[10px] font-bold text-[#86868B] uppercase tracking-wide mb-1">Foto enviada por el trabajador:</p>
            <img :src="`/storage/${rechazoInc.foto_despues}`" class="h-32 w-full object-cover rounded-lg border border-[#E8E8ED]" />
          </div>
        </div>

        <div class="flex flex-col gap-1.5">
          <label class="text-xs font-bold text-[#1D1D1F] uppercase tracking-wide flex items-center gap-1">
            <i class="pi pi-comment text-rose-500"></i>
            Motivo del rechazo <span class="text-rose-500">*</span>
          </label>
          <Textarea
            v-model="motivoRechazo"
            :rows="3"
            placeholder="Explica al trabajador qué debe corregir..."
            class="w-full text-sm resize-none"
            :class="{ '!border-rose-400': !motivoRechazo && rechazoError }"
          />
          <p v-if="rechazoError" class="text-xs text-rose-500">{{ rechazoError }}</p>
        </div>

        <div class="bg-amber-50 border border-amber-200 rounded-xl px-3 py-2 text-xs text-amber-700 flex items-start gap-2">
          <i class="pi pi-info-circle mt-0.5 shrink-0"></i>
          El trabajador verá este motivo y deberá corregir y reenviar la evidencia.
        </div>
      </div>

      <template #footer>
        <div class="flex gap-2 justify-end">
          <Button label="Cancelar" severity="secondary" outlined @click="rechazoVisible = false" :disabled="rechazando" />
          <Button
            label="Confirmar rechazo" icon="pi pi-times"
            severity="danger" :loading="rechazando"
            :disabled="!motivoRechazo"
            @click="confirmarRechazo"
          />
        </div>
      </template>
    </Dialog>

    <!-- Dialog: Asignar Trabajador -->
    <Dialog
      v-model:visible="dialogVisible"
      modal
      header="Asignar Trabajador"
      :style="{ width: '420px' }"
      :closable="true"
    >
      <div class="flex flex-col gap-4 pt-2">
        <p class="text-sm text-gray-600">
          Incidencia <strong>#{{ selectedInc?.id }}</strong> — {{ selectedInc?.tipo_incidencia }}<br/>
          <span class="text-xs text-gray-400">{{ selectedInc?.direccion }}</span>
        </p>
        <div class="flex flex-col gap-1">
          <label class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Selecciona un trabajador</label>
          <Dropdown
            v-model="selectedTrabajador"
            :options="trabajadores"
            optionLabel="name"
            optionValue="id"
            placeholder="— Elige trabajador —"
            class="w-full"
            showClear
          >
            <template #option="{ option }">
              <div class="flex flex-col leading-tight py-0.5">
                <span class="text-sm font-semibold">{{ option.name }}</span>
                <span class="text-xs text-gray-400">{{ option.email }} · {{ option.rol }}</span>
              </div>
            </template>
          </Dropdown>
        </div>
        <Message v-if="assignError" severity="error" :closable="false">{{ assignError }}</Message>
      </div>
      <template #footer>
        <div class="flex justify-end gap-2 pt-2">
          <Button label="Cancelar" text @click="cerrarAsignar" />
          <Button
            label="Asignar y poner en proceso"
            icon="pi pi-check"
            :loading="assigning"
            :disabled="!selectedTrabajador"
            @click="confirmarAsignar"
            class="!bg-[#850D12] !border-[#850D12]"
          />
        </div>
      </template>
    </Dialog>

    <!-- Modals CRUD incidencias -->
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
import AutoTable           from '@/Components/AutoTable.vue'
import StatsGrid           from '@/Components/StatsGrid.vue'
import IncidenciaFormModal from '@/Components/IncidenciaFormModal.vue'
import Textarea            from 'primevue/textarea'

export default {
  components: { AutoTable, StatsGrid, IncidenciaFormModal, Textarea },

  props: {
    incidencias:  { type: Array, default: () => [] },
    trabajadores: { type: Array, default: () => [] },
  },

  data() {
    return {
      localData: [],
      loading: false,
      loadingId: null,
      dialogVisible: false,
      selectedInc: null,
      selectedTrabajador: null,
      assigning: false,
      assignError: null,
      // Rechazo de cierre
      rechazoVisible: false,
      rechazoInc: null,
      motivoRechazo: '',
      rechazando: false,
      rechazoError: null,
      // Detalle reporte
      detalleVisible: false,
      detalleInc: null,
      // Crear / Editar incidencia
      crearVisible: false,
      editarVisible: false,
      editarInc: null,
    }
  },

  computed: {},

  mounted() {
    this.localData = [...this.incidencias]
  },

  methods: {
    abrirDetalle(inc) {
      this.detalleInc    = { ...inc }
      this.detalleVisible = true
    },

    abrirCrear() {
      this.crearVisible = true
    },

    abrirEditar(inc) {
      this.editarInc    = { ...inc }
      this.editarVisible = true
    },

    onIncidenciaCreada(inc) {
      // Recargar para obtener el nuevo registro con todos los campos
      window.location.reload()
    },

    onIncidenciaEditada(inc) {
      // Actualizar el registro local si el backend lo devuelve, si no recargar
      if (inc) {
        const idx = this.localData.findIndex(i => i.id === inc.id)
        if (idx !== -1) this.localData.splice(idx, 1, { ...this.localData[idx], ...inc })
      } else {
        window.location.reload()
      }
    },

    eliminarIncidencia(inc) {
      if (!confirm(`¿Eliminar la incidencia #${inc.id} "${inc.tipo_incidencia}"? Esta acción no se puede deshacer.`)) return
      this.loadingId = inc.id
      axios.delete(`/admin/incidencias/${inc.id}`, {
        headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content },
      }).then(() => {
        this.localData = this.localData.filter(i => i.id !== inc.id)
        this.$toast.add({ severity: 'info', summary: 'Eliminada', detail: `Incidencia #${inc.id} eliminada.`, life: 3000 })
      }).catch(() => {
        this.$toast.add({ severity: 'error', summary: 'Error', detail: 'No se pudo eliminar.', life: 3000 })
      }).finally(() => { this.loadingId = null })
    },

    badgeClass(estatus) {
      const m = {
        'pendiente':   'bg-gray-100 text-gray-600',
        'en proceso':  'bg-sky-100 text-sky-700',
        'en revisión': 'bg-amber-100 text-amber-700',
        'resuelto':    'bg-emerald-100 text-emerald-700',
        'rechazado':   'bg-rose-100 text-rose-700',
      }
      return m[estatus] || 'bg-gray-100 text-gray-600'
    },

    cambiarEstatus(id, estatus) {
      this.loadingId = id
      axios.patch(`/admin/incidencias/${id}/estatus`, { estatus }, {
        headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content },
      }).then(() => {
        const idx = this.localData.findIndex(i => i.id === id)
        if (idx !== -1) this.localData.splice(idx, 1, { ...this.localData[idx], estatus })
        this.$toast.add({ severity: 'success', summary: 'Listo', detail: `Estatus: "${estatus}"`, life: 3000 })
      }).catch(() => {
        this.$toast.add({ severity: 'error', summary: 'Error', detail: 'No se pudo actualizar.', life: 3000 })
      }).finally(() => { this.loadingId = null })
    },

    aprobarCierre(id) {
      this.loadingId = id
      axios.patch(`/admin/incidencias/${id}/revisar`, { accion: 'aprobar' }, {
        headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content },
      }).then(() => {
        const idx = this.localData.findIndex(i => i.id === id)
        if (idx !== -1) this.localData.splice(idx, 1, { ...this.localData[idx], estatus: 'resuelto' })
        this.$toast.add({ severity: 'success', summary: '¡Aprobado!', detail: 'Orden marcada como resuelta.', life: 3000 })
      }).catch(() => {
        this.$toast.add({ severity: 'error', summary: 'Error', detail: 'No se pudo aprobar.', life: 3000 })
      }).finally(() => { this.loadingId = null })
    },

    abrirRechazo(inc) {
      this.rechazoInc   = inc
      this.motivoRechazo = ''
      this.rechazoError  = null
      this.rechazoVisible = true
    },

    confirmarRechazo() {
      if (!this.motivoRechazo.trim()) { this.rechazoError = 'El motivo es obligatorio.'; return }
      this.rechazando = true
      axios.patch(`/admin/incidencias/${this.rechazoInc.id}/revisar`, {
        accion: 'rechazar', motivo_rechazo: this.motivoRechazo,
      }, {
        headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content },
      }).then(() => {
        const idx = this.localData.findIndex(i => i.id === this.rechazoInc.id)
        if (idx !== -1) this.localData.splice(idx, 1, { ...this.localData[idx], estatus: 'en proceso', motivo_rechazo: this.motivoRechazo })
        this.$toast.add({ severity: 'warn', summary: 'Rechazado', detail: 'El trabajador deberá corregir.', life: 4000 })
        this.rechazoVisible = false
      }).catch(() => {
        this.rechazoError = 'Error al rechazar. Intenta de nuevo.'
      }).finally(() => { this.rechazando = false })
    },

    abrirAsignar(inc) {
      this.selectedInc = inc
      this.selectedTrabajador = inc.asignado_a ?? null
      this.assignError = null
      this.dialogVisible = true
    },

    cerrarAsignar() {
      this.dialogVisible = false
      this.selectedInc = null
      this.selectedTrabajador = null
    },

    confirmarAsignar() {
      if (!this.selectedTrabajador) return
      this.assigning = true
      this.assignError = null
      axios.patch(`/admin/incidencias/${this.selectedInc.id}/asignar`, { asignado_a: this.selectedTrabajador }, {
        headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content },
      }).then(() => {
        const trabajador = this.trabajadores.find(t => t.id === this.selectedTrabajador)
        const idx = this.localData.findIndex(i => i.id === this.selectedInc.id)
        if (idx !== -1) {
          this.localData.splice(idx, 1, {
            ...this.localData[idx],
            asignado_a: this.selectedTrabajador,
            trabajador_nombre: trabajador?.name ?? '—',
            estatus: 'en proceso',
          })
        }
        this.$toast.add({ severity: 'success', summary: 'Asignado', detail: `Trabajador: ${trabajador?.name}`, life: 3000 })
        this.cerrarAsignar()
      }).catch(e => {
        this.assignError = e.response?.data?.message || 'Error al asignar.'
      }).finally(() => { this.assigning = false })
    },
  },
}
</script>

<style scoped>
/* Los estilos de la tabla los maneja AutoTable.vue internamente */
@keyframes fadeIn { from { opacity:0; transform:translateY(8px) } to { opacity:1; transform:translateY(0) } }
.animate-fade-in { animation: fadeIn .35s ease-out forwards }
</style>
