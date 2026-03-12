<template>
  <div class="animate-fade-in py-6 pl-4 pr-6">
    <PageHeader
      title="Mis Órdenes de Trabajo"
      subtitle="Tareas asignadas. Confirma tu presencia y cierra cada orden con foto de evidencia."
    >
      <template #actions>
        <div v-if="nuevas > 0" class="flex items-center gap-2 bg-brand-red/10 border border-brand-red/20 text-brand-red px-4 py-2 rounded-xl text-sm font-bold animate-pulse">
          <i class="pi pi-bell text-brand-red"></i>
          {{ nuevas }} nueva{{ nuevas > 1 ? 's' : '' }} asignada{{ nuevas > 1 ? 's' : '' }}
        </div>
      </template>
    </PageHeader>

    <!-- KPIs automáticas -->
    <StatsGrid :data="tareas" groupBy="estatus" :cols="4" gap="gap-4" mb="mb-6" />

    <!-- Vacío -->
    <div v-if="!tareas.length" class="bg-app-card rounded-2xl p-14 text-center border border-app-border">
      <div class="w-20 h-20 bg-app-secondary rounded-full flex items-center justify-center mx-auto mb-4">
        <i class="pi pi-inbox text-4xl text-[#86868B] dark:text-[#A1A1A6] opacity-30"></i>
      </div>
      <p class="text-[#86868B] dark:text-[#A1A1A6] font-medium">No tienes órdenes asignadas por ahora.</p>
    </div>

    <!-- Cards de órdenes -->
    <div v-else class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-5">
      <div
        v-for="tarea in tareas"
        :key="tarea.id"
        class="bg-app-card rounded-2xl border border-app-border shadow-sm overflow-hidden flex flex-col transition-all duration-200 hover:shadow-md"
        :class="tarea.estatus === 'en proceso' ? 'ring-2 ring-brand-red/20 border-brand-red/30' : ''"
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
              <p class="text-xs text-[#86868B] dark:text-[#A1A1A6] font-bold uppercase tracking-wide">{{ tarea.tipo_incidencia }}</p>
              <p class="text-sm font-bold text-[#1D1D1F] dark:text-white mt-0.5 leading-snug">{{ tarea.direccion }}</p>
            </div>
            <span class="text-xs text-gray-400 shrink-0 font-mono">#{{ tarea.id }}</span>
          </div>
          <p v-if="tarea.descripcion" class="text-xs text-[#86868B] dark:text-[#A1A1A6] line-clamp-2">{{ tarea.descripcion }}</p>
          
          <!-- Nota del Administrador -->
          <div v-if="tarea.nota_admin && tarea.estatus === 'en proceso'" class="bg-amber-50 dark:bg-amber-500/10 border border-amber-200 dark:border-amber-500/30 rounded-xl px-3 py-2 flex items-start gap-2">
            <i class="pi pi-info-circle text-amber-500 mt-0.5 shrink-0"></i>
            <div>
              <p class="text-[10px] font-black text-amber-800 dark:text-amber-400 uppercase tracking-widest">Nota del Admin:</p>
              <p class="text-xs text-amber-700 dark:text-amber-300 mt-0.5 leading-relaxed">{{ tarea.nota_admin }}</p>
            </div>
          </div>

          <!-- Aviso de rechazo del admin -->
          <div v-if="tarea.motivo_rechazo && tarea.estatus === 'en proceso'" class="bg-rose-50 border border-rose-200 rounded-xl px-3 py-2 flex items-start gap-2">
            <i class="pi pi-exclamation-triangle text-rose-500 mt-0.5 shrink-0"></i>
            <div>
              <p class="text-xs font-bold text-rose-700">El admin rechazó la evidencia:</p>
              <p class="text-xs text-rose-600 mt-0.5">{{ tarea.motivo_rechazo }}</p>
            </div>
          </div>          <div class="flex items-center gap-1.5 text-xs text-[#86868B] dark:text-[#A1A1A6] mt-1">
            <i class="pi pi-user text-[10px]"></i>
            <span>{{ tarea.nombre_ciudadano || 'Ciudadano' }}</span>
            <span class="mx-1 text-gray-300 dark:text-gray-700">·</span>
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
            class="w-full flex items-center justify-center gap-2 py-2 rounded-xl text-sm font-semibold bg-app-secondary text-[#1D1D1F] dark:text-white hover:bg-app-bg transition border border-transparent"
          >
            <i class="pi pi-map-marker text-brand-red"></i> Ver ubicación en mapa
          </button>
          <p v-else class="text-xs text-center text-gray-400 italic py-1">Sin coordenadas registradas</p>

          <!-- Cerrar orden -->
          <button
            v-if="tarea.estatus === 'en proceso'"
            @click="abrirCierre(tarea)"
            class="w-full flex items-center justify-center gap-2 py-2.5 rounded-xl text-sm font-bold bg-brand-gradient text-white hover:opacity-90 shadow-lg shadow-brand-red/20 transition-all active:scale-95"
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
          :href="`https://www.google.com/maps?q=${mapaActual?.latitud},${mapaActual?.longitud}`"
          target="_blank" class="text-xs text-center text-[#8A1538] hover:underline"
        ><i class="pi pi-external-link mr-1"></i>Abrir en Google Maps</a>
      </div>
    </Dialog>

    <!-- Modal de Cierre Refactorizado -->
    <CerrarOrdenModal 
      v-model:visible="cierreVisible"
      :tarea="cierreActual"
      @success="onCierreExito"
    />

    <Toast />
  </div>
