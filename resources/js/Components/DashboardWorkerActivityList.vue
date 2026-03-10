<template>
  <div class="grid grid-cols-1 xl:grid-cols-2 gap-4">

    <!-- Órdenes activas -->
    <div class="bg-app-card rounded-2xl border border-app-border shadow-sm overflow-hidden text-app-text">
      <div class="flex items-center justify-between px-5 py-4 border-b border-app-secondary">
        <div class="flex items-center gap-2">
          <div class="w-8 h-8 rounded-xl bg-app-secondary flex items-center justify-center">
            <i class="pi pi-list-check text-brand-red text-sm"></i>
          </div>
          <div>
            <h3 class="text-sm font-black">Órdenes activas</h3>
            <p class="text-[10px] text-app-text-light font-semibold uppercase tracking-wide">
              {{ activas.length }} pendiente{{ activas.length !== 1 ? 's' : '' }} de cierre
            </p>
          </div>
        </div>
        <Link href="/trabajador/tareas" class="text-[11px] font-bold text-sky-600 hover:underline flex items-center gap-1">
          Ver todas <i class="pi pi-arrow-right text-[10px]"></i>
        </Link>
      </div>

      <div v-if="!activas.length" class="flex flex-col items-center py-12 text-center text-app-text-light">
        <i class="pi pi-check-circle text-4xl text-emerald-300 mb-3"></i>
        <p class="text-sm font-medium">¡Todo al día! No tienes órdenes activas.</p>
      </div>

      <ul v-else class="divide-y divide-app-secondary">
        <li
          v-for="t in activas.slice(0, 6)"
          :key="t.id"
          class="flex items-center gap-4 px-5 py-4 hover:bg-app-secondary transition-colors group cursor-default"
        >
          <div class="flex-shrink-0 w-9 h-9 rounded-xl flex items-center justify-center"
            :class="iconoFondo(t.estatus)"
          >
            <i :class="[iconoCategoria(t.tipo_incidencia), 'text-sm', iconoColor(t.estatus)]"></i>
          </div>

          <div class="flex-1 min-w-0">
            <p class="text-sm font-bold truncate">{{ t.tipo_incidencia }}</p>
            <div class="flex items-center gap-2 mt-0.5">
              <span :class="['text-[10px] font-bold px-2 py-0.5 rounded-full shadow-sm border', badgeClass(t.estatus)]">
                {{ t.estatus }}
              </span>
              <span class="text-[10px] text-app-text-light truncate hidden sm:block">{{ t.direccion }}</span>
            </div>
          </div>

          <div class="flex flex-col items-end gap-1 shrink-0">
            <span class="text-[10px] text-app-text-light">{{ formatDate(t.created_at) }}</span>
            <Link href="/trabajador/tareas"
              class="w-7 h-7 rounded-lg bg-app-card border border-app-border flex items-center justify-center group-hover:border-brand-red group-hover:text-brand-red transition"
            >
              <i class="pi pi-arrow-right text-[10px]"></i>
            </Link>
          </div>
        </li>
      </ul>

      <div v-if="activas.length > 6" class="px-5 py-3 border-t border-app-secondary text-center">
        <Link href="/trabajador/tareas" class="text-xs font-bold text-sky-600 hover:underline">
          + {{ activas.length - 6 }} más
        </Link>
      </div>
    </div>

    <!-- Resueltas recientes -->
    <div class="bg-app-card rounded-2xl border border-app-border shadow-sm overflow-hidden text-app-text">
      <div class="flex items-center justify-between px-5 py-4 border-b border-app-secondary">
        <div class="flex items-center gap-2">
          <div class="w-8 h-8 rounded-xl bg-app-secondary flex items-center justify-center">
            <i class="pi pi-verified text-emerald-600 text-sm"></i>
          </div>
          <div>
            <h3 class="text-sm font-black">Resueltas recientemente</h3>
            <p class="text-[10px] text-app-text-light font-semibold uppercase tracking-wide">Últimas órdenes cerradas</p>
          </div>
        </div>
        <Link href="/trabajador/reportes" class="text-[11px] font-bold text-sky-600 hover:underline flex items-center gap-1">
          Ver historial <i class="pi pi-arrow-right text-[10px]"></i>
        </Link>
      </div>

      <div v-if="!resueltas.length" class="flex flex-col items-center py-12 text-center text-app-text-light">
        <i class="pi pi-history text-4xl text-gray-200 mb-3"></i>
        <p class="text-sm font-medium">Aún no tienes órdenes resueltas.</p>
      </div>

      <ul v-else class="divide-y divide-app-secondary">
        <li
          v-for="t in resueltas.slice(0, 6)"
          :key="t.id"
          class="flex items-center gap-4 px-5 py-4 hover:bg-app-secondary transition-colors cursor-default"
        >
          <div class="flex-shrink-0 w-9 h-9 rounded-xl bg-emerald-500/10 dark:bg-emerald-500/20 flex items-center justify-center">
            <i :class="[iconoCategoria(t.tipo_incidencia), 'text-sm text-emerald-600']"></i>
          </div>

          <div class="flex-1 min-w-0">
            <p class="text-sm font-bold truncate">{{ t.tipo_incidencia }}</p>
            <p class="text-[10px] text-app-text-light truncate mt-0.5">{{ t.direccion }}</p>
          </div>

          <div class="flex flex-col items-end gap-1 shrink-0">
            <span class="text-[10px] font-bold text-emerald-600">Resuelto</span>
            <span class="text-[10px] text-app-text-light">{{ formatDate(t.cerrado_en || t.updated_at) }}</span>
          </div>
        </li>
      </ul>
    </div>
  </div>
</template>

<script setup>
import { Link } from '@inertiajs/inertia-vue3'

defineProps({
  activas: { type: Array, required: true },
  resueltas: { type: Array, required: true },
  iconoFondo: Function,
  iconoCategoria: Function,
  iconoColor: Function,
  badgeClass: Function,
  formatDate: Function
})
</script>
