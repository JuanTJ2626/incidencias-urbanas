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
							{{ filteredIncidencias.length }} visibles de {{ incidenciasConCoordenadas.length }} con coordenadas válidas.
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
				<div class="relative overflow-hidden rounded-3xl border border-app-border h-[68vh] min-h-[520px] dark:border-app-border">
					
					<IncidenciasMap
						ref="mapComp"
						:incidencias="filteredIncidencias"
						:isHeatmapActive="isHeatmapActive"
						:activeIncidencia="incidenciaActiva"
						@marker-click="abrirIncidencia"
						@map-ready="onMapReady"
					/>

					<!-- Botón Mi Ubicación (Flotante dentro del mapa) -->
					<div class="absolute top-4 right-4 z-10 flex flex-col gap-2">
						<button
							@click="verMiUbicacion(mapInstance)"
							:disabled="locationLoading"
							class="flex items-center justify-center gap-2 px-4 py-3 min-w-[140px] rounded-xl bg-white/95 backdrop-blur-md text-[#1D1D1F] text-[13px] font-extrabold hover:bg-white border border-app-border transition-all shadow-md disabled:opacity-60 dark:bg-app-card/95 dark:border-app-border dark:text-white dark:hover:bg-app-secondary"
							title="Ir a mi ubicación"
						>
							<i :class="locationLoading ? 'pi pi-spin pi-spinner text-brand-red' : 'pi pi-compass text-brand-red'"></i> 
							{{ locationLoading ? 'Buscando...' : 'Mi ubicación' }}
						</button>
					</div>

					<div
						v-if="locationError"
						class="absolute right-4 bottom-4 z-10 rounded-2xl bg-rose-50 text-rose-700 border border-rose-200 px-4 py-3 text-sm font-medium shadow-sm"
					>
						<i class="pi pi-exclamation-triangle mr-2"></i>{{ locationError }}
					</div>

					<!-- Botón flotante para ver la incidencia seleccionada -->
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
				:incidencias="filteredIncidencias"
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
			@ver-ubicacion="verMiUbicacion(mapInstance)"
		/>
	</div>
</template>

<script setup>
import { ref, computed, watch } from 'vue'
import PageHeader from '@/Components/PageHeader.vue'
import MapControlsBar from '@/Components/MapControlsBar.vue'
import MapIncidenciaModal from '@/Components/MapIncidenciaModal.vue'
import MapIncidenciaList from '@/Components/MapIncidenciaList.vue'
import IncidenciasMap from '@/Components/IncidenciasMap.vue'
import { useUserLocation } from '@/composables/useUserLocation.js'
import { tieneCoordenadas, statusType, calcularDistanciaKm } from '@/utils/mapUtils.js'

const props = defineProps({
	incidencias: { type: Array, default: () => [] },
})

const {
	userPosition,
	locationLoading,
	locationError,
	verMiUbicacion,
} = useUserLocation()

const mapComp = ref(null)
const mapInstance = ref(null)
const modalVisible = ref(false)
const incidenciaActiva = ref(null)
const selectedStatusFilter = ref('all')
const filteredIndex = ref(-1)
const isHeatmapActive = ref(false)

// --- Computados ---
const incidenciasConCoordenadas = computed(() => {
	return props.incidencias.filter(inc => tieneCoordenadas(inc))
})

const filteredIncidencias = computed(() => {
	if (selectedStatusFilter.value === 'all') return incidenciasConCoordenadas.value
	return incidenciasConCoordenadas.value.filter(inc => statusType(inc.estatus) === selectedStatusFilter.value)
})

const distanciaSeleccionada = computed(() => {
	if (!userPosition.value || !incidenciaActiva.value || !tieneCoordenadas(incidenciaActiva.value)) return ''
	const km = calcularDistanciaKm(
		userPosition.value.lat, userPosition.value.lng,
		Number(incidenciaActiva.value.latitud), Number(incidenciaActiva.value.longitud)
	)
	return km < 1 ? `${Math.round(km * 1000)} m` : `${km.toFixed(1)} km`
})

const leyendaEstatus = [
	{ label: 'Pendiente', value: 'pending' },
	{ label: 'En proceso', value: 'inprogress' },
	{ label: 'Resuelto', value: 'resolved' },
	{ label: 'Rechazado', value: 'rejected' },
]

const statusFilterOptions = [
	{ label: 'Todos', value: 'all' },
	{ label: 'Pendientes', value: 'pending' },
	{ label: 'En proceso / revisión', value: 'inprogress' },
	{ label: 'Resueltos', value: 'resolved' },
	{ label: 'Rechazados', value: 'rejected' },
]

const activeFilterLabel = computed(() => {
	const selected = statusFilterOptions.find(o => o.value === selectedStatusFilter.value)
	return selected ? `Filtro: ${selected.label}` : 'Filtro: Todos'
})

const canNavigateFiltered = computed(() => {
	return selectedStatusFilter.value !== 'all' && filteredIncidencias.value.length > 0
})

const filteredCounterLabel = computed(() => {
	if (!canNavigateFiltered.value) return 'Activa un filtro específico para navegar.'
	return `${filteredIndex.value >= 0 ? filteredIndex.value + 1 : 0}/${filteredIncidencias.value.length}`
})

// --- Watchers ---
watch(selectedStatusFilter, () => {
	filteredIndex.value = filteredIncidencias.value.length > 0 ? 0 : -1
})

// --- Métodos ---
const onMapReady = (map) => {
	mapInstance.value = map
}

const encuadrarIncidencias = () => {
	if (mapComp.value) mapComp.value.renderMarkers(true)
}

const toggleHeatmap = () => {
	isHeatmapActive.value = !isHeatmapActive.value
}

const abrirIncidencia = (inc) => {
	incidenciaActiva.value = inc
	modalVisible.value = true
}

const enfocarIncidencia = (inc) => {
	incidenciaActiva.value = inc
}

const limpiarSeleccionVisual = () => {
	// Logic handled inside IncidenciasMap or through data binding
}

const nextFilteredIncidencia = () => {
	if (!canNavigateFiltered.value) return
	filteredIndex.value = (filteredIndex.value + 1) % filteredIncidencias.value.length
	incidenciaActiva.value = filteredIncidencias.value[filteredIndex.value]
}

const prevFilteredIncidencia = () => {
	if (!canNavigateFiltered.value) return
	filteredIndex.value = (filteredIndex.value - 1 + filteredIncidencias.value.length) % filteredIncidencias.value.length
	incidenciaActiva.value = filteredIncidencias.value[filteredIndex.value]
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

@keyframes livePulse {
	0%   { box-shadow: 0 0 0 0   rgba(249,115,22,.55); }
	70%  { box-shadow: 0 0 0 10px rgba(249,115,22,0); }
	100% { box-shadow: 0 0 0 0   rgba(249,115,22,0); }
}

.animate-fade-in { animation: fadeIn .35s ease-out forwards; }

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
</style>


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
