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
          v-if="mapsReady"
          @click="isHeatmapActive = !isHeatmapActive; renderMarkers()"
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
          :disabled="locationLoading || !mapsReady"
          class="flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-[#1D1D1F] text-white text-[11px] font-bold hover:bg-black transition shadow-sm disabled:opacity-60"
        >
          <i :class="locationLoading ? 'pi pi-spin pi-spinner' : 'pi pi-compass'" class="text-[10px]"></i>
          {{ locationLoading ? 'Buscando...' : 'Mi ubicación' }}
        </button>
      </div>
    </div>

    <!-- Mapa -->
    <div class="relative flex-1">
      <div ref="mapContainer" class="w-full h-full" style="min-height: 320px;"></div>

      <!-- Loading overlay -->
      <div
        v-if="loadingMap"
        class="absolute inset-0 bg-white/80 backdrop-blur-sm flex flex-col items-center justify-center gap-2 z-10"
      >
        <div class="w-10 h-10 rounded-full border-4 border-[#E8E8ED] border-t-[#1D1D1F] animate-spin"></div>
        <p class="text-xs font-semibold text-[#1D1D1F]">Cargando mapa...</p>
      </div>

      <!-- Badge conteo -->
      <div
        v-if="mapsReady"
        class="absolute top-3 right-3 bg-white/90 dark:bg-app-card/90 backdrop-blur-sm rounded-xl px-3 py-1.5 border border-app-border flex items-center gap-1.5"
      >
        <span class="w-2 h-2 bg-brand-red rounded-full animate-pulse"></span>
        <span class="text-[11px] font-black text-[#1D1D1F] dark:text-white">{{ totalMarcadores }} ubicaciones</span>
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

<script>
import { markRaw } from 'vue'
import { useUserLocation } from '@/composables/useUserLocation.js'
import {
  loadGoogleMapsApi,
  createPulseOverlayClass,
  construirIconoMarker,
  tieneCoordenadas,
  statusType,
  statusColor,
  DEFAULT_CENTER,
} from '@/utils/mapUtils.js'

