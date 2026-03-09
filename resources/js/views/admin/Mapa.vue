<template>
	<div class="animate-fade-in py-6 pl-4 pr-6">
		<PageHeader
			title="Mapa de Incidencias"
			subtitle="Marcadores con íconos por estatus, efecto pulse, filtro rápido y vista inicial."
		>
			<template #actions>
				<div class="flex flex-wrap items-center gap-2">
					<button
						@click="encuadrarIncidencias"
						class="flex items-center gap-2 px-4 py-2.5 rounded-xl border border-app-border bg-white text-[#1D1D1F] text-sm font-bold hover:bg-app-secondary transition shadow-sm dark:bg-app-card dark:border-app-border dark:text-white dark:hover:bg-app-secondary"
					><i class="pi pi-map"></i> Ver incidencias</button>
				</div>
			</template>
		</PageHeader>

		<div class="grid grid-cols-1 2xl:grid-cols-[minmax(0,1fr)_360px] gap-6 mt-4">
			<div class="bg-white border border-app-border rounded-3xl shadow-sm p-4 md:p-5 dark:bg-app-card dark:border-app-border">
				<div class="flex flex-wrap items-center justify-between gap-3 mb-4">
					<div>
						<p class="text-sm font-bold text-[#1D1D1F] dark:text-white">Cobertura del mapa</p>
						<p class="text-xs text-[#86868B] mt-0.5 dark:text-[#A1A1A6]">
							{{ filteredIncidenciasConCoordenadas.length }} visibles de {{ incidenciasConCoordenadas.length }} con coordenadas válidas.
						</p>
					</div>

					<div class="flex flex-wrap items-center gap-2">
						<span class="map-live-badge">
							<span class="map-live-dot"></span>
							{{ activeFilterLabel }}
						</span>
						<span class="px-3 py-1.5 rounded-full bg-app-secondary border border-app-border text-xs font-semibold text-[#4A4A4D] dark:bg-app-secondary dark:border-app-border dark:text-white/80">
							{{ incidenciaActiva ? `Seleccionada #${incidenciaActiva.id}` : 'Sin selección' }}
						</span>
					</div>
				</div>

				<MapControlsBar
					v-model:selected-status-filter="selectedStatusFilter"
					:can-navigate-filtered="canNavigateFiltered"
					:leyenda-estatus="leyendaEstatus"
					:status-filter-options="statusFilterOptions"
					:active-filter-label="activeFilterLabel"
					:is-heatmap-active="isHeatmapActive"
					class="mb-4"
					@prev="prevFilteredIncidencia"
					@next="nextFilteredIncidencia"
					@toggle-heatmap="toggleHeatmap"
				/>

				<!-- Contenedor del mapa -->
				<div class="relative overflow-hidden rounded-3xl border border-app-border bg-app-secondary h-[68vh] min-h-[520px] dark:border-app-border dark:bg-app-secondary">
					
					<!-- Botón Mi Ubicación (Flotante dentro del mapa) -->
					<div class="absolute top-4 right-4 z-10 flex flex-col gap-2">
						<button
							@click="verMiUbicacion(map)"
							:disabled="locationLoading || !mapsReady"
							class="flex items-center justify-center gap-2 px-4 py-3 min-w-[140px] rounded-xl bg-white/95 backdrop-blur-md text-[#1D1D1F] text-[13px] font-extrabold hover:bg-white border border-app-border transition-all shadow-md disabled:opacity-60 dark:bg-app-card/95 dark:border-app-border dark:text-white dark:hover:bg-app-secondary"
							title="Ir a mi ubicación"
						>
							<i :class="locationLoading ? 'pi pi-spin pi-spinner text-brand-red' : 'pi pi-compass text-brand-red'"></i> 
							{{ locationLoading ? 'Buscando...' : 'Mi ubicación' }}
						</button>
					</div>

					<div ref="mapContainer" class="h-full w-full"></div>

					<div
						v-if="loadingMap"
						class="absolute inset-0 bg-white/80 backdrop-blur-sm flex flex-col items-center justify-center gap-3 z-10 dark:bg-[#1C1C1E]/80"
					>
						<div class="w-12 h-12 rounded-full border-4 border-[#E8E8ED] border-t-[#1D1D1F] animate-spin dark:border-white/10 dark:border-t-white"></div>
						<p class="text-sm font-semibold text-[#1D1D1F] dark:text-white">Cargando Google Maps...</p>
					</div>

					<div
						v-else-if="mapError"
						class="absolute inset-0 bg-white flex flex-col items-center justify-center gap-3 z-10 px-6 text-center dark:bg-[#1C1C1E]"
					>
						<div class="w-16 h-16 rounded-full bg-rose-50 text-rose-500 flex items-center justify-center dark:bg-rose-500/20 dark:text-rose-400">
							<i class="pi pi-map text-3xl"></i>
						</div>
						<div>
							<p class="text-base font-bold text-[#1D1D1F] dark:text-white">No se pudo cargar el mapa</p>
							<p class="text-sm text-[#86868B] mt-1 dark:text-[#A1A1A6]">{{ mapError }}</p>
						</div>
					</div>

					<div
						v-if="locationError"
						class="absolute right-4 bottom-4 z-10 rounded-2xl bg-rose-50 text-rose-700 border border-rose-200 px-4 py-3 text-sm font-medium shadow-sm"
					>
						<i class="pi pi-exclamation-triangle mr-2"></i>{{ locationError }}
					</div>

					<!-- Botón flotante para ver la incidencia seleccionada (Útil cuando están amontonadas) -->
					<div 
						v-if="incidenciaActiva" 
						class="absolute bottom-6 left-1/2 -translate-x-1/2 z-10 animate-scale-in"
					>
						<button
							@click="modalVisible = true"
							class="flex items-center gap-3 px-6 py-3.5 rounded-2xl bg-brand-red text-white shadow-[0_20px_50px_rgba(138,21,56,0.3)] hover:scale-105 active:scale-95 transition-all duration-300 font-bold text-sm"
						>
							<i class="pi pi-eye text-lg"></i>
							<span>Ver incidencia #{{ incidenciaActiva.id }}</span>
						</button>
					</div>
				</div>
			</div>

			<MapIncidenciaList
				:incidencias="filteredIncidenciasConCoordenadas"
				:activa="incidenciaActiva"
				:can-navigate="canNavigateFiltered"
				:counter-label="filteredCounterLabel"
				@enfocar="enfocarIncidencia"
				@prev="prevFilteredIncidencia"
				@next="nextFilteredIncidencia"
			/>
		</div>

		<MapIncidenciaModal
			v-model:visible="modalVisible"
			:inc="incidenciaActiva"
			:distancia="distanciaSeleccionada"
			@hide="limpiarSeleccionVisual"
			@enfocar="enfocarIncidencia"
			@ver-ubicacion="verMiUbicacion(map)"
		/>
	</div>
