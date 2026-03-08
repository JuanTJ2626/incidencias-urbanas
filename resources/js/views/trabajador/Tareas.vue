<template>
  <div class="animate-fade-in py-6 pl-4 pr-6">
    <PageHeader
      title="Mis Órdenes de Trddabajo"
      subtitle="Tareas asignadas. Confirma tu presencia y cierra cada orden con foto de evidencia."
    >
      <template #actions>
        <div v-if="nuevas > 0" class="flex items-center gap-2 bg-sky-50 border border-sky-200 text-sky-700 px-4 py-2 rounded-xl text-sm font-bold animate-pulse">
          <i class="pi pi-bell text-sky-500"></i>
          {{ nuevas }} nueva{{ nuevas > 1 ? 's' : '' }} asignada{{ nuevas > 1 ? 's' : '' }}
        </div>
      </template>
    </PageHeader>

    <!-- KPIs automáticas -->
    <StatsGrid :data="tareas" groupBy="estatus" :cols="4" gap="gap-4" mb="mb-6" />

    <!-- Vacío -->
    <div v-if="!tareas.length" class="bg-white rounded-2xl p-14 text-center border border-[#E8E8ED]">
      <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4">
        <i class="pi pi-inbox text-4xl text-gray-300"></i>
      </div>
      <p class="text-gray-500 font-medium">No tienes órdenes asignadas por ahora.</p>
    </div>

    <!-- Cards de órdenes -->
    <div v-else class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-5">
      <div
        v-for="tarea in tareas"
        :key="tarea.id"
        class="bg-white rounded-2xl border shadow-sm overflow-hidden flex flex-col transition-all duration-200 hover:shadow-md"
        :class="tarea.estatus === 'en proceso' ? 'border-sky-300 ring-2 ring-sky-100' : 'border-[#E8E8ED]'"
      >
        <!-- Foto Antes -->
        <div class="relative h-40 bg-gray-100 shrink-0">
          <img v-if="tarea.foto_url" :src="tarea.foto_url" class="w-full h-full object-cover" alt="Foto antes" />
          <div v-else class="w-full h-full flex flex-col items-center justify-center gap-1">
            <i class="pi pi-image text-4xl text-gray-300"></i>
            <span class="text-xs text-gray-400">Sin foto</span>
          </div>
          <!-- Badge estatus -->
          <span
            :class="statusBadge(tarea.estatus)"
            class="absolute top-3 left-3 px-2.5 py-1 rounded-full text-xs font-bold shadow flex items-center gap-1.5"
          >
            <span v-if="tarea.estatus === 'en proceso'" class="w-2 h-2 rounded-full bg-white animate-ping inline-block"></span>
            {{ tarea.estatus }}
          </span>
          <span class="absolute top-3 right-3 bg-black/50 text-white text-[10px] font-bold px-2 py-0.5 rounded-md">ANTES</span>
        </div>

        <!-- Cuerpo -->
        <div class="p-4 flex flex-col flex-1 gap-2">
          <div class="flex items-start justify-between gap-2">
            <div>
              <p class="text-xs text-[#86868B] font-bold uppercase tracking-wide">{{ tarea.tipo_incidencia }}</p>
              <p class="text-sm font-bold text-[#1D1D1F] mt-0.5 leading-snug">{{ tarea.direccion }}</p>
            </div>
            <span class="text-xs text-gray-400 shrink-0 font-mono">#{{ tarea.id }}</span>
          </div>
          <p v-if="tarea.descripcion" class="text-xs text-[#86868B] line-clamp-2">{{ tarea.descripcion }}</p>
          <!-- Aviso de rechazo del admin -->
          <div v-if="tarea.motivo_rechazo && tarea.estatus === 'en proceso'" class="bg-rose-50 border border-rose-200 rounded-xl px-3 py-2 flex items-start gap-2">
            <i class="pi pi-exclamation-triangle text-rose-500 mt-0.5 shrink-0"></i>
            <div>
              <p class="text-xs font-bold text-rose-700">El admin rechazó la evidencia:</p>
              <p class="text-xs text-rose-600 mt-0.5">{{ tarea.motivo_rechazo }}</p>
            </div>
          </div>          <div class="flex items-center gap-1.5 text-xs text-gray-500 mt-1">
            <i class="pi pi-user text-[10px]"></i>
            <span>{{ tarea.nombre_ciudadano || 'Ciudadano' }}</span>
            <span class="mx-1 text-gray-300">·</span>
            <i class="pi pi-calendar text-[10px]"></i>
            <span>{{ formatDate(tarea.created_at) }}</span>
          </div>
        </div>

        <!-- Acciones -->
        <div class="px-4 pb-4 flex flex-col gap-2">
          <!-- Ver mapa -->
          <button
            v-if="tarea.latitud && tarea.longitud"
            @click="abrirMapa(tarea)"
            class="w-full flex items-center justify-center gap-2 py-2 rounded-xl text-sm font-semibold bg-[#F5F5F7] text-[#1D1D1F] hover:bg-indigo-50 hover:text-indigo-600 border border-transparent hover:border-indigo-200 transition"
          >
            <i class="pi pi-map-marker"></i> Ver ubicación en mapa
          </button>
          <p v-else class="text-xs text-center text-gray-400 italic py-1">Sin coordenadas registradas</p>

          <!-- Cerrar orden -->
          <button
            v-if="tarea.estatus === 'en proceso'"
            @click="abrirCierre(tarea)"
            class="w-full flex items-center justify-center gap-2 py-2.5 rounded-xl text-sm font-bold bg-emerald-500 text-white hover:bg-emerald-600 shadow-sm hover:shadow-emerald-200 hover:shadow-md transition"
          >
            <i class="pi pi-send"></i> Enviar evidencia para revisión
          </button>

          <!-- En revisión: esperando al admin -->
          <div v-else-if="tarea.estatus === 'en revisión'" class="flex items-center justify-center gap-1.5 py-2 text-xs font-bold text-sky-600 bg-sky-50 rounded-xl border border-sky-200">
            <i class="pi pi-clock"></i> Evidencia enviada — esperando revisión del admin
          </div>

          <!-- Aprobado -->
          <div v-else-if="tarea.estatus === 'resuelto'" class="flex items-center justify-center gap-1.5 py-2 text-xs font-bold text-emerald-600">
            <i class="pi pi-verified"></i> Aprobado por el administrador
          </div>

          <!-- Rechazado: mostrar motivo y permitir reenviar -->
          <div v-else-if="tarea.estatus === 'en proceso' && tarea.motivo_rechazo">
            <!-- este caso ya está cubierto arriba por el botón, pero mostramos el aviso -->
          </div>
        </div>
      </div>
    </div>

    <!-- Dialog: Mapa -->
    <Dialog
      v-model:visible="mapaVisible"
      :header="`Ubicación · ${mapaActual?.tipo_incidencia || ''}`"
      :modal="true" :style="{ width: '680px', maxWidth: '95vw' }" :draggable="false"
    >
      <div class="flex flex-col gap-3">
        <p class="text-sm text-[#86868B]"><i class="pi pi-map-marker text-[#850D12] mr-1"></i>{{ mapaActual?.direccion }}</p>
        <iframe
          v-if="mapaActual"
          :src="osmUrl(mapaActual.latitud, mapaActual.longitud)"
          class="w-full rounded-xl border border-[#E8E8ED]"
          style="height:380px" frameborder="0" scrolling="no" loading="lazy"
        ></iframe>
        <a
          :href="`https://www.openstreetmap.org/?mlat=${mapaActual?.latitud}&mlon=${mapaActual?.longitud}#map=17/${mapaActual?.latitud}/${mapaActual?.longitud}`"
          target="_blank" class="text-xs text-center text-sky-600 hover:underline"
        ><i class="pi pi-external-link mr-1"></i>Abrir en OpenStreetMap</a>
      </div>
    </Dialog>

    <!-- Dialog: Cerrar Orden -->
    <Dialog
      v-model:visible="cierreVisible"
      header="Cerrar Orden de Trabajo"
      :modal="true" :style="{ width: '520px', maxWidth: '95vw' }" :draggable="false"
      :closable="!cerrando"
    >
      <div class="flex flex-col gap-5 py-2">
        <!-- Resumen -->
        <div class="bg-[#F5F5F7] rounded-xl p-3 flex items-center gap-3">
          <div class="w-10 h-10 rounded-xl bg-emerald-100 flex items-center justify-center shrink-0">
            <i class="pi pi-wrench text-emerald-600"></i>
          </div>
          <div>
            <p class="text-sm font-bold text-[#1D1D1F]">{{ cierreActual?.tipo_incidencia }}</p>
            <p class="text-xs text-[#86868B]">{{ cierreActual?.direccion }}</p>
          </div>
        </div>

        <!-- Foto DESPUÉS (obligatoria) -->
        <div class="flex flex-col gap-1.5">
          <label class="text-xs font-bold text-[#1D1D1F] uppercase tracking-wide flex items-center gap-1">
            <i class="pi pi-camera text-[#850D12]"></i>
            Foto “Después” <span class="text-rose-500">*</span>
          </label>
          <input
            type="file" accept="image/*" capture="environment"
            @change="onFotoChange"
            class="block w-full text-sm text-gray-600 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-bold file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100 border border-[#E8E8ED] rounded-xl p-1 cursor-pointer"
          />
          <div v-if="fotoPreview" class="relative mt-1 rounded-xl overflow-hidden border border-[#E8E8ED] h-40">
            <img :src="fotoPreview" class="w-full h-full object-cover" alt="preview" />
            <button @click="fotoPreview = null; fotoDespues = null" class="absolute top-2 right-2 w-7 h-7 bg-red-500 text-white rounded-full flex items-center justify-center hover:bg-red-600">
              <i class="pi pi-times text-[10px]"></i>
            </button>
          </div>
          <p v-if="errors.foto_despues" class="text-xs text-rose-500">{{ errors.foto_despues }}</p>
        </div>

        <!-- GPS (obligatorio) -->
        <div class="flex flex-col gap-1.5">
          <label class="text-xs font-bold text-[#1D1D1F] uppercase tracking-wide flex items-center gap-1">
            <i class="pi pi-map-marker text-[#850D12]"></i>
            Ubicación actual <span class="text-rose-500">*</span>
          </label>
          <button
            @click="capturarGPS" :disabled="gpsLoading"
            class="flex items-center justify-center gap-2 py-2.5 rounded-xl font-bold text-sm border transition"
            :class="gpsOk ? 'bg-emerald-50 border-emerald-300 text-emerald-700' : 'bg-[#F5F5F7] border-[#E8E8ED] text-[#1D1D1F] hover:bg-sky-50 hover:border-sky-300 hover:text-sky-700'"
          >
            <i :class="gpsLoading ? 'pi pi-spin pi-spinner' : gpsOk ? 'pi pi-check-circle' : 'pi pi-crosshairs'"></i>
            {{ gpsLoading ? 'Obteniendo ubicación...' : gpsOk ? `GPS capturado (±${gpsAccuracy}m)` : 'Capturar mi ubicación GPS' }}
          </button>
          <p v-if="gpsError" class="text-xs text-rose-500">{{ gpsError }}</p>
          <iframe
            v-if="gpsOk"
            :src="osmUrl(latCierre, lngCierre)"
            class="w-full rounded-xl border border-emerald-200 mt-1" style="height:160px"
            frameborder="0" scrolling="no"
          ></iframe>
        </div>

        <!-- Notas (opcional) -->
        <div class="flex flex-col gap-1.5">
          <label class="text-xs font-bold text-[#1D1D1F] uppercase tracking-wide">
            <i class="pi pi-pencil text-[#86868B] mr-1"></i>Notas de cierre
            <span class="text-[#86868B] font-normal normal-case tracking-normal">(opcional)</span>
          </label>
          <Textarea v-model="notas" :rows="3" placeholder="Describe brevemente el trabajo realizado..." class="w-full text-sm resize-none" />
        </div>

        <p v-if="cierreError" class="text-sm text-rose-600 bg-rose-50 rounded-xl px-3 py-2 border border-rose-200">
          <i class="pi pi-exclamation-circle mr-1"></i>{{ cierreError }}
        </p>
      </div>

      <template #footer>
        <div class="flex gap-2 justify-end">
          <Button label="Cancelar" severity="secondary" outlined @click="cierreVisible = false" :disabled="cerrando" />
          <Button
            label="Confirmar cierre" icon="pi pi-check" :loading="cerrando"
            :disabled="!fotoDespues || !gpsOk"
            @click="confirmarCierre"
          />
        </div>
      </template>
    </Dialog>

    <Toast />
  </div>
