<template>
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
            v-for="(item, i) in legend" :key="i"
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
</template>

<script setup>
import Chart from 'primevue/chart'
import EmptyState from '@/Components/EmptyState.vue'

defineProps({
  tieneIncidencias: { type: Boolean, required: true },
  total: { type: Number, required: true },
  chartEstatus: { type: Object, required: true },
  donutOptions: { type: Object, required: true },
  legend: { type: Array, required: true },
  chartCategoria: { type: Object, required: true },
  barOptions: { type: Object, required: true }
})
</script>
