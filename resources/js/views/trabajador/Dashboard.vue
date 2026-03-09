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
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
      <div
        v-for="card in statCards"
        :key="card.label"
        class="group relative bg-app-card rounded-2xl border border-app-border shadow-sm p-5 flex flex-col gap-4 overflow-hidden hover:-translate-y-1 hover:shadow-lg transition-all duration-300 cursor-default"
        :class="card.hover"
      >
        <div class="absolute -top-2 -right-2 w-20 h-20 rounded-full opacity-5 group-hover:opacity-20 transition-opacity duration-500"
          :class="card.deco"></div>

        <div class="flex items-start justify-between">
          <div :class="['w-11 h-11 rounded-xl flex items-center justify-center text-xl shrink-0', card.iconBg]">
            <i :class="[card.icon, card.iconColor]"></i>
          </div>
          <span :class="['text-[10px] font-black uppercase tracking-widest px-2 py-0.5 rounded-full', card.badgeBg, card.badgeText]">
            {{ card.pct }}%
          </span>
        </div>

        <div>
          <p class="text-3xl font-black text-[#1D1D1F] dark:text-white tracking-tight group-hover:text-white transition-colors duration-300">
            {{ card.value }}
          </p>
          <p class="text-[11px] font-bold text-[#86868B] dark:text-[#A1A1A6] uppercase tracking-[0.12em] mt-1 group-hover:text-white/70 transition-colors duration-300">
            {{ card.label }}
          </p>
        </div>
      </div>
    </div>

    <!-- ── PROGRESO GLOBAL ─────────────────────────────────────────────────── -->
    <div class="bg-app-card rounded-2xl border border-app-border shadow-sm p-5">
      <div class="flex flex-wrap items-center justify-between gap-3 mb-4">
        <div class="flex items-center gap-2">
          <div class="w-8 h-8 rounded-xl bg-app-secondary flex items-center justify-center">
            <i class="pi pi-trophy text-brand-red text-sm"></i>
          </div>
          <div>
            <h3 class="text-sm font-black text-[#1D1D1F] dark:text-white">Tu progreso</h3>
            <p class="text-[10px] text-[#86868B] dark:text-[#A1A1A6] font-semibold uppercase tracking-wide">
              {{ countResueltas }} de {{ total }} órdenes resueltas
            </p>
          </div>
        </div>
        <span class="text-2xl font-black"
          :class="tasaResolucion >= 70 ? 'text-emerald-600' : tasaResolucion >= 40 ? 'text-amber-500' : 'text-rose-500'"
        >{{ tasaResolucion }}%</span>
      </div>

      <div class="w-full h-3 bg-app-secondary rounded-full overflow-hidden">
        <div
          class="h-full rounded-full transition-all duration-1000"
          :class="tasaResolucion >= 70 ? 'bg-emerald-500' : tasaResolucion >= 40 ? 'bg-amber-500' : 'bg-sky-500'"
          :style="{ width: tasaResolucion + '%' }"
        ></div>
      </div>

      <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mt-4">
        <div v-for="mini in miniStats" :key="mini.label"
          class="flex items-center gap-2 bg-app-bg rounded-xl px-3 py-2.5"
        >
          <span class="w-2 h-2 rounded-full shrink-0" :style="{ background: mini.color }"></span>
          <div>
            <p class="text-base font-black text-[#1D1D1F] dark:text-white leading-none">{{ mini.value }}</p>
            <p class="text-[10px] text-[#86868B] dark:text-[#A1A1A6] font-semibold capitalize mt-0.5">{{ mini.label }}</p>
          </div>
        </div>
      </div>
    </div>

    <!-- ── GRÁFICA + CATEGORÍAS ───────────────────────────────────────────── -->
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
              v-for="(item, i) in chartEstatusLeyenda" :key="i"
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

    <!-- ── ÓRDENES ACTIVAS + RESUELTAS RECIENTES ───────────────────────────── -->
    <div class="grid grid-cols-1 xl:grid-cols-2 gap-4">

      <!-- Órdenes activas -->
      <div class="bg-app-card rounded-2xl border border-app-border shadow-sm overflow-hidden">
        <div class="flex items-center justify-between px-5 py-4 border-b border-app-secondary">
          <div class="flex items-center gap-2">
            <div class="w-8 h-8 rounded-xl bg-app-secondary flex items-center justify-center">
              <i class="pi pi-list-check text-brand-red text-sm"></i>
            </div>
            <div>
              <h3 class="text-sm font-black text-app-text">Órdenes activas</h3>
              <p class="text-[10px] text-app-text-light font-semibold uppercase tracking-wide">
                {{ activas.length }} pendiente{{ activas.length !== 1 ? 's' : '' }} de cierre
              </p>
            </div>
          </div>
          <a href="/trabajador/tareas" class="text-[11px] font-bold text-sky-600 hover:underline flex items-center gap-1">
            Ver todas <i class="pi pi-arrow-right text-[10px]"></i>
          </a>
        </div>

        <div v-if="!activas.length" class="flex flex-col items-center py-12 text-center">
          <i class="pi pi-check-circle text-4xl text-emerald-300 mb-3"></i>
          <p class="text-sm text-gray-500 font-medium">¡Todo al día! No tienes órdenes activas.</p>
        </div>

        <ul class="divide-y divide-app-secondary">
          <li
            v-for="t in activas.slice(0, 6)"
            :key="t.id"
            class="flex items-center gap-4 px-5 py-4 hover:bg-app-secondary transition-colors group"
          >
            <div class="flex-shrink-0 w-9 h-9 rounded-xl flex items-center justify-center"
              :class="iconoFondo(t.estatus)"
            >
              <i :class="[iconoCategoria(t.tipo_incidencia), 'text-sm', iconoColor(t.estatus)]"></i>
            </div>

            <div class="flex-1 min-w-0">
              <p class="text-sm font-bold text-app-text truncate">{{ t.tipo_incidencia }}</p>
              <div class="flex items-center gap-2 mt-0.5">
                <span :class="['text-[10px] font-bold px-2 py-0.5 rounded-full shadow-sm border', badgeClass(t.estatus)]">
                  {{ t.estatus }}
                </span>
                <span class="text-[10px] text-app-text-light truncate hidden sm:block">{{ t.direccion }}</span>
              </div>
            </div>

            <div class="flex flex-col items-end gap-1 shrink-0">
              <span class="text-[10px] text-app-text-light">{{ formatDate(t.created_at) }}</span>
              <a href="/trabajador/tareas"
                class="w-7 h-7 rounded-lg bg-app-card border border-app-border flex items-center justify-center group-hover:border-brand-red group-hover:text-brand-red transition"
              >
                <i class="pi pi-arrow-right text-[10px]"></i>
              </a>
            </div>
          </li>
        </ul>

        <div v-if="activas.length > 6" class="px-5 py-3 border-t border-app-secondary text-center">
          <a href="/trabajador/tareas" class="text-xs font-bold text-sky-600 hover:underline">
            + {{ activas.length - 6 }} más
          </a>
        </div>
      </div>

      <!-- Resueltas recientes -->
      <div class="bg-app-card rounded-2xl border border-app-border shadow-sm overflow-hidden">
        <div class="flex items-center justify-between px-5 py-4 border-b border-app-secondary">
          <div class="flex items-center gap-2">
            <div class="w-8 h-8 rounded-xl bg-app-secondary flex items-center justify-center">
              <i class="pi pi-verified text-emerald-600 text-sm"></i>
            </div>
            <div>
              <h3 class="text-sm font-black text-app-text">Resueltas recientemente</h3>
              <p class="text-[10px] text-app-text-light font-semibold uppercase tracking-wide">Últimas órdenes cerradas</p>
            </div>
          </div>
          <a href="/trabajador/reportes" class="text-[11px] font-bold text-sky-600 hover:underline flex items-center gap-1">
            Ver historial <i class="pi pi-arrow-right text-[10px]"></i>
          </a>
        </div>

        <div v-if="!resueltas.length" class="flex flex-col items-center py-12 text-center">
          <i class="pi pi-history text-4xl text-gray-200 mb-3"></i>
          <p class="text-sm text-gray-500 font-medium">Aún no tienes órdenes resueltas.</p>
        </div>

        <ul v-else class="divide-y divide-app-secondary">
          <li
            v-for="t in resueltas.slice(0, 6)"
            :key="t.id"
            class="flex items-center gap-4 px-5 py-4 hover:bg-app-secondary transition-colors"
          >
            <div class="flex-shrink-0 w-9 h-9 rounded-xl bg-emerald-500/10 dark:bg-emerald-500/20 flex items-center justify-center">
              <i :class="[iconoCategoria(t.tipo_incidencia), 'text-sm text-emerald-600']"></i>
            </div>

            <div class="flex-1 min-w-0">
              <p class="text-sm font-bold text-[#1D1D1F] dark:text-white truncate">{{ t.tipo_incidencia }}</p>
              <p class="text-[10px] text-[#86868B] dark:text-[#A1A1A6] truncate mt-0.5">{{ t.direccion }}</p>
            </div>

            <div class="flex flex-col items-end gap-1 shrink-0">
              <span class="text-[10px] font-bold text-emerald-600">Resuelto</span>
              <span class="text-[10px] text-app-text-light">{{ formatDate(t.cerrado_en || t.updated_at) }}</span>
            </div>
          </li>
        </ul>
      </div>
    </div>

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

