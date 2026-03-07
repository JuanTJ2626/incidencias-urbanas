<template>
  <div class="animate-fade-in py-6 pl-4 pr-6">
    <PageHeader
      :title="`Bienvenido, ${user?.name?.split(' ')[0] || 'Trabajador'}`"
      subtitle="Resumen de tus órdenes de trabajo asignadas."
    >
      <template #actions>
        <div v-if="nuevas > 0"
          class="flex items-center gap-2 bg-sky-50 border border-sky-200 text-sky-700 px-4 py-2 rounded-xl text-sm font-bold"
        >
          <i class="pi pi-bell text-sky-500 animate-bounce"></i>
          {{ nuevas }} orden{{ nuevas > 1 ? 'es' : '' }} nueva{{ nuevas > 1 ? 's' : '' }} asignada{{ nuevas > 1 ? 's' : '' }}
        </div>
      </template>
    </PageHeader>

    <!-- KPIs automáticas desde los datos del trabajador -->
    <StatsGrid :data="tareas" groupBy="estatus" :cols="4" gap="gap-4" mb="mb-6" />

    <!-- Órdenes activas (en proceso / pendientes) -->
    <div class="bg-white border border-[#E8E8ED] rounded-2xl shadow-sm overflow-hidden">
      <div class="flex items-center justify-between px-6 py-5 border-b border-[#F5F5F7]">
        <div class="flex items-center gap-3">
          <div class="w-10 h-10 rounded-xl bg-sky-100 flex items-center justify-center">
            <i class="pi pi-list-check text-sky-600 text-lg"></i>
          </div>
          <div>
            <h3 class="text-base font-bold text-[#1D1D1F] leading-none">Órdenes activas</h3>
            <p class="text-xs text-[#86868B] mt-0.5">{{ activas.length }} pendiente{{ activas.length !== 1 ? 's' : '' }} de cierre</p>
          </div>
        </div>
        <a href="/trabajador/tareas" class="text-xs font-bold text-sky-600 hover:text-sky-700 flex items-center gap-1">
          Ver todas <i class="pi pi-arrow-right text-[10px]"></i>
        </a>
      </div>

      <div v-if="!activas.length" class="flex flex-col items-center py-12 text-center">
        <i class="pi pi-check-circle text-4xl text-emerald-300 mb-3"></i>
        <p class="text-sm text-gray-500 font-medium">Todo al día. No tienes órdenes pendientes.</p>
      </div>

      <ul v-else class="divide-y divide-[#F5F5F7]">
        <li
          v-for="t in activas"
          :key="t.id"
          class="flex items-center gap-4 px-6 py-4 hover:bg-[#F5F5F7] transition-colors group"
        >
          <!-- Dot de estatus -->
          <span
            class="w-2.5 h-2.5 rounded-full shrink-0"
            :class="t.estatus === 'en proceso' ? 'bg-sky-400 animate-pulse' : 'bg-amber-400'"
          ></span>

          <!-- Info -->
          <div class="flex-1 min-w-0">
            <p class="text-sm font-bold text-[#1D1D1F] truncate">{{ t.tipo_incidencia }}</p>
            <p class="text-xs text-[#86868B] truncate">{{ t.direccion }}</p>
          </div>

          <!-- Fecha -->
          <span class="text-xs text-[#86868B] shrink-0 hidden sm:block">{{ formatDate(t.created_at) }}</span>

          <!-- Link a tareas -->
          <a href="/trabajador/tareas" class="shrink-0 w-8 h-8 rounded-xl bg-white border border-[#E8E8ED] flex items-center justify-center group-hover:border-sky-300 group-hover:text-sky-600 transition">
            <i class="pi pi-arrow-right text-xs"></i>
          </a>
        </li>
      </ul>
    </div>
  </div>
</template>

<script>
export default {
  props: {
    user:   { type: Object, default: () => ({}) },
    tareas: { type: Array,  default: () => [] },
    nuevas: { type: Number, default: 0 },
  },
  computed: {
    activas() {
      return this.tareas.filter(t => t.estatus !== 'resuelto' && t.estatus !== 'rechazado')
    },
  },
  methods: {
    formatDate(val) {
      if (!val) return '—'
      return new Date(val).toLocaleDateString('es-MX', { day: '2-digit', month: 'short' })
    },
  },
}
</script>

<style scoped>
@keyframes fadeIn { from { opacity:0; transform:translateY(8px) } to { opacity:1; transform:translateY(0) } }
.animate-fade-in { animation: fadeIn .35s ease-out forwards }
</style>
