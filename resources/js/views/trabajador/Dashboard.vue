<template>
  <div class="animate-fade-in py-6 pl-4 pr-6 space-y-6">

    <!-- ── HEADER ─────────────────────────────────────────────────────────── -->
    <PageHeader
      :title="`Bienvenido, ${user?.name?.split(' ')[0] || 'Trabajador'}`"
      subtitle="Resumen de tu actividad y órdenes de trabajo asignadas."
    >
      <template #actions>
        <div class="flex items-center gap-2">
          <div v-if="nuevas > 0"
            class="flex items-center gap-2 bg-sky-50 border border-sky-200 text-sky-700 px-4 py-2 rounded-xl text-sm font-bold"
          >
            <i class="pi pi-bell text-sky-500 animate-bounce"></i>
            {{ nuevas }} nueva{{ nuevas > 1 ? 's' : '' }}
          </div>
          <div class="flex items-center gap-2 text-xs text-[#86868B] dark:text-[#A1A1A6] bg-app-card border border-app-border rounded-xl px-3 py-2 shadow-sm">
            <i class="pi pi-calendar text-brand-red"></i>
            <span class="font-semibold">{{ fechaHoy }}</span>
          </div>
        </div>
      </template>
    </PageHeader>

    <!-- ── STAT CARDS ─────────────────────────────────────────────────────── -->
    <DashboardWorkerStatsGrid :cards="statCards" />

    <!-- ── PROGRESO GLOBAL ─────────────────────────────────────────────────── -->
    <DashboardWorkerProgress 
      :total="total" 
      :countResueltas="countResueltas" 
      :tasaResolucion="tasaResolucion" 
      :miniStats="miniStats" 
    />

    <!-- ── GRÁFICA + CATEGORÍAS ───────────────────────────────────────────── -->
    <DashboardWorkerCharts 
      :total="total"
      :chartEstatus="chartEstatus"
      :donutOptions="donutOptions"
      :legend="chartEstatusLeyenda"
      :chartCategoria="chartCategoria"
      :barOptions="barOptions"
    />

    <!-- ── ÓRDENES ACTIVAS + RESUELTAS RECIENTES ───────────────────────────── -->
    <DashboardWorkerActivityList 
      :activas="activas"
      :resueltas="resueltas"
      :iconoFondo="iconoFondo"
      :iconoCategoria="iconoCategoria"
      :iconoColor="iconoColor"
      :badgeClass="badgeClass"
      :formatDate="formatDate"
    />

    <!-- ── BANNER MOTIVACIONAL (solo cuando todo está resuelto) ─────────────── -->
    <div
      v-if="total > 0 && activas.length === 0"
      class="rounded-2xl overflow-hidden bg-gradient-to-br from-emerald-500 to-teal-600 p-6 flex items-center gap-5 shadow-lg"
    >
      <div class="w-14 h-14 rounded-2xl bg-white/20 flex items-center justify-center shrink-0">
        <i class="pi pi-star-fill text-white text-2xl"></i>
      </div>
      <div class="flex-1">
        <p class="text-white font-black text-lg leading-tight">
          ¡Excelente trabajo, {{ user?.name?.split(' ')[0] || 'trabajador' }}!
        </p>
        <p class="text-white/80 text-sm mt-0.5">Has completado todas tus órdenes asignadas. Sigue así.</p>
      </div>
      <span class="text-4xl font-black text-white/30 hidden sm:block">100%</span>
    </div>

  </div>
</template>

<script setup>
import { computed } from 'vue'
import PageHeader from '@/Components/PageHeader.vue'
import DashboardWorkerStatsGrid from '@/Components/DashboardWorkerStatsGrid.vue'
import DashboardWorkerProgress from '@/Components/DashboardWorkerProgress.vue'
import DashboardWorkerCharts from '@/Components/DashboardWorkerCharts.vue'
import DashboardWorkerActivityList from '@/Components/DashboardWorkerActivityList.vue'

const props = defineProps({
  user:   { type: Object, default: () => ({}) },
  tareas: { type: Array,  default: () => [] },
  nuevas: { type: Number, default: 0 },
})

const ESTATUS_COLORS = {
  'pendiente':   '#F59E0B',
  'en proceso':  '#0EA5E9',
  'en revisión': '#A855F7',
  'resuelto':    '#10B981',
  'completado':  '#10B981',
  'rechazado':   '#EF4444',
}
const ESTATUS_DEFAULT = '#86868B'
const BAR_COLORS = ['#0EA5E9','#F59E0B','#10B981','#A855F7','#EF4444','#8A1538','#14B8A6','#F97316']

