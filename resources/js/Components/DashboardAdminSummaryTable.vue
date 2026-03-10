<template>
  <div class="chart-container-card">
    <div class="chart-header border-b border-app-border/50">
      <h3 class="chart-title text-brand-red">Resumen Operativo</h3>
      <p class="chart-subtitle">Datos consolidados para auditoría rápida</p>
    </div>
    <div class="p-0 overflow-hidden">
      <DataTable :value="data" class="p-datatable-sm p-datatable-dashboard h-[300px]" scrollable scrollHeight="flex">
        <Column field="id" header="ID"></Column>
        <Column field="tipo" header="Tipo"></Column>
        <Column field="fecha" header="Fecha"></Column>
        <Column field="estatus" header="Estatus">
          <template #body="slotProps">
            <Tag :value="slotProps.data.estatus" :severity="getSeverity(slotProps.data.estatus)" />
          </template>
        </Column>
      </DataTable>
    </div>
  </div>
</template>

<script setup>
import DataTable from 'primevue/datatable'
import Column from 'primevue/column'
import Tag from 'primevue/tag'

defineProps({
  data: { type: Array, required: true }
})

const getSeverity = (estatus) => {
  switch (estatus) {
    case 'Resuelto': return 'success'
    case 'En Proceso': return 'info'
    case 'Pendiente': return 'warning'
    case 'Rechazado': return 'danger'
    default: return null
  }
}
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

:deep(.p-datatable-dashboard) {
  @apply text-sm;
}
:deep(.p-datatable-dashboard .p-datatable-thead > tr > th) {
  @apply bg-app-secondary/30 dark:bg-white/5 text-[10px] font-black uppercase tracking-widest py-4 px-8 border-b border-app-border;
}
:deep(.p-datatable-dashboard .p-datatable-tbody > tr) {
  @apply bg-transparent;
}
:deep(.p-datatable-dashboard .p-datatable-tbody > tr > td) {
  @apply py-4 px-8 border-b border-app-border/40 text-xs font-semibold dark:text-gray-300;
}
</style>
