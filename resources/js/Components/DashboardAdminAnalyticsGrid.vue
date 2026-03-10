<template>
  <div class="space-y-8">
    <!-- Primera Fila: Estatus y Categorías -->
    <div class="grid grid-cols-1 xl:grid-cols-2 gap-8">
      <div class="chart-container-card" id="chart-status">
        <div class="chart-header">
          <h3 class="chart-title">Estado de Incidencias</h3>
          <p class="chart-subtitle">Distribución porcentual por estatus actual</p>
        </div>
        <div class="p-8 flex justify-center items-center h-[350px]">
          <Chart type="pie" :data="statusData" :options="pieOptions" class="w-full max-w-[300px]" />
        </div>
      </div>

      <div class="chart-container-card" id="chart-categories">
        <div class="chart-header">
          <h3 class="chart-title">Incidencias por Categoría</h3>
          <p class="chart-subtitle">Top 5 tipos de reportes más frecuentes</p>
        </div>
        <div class="p-8 h-[350px]">
          <Chart type="bar" :data="categoryData" :options="barOptions" class="h-full" />
        </div>
      </div>
    </div>

    <!-- Segunda Fila: Línea de tiempo y Usuarios -->
    <div class="grid grid-cols-1 xl:grid-cols-[1fr_400px] gap-8">
      <div class="chart-container-card" id="chart-timeline">
        <div class="chart-header">
          <h3 class="chart-title">Evolución de Reportes</h3>
          <p class="chart-subtitle">Resumen mensual de incidencias recibidas</p>
        </div>
        <div class="p-8 h-[400px]">
          <Chart type="line" :data="timelineData" :options="lineOptions" class="h-full" />
        </div>
      </div>

      <div class="chart-container-card" id="chart-users">
        <div class="chart-header">
          <h3 class="chart-title">Distribución de Usuarios</h3>
          <p class="chart-subtitle">Personal operativo vs ciudadanos</p>
        </div>
        <div class="p-8 flex justify-center items-center h-[400px]">
          <Chart type="doughnut" :data="userData" :options="doughnutOptions" class="w-full max-w-[280px]" />
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import Chart from 'primevue/chart'

defineProps({
  statusData: Object,
  categoryData: Object,
  timelineData: Object,
  userData: Object,
  pieOptions: Object,
  barOptions: Object,
  lineOptions: Object,
  doughnutOptions: Object
})
</script>

<style scoped>
@reference "../../css/app.css";

.chart-container-card {
  @apply bg-white/80 dark:bg-app-card/80 backdrop-blur-3xl rounded-[2.5rem] border border-app-border 
         shadow-[0_20px_60px_-15px_rgba(0,0,0,0.05)] overflow-hidden transition-all duration-500
         hover:border-brand-red/20;
}

.chart-header {
  @apply px-8 py-6;
}

.chart-title {
  @apply text-lg font-black text-[#1D1D1F] dark:text-white tracking-tight leading-none;
}

.chart-subtitle {
  @apply text-[10px] text-[#86868B] dark:text-[#A1A1A6] font-black uppercase tracking-[0.2em] mt-2;
}
</style>
