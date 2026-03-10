<template>
  <div class="xl:col-span-2 bg-white dark:bg-app-card rounded-2xl border border-app-border shadow-sm overflow-hidden flex flex-col min-h-[380px]">
    <!-- Header -->
    <div class="flex items-center justify-between px-5 py-4 border-b border-app-secondary dark:border-app-border">
      <div class="flex items-center gap-2">
        <div class="w-8 h-8 rounded-xl bg-app-secondary flex items-center justify-center">
          <i class="pi pi-map text-brand-red text-sm"></i>
        </div>
        <div>
          <h3 class="text-sm font-black text-[#1D1D1F] dark:text-white">Mapa de Incidencias</h3>
          <p class="text-[10px] text-[#86868B] dark:text-[#A1A1A6] font-semibold uppercase tracking-wide">Distribución geográfica</p>
        </div>
      </div>

      <div class="flex items-center gap-2">
        <!-- Modo Calor -->
        <button
          @click="isHeatmapActive = !isHeatmapActive"
          :class="isHeatmapActive 
            ? 'bg-[#9D1B32] text-white border-[#9D1B32] dark:bg-brand-red dark:text-[#111112] dark:border-brand-red' 
            : 'bg-white text-[#4A4A4D] border-app-border hover:bg-app-secondary dark:bg-app-secondary dark:text-[#A1A1A6] dark:border-app-border dark:hover:bg-app-card'"
          class="flex items-center gap-1.5 px-3 py-1.5 rounded-xl border text-[11px] font-bold transition shadow-sm"
        >
          <i class="pi pi-map text-[10px]"></i>
          <span>Modo Calor</span>
        </button>

        <button
          @click="handleVerUbicacion"
          :disabled="locationLoading"
          class="flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-[#1D1D1F] text-white text-[11px] font-bold hover:bg-black transition shadow-sm disabled:opacity-60"
        >
          <i :class="locationLoading ? 'pi pi-spin pi-spinner' : 'pi pi-compass'" class="text-[10px]"></i>
          {{ locationLoading ? 'Buscando...' : 'Mi ubicación' }}
        </button>
      </div>
    </div>

    <!-- Mapa -->
    <div class="relative flex-1 group">
      <IncidenciasMap
        :incidencias="incidencias"
        :isHeatmapActive="isHeatmapActive"
        :fitView="true"
        @map-ready="onMapReady"
      />

      <!-- Badge conteo -->
      <div
        class="absolute top-3 right-3 bg-white/90 dark:bg-app-card/90 backdrop-blur-sm rounded-xl px-3 py-1.5 border border-app-border flex items-center gap-1.5 z-10 opacity-0 group-hover:opacity-100 transition-opacity duration-300"
      >
        <span class="w-2 h-2 bg-brand-red rounded-full animate-pulse"></span>
        <span class="text-[11px] font-black text-[#1D1D1F] dark:text-white">{{ incidencias.length }} ubicaciones</span>
      </div>

      <!-- Error ubicación -->
      <div
        v-if="locationError"
        class="absolute left-3 bottom-3 z-10 rounded-xl bg-rose-50 text-rose-700 border border-rose-200 px-3 py-2 text-[11px] font-medium shadow-sm max-w-[220px]"
      >
        <i class="pi pi-exclamation-triangle mr-1"></i>{{ locationError }}
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import IncidenciasMap from '@/Components/IncidenciasMap.vue'
import { useUserLocation } from '@/composables/useUserLocation.js'

const props = defineProps({
  incidencias: { type: Array, default: () => [] },
})

const {
  locationLoading,
  locationError,
  verMiUbicacion,
} = useUserLocation()

const mapInstance = ref(null)
const isHeatmapActive = ref(false)

const onMapReady = (map) => {
  mapInstance.value = map
}

const handleVerUbicacion = () => {
  if (mapInstance.value) {
    verMiUbicacion(mapInstance.value)
  }
}
</script>