export default {
  props: {
    incidencias: { type: Array, default: () => [] },
  },

  setup() {
    const {
      userPosition,
      locationLoading,
      locationError,
      verMiUbicacion,
      limpiarUbicacion,
    } = useUserLocation()

    return { userPosition, locationLoading, locationError, verMiUbicacion, limpiarUbicacion }
  },

  data() {
    return {
      map: null,
      loadingMap: true,
      mapsReady: false,
      markers: [],
      pulseOverlays: [],
      PulseOverlayClass: null,
      heatmapLayer: null,
      isHeatmapActive: false,
      totalMarcadores: 0,
      isDarkMode: false,
    }
  },

  computed: {
    incidenciasValidas() {
      return this.incidencias.filter(inc => tieneCoordenadas(inc))
    },
  },

  watch: {
    incidencias: {
      deep: true,
      handler() {
        if (this.mapsReady) this.renderMarkers()
      },
    },
  },

  mounted() {
    this.isDarkMode = document.documentElement.classList.contains('dark')
    this.darkModeObserver = new MutationObserver((mutations) => {
      for (let m of mutations) {
        if (m.attributeName === 'class') {
          const isDark = document.documentElement.classList.contains('dark')
          if (this.isDarkMode !== isDark) {
            this.isDarkMode = isDark
            this.updateMapStyle()
          }
        }
      }
    })
    this.darkModeObserver.observe(document.documentElement, { attributes: true })
    
    this.inicializarMapa()
  },

  beforeUnmount() {
    if (this.darkModeObserver) this.darkModeObserver.disconnect()
    this.markers.forEach(m => m.setMap(null))
    this.pulseOverlays.forEach(p => p.setMap(null))
    if (this.heatmapLayer) this.heatmapLayer.setMap(null)
    this.markers = []
    this.pulseOverlays = []
    this.limpiarUbicacion()
  },

  methods: {
    async inicializarMapa() {
      this.loadingMap = true
      try {
        await loadGoogleMapsApi()
        this.PulseOverlayClass = createPulseOverlayClass()

        this.map = markRaw(new window.google.maps.Map(this.$refs.mapContainer, {
          center: DEFAULT_CENTER,
          zoom: 13,
          mapTypeControl: false,
          streetViewControl: false,
          fullscreenControl: false,
          clickableIcons: false,
          gestureHandling: 'cooperative',
          zoomControl: true,
          styles: this.isDarkMode ? this.getMapDarkStyle() : [],
        }))

        this.mapsReady = true
        this.renderMarkers()
      } catch {
        // Silencioso en el dashboard
      } finally {
        this.loadingMap = false
      }
    },

    renderMarkers() {
      if (!this.map || !window.google?.maps) return

      // Limpiar anteriores
      this.markers.forEach(m => m.setMap(null))
      this.pulseOverlays.forEach(p => p.setMap(null))
      if (this.heatmapLayer) this.heatmapLayer.setMap(null)
      this.markers = []
      this.pulseOverlays = []

      const incidencias = this.incidenciasValidas
      this.totalMarcadores = incidencias.length
      
      const heatmapData = []

      incidencias.forEach(inc => {
        const position = {
          lat: Number(inc.latitud),
          lng: Number(inc.longitud),
        }
        
        heatmapData.push(new window.google.maps.LatLng(position.lat, position.lng))

        if (!this.isHeatmapActive) {
          const marker = markRaw(new window.google.maps.Marker({
            map: this.map,
            position,
            title: `${inc.tipo_incidencia || 'Incidencia'} #${inc.id}`,
            icon: construirIconoMarker(inc),
            zIndex: 20,
          }))

          // Pulse overlay
          const pulse = markRaw(new this.PulseOverlayClass(
            new window.google.maps.LatLng(position.lat, position.lng),
            statusColor(statusType(inc.estatus)),
          ))
          pulse.setMap(this.map)

          this.markers.push(marker)
          this.pulseOverlays.push(pulse)
        }
      })
      
      // Renderizar el heatmap
      if (this.isHeatmapActive) {
        const gradientOptions = [
					'rgba(0, 255, 255, 0)',
					'rgba(0, 255, 255, 1)',
					'rgba(0, 191, 255, 1)',
					'rgba(0, 127, 255, 1)',
					'rgba(0, 63, 255, 1)',
					'rgba(0, 0, 255, 1)',
					'rgba(0, 0, 223, 1)',
					'rgba(0, 0, 191, 1)',
					'rgba(0, 0, 159, 1)',
					'rgba(0, 0, 127, 1)',
					'rgba(63, 0, 91, 1)',
					'rgba(127, 0, 63, 1)',
					'rgba(191, 0, 31, 1)',
					'rgba(255, 0, 0, 1)'
        ]
        this.heatmapLayer = markRaw(new window.google.maps.visualization.HeatmapLayer({
          data: heatmapData,
          radius: 25,
          opacity: 0.85,
          gradient: gradientOptions,
        }))
        this.heatmapLayer.setMap(this.map)
      }
    },

    handleVerUbicacion() {
      this.verMiUbicacion(this.map)
    },

    updateMapStyle() {
      if (!this.map) return
      this.map.setOptions({ styles: this.isDarkMode ? this.getMapDarkStyle() : [] })
    },

    getMapDarkStyle() {
      return [
        { elementType: "geometry", stylers: [{ color: "#242f3e" }] },
        { elementType: "labels.text.stroke", stylers: [{ color: "#242f3e" }] },
        { elementType: "labels.text.fill", stylers: [{ color: "#746855" }] },
        { featureType: "administrative.locality", elementType: "labels.text.fill", stylers: [{ color: "#d59563" }] },
        { featureType: "poi", elementType: "labels.text.fill", stylers: [{ color: "#d59563" }] },
        { featureType: "poi.park", elementType: "geometry", stylers: [{ color: "#263c3f" }] },
        { featureType: "poi.park", elementType: "labels.text.fill", stylers: [{ color: "#6b9a76" }] },
        { featureType: "road", elementType: "geometry", stylers: [{ color: "#38414e" }] },
        { featureType: "road", elementType: "geometry.stroke", stylers: [{ color: "#212a37" }] },
        { featureType: "road", elementType: "labels.text.fill", stylers: [{ color: "#9ca5b3" }] },
        { featureType: "road.highway", elementType: "geometry", stylers: [{ color: "#746855" }] },
        { featureType: "road.highway", elementType: "geometry.stroke", stylers: [{ color: "#1f2835" }] },
        { featureType: "road.highway", elementType: "labels.text.fill", stylers: [{ color: "#f3d19c" }] },
        { featureType: "transit", elementType: "geometry", stylers: [{ color: "#2f3948" }] },
        { featureType: "transit.station", elementType: "labels.text.fill", stylers: [{ color: "#d59563" }] },
        { featureType: "water", elementType: "geometry", stylers: [{ color: "#17263c" }] },
        { featureType: "water", elementType: "labels.text.fill", stylers: [{ color: "#515c6d" }] },
        { featureType: "water", elementType: "labels.text.stroke", stylers: [{ color: "#17263c" }] }
      ]
    },
  },
}
</script>

<style scoped>
@keyframes mp-dot-glow {
  0%, 100% { box-shadow: 0 0 0 3px rgba(255,255,255,0.7), 0 0 12px 3px var(--pulse-color, #f59e0b); }
  50%      { box-shadow: 0 0 0 5px rgba(255,255,255,0.85), 0 0 26px 7px var(--pulse-color, #f59e0b); }
}

@keyframes mp-ring {
  0%   { transform: scale(0.7); opacity: 0.85; }
  70%  { transform: scale(3.6); opacity: 0; }
  100% { transform: scale(3.6); opacity: 0; }
}

:deep(.map-pulse) {
  position: absolute;
  width: 16px;
  height: 16px;
  border-radius: 50%;
  pointer-events: none;
  transform: translate(-50%, -50%);
  background: var(--pulse-color, #f59e0b);
  box-shadow: 0 0 0 3px rgba(255,255,255,0.7), 0 0 12px 3px var(--pulse-color, #f59e0b);
  animation: mp-dot-glow 2s ease-in-out infinite var(--pulse-delay, 0s);
  z-index: 10;
}

:deep(.map-pulse::before),
:deep(.map-pulse::after) {
  content: '';
  position: absolute;
  inset: -3px;
  border-radius: 50%;
  border: 2px solid var(--pulse-color, #f59e0b);
  opacity: 0;
}

:deep(.map-pulse::before) {
  animation: mp-ring 2.2s ease-out infinite var(--pulse-delay, 0s);
}

:deep(.map-pulse::after) {
  animation: mp-ring 2.2s ease-out infinite calc(var(--pulse-delay, 0s) + 1.1s);
}
</style>