<script>
import Chart from 'primevue/chart'

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

export default {
  components: { Chart },

  props: {
    user:   { type: Object, default: () => ({}) },
    tareas: { type: Array,  default: () => [] },
    nuevas: { type: Number, default: 0 },
  },

  computed: {
    // ─── Conteos ──────────────────────────────────────────────────────────
    total()           { return this.tareas.length },
    countPendiente()  { return this.tareas.filter(t => t.estatus === 'pendiente').length },
    countEnProceso()  { return this.tareas.filter(t => t.estatus === 'en proceso').length },
    countEnRevision() { return this.tareas.filter(t => t.estatus === 'en revisión').length },
    countResueltas()  { return this.tareas.filter(t => ['resuelto','completado'].includes(t.estatus)).length },
    countRechazadas() { return this.tareas.filter(t => t.estatus === 'rechazado').length },

    tasaResolucion() {
      return this.total ? Math.round((this.countResueltas / this.total) * 100) : 0
    },

    pct() {
      return (val) => this.total ? Math.round((val / this.total) * 100) : 0
    },

    // ─── Listas ───────────────────────────────────────────────────────────
    activas() {
      return this.tareas.filter(t => !['resuelto','completado','rechazado'].includes(t.estatus))
    },

    resueltas() {
      return [...this.tareas]
        .filter(t => ['resuelto','completado'].includes(t.estatus))
        .sort((a, b) => new Date(b.cerrado_en || b.updated_at) - new Date(a.cerrado_en || a.updated_at))
    },

    // ─── Fecha ────────────────────────────────────────────────────────────
    fechaHoy() {
      return new Date().toLocaleDateString('es-MX', {
        weekday: 'long', year: 'numeric', month: 'long', day: 'numeric',
      })
    },

    // ─── Stat Cards ───────────────────────────────────────────────────────
    statCards() {
      return [
        {
          label: 'Total Asignadas', value: this.total, pct: 100,
          icon: 'pi pi-inbox', iconBg: 'bg-app-secondary', iconColor: 'text-[#1D1D1F] dark:text-white',
          badgeBg: 'bg-gray-100 dark:bg-white/5', badgeText: 'text-[#86868B] dark:text-[#A1A1A6]', deco: 'bg-gray-400',
          hover: 'hover:bg-brand-red hover:border-brand-red hover:shadow-[0_20px_40px_rgba(138,21,56,0.2)]',
        },
        {
          label: 'En Proceso', value: this.countEnProceso + this.countEnRevision,
          pct: this.pct(this.countEnProceso + this.countEnRevision),
          icon: 'pi pi-sync', iconBg: 'bg-sky-50', iconColor: 'text-sky-600',
          badgeBg: 'bg-sky-50', badgeText: 'text-sky-700', deco: 'bg-sky-400',
          hover: 'hover:bg-sky-600 hover:border-sky-600 hover:shadow-[0_20px_40px_rgba(2,132,199,0.3)]',
        },
        {
          label: 'Resueltas', value: this.countResueltas, pct: this.tasaResolucion,
          icon: 'pi pi-check-circle', iconBg: 'bg-emerald-50', iconColor: 'text-emerald-600',
          badgeBg: 'bg-emerald-50', badgeText: 'text-emerald-700', deco: 'bg-emerald-400',
          hover: 'hover:bg-emerald-600 hover:border-emerald-600 hover:shadow-[0_20px_40px_rgba(5,150,105,0.3)]',
        },
        {
          label: 'Pendientes', value: this.countPendiente, pct: this.pct(this.countPendiente),
          icon: 'pi pi-clock', iconBg: 'bg-amber-500/10', iconColor: 'text-amber-500',
          badgeBg: 'bg-amber-500/10', badgeText: 'text-amber-600', deco: 'bg-amber-400',
          hover: 'hover:bg-amber-500 hover:border-amber-500 hover:shadow-[0_20px_40px_rgba(245,158,11,0.3)]',
        },
      ]
    },

    // ─── Mini stats (debajo de la barra de progreso) ─────────────────────
    miniStats() {
      return [
        { label: 'Pendientes',  value: this.countPendiente,  color: '#F59E0B' },
        { label: 'En proceso',  value: this.countEnProceso,  color: '#0EA5E9' },
        { label: 'En revisión', value: this.countEnRevision, color: '#A855F7' },
        { label: 'Rechazadas',  value: this.countRechazadas, color: '#EF4444' },
      ]
    },

    // ─── Chart Donut ──────────────────────────────────────────────────────
    estatusAgrupado() {
      const map = {}
      this.tareas.forEach(t => {
        const k = t.estatus ?? 'sin estatus'
        map[k] = (map[k] || 0) + 1
      })
      return map
    },

    chartEstatus() {
      const labels = Object.keys(this.estatusAgrupado)
      const colors = labels.map(l => ESTATUS_COLORS[l] ?? ESTATUS_DEFAULT)
      return {
        labels,
        datasets: [{
          data: Object.values(this.estatusAgrupado),
          backgroundColor: colors,
          borderColor: '#fff',
          borderWidth: 3,
          hoverOffset: 8,
        }],
      }
    },

    chartEstatusLeyenda() {
      return Object.entries(this.estatusAgrupado).map(([label, value]) => ({
        label, value,
        pct: this.pct(value),
        color: ESTATUS_COLORS[label] ?? ESTATUS_DEFAULT,
      }))
    },

    donutOptions() {
      return {
        responsive: true,
        maintainAspectRatio: true,
        cutout: '68%',
        plugins: {
          legend: { display: false },
          tooltip: {
            callbacks: {
              label: (ctx) => {
                const total = ctx.dataset.data.reduce((a, b) => a + b, 0)
                return ` ${ctx.label}: ${ctx.parsed} (${Math.round((ctx.parsed / total) * 100)}%)`
              },
            },
          },
        },
      }
    },

    // ─── Chart Bar ────────────────────────────────────────────────────────
    categoriaAgrupado() {
      const map = {}
      this.tareas.forEach(t => {
        const k = t.tipo_incidencia ?? 'otro'
        map[k] = (map[k] || 0) + 1
      })
      return Object.entries(map).sort((a, b) => b[1] - a[1]).slice(0, 8)
    },

    chartCategoria() {
      return {
        labels: this.categoriaAgrupado.map(([k]) => k),
        datasets: [{
          label: 'Órdenes',
          data: this.categoriaAgrupado.map(([, v]) => v),
          backgroundColor: this.categoriaAgrupado.map((_, i) => BAR_COLORS[i % BAR_COLORS.length]),
          borderRadius: 8,
          borderSkipped: false,
          hoverBackgroundColor: '#0EA5E9',
        }],
      }
    },

    barOptions() {
      return {
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
            grid: { color: 'var(--app-border)' },
            ticks: { font: { size: 10 }, color: '#86868B', stepSize: 1, precision: 0 },
            beginAtZero: true,
          },
        },
      }
    },
  },

  methods: {
    formatDate(val) {
      if (!val) return '—'
      return new Date(val).toLocaleDateString('es-MX', { day: '2-digit', month: 'short', year: 'numeric' })
    },

    badgeClass(estatus) {
      const m = {
        'pendiente':   'bg-amber-100 text-amber-700 border-amber-200 dark:bg-amber-500/10 dark:text-amber-400 dark:border-amber-500/20',
        'en proceso':  'bg-sky-100 text-sky-700 border-sky-200 dark:bg-sky-500/10 dark:text-sky-400 dark:border-sky-500/20',
        'en revisión': 'bg-purple-100 text-purple-700 border-purple-200 dark:bg-purple-500/10 dark:text-purple-400 dark:border-purple-500/20',
        'resuelto':    'bg-emerald-100 text-emerald-700 border-emerald-200 dark:bg-emerald-500/10 dark:text-emerald-400 dark:border-emerald-500/20',
        'completado':  'bg-emerald-100 text-emerald-700 border-emerald-200 dark:bg-emerald-500/10 dark:text-emerald-400 dark:border-emerald-500/20',
        'rechazado':   'bg-rose-100 text-rose-700 border-rose-200 dark:bg-rose-500/10 dark:text-rose-400 dark:border-rose-500/20',
      }
      return m[estatus] ?? 'bg-gray-100 text-gray-600'
    },

    iconoCategoria(nombre) {
      const n = (nombre || '').toLowerCase()
      if (n.includes('bache') || n.includes('paviment')) return 'pi pi-exclamation-triangle'
      if (n.includes('alumbr') || n.includes('luz'))     return 'pi pi-sun'
      if (n.includes('basura') || n.includes('residuo')) return 'pi pi-trash'
      if (n.includes('agua')  || n.includes('fuga'))     return 'pi pi-tint'
      if (n.includes('árbol') || n.includes('verde'))    return 'pi pi-apple'
      if (n.includes('graffiti')|| n.includes('vand'))   return 'pi pi-palette'
      if (n.includes('ruido') || n.includes('sonido'))   return 'pi pi-volume-up'
      return 'pi pi-map-marker'
    },

    iconoFondo(estatus) {
      const m = { 'pendiente': 'bg-amber-500/10', 'en proceso': 'bg-sky-500/10', 'en revisión': 'bg-purple-500/10' }
      return m[estatus] ?? 'bg-app-secondary'
    },

    iconoColor(estatus) {
      const m = { 'pendiente': 'text-amber-600', 'en proceso': 'text-sky-600', 'en revisión': 'text-purple-600' }
      return m[estatus] ?? 'text-gray-600'
    },
  },
}
</script>

<style scoped>
@keyframes fadeIn {
  from { opacity: 0; transform: translateY(10px); }
  to   { opacity: 1; transform: translateY(0); }
}
.animate-fade-in { animation: fadeIn .4s ease-out forwards; }
</style>
