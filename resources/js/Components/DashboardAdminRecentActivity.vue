<template>
  <div class="xl:col-span-5 bg-white dark:bg-app-card rounded-[2.5rem] border border-app-border shadow-sm overflow-hidden flex flex-col">
    <div class="flex items-center justify-between p-8 border-b border-app-border bg-white dark:bg-app-card sticky top-0 z-10">
      <div class="flex items-center gap-4">
        <div class="w-10 h-10 rounded-xl bg-brand-red/10 flex items-center justify-center">
          <i class="pi pi-bolt text-brand-red text-sm"></i>
        </div>
        <div>
          <h3 class="text-base font-black text-[#1D1D1F] dark:text-white tracking-tight">Actividad Reciente</h3>
          <p class="text-[10px] text-[#86868B] dark:text-[#A1A1A6] font-bold uppercase tracking-widest">Feed Directo</p>
        </div>
      </div>
      <Link href="/admin/gestion-incidencias" class="group flex items-center gap-2 text-[11px] font-black text-brand-red hover:underline uppercase tracking-widest">
        Explorar <i class="pi pi-arrow-right text-[10px] group-hover:translate-x-1 transition-transform"></i>
      </Link>
    </div>

    <div v-if="tieneIncidencias" class="flex-1 overflow-y-auto max-h-[500px] divide-y divide-app-secondary dark:divide-white/5 custom-scrollbar">
      <div
        v-for="inc in ultimasIncidencias"
        :key="inc.id"
        class="flex items-center gap-4 px-8 py-5 hover:bg-[#F9F9FB] dark:hover:bg-white/5 transition-all group cursor-default"
      >
        <div class="w-10 h-10 rounded-xl bg-[#F8E7EB] dark:bg-brand-red/20 border border-brand-red/10 flex items-center justify-center shrink-0 group-hover:scale-110 transition-all duration-300">
          <i :class="[iconoCategoria(inc.tipo_incidencia), 'text-brand-red text-xs']"></i>
        </div>
        
        <div class="flex-1 min-w-0">
          <div class="flex items-center justify-between mb-1">
            <span class="text-xs font-black text-[#1D1D1F] dark:text-white truncate pr-4 capitalize">{{ inc.tipo_incidencia }}</span>
            <span class="text-[9px] font-black text-[#C1C1C7] dark:text-[#636366] uppercase">{{ formatDate(inc.created_at) }}</span>
          </div>
          <p class="text-[11px] font-bold text-[#86868B] dark:text-[#A1A1A6] truncate flex items-center gap-1">
            <i class="pi pi-map-marker text-[9px] text-[#C1C1C7]"></i>{{ inc.direccion }}
          </p>
        </div>
        
        <div class="shrink-0 flex flex-col items-end">
          <div :class="['text-[9px] font-black px-2.5 py-1 rounded-lg uppercase tracking-widest shadow-sm', badgeClass(inc.estatus)]">
            {{ inc.estatus }}
          </div>
        </div>
      </div>
    </div>
    <EmptyState v-else mensaje="Sin actividad reportada" class="flex-1" />
  </div>
</template>

<script setup>
import { Link } from '@inertiajs/inertia-vue3'
import EmptyState from '@/Components/EmptyState.vue'

defineProps({
  tieneIncidencias: { type: Boolean, required: true },
  ultimasIncidencias: { type: Array, required: true },
  iconoCategoria: Function,
  formatDate: Function,
  badgeClass: Function
})
</script>

<style scoped>
.custom-scrollbar::-webkit-scrollbar {
  width: 4px;
}
.custom-scrollbar::-webkit-scrollbar-track {
  background: transparent;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
  background: #E5E5E7;
  border-radius: 10px;
}
.dark .custom-scrollbar::-webkit-scrollbar-thumb {
  background: #3A3A3C;
}
</style>