</template>

<script>
import Dialog   from 'primevue/dialog'
import Button   from 'primevue/button'
import Textarea from 'primevue/textarea'

export default {
  components: { Dialog, Button, Textarea },
  props: {
    tareas: { type: Array,  default: () => [] },
    nuevas: { type: Number, default: 0 },
  },
  data() {
    return {
      mapaVisible: false, mapaActual: null,
      cierreVisible: false, cierreActual: null,
      fotoDespues: null, fotoPreview: null,
      latCierre: null, lngCierre: null,
      gpsLoading: false, gpsOk: false, gpsAccuracy: null, gpsError: null,
      notas: '', cerrando: false, cierreError: null, errors: {},
    }
  },
  methods: {
    formatDate(val) {
      if (!val) return '—'
      return new Date(val).toLocaleDateString('es-MX', { day: '2-digit', month: 'short', year: 'numeric' })
    },
    statusBadge(s) {
      const v = String(s ?? '').toLowerCase()
      if (v.includes('proceso'))   return 'bg-sky-500 text-white'
      if (v.includes('resuelto'))  return 'bg-emerald-500 text-white'
      if (v.includes('rechazado')) return 'bg-rose-500 text-white'
      return 'bg-amber-400 text-white'
    },
    osmUrl(lat, lng) {
      const d = 0.008
      return `https://www.openstreetmap.org/export/embed.html?bbox=${lng-d},${lat-d},${lng+d},${lat+d}&layer=mapnik&marker=${lat},${lng}`
    },
    abrirMapa(tarea)  { this.mapaActual = tarea; this.mapaVisible = true },
    abrirCierre(tarea) {
      Object.assign(this.$data, {
        cierreActual: tarea, fotoDespues: null, fotoPreview: null,
        latCierre: null, lngCierre: null, gpsOk: false, gpsAccuracy: null,
        gpsError: null, notas: '', cierreError: null, errors: {},
      })
      this.cierreVisible = true
    },
    onFotoChange(e) {
      const file = e.target.files[0]
      if (!file) return
      this.fotoDespues = file
      this.fotoPreview = URL.createObjectURL(file)
      this.errors = { ...this.errors, foto_despues: null }
    },
    capturarGPS() {
      if (!navigator.geolocation) { this.gpsError = 'Tu dispositivo no soporta geolocalización.'; return }
      this.gpsLoading = true; this.gpsError = null; this.gpsOk = false
      navigator.geolocation.getCurrentPosition(
        (pos) => {
          this.latCierre = pos.coords.latitude
          this.lngCierre = pos.coords.longitude
          this.gpsAccuracy = Math.round(pos.coords.accuracy)
          this.gpsOk = true; this.gpsLoading = false
        },
        (err) => { this.gpsError = `No se pudo obtener la ubicación: ${err.message}`; this.gpsLoading = false },
        { enableHighAccuracy: true, timeout: 10000 }
      )
    },
    confirmarCierre() {
      if (!this.fotoDespues || !this.gpsOk) return
      this.cerrando = true; this.cierreError = null
      const form = new FormData()
      form.append('foto_despues', this.fotoDespues)
      form.append('lat_cierre',   this.latCierre)
      form.append('lng_cierre',   this.lngCierre)
      form.append('notas_cierre', this.notas)
      axios.post(`/trabajador/incidencias/${this.cierreActual.id}/cerrar`, form, {
        headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content },
      }).then(() => {
        this.$toast.add({ severity: 'info', summary: 'Evidencia enviada', detail: 'Esperando revisión del administrador.', life: 4000 })
        this.cierreVisible = false
        const t = this.tareas.find(t => t.id === this.cierreActual.id)
        if (t) { t.estatus = 'en revisión'; t.motivo_rechazo = null }
      }).catch(e => {
        const errs = e.response?.data?.errors
        if (errs) this.errors = Object.fromEntries(Object.entries(errs).map(([k,v]) => [k, v[0]]))
        else this.cierreError = 'Error al cerrar la orden. Intenta de nuevo.'
      }).finally(() => { this.cerrando = false })
    },
  },
}
</script>

<style scoped>
@keyframes fadeIn { from { opacity:0; transform:translateY(8px) } to { opacity:1; transform:translateY(0) } }
.animate-fade-in { animation: fadeIn .35s ease-out forwards }
</style>