// ─── Conteos ──────────────────────────────────────────────────────────
const total           = computed(() => props.tareas.length)
const countPendiente  = computed(() => props.tareas.filter(t => t.estatus === 'pendiente').length)
const countEnProceso  = computed(() => props.tareas.filter(t => t.estatus === 'en proceso').length)
const countEnRevision = computed(() => props.tareas.filter(t => t.estatus === 'en revisión').length)
const countResueltas  = computed(() => props.tareas.filter(t => ['resuelto','completado'].includes(t.estatus)).length)
const countRechazadas = computed(() => props.tareas.filter(t => t.estatus === 'rechazado').length)

const tasaResolucion = computed(() =>
  total.value ? Math.round((countResueltas.value / total.value) * 100) : 0
)

const getPct = (val) => total.value ? Math.round((val / total.value) * 100) : 0

// ─── Listas ───────────────────────────────────────────────────────────
const activas = computed(() =>
  props.tareas.filter(t => !['resuelto','completado','rechazado'].includes(t.estatus))
)

const resueltas = computed(() =>
  [...props.tareas]
    .filter(t => ['resuelto','completado'].includes(t.estatus))
    .sort((a, b) => new Date(b.cerrado_en || b.updated_at) - new Date(a.cerrado_en || a.updated_at))
)

// ─── Fecha ────────────────────────────────────────────────────────────
const fechaHoy = computed(() =>
  new Date().toLocaleDateString('es-MX', {
    weekday: 'long', year: 'numeric', month: 'long', day: 'numeric',
  })
)

// ─── Stat Cards ───────────────────────────────────────────────────────
const statCards = computed(() => [
  {
    label: 'Total Asignadas', value: total.value, pct: 100,
    icon: 'pi pi-inbox', iconBg: 'bg-app-secondary', iconColor: 'text-[#1D1D1F] dark:text-white',
    badgeBg: 'bg-gray-100 dark:bg-white/5', badgeText: 'text-[#86868B] dark:text-[#A1A1A6]', deco: 'bg-gray-400',
    hover: 'hover:bg-brand-red hover:border-brand-red hover:shadow-[0_20px_40px_rgba(138,21,56,0.2)]',
  },
  {
    label: 'En Proceso', value: countEnProceso.value + countEnRevision.value,
    pct: getPct(countEnProceso.value + countEnRevision.value),
    icon: 'pi pi-sync', iconBg: 'bg-sky-50', iconColor: 'text-sky-600',
    badgeBg: 'bg-sky-50', badgeText: 'text-sky-700', deco: 'bg-sky-400',
    hover: 'hover:bg-sky-600 hover:border-sky-600 hover:shadow-[0_20px_40px_rgba(2,132,199,0.3)]',
  },
  {
    label: 'Resueltas', value: countResueltas.value, pct: tasaResolucion.value,
    icon: 'pi pi-check-circle', iconBg: 'bg-emerald-50', iconColor: 'text-emerald-600',
    badgeBg: 'bg-emerald-50', badgeText: 'text-emerald-700', deco: 'bg-emerald-400',
    hover: 'hover:bg-emerald-600 hover:border-emerald-600 hover:shadow-[0_20px_40px_rgba(5,150,105,0.3)]',
  },
  {
    label: 'Pendientes', value: countPendiente.value, pct: getPct(countPendiente.value),
    icon: 'pi pi-clock', iconBg: 'bg-amber-500/10', iconColor: 'text-amber-500',
    badgeBg: 'bg-amber-500/10', badgeText: 'text-amber-600', deco: 'bg-amber-400',
    hover: 'hover:bg-amber-500 hover:border-amber-500 hover:shadow-[0_20px_40px_rgba(245,158,11,0.3)]',
  },
])

// ─── Mini stats (debajo de la barra de progreso) ─────────────────────
const miniStats = computed(() => [
  { label: 'Pendientes',  value: countPendiente.value,  color: '#F59E0B' },
  { label: 'En proceso',  value: countEnProceso.value,  color: '#0EA5E9' },
  { label: 'En revisión', value: countEnRevision.value, color: '#A855F7' },
  { label: 'Rechazadas',  value: countRechazadas.value, color: '#EF4444' },
])

// ─── Chart Donut ──────────────────────────────────────────────────────
const estatusAgrupado = computed(() => {
  const map = {}
  props.tareas.forEach(t => {
    const k = t.estatus ?? 'sin estatus'
    map[k] = (map[k] || 0) + 1
  })
  return map
})

const chartEstatus = computed(() => {
  const labels = Object.keys(estatusAgrupado.value)
  const colors = labels.map(l => ESTATUS_COLORS[l] ?? ESTATUS_DEFAULT)
  return {
    labels,
    datasets: [{
      data: Object.values(estatusAgrupado.value),
      backgroundColor: colors,
      borderColor: '#fff',
      borderWidth: 3,
      hoverOffset: 8,
    }],
  }
})

const chartEstatusLeyenda = computed(() =>
  Object.entries(estatusAgrupado.value).map(([label, value]) => ({
    label, value,
    pct: getPct(value),
    color: ESTATUS_COLORS[label] ?? ESTATUS_DEFAULT,
  }))
)