</template>

<script>
import { markRaw } from 'vue'
import MapControlsBar from '@/Components/MapControlsBar.vue'
import MapIncidenciaModal from '@/Components/MapIncidenciaModal.vue'
import MapIncidenciaList from '@/Components/MapIncidenciaList.vue'
import { useUserLocation } from '@/composables/useUserLocation.js'
import {
	loadGoogleMapsApi,
	createPulseOverlayClass,
	construirIconoMarker,
	tieneCoordenadas,
	statusType,
	statusColor,
	calcularDistanciaKm,
	DEFAULT_CENTER,
} from '@/utils/mapUtils.js'

export default {
	components: { MapControlsBar, MapIncidenciaModal, MapIncidenciaList },
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
			localIncidencias: [...(this.incidencias || [])],
			map: null,
			loadingMap: true,
			mapsReady: false,
			mapError: '',
			markers: [],
			markerIndex: {},
			pulseOverlays: [],
			PulseOverlayClass: null,
			activeMarker: null,
			modalVisible: false,
			incidenciaActiva: null,
			selectedStatusFilter: 'all',
			filteredIndex: -1,
			isHeatmapActive: false,
			heatmapLayer: null,
			isDarkMode: false,
		}
	},

	computed: {
		incidenciasConCoordenadas() {
			return this.localIncidencias.filter(inc => tieneCoordenadas(inc))
		},

		filteredIncidenciasConCoordenadas() {
			if (this.selectedStatusFilter === 'all') return this.incidenciasConCoordenadas
			return this.incidenciasConCoordenadas.filter(inc => statusType(inc.estatus) === this.selectedStatusFilter)
		},

		distanciaSeleccionada() {
			if (!this.userPosition || !this.incidenciaActiva || !tieneCoordenadas(this.incidenciaActiva)) return ''

			const km = calcularDistanciaKm(
				this.userPosition.lat,
				this.userPosition.lng,
				Number(this.incidenciaActiva.latitud),
				Number(this.incidenciaActiva.longitud),
			)

			if (km < 1) return `${Math.round(km * 1000)} m`
			return `${km.toFixed(1)} km`
		},

		leyendaEstatus() {
			return [
				{ label: 'Pendiente', value: 'pending' },
				{ label: 'En proceso', value: 'inprogress' },
				{ label: 'En revisión', value: 'inprogress' },
				{ label: 'Resuelto', value: 'resolved' },
				{ label: 'Rechazado', value: 'rejected' },
			]
		},

		statusFilterOptions() {
			return [
				{ label: 'Todos', value: 'all' },
				{ label: 'Pendientes', value: 'pending' },
				{ label: 'En proceso / revisión', value: 'inprogress' },
				{ label: 'Resueltos', value: 'resolved' },
				{ label: 'Rechazados', value: 'rejected' },
			]
		},

		activeFilterLabel() {
			const selected = this.statusFilterOptions.find(option => option.value === this.selectedStatusFilter)
			return selected ? `Filtro: ${selected.label}` : 'Filtro: Todos'
		},

		canNavigateFiltered() {
			return this.selectedStatusFilter !== 'all' && this.filteredIncidenciasConCoordenadas.length > 0
		},

		filteredCounterLabel() {
			if (!this.canNavigateFiltered) return 'Activa un filtro específico para navegar.'
			return `${this.filteredIndex >= 0 ? this.filteredIndex + 1 : 0}/${this.filteredIncidenciasConCoordenadas.length}`
		},
	},

	watch: {
		incidencias: {
			deep: true,
			handler(newValue) {
				this.localIncidencias = [...(newValue || [])]
				this.updateFilteredIndex(true)
				if (this.mapsReady) this.renderMarkers()
			},
		},

		selectedStatusFilter() {
			this.updateFilteredIndex(true)
			if (this.mapsReady) {
				this.renderMarkers(false)
				
				// Efecto "Va hacia allá" suave enfocando el primer elemento resultante del filtro
				if (this.filteredIncidenciasConCoordenadas.length > 0) {
					const primerIncidencia = this.filteredIncidenciasConCoordenadas[0]
					this.centrarEnIncidencia(primerIncidencia)
				}
			}
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

		this.updateFilteredIndex(true)
		this.inicializarMapa()
	},

	beforeUnmount() {
		if (this.darkModeObserver) this.darkModeObserver.disconnect()
		this.limpiarMapa()
	},

	methods: {
		// ─── Mapa ──────────────────────────────────────────────
		async inicializarMapa() {
			this.loadingMap = true
			this.mapError = ''

			try {
				await loadGoogleMapsApi()
				this.PulseOverlayClass = createPulseOverlayClass()

				this.map = markRaw(new window.google.maps.Map(this.$refs.mapContainer, {
					center: DEFAULT_CENTER,
					zoom: 13,
					mapTypeControl: false,
					streetViewControl: false,
					fullscreenControl: true,
					clickableIcons: false,
					gestureHandling: 'cooperative',
					styles: this.isDarkMode ? this.getMapDarkStyle() : [],
				}))

				this.mapsReady = true
				this.renderMarkers(false)
			} catch (error) {
				this.mapError = error?.message || 'Ocurrió un error al inicializar Google Maps.'
			} finally {
				this.loadingMap = false
			}
		},

		limpiarMapa() {
			this.limpiarMarkers()
			this.limpiarUbicacion()
			this.activeMarker = null
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

		limpiarMarkers() {
			this.markers.forEach(marker => marker.setMap(null))
			this.pulseOverlays.forEach(pulse => pulse.setMap(null))
			if (this.heatmapLayer) this.heatmapLayer.setMap(null)
			this.markers = []
			this.pulseOverlays = []
			this.markerIndex = {}
		},

		// ─── Marcadores ────────────────────────────────────────
		renderMarkers(fitView = true) {
			if (!this.map || !window.google?.maps) return

			this.limpiarMarkers()
			const incidencias = this.filteredIncidenciasConCoordenadas

			if (!incidencias.length) {
				if (fitView) {
					this.map.setCenter(DEFAULT_CENTER)
					this.map.setZoom(13)
				}
				return
			}

			const bounds = new window.google.maps.LatLngBounds()

			// Preparar datos para Heatmap
			const heatmapData = []

			incidencias.forEach(inc => {
				const position = {
					lat: Number(inc.latitud),
					lng: Number(inc.longitud),
				}
				const latLng = new window.google.maps.LatLng(position.lat, position.lng)
				bounds.extend(position)
				heatmapData.push(latLng)

				if (!this.isHeatmapActive) {
					const marker = markRaw(new window.google.maps.Marker({
						map: this.map,
						position,
						title: `${inc.tipo_incidencia || 'Incidencia'} #${inc.id}`,
						icon: construirIconoMarker(inc),
						zIndex: 20,
					}))

					marker.addListener('click', () => this.abrirIncidencia(inc, marker))

					const pulse = markRaw(new this.PulseOverlayClass(
						latLng,
						statusColor(statusType(inc.estatus)),
					))
					pulse.setMap(this.map)

					this.markers.push(marker)
					this.pulseOverlays.push(pulse)
					this.markerIndex[inc.id] = marker
				}
			})

			// Renderizar HeatmapLayer si está activo
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

			// Solo ajustar la vista si se pidió explícitamente
			if (!fitView) return

			if (incidencias.length === 1) {
				this.map.setCenter(bounds.getCenter())
				this.map.setZoom(16)
				return
			}

			this.map.fitBounds(bounds, 70)
		},

		encuadrarIncidencias() {
			if (!this.mapsReady) return
			this.renderMarkers(true)
		},

		toggleHeatmap() {
			this.isHeatmapActive = !this.isHeatmapActive
			this.renderMarkers(false)
		},

		// ─── Interacción ───────────────────────────────────────

		/** Clic directo en el marcador del mapa → abre modal */
		abrirIncidencia(inc, marker) {
			if (this.activeMarker && this.activeMarker !== marker) this.activeMarker.setAnimation(null)

			this.activeMarker = marker
			this.incidenciaActiva = { ...inc }
			this.modalVisible = true

			marker.setAnimation(window.google.maps.Animation.BOUNCE)
			window.setTimeout(() => marker.setAnimation(null), 1400)

			this.map.panTo({ lat: Number(inc.latitud), lng: Number(inc.longitud) })
			if ((this.map.getZoom() || 0) < 16) this.map.setZoom(16)
		},

		/** Solo centra + bounce, SIN abrir modal (flechitas y lista) */
		centrarEnIncidencia(inc) {
			const marker = this.markerIndex[inc.id]
			if (!marker) return

			if (this.activeMarker && this.activeMarker !== marker) this.activeMarker.setAnimation(null)

			this.activeMarker = marker
			this.incidenciaActiva = { ...inc }

			marker.setAnimation(window.google.maps.Animation.BOUNCE)
			window.setTimeout(() => marker.setAnimation(null), 1400)

			this.map.panTo({ lat: Number(inc.latitud), lng: Number(inc.longitud) })
			if ((this.map.getZoom() || 0) < 16) this.map.setZoom(16)
		},

		/** Desde la lista lateral o el modal "enfocar" */
		enfocarIncidencia(inc) {
			this.centrarEnIncidencia(inc)
		},

		limpiarSeleccionVisual() {
			if (this.activeMarker) this.activeMarker.setAnimation(null)
		},

		// ─── Navegación filtrada ───────────────────────────────
		updateFilteredIndex(forceReset = false) {
			const total = this.filteredIncidenciasConCoordenadas.length
			if (!total) {
				this.filteredIndex = -1
				return
			}

			if (forceReset || this.filteredIndex < 0 || this.filteredIndex >= total) {
				this.filteredIndex = 0
			}
		},

		nextFilteredIncidencia() {
			if (!this.canNavigateFiltered) return
			this.filteredIndex = (this.filteredIndex + 1) % this.filteredIncidenciasConCoordenadas.length
			this.centrarEnIncidencia(this.filteredIncidenciasConCoordenadas[this.filteredIndex])
		},

		prevFilteredIncidencia() {
			if (!this.canNavigateFiltered) return
			this.filteredIndex = (this.filteredIndex - 1 + this.filteredIncidenciasConCoordenadas.length) % this.filteredIncidenciasConCoordenadas.length
			this.centrarEnIncidencia(this.filteredIncidenciasConCoordenadas[this.filteredIndex])
		},
	},
}
</script>

<style scoped>
@keyframes fadeIn {
	from { opacity: 0; transform: translateY(8px); }
	to   { opacity: 1; transform: translateY(0); }
}

@keyframes scaleIn {
	from { opacity: 0; transform: translate(-50%, 20px) scale(0.9); }
	to   { opacity: 1; transform: translate(-50%, 0) scale(1); }
}

.animate-scale-in { animation: scaleIn 0.4s cubic-bezier(0.34, 1.56, 0.64, 1) forwards; }

@keyframes mp-dot-glow {
	0%, 100% { box-shadow: 0 0 0 3px rgba(255,255,255,0.7), 0 0 12px 3px var(--pulse-color, #f59e0b); }
	50%      { box-shadow: 0 0 0 5px rgba(255,255,255,0.85), 0 0 26px 7px var(--pulse-color, #f59e0b); }
}

@keyframes mp-ring {
	0%   { transform: scale(0.7); opacity: 0.85; }
	70%  { transform: scale(3.6); opacity: 0; }
	100% { transform: scale(3.6); opacity: 0; }
}

@keyframes livePulse {
	0%   { box-shadow: 0 0 0 0   rgba(249,115,22,.55); }
	70%  { box-shadow: 0 0 0 10px rgba(249,115,22,0); }
	100% { box-shadow: 0 0 0 0   rgba(249,115,22,0); }
}

.animate-fade-in { animation: fadeIn .35s ease-out forwards; }

/* ─── Badge en vivo ─────────────────────────────────────────────── */
.map-live-badge {
	display: inline-flex;
	align-items: center;
	gap: 8px;
	padding: 8px 12px;
	border-radius: 999px;
	background: #fff7ed;
	border: 1px solid #fed7aa;
	color: #9a3412;
	font-size: 12px;
	font-weight: 800;
}

.map-live-dot {
	width: 10px;
	height: 10px;
	border-radius: 999px;
	background: #f97316;
	box-shadow: 0 0 0 0 rgba(249,115,22,.55);
	animation: livePulse 1.6s infinite;
}

/* ─── Controles del mapa ────────────────────────────────────────── */
.mps-controls-bar {
	display: flex;
	flex-wrap: wrap;
	align-items: center;
	justify-content: space-between;
	gap: 12px;
}

.mps-legend {
	display: flex;
	flex-wrap: wrap;
	gap: 8px;
}

.mps-legend-item {
	display: inline-flex;
	align-items: center;
	gap: 8px;
	padding: 8px 12px;
	border-radius: 999px;
	border: 1px solid #e5e7eb;
	background: #fff;
	font-size: 12px;
	font-weight: 700;
	color: #374151;
}

.mps-legend-icon {
	display: inline-flex;
	align-items: center;
	justify-content: center;
	width: 22px;
	height: 22px;
	border-radius: 7px;
	color: white;
	box-shadow: 0 6px 16px rgba(15, 23, 42, 0.14);
}

.mps-legend-icon--pending    { background: #f59e0b; }
.mps-legend-icon--inprogress { background: #3b82f6; }
.mps-legend-icon--resolved   { background: #10b981; }
.mps-legend-icon--rejected   { background: #ef4444; }

.mps-toolbar,
.mps-filter {
	display: flex;
	align-items: center;
	gap: 8px;
	flex-wrap: wrap;
}

.mps-filter label {
	font-size: 13px;
	font-weight: 600;
	color: #4b5563;
}

.map-status-filter {
	min-width: 180px;
	padding: 8px 10px;
	border-radius: 10px;
	border: 1px solid #d1d5db;
	background: #fff;
	font-size: 14px;
	color: #111827;
	outline: none;
}

.map-filter-btn {
	width: 36px;
	height: 36px;
	border-radius: 10px;
	border: 1px solid #d1d5db;
	background: white;
	font-weight: 700;
	color: #111827;
	transition: .2s ease;
}

.map-filter-btn:hover:not(:disabled) { background: #f3f4f6; }
.map-filter-btn:disabled             { opacity: .45; cursor: not-allowed; }

/* ─── Pulse overlay del mapa (deep porque lo inyecta JS) ──────── */
/* ─── Pulse overlay del mapa (deep porque lo inyecta JS) ──────── */
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

/* ─── Responsive ────────────────────────────────────────────────── */
@media (max-width: 768px) {
	.mps-controls-bar { align-items: stretch; }
	.mps-toolbar,
	.mps-filter       { width: 100%; }
	.map-status-filter { flex: 1; min-width: 0; }
}
</style>