</template>

<script>
import Dialog from 'primevue/dialog'
import PageHeader from '@/Components/PageHeader.vue'
import StatsGrid from '@/Components/StatsGrid.vue'
import CerrarOrdenModal from '@/Components/CerrarOrdenModal.vue'

export default {
  name: 'Tareas',
  components: { Dialog, PageHeader, StatsGrid, CerrarOrdenModal },
  props: {
    tareas: { type: Array,  default: () => [] },
    nuevas: { type: Number, default: 0 },
  },
  data() {
    return {
      mapaVisible: false,
      mapaActual: null,
      cierreVisible: false,
      cierreActual: null,
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
      return `https://www.google.com/maps/embed/v1/place?key=AIzaSyCchiqlRlOnv6C4pXxh59tYDMRiK501Tmc&q=${lat},${lng}&zoom=17`
    },
    abrirMapa(tarea) { 
      this.mapaActual = tarea
      this.mapaVisible = true 
    },
    abrirCierre(tarea) {
      this.cierreActual = tarea
      this.cierreVisible = true
    },
    onCierreExito(tareaId) {
      this.$toast.add({ 
        severity: 'info', 
        summary: 'Evidencia enviada', 
        detail: 'Esperando revisión del administrador.', 
        life: 4000 
      })
      const t = this.tareas.find(t => t.id === tareaId)
      if (t) { 
        t.estatus = 'en revisión'
        t.motivo_rechazo = null 
      }
    }
  }
}
</script>

<style scoped>
@keyframes fadeIn { from { opacity:0; transform:translateY(8px) } to { opacity:1; transform:translateY(0) } }
.animate-fade-in { animation: fadeIn .35s ease-out forwards }
</style>

<style>
/* Estilo GLOBAL para el contenedor de sugerencias de Google Places */
.pac-container {
  z-index: 10000 !important; /* Ajustado para que funcione bien con el nuevo modal */
  border-radius: 12px;
  border: 1px solid rgba(0,0,0,0.1);
  box-shadow: 0 12px 40px rgba(0,0,0,0.15);
  font-family: inherit;
}
.pac-item {
  padding: 8px 12px;
  cursor: pointer;
}
.pac-item:hover {
  background-color: #F5F5F7;
}
.pac-item-query {
  font-size: 14px;
  color: #1D1D1F;
}
</style>
