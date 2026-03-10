<template>
  <div class="animate-fade-in py-6 pl-4 pr-6">
    <PageHeader
      title="Monitoreo de Trabajadores"
      subtitle="Haz clic en una tarjeta 'en revisión' para ver la evidencia y aprobarla o rechazarla."
    />

    <StatsGrid :data="localData" groupBy="estatus" gap="gap-4" mb="mb-6" />

    <!-- Grilla de tarjetas -->
    <div v-if="localData.length" class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-5">
      <div
        v-for="inc in localData"
        :key="inc.id"
        @click="abrirRevision(inc)"
        :class="[
          'group relative bg-white dark:bg-app-card rounded-2xl border overflow-hidden flex flex-col transition-all duration-200',
          inc.estatus === 'en revisión'
            ? 'border-amber-300 dark:border-amber-500/50 shadow-amber-100 dark:shadow-amber-900/10 shadow-md cursor-pointer hover:shadow-amber-200 dark:hover:shadow-amber-900/20 hover:-translate-y-0.5'
            : 'border-app-border dark:border-app-border shadow-sm cursor-pointer hover:shadow-md hover:-translate-y-0.5'
        ]"
      >
        <!-- Banner "en revisión" -->
        <div v-if="inc.estatus === 'en revisión'" class="bg-amber-500 text-white text-[11px] font-bold tracking-wide flex items-center justify-center gap-1.5 py-1.5 px-3">
          <i class="pi pi-eye text-xs"></i>
          EVIDENCIA PENDIENTE DE REVISIÓN — clic para revisar
        </div>

        <!-- Foto antes -->
        <div class="relative h-44 bg-app-secondary dark:bg-app-secondary overflow-hidden">
          <img
            v-if="inc.foto"
            :src="`/storage/${inc.foto}`"
            class="w-full h-full object-cover group-hover:scale-[1.02] transition-transform duration-300"
          />
          <div v-else class="h-full flex items-center justify-center">
            <i class="pi pi-image text-4xl text-gray-300 dark:text-gray-600"></i>
          </div>

          <!-- Badge estatus -->
          <span :class="['absolute top-2 right-2 px-2.5 py-1 rounded-full text-[10px] font-black uppercase tracking-wide shadow-sm', badgeClass(inc.estatus)]">
            {{ inc.estatus }}
          </span>

          <!-- Indicador foto después -->
          <span
            v-if="inc.foto_despues"
            class="absolute bottom-2 right-2 bg-black/60 text-white text-[10px] font-bold px-2 py-0.5 rounded-full flex items-center gap-1"
          ><i class="pi pi-camera text-[9px]"></i> Foto enviada</span>
        </div>

        <!-- Cuerpo -->
        <div class="p-4 flex flex-col flex-1 gap-2">
          <div>
            <p class="text-[10px] font-black text-[#86868B] dark:text-[#A1A1A6] uppercase tracking-widest">{{ inc.tipo_incidencia }}</p>
            <p class="text-sm font-bold text-[#1D1D1F] dark:text-white mt-0.5 leading-snug line-clamp-2">{{ inc.direccion }}</p>
          </div>

          <!-- Worker -->
          <div class="mt-auto pt-3 border-t border-app-secondary dark:border-app-border flex items-center gap-2">
            <div class="w-7 h-7 rounded-full bg-[#1D1D1F] dark:bg-white dark:text-black flex items-center justify-center text-white text-[11px] font-black shrink-0">
              {{ (inc.trabajador_nombre || 'T').charAt(0).toUpperCase() }}
            </div>
            <div class="min-w-0 flex-1">
              <p class="text-xs font-semibold text-[#1D1D1F] dark:text-gray-200 truncate">{{ inc.trabajador_nombre || 'Sin asignar' }}</p>
              <p class="text-[10px] text-[#86868B] dark:text-[#A1A1A6] truncate">{{ inc.trabajador_email || '' }}</p>
            </div>
            <span class="text-[10px] text-gray-400 dark:text-gray-500 shrink-0 font-mono">#{{ inc.id }}</span>
          </div>
        </div>
      </div>
    </div>

    <!-- Vacío -->
    <div v-else class="bg-white dark:bg-[#18181b] rounded-2xl p-12 text-center border border-[#E8E8ED] dark:border-white/10">
      <div class="w-20 h-20 bg-gray-50 dark:bg-white/5 rounded-full flex items-center justify-center mx-auto mb-4">
        <i class="pi pi-eye text-4xl text-gray-300 dark:text-gray-600"></i>
      </div>
      <p class="text-gray-500 dark:text-gray-400 font-medium">Aún no hay incidencias asignadas a trabajadores.</p>
    </div>

    <!-- ================================================================
         DIALOG — antes / después + GPS + notas + Aprobar / Rechazar
         ================================================================ -->
    <Dialog
      v-model:visible="dialogVisible"
      :header="dialogInc ? `${dialogInc.tipo_incidencia} — #${dialogInc.id}` : 'Revisión'"
      :modal="true"
      :style="{ width: '860px', maxWidth: '96vw' }"
      :draggable="false"
      :closable="!procesando"
    >
      <div v-if="dialogInc" class="flex flex-col gap-5 py-1">

        <!-- Dirección + worker -->
        <div class="flex items-start justify-between gap-4 flex-wrap">
          <div>
            <p class="text-sm font-bold text-[#1D1D1F] dark:text-white">{{ dialogInc.direccion }}</p>
            <p class="text-xs text-[#86868B] dark:text-[#A1A1A6] mt-0.5">{{ dialogInc.descripcion }}</p>
          </div>
          <div class="flex items-center gap-2 bg-[#F5F5F7] dark:bg-white/5 rounded-xl px-3 py-1.5 shrink-0">
            <div class="w-6 h-6 rounded-full bg-[#1D1D1F] dark:bg-white dark:text-black flex items-center justify-center text-white text-[10px] font-black">
              {{ (dialogInc.trabajador_nombre || 'T').charAt(0).toUpperCase() }}
            </div>
            <div>
              <p class="text-xs font-bold text-[#1D1D1F] dark:text-gray-200">{{ dialogInc.trabajador_nombre || '—' }}</p>
              <p class="text-[10px] text-[#86868B] dark:text-[#A1A1A6]">{{ dialogInc.trabajador_email }}</p>
            </div>
          </div>
        </div>

        <!-- Fotos antes / después -->
        <div class="grid grid-cols-2 gap-4">
          <div class="flex flex-col gap-2">
            <p class="text-[10px] font-black text-[#86868B] dark:text-[#A1A1A6] uppercase tracking-widest flex items-center gap-1">
              <i class="pi pi-image"></i> Foto ANTES (original)
            </p>
            <div class="h-52 bg-[#F5F5F7] dark:bg-[#1C1C1E] rounded-xl overflow-hidden border border-[#E8E8ED] dark:border-white/5">
              <img v-if="dialogInc.foto" :src="`/storage/${dialogInc.foto}`" class="w-full h-full object-cover" />
              <div v-else class="h-full flex items-center justify-center text-gray-300 dark:text-gray-600 text-sm">Sin foto</div>
            </div>
          </div>

          <div class="flex flex-col gap-2">
            <p class="text-[10px] font-black text-[#86868B] dark:text-[#A1A1A6] uppercase tracking-widest flex items-center gap-1">
              <i class="pi pi-camera"></i> Foto DESPUÉS (evidencia del trabajador)
            </p>
            <div class="h-52 bg-[#F5F5F7] dark:bg-[#1C1C1E] rounded-xl overflow-hidden border border-[#E8E8ED] dark:border-white/5">
              <img v-if="dialogInc.foto_despues" :src="`/storage/${dialogInc.foto_despues}`" class="w-full h-full object-cover" />
              <div v-else class="h-full flex flex-col items-center justify-center text-gray-300 dark:text-gray-600 gap-1">
                <i class="pi pi-camera text-3xl"></i>
                <p class="text-xs">Sin evidencia aún</p>
              </div>
            </div>
          </div>
        </div>

        <!-- Mapa cierre + notas -->
        <div v-if="dialogInc.lat_cierre && dialogInc.lng_cierre" class="grid grid-cols-5 gap-4">
          <div class="col-span-3 flex flex-col gap-2">
            <p class="text-[10px] font-black text-[#86868B] dark:text-[#A1A1A6] uppercase tracking-widest flex items-center gap-1">
              <i class="pi pi-map-marker"></i> Ubicación de cierre (GPS)
            </p>
            <div class="h-44 rounded-xl overflow-hidden border border-[#E8E8ED] dark:border-white/5">
              <iframe
                :src="`https://www.google.com/maps/embed/v1/place?key=AIzaSyCchiqlRlOnv6C4pXxh59tYDMRiK501Tmc&q=${dialogInc.lat_cierre},${dialogInc.lng_cierre}&zoom=17`"
                width="100%" height="100%" style="border:0" loading="lazy" allowfullscreen
              ></iframe>
            </div>
          </div>
          <div class="col-span-2 flex flex-col gap-2">
            <p class="text-[10px] font-black text-[#86868B] dark:text-[#A1A1A6] uppercase tracking-widest flex items-center gap-1">
              <i class="pi pi-align-left"></i> Notas del cierre
            </p>
            <div class="h-44 bg-app-secondary dark:bg-app-bg rounded-xl border border-app-border dark:border-app-border p-3 overflow-y-auto text-sm text-[#1D1D1F] dark:text-gray-200 leading-relaxed">
              {{ dialogInc.notas_cierre || 'Sin notas.' }}
            </div>
          </div>
        </div>

        <!-- Zona de decisión — solo si está en revisión y tiene foto después -->
        <template v-if="dialogInc.estatus === 'en revisión'">
          <div class="border-t border-[#E8E8ED] dark:border-white/5 pt-4 flex flex-col gap-3">
            <p class="text-xs font-black text-[#1D1D1F] dark:text-white uppercase tracking-wide flex items-center gap-1.5">
              <i class="pi pi-shield text-amber-500"></i> Decisión del administrador
            </p>

            <div v-if="!dialogInc.foto_despues" class="bg-gray-50 dark:bg-white/5 border border-gray-200 dark:border-white/10 rounded-xl px-4 py-3 text-sm text-gray-500 dark:text-gray-400 flex items-center gap-2">
              <i class="pi pi-info-circle"></i> El trabajador aún no ha enviado evidencia.
            </div>

            <template v-else>
              <div v-if="mostrarMotivo" class="flex flex-col gap-1.5">
                <label class="text-xs font-bold text-rose-600 dark:text-rose-400 flex items-center gap-1">
                  <i class="pi pi-comment"></i> Motivo del rechazo <span class="text-rose-500">*</span>
                </label>
                <Textarea
                  v-model="motivoRechazo"
                  :rows="3"
                  placeholder="Explica al trabajador qué debe corregir..."
                  class="w-full text-sm resize-none dark:bg-app-bg dark:text-white dark:border-app-border"
                />
                <p v-if="rechazoError" class="text-xs text-rose-500">{{ rechazoError }}</p>
              </div>

              <div class="flex gap-3">
                <button
                  v-if="!mostrarMotivo"
                  @click="aprobarCierre"
                  :disabled="procesando"
                  class="flex-1 flex items-center justify-center gap-2 py-2.5 rounded-xl text-sm font-bold bg-emerald-500 dark:bg-emerald-600 text-white hover:bg-emerald-600 transition disabled:opacity-50 shadow-sm"
                ><i class="pi pi-check-circle"></i> Aprobar — marcar como resuelta</button>

                <button
                  v-if="!mostrarMotivo"
                  @click="mostrarMotivo = true"
                  :disabled="procesando"
                  class="flex-1 flex items-center justify-center gap-2 py-2.5 rounded-xl text-sm font-bold bg-rose-50 dark:bg-rose-500/10 text-rose-600 dark:text-rose-400 border border-rose-200 dark:border-rose-500/30 hover:bg-rose-100 dark:hover:bg-rose-500/20 transition disabled:opacity-50"
                ><i class="pi pi-times-circle"></i> Rechazar con motivo</button>

                <template v-if="mostrarMotivo">
                  <button
                    @click="confirmarRechazo"
                    :disabled="procesando || !motivoRechazo.trim()"
                    class="flex-1 flex items-center justify-center gap-2 py-2.5 rounded-xl text-sm font-bold bg-rose-500 dark:bg-rose-600 text-white hover:bg-rose-600 transition disabled:opacity-50"
                  ><i class="pi pi-times"></i> Confirmar rechazo</button>
                  <button
                    @click="mostrarMotivo = false; motivoRechazo = ''"
                    :disabled="procesando"
                    class="px-4 py-2.5 rounded-xl text-sm font-bold border border-app-border dark:border-app-border text-[#86868B] dark:text-[#A1A1A6] hover:bg-gray-50 dark:hover:bg-white/5 transition"
                  >Cancelar</button>
                </template>
              </div>
            </template>
          </div>
        </template>

        <!-- Ya resuelta -->
        <div v-else-if="dialogInc.estatus === 'resuelto'" class="bg-emerald-50 dark:bg-emerald-500/10 border border-emerald-200 dark:border-emerald-500/30 rounded-xl px-4 py-3 text-sm text-emerald-700 dark:text-emerald-400 flex items-center gap-2 font-semibold">
          <i class="pi pi-verified"></i> Esta incidencia fue aprobada como resuelta.
        </div>
      </div>
    </Dialog>
  </div>
