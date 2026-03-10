<template>
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
</template>

<script setup>
defineProps({
  total: { type: Number, required: true },
  countResueltas: { type: Number, required: true },
  tasaResolucion: { type: Number, required: true },
  miniStats: { type: Array, required: true }
})
</script>