const donutOptions = {
  responsive: true,
  maintainAspectRatio: true,
  cutout: '68%',
  plugins: {
    legend: { display: false },
    tooltip: {
      callbacks: {
        label: (ctx) => {
          const totalVal = ctx.dataset.data.reduce((a, b) => a + b, 0)
          return ` ${ctx.label}: ${ctx.parsed} (${Math.round((ctx.parsed / totalVal) * 100)}%)`
        },
      },
    },
  },
}

// ─── Chart Bar ────────────────────────────────────────────────────────
const categoriaAgrupado = computed(() => {
  const map = {}
  props.tareas.forEach(t => {
    const k = t.tipo_incidencia ?? 'otro'
    map[k] = (map[k] || 0) + 1
  })
  return Object.entries(map).sort((a, b) => b[1] - a[1]).slice(0, 8)
})

const chartCategoria = computed(() => ({
  labels: categoriaAgrupado.value.map(([k]) => k),
  datasets: [{
    label: 'Órdenes',
    data: categoriaAgrupado.value.map(([, v]) => v),
    backgroundColor: categoriaAgrupado.value.map((_, i) => BAR_COLORS[i % BAR_COLORS.length]),
    borderRadius: 8,
    borderSkipped: false,
    hoverBackgroundColor: '#0EA5E9',
  }],
}))

const barOptions = {
  responsive: true,
  maintainAspectRatio: false,
  plugins: {
    legend: { display: false },
    tooltip: { callbacks: { label: (ctx) => ` ${ctx.parsed.y} órdenes` } },
  },
  scales: {
    x: {
      grid: { display: false },
      ticks: { font: { size: 10, weight: 'bold' }, color: '#86868B', maxRotation: 30 },
    },
    y: {
      grid: { color: 'rgba(0,0,0,0.05)' },
      ticks: { font: { size: 10 }, color: '#86868B', stepSize: 1, precision: 0 },
      beginAtZero: true,
    },
  },
}

// ─── Utilidades ────────────────────────────────────────────────────────
const formatDate = (val) => {
  if (!val) return '—'
  return new Date(val).toLocaleDateString('es-MX', { day: '2-digit', month: 'short', year: 'numeric' })
}

const badgeClass = (estatus) => {
  const m = {
    'pendiente':   'bg-amber-100 text-amber-700 border-amber-200 dark:bg-amber-500/10 dark:text-amber-400 dark:border-amber-500/20',
    'en proceso':  'bg-sky-100 text-sky-700 border-sky-200 dark:bg-sky-500/10 dark:text-sky-400 dark:border-sky-500/20',
    'en revisión': 'bg-purple-100 text-purple-700 border-purple-200 dark:bg-purple-500/10 dark:text-purple-400 dark:border-purple-500/20',
    'resuelto':    'bg-emerald-100 text-emerald-700 border-emerald-200 dark:bg-emerald-500/10 dark:text-emerald-400 dark:border-emerald-500/20',
    'completado':  'bg-emerald-100 text-emerald-700 border-emerald-200 dark:bg-emerald-500/10 dark:text-emerald-400 dark:border-emerald-500/20',
    'rechazado':   'bg-rose-100 text-rose-700 border-rose-200 dark:bg-rose-500/10 dark:text-rose-400 dark:border-rose-500/20',
  }
  return m[estatus] ?? 'bg-gray-100 text-gray-600'
}

const iconoCategoria = (nombre) => {
  const n = (nombre || '').toLowerCase()
  if (n.includes('bache') || n.includes('paviment')) return 'pi pi-exclamation-triangle'
  if (n.includes('alumbr') || n.includes('luz'))     return 'pi pi-sun'
  if (n.includes('basura') || n.includes('residuo')) return 'pi pi-trash'
  if (n.includes('agua')  || n.includes('fuga'))     return 'pi pi-tint'
  if (n.includes('árbol') || n.includes('verde'))    return 'pi pi-apple'
  if (n.includes('graffiti')|| n.includes('vand'))   return 'pi pi-palette'
  if (n.includes('ruido') || n.includes('sonido'))   return 'pi pi-volume-up'
  return 'pi pi-map-marker'
}

const iconoFondo = (estatus) => {
  const m = { 'pendiente': 'bg-amber-500/10', 'en proceso': 'bg-sky-500/10', 'en revisión': 'bg-purple-500/10' }
  return m[estatus] ?? 'bg-app-secondary'
}

const iconoColor = (estatus) => {
  const m = { 'pendiente': 'text-amber-600', 'en proceso': 'text-sky-600', 'en revisión': 'text-purple-600' }
  return m[estatus] ?? 'text-gray-600'
}
</script>

<style scoped>
@keyframes fadeIn {
  from { opacity: 0; transform: translateY(10px); }
  to   { opacity: 1; transform: translateY(0); }
}
.animate-fade-in { animation: fadeIn .4s ease-out forwards; }
</style>
