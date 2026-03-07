
<template>
  <div :class="[gridClass, gap, mb, 'grid']">
    <div
      v-for="(card, idx) in resolvedStats"
      :key="idx"
      v-ripple
      class="p-ripple bg-white p-8 rounded-[2.2rem] shadow-[0_10px_40px_rgba(0,0,0,0.02)] border border-[#E8E8ED] flex flex-col gap-6 hover:-translate-y-2 transition-all duration-500 group cursor-pointer overflow-hidden relative"
      :class="card.hoverColor"
    >
      <!-- Ícono decorativo de fondo (grande, translúcido) -->
      <div class="absolute top-0 right-0 p-4 opacity-5 group-hover:opacity-20 group-hover:scale-150 group-hover:rotate-12 transition-all duration-700">
        <i :class="[card.icon, 'text-6xl text-[#1D1D1F] group-hover:text-white']"></i>
      </div>

      <!-- Ícono principal -->
      <div
        class="w-14 h-14 rounded-2xl flex items-center justify-center text-2xl shadow-sm transition-all duration-500 group-hover:bg-white/20 group-hover:scale-110 group-hover:shadow-none"
        :class="card.bgClass"
      >
        <i :class="[card.icon, card.textClass, 'group-hover:text-white transition-colors']"></i>
      </div>

      <!-- Texto -->
      <div>
        <span class="block text-[#86868B] text-[10px] font-black uppercase tracking-[0.2em] group-hover:text-white/70 transition-colors">
          {{ card.label }}
        </span>
        <span class="block text-4xl font-black text-[#1D1D1F] tracking-tighter mt-1 group-hover:text-white transition-all transform origin-left">
          {{ card.value }}
        </span>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'

/**
 * Uso 1 — manual (como antes, para GestionUsuarios):
 *   <StatsGrid :stats="miArray" />
 *
 * Uso 2 — automático (como AutoTable, para cualquier lista):
 *   <StatsGrid :data="incidencias" groupBy="estatus" />
 *   → agrupa por el campo indicado, asigna color/icono automáticamente.
 */

// Paleta automática: keywords del valor → estilo
const AUTO_STYLES = [
  { keys: ['pendiente'],           icon: 'pi pi-clock',        bg: 'bg-amber-50',   text: 'text-amber-500',   hover: 'hover:bg-[#D97706] hover:shadow-[0_20px_50px_rgba(217,119,6,.3)] hover:border-[#D97706]' },
  { keys: ['proceso', 'progreso'], icon: 'pi pi-sync',         bg: 'bg-sky-50',     text: 'text-sky-600',     hover: 'hover:bg-[#0369A1] hover:shadow-[0_20px_50px_rgba(3,105,161,.3)] hover:border-[#0369A1]' },
  { keys: ['resuelto','completado','done','listo'], icon: 'pi pi-check-circle', bg: 'bg-emerald-50', text: 'text-emerald-600', hover: 'hover:bg-[#059669] hover:shadow-[0_20px_50px_rgba(5,150,105,.3)] hover:border-[#059669]' },
  { keys: ['rechazado','cancelado','error'],        icon: 'pi pi-times-circle', bg: 'bg-rose-50',    text: 'text-rose-600',    hover: 'hover:bg-[#DC2626] hover:shadow-[0_20px_50px_rgba(220,38,38,.3)] hover:border-[#DC2626]' },
  { keys: ['activo','active'],     icon: 'pi pi-check-circle', bg: 'bg-green-50',   text: 'text-green-600',   hover: 'hover:bg-[#34C759] hover:shadow-[0_20px_50px_rgba(52,199,89,.3)] hover:border-[#34C759]' },
  { keys: ['admin'],               icon: 'pi pi-shield',       bg: 'bg-[#F5F5F7]', text: 'text-[#607C88]',   hover: 'hover:bg-[#607C88] hover:shadow-[0_20px_50px_rgba(96,124,136,.3)] hover:border-[#607C88]' },
]
const FALLBACK_COLORS = [
  { hover: 'hover:bg-[#007AFF] hover:shadow-[0_20px_50px_rgba(0,122,255,.3)] hover:border-[#007AFF]' },
  { hover: 'hover:bg-[#FF9500] hover:shadow-[0_20px_50px_rgba(255,149,0,.3)] hover:border-[#FF9500]' },
  { hover: 'hover:bg-[#AF52DE] hover:shadow-[0_20px_50px_rgba(175,82,222,.3)] hover:border-[#AF52DE]' },
  { hover: 'hover:bg-[#FF2D55] hover:shadow-[0_20px_50px_rgba(255,45,85,.3)] hover:border-[#FF2D55]' },
]

function styleForValue(val, fallbackIdx = 0) {
  const s = String(val ?? '').toLowerCase()
  const match = AUTO_STYLES.find(a => a.keys.some(k => s.includes(k)))
  if (match) return { icon: match.icon, bgClass: match.bg, textClass: match.text, hoverColor: match.hover }
  const fb = FALLBACK_COLORS[fallbackIdx % FALLBACK_COLORS.length]
  return { icon: 'pi pi-tag', bgClass: 'bg-[#F5F5F7]', textClass: 'text-[#1D1D1F]', hoverColor: fb.hover }
}

const props = defineProps({
  // Uso manual: array ya construido
  stats:      { type: Array,  default: null },
  // Uso automático: datos crudos + campo a agrupar
  data:       { type: Array,  default: null },
  groupBy:    { type: String, default: 'estatus' },
  totalLabel: { type: String, default: 'Total' },
  totalIcon:  { type: String, default: 'pi pi-list' },
  // Layout
  cols: { type: Number, default: 4 },
  gap:  { type: String, default: 'gap-8' },
  mb:   { type: String, default: 'mb-12' },
})

/** Si recibe :data, auto-genera las KPIs agrupando por `groupBy` */
const resolvedStats = computed(() => {
  if (props.stats) return props.stats   // modo manual

  const rows = props.data ?? []
  const counts = {}
  rows.forEach(row => {
    const val = String(row[props.groupBy] ?? 'sin valor').trim()
    counts[val] = (counts[val] ?? 0) + 1
  })

  const result = [{
    label: props.totalLabel,
    value: rows.length,
    icon:  props.totalIcon,
    bgClass:    'bg-[#F5F5F7]',
    textClass:  'text-[#1D1D1F]',
    hoverColor: 'hover:bg-[#607C88] hover:shadow-[0_20px_50px_rgba(96,124,136,.3)] hover:border-[#607C88]',
  }]

  Object.entries(counts).forEach(([val, count], i) => {
    result.push({
      label: val.charAt(0).toUpperCase() + val.slice(1),
      value: count,
      ...styleForValue(val, i),
    })
  })
  return result
})

const gridClass = computed(() => {
  const map = {
    1: 'grid-cols-1',
    2: 'grid-cols-1 sm:grid-cols-2',
    3: 'grid-cols-1 sm:grid-cols-2 lg:grid-cols-3',
    4: 'grid-cols-1 sm:grid-cols-2 lg:grid-cols-4',
  }
  return map[props.cols] ?? 'grid-cols-1 sm:grid-cols-2 lg:grid-cols-4'
})
</script>
