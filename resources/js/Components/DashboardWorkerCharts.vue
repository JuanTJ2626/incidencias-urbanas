<template>
  <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">

    <!-- Donut: por estatus -->
    <div class="bg-app-card rounded-2xl border border-app-border shadow-sm p-5">
      <div class="flex items-center gap-2 mb-5">
        <div class="w-8 h-8 rounded-xl bg-app-secondary flex items-center justify-center">
          <i class="pi pi-chart-pie text-brand-red text-sm"></i>
        </div>
        <div>
          <h3 class="text-sm font-black text-[#1D1D1F] dark:text-white">Mis órdenes por estatus</h3>
          <p class="text-[10px] text-[#86868B] dark:text-[#A1A1A6] font-semibold uppercase tracking-wide">Distribución actual</p>
        </div>
      </div>

      <div v-if="total > 0" class="flex flex-col sm:flex-row items-center gap-6">
        <Chart type="doughnut" :data="chartEstatus" :options="donutOptions" class="w-44 h-44 shrink-0" />
        <div class="flex flex-col gap-2 flex-1 w-full">
          <div
            v-for="(item, i) in legend" :key="i"
            class="flex items-center justify-between bg-app-bg rounded-xl px-3 py-2 hover:bg-app-secondary transition-colors"
          >
            <div class="flex items-center gap-2">
              <span class="w-2.5 h-2.5 rounded-full shrink-0" :style="{ background: item.color }"></span>
              <span class="text-xs font-semibold text-app-text capitalize">{{ item.label }}</span>
            </div>
            <div class="flex items-center gap-2">
              <span class="text-xs font-black text-[#1D1D1F] dark:text-white">{{ item.value }}</span>
              <span class="text-[10px] text-[#86868B] dark:text-[#A1A1A6]">({{ item.pct }}%)</span>
            </div>
          </div>
        </div>
      </div>
      <div v-else class="flex flex-col items-center justify-center py-10 text-app-text-light gap-2">
        <i class="pi pi-inbox text-3xl opacity-30"></i>
        <p class="text-sm font-semibold">Sin órdenes asignadas</p>
      </div>
    </div>

    <!-- Bar: por categoría -->
    <div class="bg-app-card rounded-2xl border border-app-border shadow-sm p-5">
      <div class="flex items-center gap-2 mb-5">
        <div class="w-8 h-8 rounded-xl bg-app-secondary flex items-center justify-center">
          <i class="pi pi-chart-bar text-brand-red text-sm"></i>
        </div>
        <div>
          <h3 class="text-sm font-black text-[#1D1D1F] dark:text-white">Tipos de incidencia</h3>
          <p class="text-[10px] text-[#86868B] dark:text-[#A1A1A6] font-semibold uppercase tracking-wide">Categorías asignadas</p>
        </div>
      </div>
      <div v-if="total > 0">
        <Chart type="bar" :data="chartCategoria" :options="barOptions" class="w-full" style="height:220px" />
      </div>
      <div v-else class="flex flex-col items-center justify-center py-10 text-app-text-light gap-2">
        <i class="pi pi-inbox text-3xl opacity-30"></i>
        <p class="text-sm font-semibold">Sin datos</p>
      </div>
    </div>
  </div>
</template>

<script setup>
import Chart from 'primevue/chart'

defineProps({
  total: { type: Number, required: true },
  chartEstatus: { type: Object, required: true },
  donutOptions: { type: Object, required: true },
  legend: { type: Array, required: true },
  chartCategoria: { type: Object, required: true },
  barOptions: { type: Object, required: true }
})
</script>
