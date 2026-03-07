<template>
  <div class="animate-fade-in py-6 pl-4 pr-6 flex flex-col h-full">
    <PageHeader
      title="Mapa de Incidencias"
      subtitle="Visualiza zonas críticas, densidad de reportes y el estatus de cada incidencia."
    />

    <!-- KPIs rápidos -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mb-5">
      <div v-for="s in stats" :key="s.label"
        :class="['rounded-2xl px-4 py-3 flex items-center gap-3 border cursor-pointer transition-all', s.active ? s.activeClass : 'bg-white border-[#E8E8ED] hover:shadow-sm']"
        @click="toggleFiltro(s.key)"
      >
        <div :class="['w-9 h-9 rounded-xl flex items-center justify-center text-lg shrink-0', s.iconBg]">
          <i :class="s.icon"></i>
        </div>
        <div>
          <p class="text-xl font-black text-[#1D1D1F] leading-none">{{ s.count }}</p>
          <p class="text-[10px] font-bold text-[#86868B] uppercase tracking-wide mt-0.5">{{ s.label }}</p>
        </div>
      </div>
    </div>

    <!-- Toolbar -->
    <div class="flex flex-wrap items-center gap-3 mb-4">
      <!-- Toggle mapa/satélite -->
      <div class="flex rounded-xl overflow-hidden border border-[#E8E8ED] bg-white">
        <button
          v-for="t in ['roadmap','satellite']" :key="t"
          @click="setMapType(t)"
          :class="['px-3 py-2 text-xs font-bold transition', mapType === t ? 'bg-[#1D1D1F] text-white' : 'text-[#86868B] hover:bg-gray-50']"
        >{{ t === 'roadmap' ? 'Mapa' : 'Satélite' }}</button>
      </div>

      <!-- Toggle capa calor -->
      <button
        @click="toggleHeatmap"
        :class="['flex items-center gap-1.5 px-3 py-2 rounded-xl border text-xs font-bold transition', heatmapOn ? 'bg-orange-500 text-white border-orange-500' : 'bg-white text-[#86868B] border-[#E8E8ED] hover:bg-gray-50']"
      ><i class="pi pi-map"></i> Mapa de calor</button>

      <!-- Toggle marcadores -->
      <button
        @click="markersOn = !markersOn; renderMarkers()"
        :class="['flex items-center gap-1.5 px-3 py-2 rounded-xl border text-xs font-bold transition', markersOn ? 'bg-[#1D1D1F] text-white border-[#1D1D1F]' : 'bg-white text-[#86868B] border-[#E8E8ED] hover:bg-gray-50']"
      ><i class="pi pi-map-marker"></i> Marcadores</button>

      <span class="text-xs text-[#86868B] ml-auto">
        {{ filtroActivo ? `Filtrando: ${filtroActivo}` : 'Todas las incidencias' }}
        <button v-if="filtroActivo" @click="filtroActivo = null; renderMarkers()" class="ml-1 text-rose-500 hover:underline">✕ limpiar</button>
      </span>
    </div>

    <!-- Mapa -->
    <div class="relative flex-1 min-h-[520px] rounded-2xl overflow-hidden border border-[#E8E8ED] shadow-sm">
      <div ref="mapEl" class="w-full h-full min-h-[520px]"></div>

      <!-- Leyenda -->
      <div class="absolute bottom-4 left-4 bg-white/90 backdrop-blur-sm rounded-xl border border-[#E8E8ED] shadow-sm px-4 py-3 flex flex-col gap-1.5">
        <p class="text-[10px] font-black text-[#86868B] uppercase tracking-widest mb-1">Leyenda</p>
        <div v-for="l in leyenda" :key="l.label" class="flex items-center gap-2">
          <span class="w-3 h-3 rounded-full shrink-0" :style="{ background: l.color }"></span>
          <span class="text-xs text-[#1D1D1F] capitalize">{{ l.label }}</span>
        </div>
      </div>

      <!-- Cargando -->
      <div v-if="!mapReady" class="absolute inset-0 flex items-center justify-center bg-white/80 backdrop-blur-sm">
        <div class="flex flex-col items-center gap-3">
          <div class="w-10 h-10 border-4 border-[#1D1D1F] border-t-transparent rounded-full animate-spin"></div>
          <p class="text-sm text-[#86868B]">Cargando mapa…</p>
        </div>
      </div>
    </div>

    <!-- Panel detalle incidencia seleccionada -->
    <transition name="slide-up">
      <div v-if="selected" class="mt-4 bg-white rounded-2xl border border-[#E8E8ED] shadow-sm p-5 flex flex-col sm:flex-row gap-4">
        <div class="h-32 w-full sm:w-40 rounded-xl overflow-hidden bg-[#F5F5F7] shrink-0">
          <img v-if="selected.foto" :src="`/storage/${selected.foto}`" class="w-full h-full object-cover" />
          <div v-else class="h-full flex items-center justify-center"><i class="pi pi-image text-3xl text-gray-300"></i></div>
        </div>
        <div class="flex flex-col gap-1.5 flex-1 min-w-0">
          <div class="flex items-center gap-2 flex-wrap">
            <span :class="['text-[10px] font-black px-2.5 py-0.5 rounded-full uppercase tracking-wide', badgeClass(selected.estatus)]">{{ selected.estatus }}</span>
            <span class="text-[10px] text-[#86868B] font-mono">#{{ selected.id }}</span>
          </div>
          <p class="text-sm font-black text-[#1D1D1F] uppercase tracking-wide">{{ selected.tipo_incidencia }}</p>
          <p class="text-sm text-[#1D1D1F] font-semibold">{{ selected.direccion }}</p>
          <p class="text-xs text-[#86868B] line-clamp-2">{{ selected.descripcion }}</p>
          <p class="text-[10px] text-[#86868B]">Ciudadano: {{ selected.nombre_ciudadano }} · {{ selected.created_at }}</p>
        </div>
        <button @click="selected = null" class="self-start text-gray-400 hover:text-gray-600 transition shrink-0">
          <i class="pi pi-times text-lg"></i>
        </button>
      </div>
    </transition>
  </div>
