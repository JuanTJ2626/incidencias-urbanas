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
    <DashboardAdminStatsGrid :cards="statCards" />

    <!-- ── BLOQUE DE MÉTRICAS ANALÍTICAS ── -->
    <DashboardAdminChartsSection
      :tieneIncidencias="tieneIncidencias"
      :total="total"
      :chartEstatus="chartEstatus"
      :donutOptions="donutOptions"
      :legend="chartEstatusLeyenda"
      :chartCategoria="chartCategoria"
      :barOptions="barOptions"
    />

    <!-- ── SECCIÓN CENTRAL: MAPA + RECIENTES ── -->
    <div class="grid grid-cols-1 xl:grid-cols-12 gap-6 items-stretch">
      
      <!-- Listado de Últimas (5/12) -->
      <DashboardAdminRecentActivity
        :tieneIncidencias="tieneIncidencias"
        :ultimasIncidencias="ultimasIncidencias"
        :iconoCategoria="iconoCategoria"
        :formatDate="formatDate"
        :badgeClass="badgeClass"
      />

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
import DashboardAdminStatsGrid from '@/Components/DashboardAdminStatsGrid.vue'
import DashboardAdminChartsSection from '@/Components/DashboardAdminChartsSection.vue'
import DashboardAdminRecentActivity from '@/Components/DashboardAdminRecentActivity.vue'
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
