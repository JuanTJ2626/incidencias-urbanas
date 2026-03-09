<template>
  <Dialog
    v-model:visible="visible"
    :header="inc ? `${inc.tipo_incidencia} — #${inc.id}` : 'Detalle'"
    :modal="true"
    :style="{ width: '780px', maxWidth: '96vw' }"
    :draggable="false"
  >
    <div v-if="inc" class="flex flex-col gap-5 py-1">
      <!-- Ciudadano -->
      <div class="grid grid-cols-2 gap-4 text-sm">
        <div>
          <p class="text-[10px] font-black text-[#86868B] dark:text-[#A1A1A6] uppercase tracking-widest mb-1">Ciudadano</p>
          <p class="font-semibold text-[#1D1D1F] dark:text-white">{{ inc.nombre_ciudadano }}</p>
          <p class="text-xs text-[#86868B] dark:text-[#A1A1A6]">{{ inc.email }}</p>
        </div>
        <div>
          <p class="text-[10px] font-black text-[#86868B] dark:text-[#A1A1A6] uppercase tracking-widest mb-1">Fecha reporte</p>
          <p class="font-semibold text-[#1D1D1F] dark:text-white">{{ inc.created_at }}</p>
          <span :class="['inline-block mt-1 text-[10px] font-black px-2.5 py-0.5 rounded-full uppercase', badgeClass(inc.estatus)]">
            {{ inc.estatus }}
          </span>
        </div>
      </div>

      <div>
        <p class="text-[10px] font-black text-[#86868B] dark:text-[#A1A1A6] uppercase tracking-widest mb-1">Dirección</p>
        <p class="text-sm font-semibold text-[#1D1D1F] dark:text-white">{{ inc.direccion }}</p>
        <p class="text-xs text-[#86868B] dark:text-[#A1A1A6] mt-1">{{ inc.descripcion }}</p>
      </div>

      <!-- Foto original + Mapa reporte -->
      <div class="grid grid-cols-2 gap-4">
        <div class="flex flex-col gap-2">
          <p class="text-[10px] font-black text-[#86868B] dark:text-[#A1A1A6] uppercase tracking-widest flex items-center gap-1">
            <i class="pi pi-image text-brand-red"></i> Foto del reporte
          </p>
          <div class="h-52 bg-app-secondary dark:bg-app-bg rounded-xl overflow-hidden border border-app-border">
            <img v-if="inc.foto" :src="`/storage/${inc.foto}`" class="w-full h-full object-cover" />
            <div v-else class="h-full flex flex-col items-center justify-center text-gray-300 dark:text-gray-600 gap-1">
              <i class="pi pi-image text-3xl"></i>
              <p class="text-xs">Sin foto</p>
            </div>
          </div>
        </div>

        <div class="flex flex-col gap-2">
          <p class="text-[10px] font-black text-[#86868B] dark:text-[#A1A1A6] uppercase tracking-widest flex items-center gap-1">
            <i class="pi pi-map-marker text-brand-red"></i> Ubicación reportada
          </p>
          <div class="h-52 rounded-xl overflow-hidden border border-app-border bg-app-secondary dark:bg-app-bg">
            <iframe
              v-if="inc.latitud && inc.longitud"
              :src="`https://www.google.com/maps/embed/v1/place?key=AIzaSyCchiqlRlOnv6C4pXxh59tYDMRiK501Tmc&q=${inc.latitud},${inc.longitud}&zoom=16`"
              width="100%" height="100%" style="border:0" loading="lazy" allowfullscreen
            ></iframe>
            <div v-else class="h-full flex flex-col items-center justify-center text-gray-300 gap-1">
              <i class="pi pi-map text-3xl"></i>
              <p class="text-xs">Sin coordenadas</p>
            </div>
          </div>
        </div>
      </div>

      <!-- Acciones rápidas -->
      <div class="border-t border-app-border pt-4 flex flex-wrap gap-2">
        <button
          v-if="inc.estatus === 'pendiente'"
          @click="$emit('cambiar-estatus', inc.id, 'en proceso')"
          class="px-3 py-2 rounded-xl text-xs font-bold bg-sky-50 text-sky-600 border border-sky-200 hover:bg-sky-100 transition"
        ><i class="pi pi-play mr-1"></i>Poner en proceso</button>
        <button
          @click="$emit('asignar', inc)"
          class="px-3 py-2 rounded-xl text-xs font-bold bg-amber-50 text-amber-600 border border-amber-200 hover:bg-amber-100 transition"
        ><i class="pi pi-user-plus mr-1"></i>Asignar trabajador</button>
        <button
          v-if="inc.estatus !== 'rechazado'"
          @click="$emit('cambiar-estatus', inc.id, 'rechazado')"
          class="px-3 py-2 rounded-xl text-xs font-bold bg-rose-50 text-rose-600 border border-rose-200 hover:bg-rose-100 transition"
        ><i class="pi pi-times mr-1"></i>Rechazar reporte</button>
      </div>
    </div>
  </Dialog>
</template>

<script>
export default {
  props: {
    modelValue: { type: Boolean, default: false },
    inc: { type: Object, default: null },
  },
  emits: ['update:modelValue', 'cambiar-estatus', 'asignar'],

  computed: {
    visible: {
      get() { return this.modelValue },
      set(val) { this.$emit('update:modelValue', val) },
    },
  },

  methods: {
    badgeClass(estatus) {
      const m = {
        'pendiente':   'bg-gray-100 text-gray-600',
        'en proceso':  'bg-sky-100 text-sky-700',
        'en revisión': 'bg-amber-100 text-amber-700',
        'resuelto':    'bg-emerald-100 text-emerald-700',
        'rechazado':   'bg-rose-100 text-rose-700',
      }
      return m[estatus] || 'bg-gray-100 text-gray-600'
    },
  },
}
</script>