</template>

<script>
import StatsGrid from '@/Components/StatsGrid.vue'
import Dialog    from 'primevue/dialog'
import Textarea  from 'primevue/textarea'

export default {
  components: { StatsGrid, Dialog, Textarea },

  props: {
    incidencias: { type: Array, default: () => [] },
  },

  data() {
    return {
      localData:     [...(this.incidencias || [])],
      dialogVisible: false,
      dialogInc:     null,
      motivoRechazo: '',
      mostrarMotivo: false,
      rechazoError:  null,
      procesando:    false,
    }
  },

  methods: {
    abrirRevision(inc) {
      this.dialogInc     = { ...inc }
      this.motivoRechazo = ''
      this.mostrarMotivo = false
      this.rechazoError  = null
      this.dialogVisible = true
    },

    badgeClass(estatus) {
      const m = {
        'pendiente':   'bg-gray-100 text-gray-600 border border-gray-200',
        'en proceso':  'bg-sky-100 text-sky-700 border border-sky-200',
        'en revisión': 'bg-amber-400 text-white border border-amber-500',
        'resuelto':    'bg-emerald-100 text-emerald-700 border border-emerald-200',
        'rechazado':   'bg-rose-100 text-rose-700 border border-rose-200',
      }
      return m[estatus] || 'bg-gray-100 text-gray-600 border border-gray-200'
    },

    aprobarCierre() {
      this.procesando = true
      axios.patch(`/admin/incidencias/${this.dialogInc.id}/revisar`, { accion: 'aprobar' }, {
        headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content },
      }).then(() => {
        this._patchLocal(this.dialogInc.id, { estatus: 'resuelto', motivo_rechazo: null })
        this.$toast.add({ severity: 'success', summary: '¡Aprobada!', detail: 'Orden marcada como resuelta.', life: 3000 })
        this.dialogVisible = false
      }).catch(() => {
        this.$toast.add({ severity: 'error', summary: 'Error', detail: 'No se pudo aprobar.', life: 3000 })
      }).finally(() => { this.procesando = false })
    },

    confirmarRechazo() {
      if (!this.motivoRechazo.trim()) { this.rechazoError = 'El motivo es obligatorio.'; return }
      this.procesando = true
      axios.patch(`/admin/incidencias/${this.dialogInc.id}/revisar`, {
        accion: 'rechazar', motivo_rechazo: this.motivoRechazo,
      }, {
        headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content },
      }).then(() => {
        this._patchLocal(this.dialogInc.id, {
          estatus: 'en proceso', motivo_rechazo: this.motivoRechazo, foto_despues: null, cerrado_en: null,
        })
        this.$toast.add({ severity: 'warn', summary: 'Rechazada', detail: 'El trabajador deberá corregir.', life: 4000 })
        this.dialogVisible = false
      }).catch(() => {
        this.rechazoError = 'Error al rechazar. Intenta de nuevo.'
      }).finally(() => { this.procesando = false })
    },

    _patchLocal(id, changes) {
      const idx = this.localData.findIndex(i => i.id === id)
      if (idx !== -1) this.localData.splice(idx, 1, { ...this.localData[idx], ...changes })
    },
  },
}
</script>

<style scoped>
@keyframes fadeIn { from { opacity:0; transform:translateY(8px) } to { opacity:1; transform:translateY(0) } }
.animate-fade-in { animation: fadeIn .35s ease-out forwards }

:global(.dark) :deep(.p-dialog) {
	background-color: #1C1C1E !important;
	border: 1px solid rgba(255, 255, 255, 0.1) !important;
	color: #EBEBF5 !important;
}
:global(.dark) :deep(.p-dialog .p-dialog-header) {
	background-color: #1C1C1E !important;
	border-bottom: 1px solid rgba(255, 255, 255, 0.1) !important;
	color: white !important;
}
:global(.dark) :deep(.p-dialog .p-dialog-content) {
	background-color: #1C1C1E !important;
	color: #EBEBF5 !important;
}
</style>
