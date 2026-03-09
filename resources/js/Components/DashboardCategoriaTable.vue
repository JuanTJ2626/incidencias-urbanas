<template>
  <div class="bg-white dark:bg-app-card rounded-2xl border border-app-border shadow-sm overflow-hidden">
    <div class="flex items-center gap-2 px-5 py-4 border-b border-app-secondary dark:border-app-border">
      <div class="w-8 h-8 rounded-xl bg-app-secondary flex items-center justify-center">
        <i class="pi pi-users text-brand-red text-sm"></i>
      </div>
      <div>
        <h3 class="text-sm font-black text-[#1D1D1F] dark:text-white">Rendimiento por Categoría</h3>
        <p class="text-[10px] text-[#86868B] dark:text-[#A1A1A6] font-semibold uppercase tracking-wide">Tasa de resolución por tipo</p>
      </div>
    </div>

    <div class="overflow-x-auto">
      <table class="min-w-full text-sm">
        <thead>
          <tr class="bg-app-secondary dark:bg-app-secondary border-b border-app-secondary dark:border-app-border">
            <th class="text-left text-[10px] font-black text-[#86868B] dark:text-[#A1A1A6] uppercase tracking-widest px-5 py-3">Categoría</th>
            <th class="text-center text-[10px] font-black text-[#86868B] dark:text-[#A1A1A6] uppercase tracking-widest px-4 py-3">Total</th>
            <th class="text-center text-[10px] font-black text-[#86868B] dark:text-[#A1A1A6] uppercase tracking-widest px-4 py-3">Resueltas</th>
            <th class="text-center text-[10px] font-black text-[#86868B] dark:text-[#A1A1A6] uppercase tracking-widest px-4 py-3">Pendientes</th>
            <th class="text-left text-[10px] font-black text-[#86868B] dark:text-[#A1A1A6] uppercase tracking-widest px-5 py-3">Tasa</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-app-secondary dark:divide-app-border">
          <tr
            v-for="cat in stats"
            :key="cat.nombre"
            class="hover:bg-[#FAFAFA] dark:hover:bg-white/5 transition-colors"
          >
            <td class="px-5 py-3">
              <div class="flex items-center gap-2">
                <div class="w-7 h-7 rounded-lg bg-[#F8E7EB] dark:bg-brand-red/20 border border-brand-red/10 flex items-center justify-center shrink-0">
                  <i :class="[iconoCategoria(cat.nombre), 'text-brand-red text-[10px]']"></i>
                </div>
                <span class="font-semibold text-[#1D1D1F] dark:text-gray-200 capitalize text-xs">{{ cat.nombre }}</span>
              </div>
            </td>
            <td class="px-4 py-3 text-center">
              <span class="text-sm font-black text-[#1D1D1F] dark:text-white">{{ cat.total }}</span>
            </td>
            <td class="px-4 py-3 text-center">
              <span class="text-sm font-bold text-emerald-600">{{ cat.resueltas }}</span>
            </td>
            <td class="px-4 py-3 text-center">
              <span class="text-sm font-bold text-amber-600">{{ cat.pendientes }}</span>
            </td>
            <td class="px-5 py-3">
              <div class="flex items-center gap-2">
                <div class="flex-1 h-1.5 bg-app-secondary rounded-full overflow-hidden min-w-[60px]">
                  <div
                    class="h-full rounded-full transition-all duration-700"
                    :class="cat.tasa >= 70 ? 'bg-emerald-500' : cat.tasa >= 40 ? 'bg-amber-500' : 'bg-rose-500'"
                    :style="{ width: cat.tasa + '%' }"
                  ></div>
                </div>
                <span class="text-[11px] font-black text-[#1D1D1F] dark:text-white w-9 text-right">{{ cat.tasa }}%</span>
              </div>
            </td>
          </tr>
          <tr v-if="!stats.length">
            <td colspan="5" class="px-5 py-8 text-center text-sm text-[#86868B]">Sin datos</td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script>
export default {
  props: {
    stats: { type: Array, default: () => [] },
    iconoCategoria: { type: Function, required: true },
  },
}
</script>