</template>

<script>
const COLORES = {
  'pendiente':   '#6B7280',
  'en proceso':  '#0EA5E9',
  'en revisión': '#F59E0B',
  'resuelto':    '#10B981',
  'rechazado':   '#EF4444',
}

export default {
  props: {
    incidencias: { type: Array, default: () => [] },
    apiKey:      { type: String, default: '' },
  },

  data() {
    return {
      mapReady:    false,
      mapType:     'roadmap',
      heatmapOn:   true,
      markersOn:   true,
      filtroActivo: null,
      selected:    null,
      // Google Maps instances
      gmap:        null,
      heatmapLayer: null,
      gmarkers:    [],
      leyenda: Object.entries(COLORES).map(([label, color]) => ({ label, color })),
    }
  },

  computed: {
    stats() {
      const keys = ['pendiente', 'en proceso', 'en revisión', 'resuelto', 'rechazado']
      const cfg = {
        'pendiente':   { icon: 'pi pi-clock',         iconBg: 'bg-gray-100 text-gray-500',    activeClass: 'bg-gray-600 border-gray-600 text-white' },
        'en proceso':  { icon: 'pi pi-spinner',        iconBg: 'bg-sky-100 text-sky-500',      activeClass: 'bg-sky-500 border-sky-500 text-white' },
        'en revisión': { icon: 'pi pi-eye',            iconBg: 'bg-amber-100 text-amber-500',  activeClass: 'bg-amber-500 border-amber-500 text-white' },
        'resuelto':    { icon: 'pi pi-check-circle',   iconBg: 'bg-emerald-100 text-emerald-600', activeClass: 'bg-emerald-500 border-emerald-500 text-white' },
        'rechazado':   { icon: 'pi pi-times-circle',   iconBg: 'bg-rose-100 text-rose-500',    activeClass: 'bg-rose-500 border-rose-500 text-white' },
      }
      return keys.map(k => ({
        key: k,
        label: k,
        count: this.incidencias.filter(i => i.estatus === k).length,
        active: this.filtroActivo === k,
        ...cfg[k],
      }))
    },

    filtradas() {
      if (!this.filtroActivo) return this.incidencias
      return this.incidencias.filter(i => i.estatus === this.filtroActivo)
    },
  },

  mounted() {
    this.loadGoogleMaps()
  },

  beforeUnmount() {
    // Limpiar listeners
    this.gmarkers.forEach(m => m.setMap(null))
  },

  methods: {
    loadGoogleMaps() {
      if (window.google?.maps) { this.initMap(); return }
      window.initMap = () => this.initMap()
      const s = document.createElement('script')
      s.src = `https://maps.googleapis.com/maps/api/js?key=AIzaSyCchiqlRlOnv6C4pXxh59tYDMRiK501Tmc&libraries=visualization,places&callback=initMap`
      s.async = true; s.defer = true
      document.head.appendChild(s)
    },

    initMap() {
      // Centro default: México
      let center = { lat: 23.6345, lng: -102.5528 }

      // Auto-centrar en las incidencias con coords
      const conCoords = this.incidencias.filter(i => i.latitud && i.longitud)
      if (conCoords.length) {
        const avgLat = conCoords.reduce((s, i) => s + parseFloat(i.latitud), 0) / conCoords.length
        const avgLng = conCoords.reduce((s, i) => s + parseFloat(i.longitud), 0) / conCoords.length
        center = { lat: avgLat, lng: avgLng }
      }

      this.gmap = new window.google.maps.Map(this.$refs.mapEl, {
        center,
        zoom: conCoords.length ? 13 : 5,
        mapTypeId: this.mapType,
        styles: [
          { featureType: 'poi', elementType: 'labels', stylers: [{ visibility: 'off' }] },
        ],
        mapTypeControl: false,
        fullscreenControl: true,
        streetViewControl: false,
      })

      // Capa de calor
      const heatPoints = conCoords.map(i => ({
        location: new window.google.maps.LatLng(parseFloat(i.latitud), parseFloat(i.longitud)),
        weight: i.estatus === 'pendiente' ? 3 : i.estatus === 'en proceso' ? 2 : 1,
      }))

      this.heatmapLayer = new window.google.maps.visualization.HeatmapLayer({
        data: heatPoints,
        map: this.gmap,
        radius: 40,
        opacity: 0.75,
        gradient: [
          'rgba(0,0,0,0)',
          'rgba(16,185,129,0.6)',
          'rgba(245,158,11,0.8)',
          'rgba(239,68,68,1)',
        ],
      })

      this.mapReady = true
      this.renderMarkers()
    },

    renderMarkers() {
      // Limpiar marcadores anteriores
      this.gmarkers.forEach(m => m.setMap(null))
      this.gmarkers = []

      if (!this.markersOn || !this.gmap) return

      this.filtradas.forEach(inc => {
        if (!inc.latitud || !inc.longitud) return
        const color = COLORES[inc.estatus] || '#6B7280'

        const marker = new window.google.maps.Marker({
          position: { lat: parseFloat(inc.latitud), lng: parseFloat(inc.longitud) },
          map: this.gmap,
          title: `#${inc.id} ${inc.tipo_incidencia}`,
          icon: {
            path: window.google.maps.SymbolPath.CIRCLE,
            scale: 9,
            fillColor: color,
            fillOpacity: 0.95,
            strokeColor: '#ffffff',
            strokeWeight: 2,
          },
        })

        marker.addListener('click', () => {
          this.selected = inc
          this.gmap.panTo(marker.getPosition())
        })

        this.gmarkers.push(marker)
      })
    },

    toggleHeatmap() {
      this.heatmapOn = !this.heatmapOn
      if (this.heatmapLayer) {
        this.heatmapLayer.setMap(this.heatmapOn ? this.gmap : null)
      }
    },

    setMapType(type) {
      this.mapType = type
      if (this.gmap) this.gmap.setMapTypeId(type)
    },

    toggleFiltro(key) {
      this.filtroActivo = this.filtroActivo === key ? null : key
      this.renderMarkers()
    },

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

<style scoped>
@keyframes fadeIn { from { opacity:0; transform:translateY(8px) } to { opacity:1; transform:translateY(0) } }
.animate-fade-in { animation: fadeIn .35s ease-out forwards }

.slide-up-enter-active, .slide-up-leave-active { transition: all .3s ease }
.slide-up-enter-from { opacity: 0; transform: translateY(12px) }
.slide-up-leave-to   { opacity: 0; transform: translateY(12px) }
</style>
