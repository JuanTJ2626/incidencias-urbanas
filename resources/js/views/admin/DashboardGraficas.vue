<template>
  <div class="animate-fade-in py-6 pl-4 pr-6 space-y-8">
    <!-- Header Premium -->
    <PageHeader 
      title="Dashboard de Analíticas" 
      subtitle="Visualiza el rendimiento del sistema, incidencias y actividad de usuarios en tiempo real."
    >
      <template #actions>
        <div class="flex flex-wrap items-center gap-3">
          <Button 
            label="Exportar a Excel" 
            icon="pi pi-file-excel" 
            class="!rounded-xl !bg-emerald-600 !border-emerald-600 !text-white !font-bold hover:!bg-emerald-700 transition-all shadow-lg hover:shadow-emerald-500/20"
            @click="exportToExcel"
          />
          <Button 
            label="Descargar PDF" 
            icon="pi pi-file-pdf" 
            class="!rounded-xl !bg-rose-600 !border-rose-600 !text-white !font-bold hover:!bg-rose-700 transition-all shadow-lg hover:shadow-rose-500/20"
            @click="exportToPDF"
          />
        </div>
      </template>
    </PageHeader>

    <!-- Grid de KPIs rápidos -->
    <DashboardAdminKPIs :stats="quickStats" />

    <!-- Bloque de Gráficas -->
    <DashboardAdminAnalyticsGrid
      :statusData="statusChartData"
      :categoryData="categoryChartData"
      :timelineData="timelineChartData"
      :userData="userChartData"
      :pieOptions="pieOptions"
      :barOptions="barOptions"
      :lineOptions="lineOptions"
      :doughnutOptions="doughnutOptions"
    />

    <!-- Tabla de Resumen -->
    <DashboardAdminSummaryTable :data="incidenciasData" />

  </div>
</template>

<script setup>
import { ref } from 'vue'
import { utils, writeFile } from 'xlsx'
import { jsPDF } from 'jspdf'
import 'jspdf-autotable'
import PageHeader from '@/Components/PageHeader.vue'
import DashboardAdminKPIs from '@/Components/DashboardAdminKPIs.vue'
import DashboardAdminAnalyticsGrid from '@/Components/DashboardAdminAnalyticsGrid.vue'
import DashboardAdminSummaryTable from '@/Components/DashboardAdminSummaryTable.vue'
import Button from 'primevue/button'

// Datos Mock (Simulación de incidencias y usuarios)
const incidenciasData = ref([
  { id: '1023', tipo: 'Bacheo', fecha: '2024-03-01', estatus: 'Resuelto' },
  { id: '1024', tipo: 'Luminaria', fecha: '2024-03-02', estatus: 'En Proceso' },
  { id: '1025', tipo: 'Basura', fecha: '2024-03-03', estatus: 'Pendiente' },
  { id: '1026', tipo: 'Fuga de Agua', fecha: '2024-03-04', estatus: 'Resuelto' },
  { id: '1027', tipo: 'Seguridad', fecha: '2024-03-05', estatus: 'Rechazado' },
  { id: '1028', tipo: 'Poda', fecha: '2024-03-05', estatus: 'En Proceso' },
])

const quickStats = ref([
  { label: 'Total Incidencias', value: '1,280', icon: 'pi pi-exclamation-circle', colorClass: 'bg-brand-red/10 text-brand-red' },
  { label: 'En Proceso', value: '45', icon: 'pi pi-spin pi-spinner', colorClass: 'bg-sky-500/10 text-sky-500' },
  { label: 'Resueltas', value: '1,120', icon: 'pi pi-check-circle', colorClass: 'bg-emerald-500/10 text-emerald-500' },
  { label: 'Usuarios Activos', value: '850', icon: 'pi pi-users', colorClass: 'bg-purple-500/10 text-purple-500' },
])

// Configuración de Gráficas
const statusChartData = ref({
  labels: ['Pendientes', 'En Proceso', 'Resueltos', 'Rechazados'],
  datasets: [{
    data: [300, 150, 750, 80],
    backgroundColor: ['#f59e0b', '#3b82f6', '#10b981', '#ef4444'],
    hoverOffset: 20
  }]
})

const categoryChartData = ref({
  labels: ['Bacheo', 'Luminarias', 'Basura', 'Seguridad', 'Agua'],
  datasets: [{
    label: 'Reportes por Categoría',
    data: [450, 320, 210, 180, 120],
    backgroundColor: 'rgba(138, 21, 56, 0.8)',
    borderRadius: 8
  }]
})

