<template>
  <div class="animate-fade-in space-y-8">

    <!-- Header con efecto de cristal superior opcional o simplemente limpio -->
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-6">
      <PageHeader
        title="Dashboard Ejecutivo"
        subtitle="Analítica en tiempo real del ecosistema de incidencias urbanas."
        class="!py-0"
      />
      
      <div class="flex items-center gap-4">
        <div class="hidden lg:flex flex-col items-end">
          <span class="text-[10px] font-black uppercase tracking-[0.2em] text-[#86868B] dark:text-[#A1A1A6]">Estado del Sistema</span>
          <div class="flex items-center gap-2 mt-1">
            <div class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse shadow-[0_0_8px_#10b981]"></div>
            <span class="text-xs font-bold text-[#1D1D1F] dark:text-white">Operativo / 100%</span>
          </div>
        </div>
        
        <div class="h-10 w-[1px] bg-app-border mx-2 hidden lg:block"></div>
        
        <div class="flex items-center gap-3 bg-white/70 dark:bg-white/5 backdrop-blur-md border border-app-border rounded-2xl px-4 py-2.5 shadow-sm">
          <i class="pi pi-calendar text-brand-red text-sm"></i>
          <span class="text-xs font-black text-[#1D1D1F] dark:text-white tracking-tight">{{ fechaHoy }}</span>
        </div>
      </div>
    </div>

    <!-- ── STAT CARDS REVISADOS ── -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
      <div
        v-for="card in statCards"
        :key="card.label"
        class="group relative bg-white dark:bg-app-card rounded-[2rem] border border-app-border shadow-[0_10px_30px_rgba(0,0,0,0.02)] p-6 flex flex-col gap-6 overflow-hidden transition-all duration-500 hover:-translate-y-2 hover:shadow-2xl hover:shadow-black/5"
        :class="card.hover"
      >
        <!-- Decoración de fondo premium -->
        <div class="absolute -top-10 -right-10 w-32 h-32 rounded-full opacity-[0.03] group-hover:opacity-10 transition-all duration-700 blur-3xl scale-150"
          :class="card.deco"></div>

        <div class="flex items-center justify-between z-10">
          <div :class="['w-14 h-14 rounded-2xl flex items-center justify-center text-2xl shrink-0 shadow-inner group-hover:scale-110 transition-transform duration-500', card.iconBg]">
            <i :class="[card.icon, card.iconColor, 'group-hover:drop-shadow-sm']"></i>
          </div>
          <div :class="['flex flex-col items-end px-3 py-1 rounded-xl group-hover:bg-white/20 transition-colors', card.badgeBg]">
            <span :class="['text-[10px] font-black uppercase tracking-widest', card.badgeText]">
              {{ card.pct }}%
            </span>
          </div>
        </div>

        <div class="z-10">
          <div class="flex items-baseline gap-1">
            <p class="text-4xl font-black text-[#1D1D1F] dark:text-white tracking-tighter group-hover:text-white transition-colors duration-300">
              {{ card.value }}
            </p>
          </div>
          <p class="text-[11px] font-black text-[#86868B] dark:text-[#A1A1A6] uppercase tracking-[0.2em] mt-2 group-hover:text-white/80 transition-colors duration-300">
            {{ card.label }}
          </p>
        </div>
        
        <!-- Línea de progreso sutil al fondo -->
        <div class="absolute bottom-0 left-0 h-1 bg-current opacity-10 group-hover:opacity-30 transition-all duration-500" :style="{ width: card.pct + '%' }"></div>
      </div>
    </div>

    <!-- ── BLOQUE DE MÉTRICAS ANALÍTICAS ── -->
    <div class="grid grid-cols-1 xl:grid-cols-12 gap-6">
      
      <!-- Distribución por Estatus (5/12) -->
      <div class="xl:col-span-5 bg-white dark:bg-app-card rounded-[2.5rem] border border-app-border shadow-sm p-8 flex flex-col">
        <div class="flex items-center justify-between mb-8">
          <div class="flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl bg-[#F5F5F7] dark:bg-white/5 flex items-center justify-center shadow-sm">
              <i class="pi pi-chart-pie text-brand-red text-lg"></i>
            </div>
            <div>
              <h3 class="text-lg font-black text-[#1D1D1F] dark:text-white tracking-tight">Estatus Global</h3>
              <p class="text-xs text-[#86868B] dark:text-[#A1A1A6] font-bold uppercase tracking-widest mt-0.5">Distribución Operativa</p>
            </div>
          </div>
        </div>

        <div v-if="tieneIncidencias" class="flex flex-col md:flex-row items-center gap-10 flex-1">
          <div class="relative w-56 h-56 shrink-0 group">
             <Chart type="doughnut" :data="chartEstatus" :options="donutOptions" class="w-full h-full" />
             <div class="absolute inset-0 flex flex-col items-center justify-center pointer-events-none">
                <span class="text-3xl font-black text-[#1D1D1F] dark:text-white tracking-tighter">{{ total }}</span>
                <span class="text-[10px] font-black text-[#86868B] dark:text-[#A1A1A6] uppercase tracking-widest">Total</span>
             </div>
          </div>
          
          <div class="flex flex-col gap-3 flex-1 w-full">
            <div
              v-for="(item, i) in chartEstatusLeyenda" :key="i"
              class="group/item flex items-center justify-between p-3.5 rounded-[1.25rem] bg-[#F5F5F7] dark:bg-white/5 border border-transparent hover:border-app-border hover:bg-white dark:hover:bg-white/10 transition-all cursor-default"
            >
              <div class="flex items-center gap-3">
                <div class="w-3 h-3 rounded-full shadow-sm group-hover/item:scale-125 transition-transform" :style="{ background: item.color }"></div>
                <span class="text-xs font-black text-[#1D1D1F] dark:text-gray-200 capitalize tracking-tight">{{ item.label }}</span>
              </div>
              <div class="flex items-center gap-3">
                <span class="text-xs font-black text-[#1D1D1F] dark:text-white">{{ item.value }}</span>
                <span class="text-[10px] font-bold text-[#86868B] dark:text-[#A1A1A6] px-2 py-0.5 rounded-lg bg-white/50 dark:bg-black/20">{{ item.pct }}%</span>
              </div>
            </div>
          </div>
        </div>
        <EmptyState v-else mensaje="Sin datos de estatus" class="flex-1" />
      </div>

      <!-- Incidencias por Categoría (7/12) -->
      <div class="xl:col-span-7 bg-white dark:bg-app-card rounded-[2.5rem] border border-app-border shadow-sm p-8">
        <div class="flex items-center justify-between mb-8">
          <div class="flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl bg-[#F5F5F7] dark:bg-white/5 flex items-center justify-center shadow-sm">
              <i class="pi pi-chart-bar text-brand-red text-lg"></i>
            </div>
            <div>
              <h3 class="text-lg font-black text-[#1D1D1F] dark:text-white tracking-tight">Demanda por Categoría</h3>
              <p class="text-xs text-[#86868B] dark:text-[#A1A1A6] font-bold uppercase tracking-widest mt-0.5">Top tipologías reportadas</p>
            </div>
          </div>
          <div class="flex items-center gap-2 px-3 py-1.5 bg-emerald-50 dark:bg-emerald-500/10 rounded-xl border border-emerald-100 dark:border-emerald-500/20">
            <span class="text-[10px] font-black text-emerald-600 dark:text-emerald-400 uppercase tracking-widest">Tendencia Actual</span>
            <i class="pi pi-arrow-up-right text-[10px] text-emerald-500"></i>
          </div>
        </div>
        
        <div v-if="tieneIncidencias" class="h-[280px]">
          <Chart type="bar" :data="chartCategoria" :options="barOptions" class="w-full h-full" />
        </div>
        <EmptyState v-else mensaje="Sin categorías para graficar" />
      </div>

    </div>

    <!-- ── SECCIÓN CENTRAL: MAPA + RECIENTES ── -->
    <div class="grid grid-cols-1 xl:grid-cols-12 gap-6 items-stretch">
      
      <!-- Listado de Últimas (5/12) -->
      <div class="xl:col-span-5 bg-white dark:bg-app-card rounded-[2.5rem] border border-app-border shadow-sm overflow-hidden flex flex-col">
        <div class="flex items-center justify-between p-8 border-b border-app-border bg-white dark:bg-app-card sticky top-0 z-10">
          <div class="flex items-center gap-4">
            <div class="w-10 h-10 rounded-xl bg-brand-red/10 flex items-center justify-center">
              <i class="pi pi-bolt text-brand-red text-sm"></i>
            </div>
            <div>
              <h3 class="text-base font-black text-[#1D1D1F] dark:text-white tracking-tight">Actividad Reciente</h3>
              <p class="text-[10px] text-[#86868B] dark:text-[#A1A1A6] font-bold uppercase tracking-widest">Feed Directo</p>
            </div>
          </div>
          <Link href="/admin/gestion-incidencias" class="group flex items-center gap-2 text-[11px] font-black text-brand-red hover:underline uppercase tracking-widest">
            Explorar <i class="pi pi-arrow-right text-[10px] group-hover:translate-x-1 transition-transform"></i>
          </Link>
        </div>

        <div v-if="tieneIncidencias" class="flex-1 overflow-y-auto max-h-[500px] divide-y divide-app-secondary dark:divide-white/5 custom-scrollbar">
          <div
            v-for="inc in ultimasIncidencias"
            :key="inc.id"
            class="flex items-center gap-4 px-8 py-5 hover:bg-[#F9F9FB] dark:hover:bg-white/5 transition-all group cursor-default"
          >
            <div class="w-10 h-10 rounded-xl bg-[#F8E7EB] dark:bg-brand-red/20 border border-brand-red/10 flex items-center justify-center shrink-0 group-hover:scale-110 transition-all duration-300">
              <i :class="[iconoCategoria(inc.tipo_incidencia), 'text-brand-red text-xs']"></i>
            </div>
            
            <div class="flex-1 min-w-0">
              <div class="flex items-center justify-between mb-1">
                <span class="text-xs font-black text-[#1D1D1F] dark:text-white truncate pr-4 capitalize">{{ inc.tipo_incidencia }}</span>
                <span class="text-[9px] font-black text-[#C1C1C7] dark:text-[#636366] uppercase">{{ formatDate(inc.created_at) }}</span>
              </div>
              <p class="text-[11px] font-bold text-[#86868B] dark:text-[#A1A1A6] truncate flex items-center gap-1">
                <i class="pi pi-map-marker text-[9px] text-[#C1C1C7]"></i>{{ inc.direccion }}
              </p>
            </div>
            
            <div class="shrink-0 flex flex-col items-end">
              <div :class="['text-[9px] font-black px-2.5 py-1 rounded-lg uppercase tracking-widest shadow-sm', badgeClass(inc.estatus)]">
                {{ inc.estatus }}
              </div>
            </div>
          </div>
        </div>
        <EmptyState v-else mensaje="Sin actividad reportada" class="flex-1" />
      </div>

      <!-- Mapa Widget (7/12) -->
      <div class="xl:col-span-7 h-full">
         <DashboardMapWidget :incidencias="props.incidencias" class="!rounded-[2.5rem] overflow-hidden border border-app-border shadow-sm h-full" />
      </div>

    </div>

    <!-- ── RENDIMIENTO POR CATEGORÍA ── -->
    <div class="pt-4">
      <DashboardCategoriaTable :stats="categoriaStats" :iconoCategoria="iconoCategoria" />
    </div>

  </div>
</template>

<script setup>
import { computed, toRef } from 'vue'
import { Link } from '@inertiajs/inertia-vue3'
import PageHeader from '@/Components/PageHeader.vue'
import EmptyState from '@/Components/EmptyState.vue'
import DashboardCategoriaTable from '@/Components/DashboardCategoriaTable.vue'
import DashboardMapWidget from '@/Components/DashboardMapWidget.vue'
import Chart from 'primevue/chart'
import { useDashboardStats } from '@/composables/useDashboardStats.js'

const props = defineProps({
  incidencias: { type: Array, default: () => [] },
  users:       { type: Array, default: () => [] },
  roles:       { type: Array, default: () => [] },
})

const {
  tieneIncidencias,
  total,
  statCards,
  chartEstatus,
  chartEstatusLeyenda,
  donutOptions,
  chartCategoria,
  barOptions,
  ultimasIncidencias,
  categoriaStats,
  badgeClass,
  colorEstatus,
  iconoCategoria,
  formatDate,
} = useDashboardStats(toRef(props, 'incidencias'))

const fechaHoy = computed(() =>
  new Date().toLocaleDateString('es-MX', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' })
)
</script>

<style scoped>
@keyframes fadeIn {
  from { opacity: 0; transform: translateY(20px); }
  to   { opacity: 1; transform: translateY(0); }
}
.animate-fade-in {
  animation: fadeIn 0.8s cubic-bezier(0.16, 1, 0.3, 1) both;
}

.custom-scrollbar::-webkit-scrollbar {
  width: 4px;
}
.custom-scrollbar::-webkit-scrollbar-track {
  background: transparent;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
  background: #E5E5E7;
  border-radius: 10px;
}
.dark .custom-scrollbar::-webkit-scrollbar-thumb {
  background: #3A3A3C;
}
</style>
