<template>
  <div class="relative w-full h-full overflow-hidden">
    <!-- Contenedor del mapa -->
    <div ref="mapContainer" class="w-full h-full bg-app-secondary"></div>

    <!-- Loading overlay -->
    <div
      v-if="loadingMap"
      class="absolute inset-0 bg-white/80 dark:bg-[#1C1C1E]/80 backdrop-blur-sm flex flex-col items-center justify-center gap-3 z-20"
    >
      <div class="w-12 h-12 rounded-full border-4 border-[#E8E8ED] border-t-[#1D1D1F] animate-spin dark:border-white/10 dark:border-t-white"></div>
      <p class="text-sm font-semibold text-[#1D1D1F] dark:text-white">Cargando Google Maps...</p>
    </div>

    <!-- Error overlay -->
    <div
      v-else-if="mapError"
      class="absolute inset-0 bg-white dark:bg-[#1C1C1E] flex flex-col items-center justify-center gap-3 z-20 px-6 text-center"
    >
      <div class="w-16 h-16 rounded-full bg-rose-50 text-rose-500 flex items-center justify-center dark:bg-rose-500/20 dark:text-rose-400">
        <i class="pi pi-map text-3xl"></i>
      </div>
      <div>
        <p class="text-base font-bold text-[#1D1D1F] dark:text-white">No se pudo cargar el mapa</p>
        <p class="text-sm text-[#86868B] mt-1 dark:text-[#A1A1A6]">{{ mapError }}</p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, onBeforeUnmount, watch, markRaw } from 'vue'
import {
  loadGoogleMapsApi,
  createPulseOverlayClass,
  construirIconoMarker,
  tieneCoordenadas,
  statusType,
  statusColor,
  DEFAULT_CENTER,
} from '@/utils/mapUtils.js'

const props = defineProps({
  incidencias: { type: Array, default: () => [] },
  activeIncidencia: { type: Object, default: null },
  isHeatmapActive: { type: Boolean, default: false },
  fitView: { type: Boolean, default: true },
  interactive: { type: Boolean, default: true },
  zoom: { type: Number, default: 13 },
  center: { type: Object, default: () => DEFAULT_CENTER }
})

const emit = defineEmits(['marker-click', 'map-ready', 'error'])

const mapContainer = ref(null)
const map = ref(null)
const loadingMap = ref(true)
const mapError = ref('')
const markers = ref([])
const pulseOverlays = ref([])
const heatmapLayer = ref(null)
const PulseOverlayClass = ref(null)
const isDarkMode = ref(false)
let darkModeObserver = null

// --- Estilos ---
const getMapDarkStyle = () => [
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

const updateMapStyle = () => {
  if (!map.value) return
  map.value.setOptions({ styles: isDarkMode.value ? getMapDarkStyle() : [] })
}

// --- Marcadores ---
const limpiarMarkers = () => {
  markers.value.forEach(m => m.setMap(null))
  pulseOverlays.value.forEach(p => p.setMap(null))
  if (heatmapLayer.value) heatmapLayer.value.setMap(null)
  
  markers.value = []
  pulseOverlays.value = []
  heatmapLayer.value = null
}

const renderMarkers = (forceFit = props.fitView) => {
  if (!map.value || !window.google?.maps) return

  limpiarMarkers()
  const validas = props.incidencias.filter(inc => tieneCoordenadas(inc))
  if (validas.length === 0) return

  const bounds = new window.google.maps.LatLngBounds()
  const heatmapData = []

  validas.forEach(inc => {
    const pos = { lat: Number(inc.latitud), lng: Number(inc.longitud) }
    const latLng = new window.google.maps.LatLng(pos.lat, pos.lng)
    
    heatmapData.push(latLng)
    bounds.extend(pos)

    if (!props.isHeatmapActive) {
      const marker = markRaw(new window.google.maps.Marker({
        map: map.value,
        position: pos,
        title: `${inc.tipo_incidencia || 'Incidencia'} #${inc.id}`,
        icon: construirIconoMarker(inc),
        zIndex: 20,
      }))

      marker.addListener('click', () => emit('marker-click', inc, marker))

      const pulse = markRaw(new PulseOverlayClass.value(
        latLng,
        statusColor(statusType(inc.estatus)),
      ))
      pulse.setMap(map.value)

      markers.value.push(marker)
      pulseOverlays.value.push(pulse)
    }
  })

  // Heatmap
  if (props.isHeatmapActive) {
    heatmapLayer.value = markRaw(new window.google.maps.visualization.HeatmapLayer({
      data: heatmapData,
      radius: 25,
      opacity: 0.85,
      gradient: [
        'rgba(0, 255, 255, 0)', 'rgba(0, 255, 255, 1)', 'rgba(0, 191, 255, 1)',
        'rgba(0, 127, 255, 1)', 'rgba(0, 63, 255, 1)', 'rgba(0, 0, 255, 1)',
        'rgba(0, 0, 223, 1)', 'rgba(0, 0, 191, 1)', 'rgba(0, 0, 159, 1)',
        'rgba(0, 0, 127, 1)', 'rgba(63, 0, 91, 1)', 'rgba(127, 0, 63, 1)',
        'rgba(191, 0, 31, 1)', 'rgba(255, 0, 0, 1)'
      ]
    }))
    heatmapLayer.value.setMap(map.value)
  }

  if (forceFit) {
    if (validas.length === 1) {
      map.value.setCenter(bounds.getCenter())
      map.value.setZoom(16)
    } else {
      map.value.fitBounds(bounds, 70)
    }
  }
}

// --- Ciclo de vida ---
onMounted(async () => {
  isDarkMode.value = document.documentElement.classList.contains('dark')
  darkModeObserver = new MutationObserver(() => {
    const dark = document.documentElement.classList.contains('dark')
    if (isDarkMode.value !== dark) {
      isDarkMode.value = dark
      updateMapStyle()
    }
  })
  darkModeObserver.observe(document.documentElement, { attributes: true })

  try {
    await loadGoogleMapsApi()
    PulseOverlayClass.value = createPulseOverlayClass()

    map.value = markRaw(new window.google.maps.Map(mapContainer.value, {
      center: props.center,
      zoom: props.zoom,
      mapTypeControl: false,
      streetViewControl: false,
      fullscreenControl: props.interactive,
      clickableIcons: false,
      gestureHandling: props.interactive ? 'cooperative' : 'none',
      zoomControl: props.interactive,
      styles: isDarkMode.value ? getMapDarkStyle() : [],
    }))

    loadingMap.value = false
    emit('map-ready', map.value)
    renderMarkers()
  } catch (err) {
    mapError.value = err.message || 'Error al cargar el mapa'
    emit('error', mapError.value)
    loadingMap.value = false
  }
})

onBeforeUnmount(() => {
  if (darkModeObserver) darkModeObserver.disconnect()
  limpiarMarkers()
})

// --- Watchers ---
watch(() => props.incidencias, () => renderMarkers(false), { deep: true })
watch(() => props.isHeatmapActive, () => renderMarkers(false))
watch(() => props.activeIncidencia, (inc) => {
  if (inc && tieneCoordenadas(inc) && map.value) {
    map.value.panTo({ lat: Number(inc.latitud), lng: Number(inc.longitud) })
    if (map.value.getZoom() < 16) map.value.setZoom(16)
  }
})

defineExpose({
  getMap: () => map.value,
  renderMarkers
})
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