const timelineChartData = ref({
  labels: ['Sep', 'Oct', 'Nov', 'Dec', 'Jan', 'Feb', 'Mar'],
  datasets: [{
    label: 'Reportes Mensuales',
    data: [65, 59, 80, 81, 56, 55, 120],
    fill: true,
    borderColor: '#8A1538',
    backgroundColor: 'rgba(138, 21, 56, 0.1)',
    tension: 0.4
  }]
})

const userChartData = ref({
  labels: ['Trabajadores', 'Ciudadanos', 'Administradores'],
  datasets: [{
    data: [120, 680, 50],
    backgroundColor: ['#1D1D1F', '#8A1538', '#E5E5EA'],
  }]
})

// Opciones de Estilo para Chart.js
const commonOptions = {
  plugins: { legend: { labels: { color: '#86868B', font: { weight: 'bold', size: 10 } } } },
  responsive: true,
  maintainAspectRatio: false
}

const pieOptions = { ...commonOptions }
const doughnutOptions = { ...commonOptions, cutout: '65%' }

const barOptions = {
  ...commonOptions,
  scales: {
    y: { grid: { color: 'rgba(0,0,0,0.05)' }, ticks: { color: '#86868B' } },
    x: { grid: { display: false }, ticks: { color: '#86868B' } }
  }
}

const lineOptions = {
  ...commonOptions,
  scales: {
    y: { grid: { color: 'rgba(0,0,0,0.05)' }, border: { dash: [5, 5] }, ticks: { color: '#86868B' } },
    x: { grid: { display: false }, ticks: { color: '#86868B' } }
  }
}

// Funciones de Exportación
const exportToExcel = () => {
  const wsData = incidenciasData.value.map(inc => ({
    'ID': inc.id,
    'Categoría': inc.tipo,
    'Fecha de Reporte': inc.fecha,
    'Estado Actual': inc.estatus
  }))
  
  const ws = utils.json_to_sheet(wsData)
  const wb = utils.book_new()
  utils.book_append_sheet(wb, ws, "Incidencias")
  writeFile(wb, "Reporte_Incidencias_SIGIU.xlsx")
}

const exportToPDF = async () => {
  const doc = new jsPDF('p', 'mm', 'a4')
  const logo = new Image()
  logo.src = '/public/img/jordan.png'
  
  // Encabezado del PDF
  doc.setFillColor(138, 21, 56)
  doc.rect(0, 0, 210, 40, 'F')
  doc.setTextColor(255, 255, 255)
  doc.setFontSize(22)
  doc.text('SIGIU - REPORTE DE GESTIÓN', 20, 25)
  doc.setFontSize(10)
  doc.text('Generado automáticamente por el Sistema Integral de Incidencias', 20, 32)
  
  // Tabla de datos
  const columns = ["ID", "Tipo", "Fecha", "Estatus"]
  const rows = incidenciasData.value.map(inc => [inc.id, inc.tipo, inc.fecha, inc.estatus])
  
  doc.autoTable({
    startY: 50,
    head: [columns],
    body: rows,
    theme: 'grid',
    headStyles: { fillStyle: [138, 21, 56], textColor: [255, 255, 255] },
  })

  // Mensaje final
  doc.setFontSize(8)
  doc.setTextColor(150, 150, 150)
  doc.text('Este documento es una representación digital de los datos activos en el sistema.', 20, doc.lastAutoTable.finalY + 10)
  
  doc.save('Reporte_Analitico_SIGIU.pdf')
}

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
@reference "../../../css/app.css";

.animate-fade-in {
  animation: fadeIn 0.8s cubic-bezier(0.23, 1, 0.32, 1) forwards;
}

@keyframes fadeIn {
  from { opacity: 0; transform: translateY(20px); }
  to { opacity: 1; transform: translateY(0); }
}

/* Tarjetas de KPI */
.premium-stat-card {
  @apply bg-white/70 dark:bg-app-card/70 backdrop-blur-3xl rounded-[2rem] border border-app-border p-7 
         shadow-[0_8px_30px_rgba(0,0,0,0.04)] relative overflow-hidden transition-all duration-500 
         hover:shadow-2xl hover:-translate-y-1;
}

.stat-icon-wrapper {
  @apply w-12 h-12 rounded-2xl flex items-center justify-center text-xl shadow-inner;
}

/* Contenedores de Gráficas */
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

/* Estilos de Tabla Dashboard */
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
