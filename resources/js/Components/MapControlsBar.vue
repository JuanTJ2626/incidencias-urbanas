<template>
	<div class="flex flex-wrap items-center justify-between gap-3">
		<!-- Leyenda de estatus -->
		<div class="flex flex-wrap gap-2">
			<span v-for="item in leyendaEstatus" :key="item.value" class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full border border-app-border bg-white text-xs font-bold text-[#4A4A4D] shadow-sm transition dark:bg-app-card dark:border-app-border dark:text-[#EBEBF5]">
				<span class="mps-legend-icon" :class="`mps-legend-icon--${item.value}`" v-html="svgForType(item.value)"></span>
				{{ item.label }}
			</span>
		</div>

		<!-- Filtro + navegación -->
		<div class="flex items-center gap-2 flex-wrap w-full md:w-auto">
			<label for="map-status-filter" class="text-[13px] font-bold text-[#4A4A4D] dark:text-[#A1A1A6]">Estado</label>
			<select
				id="map-status-filter"
				:value="selectedStatusFilter"
				@change="$emit('update:selectedStatusFilter', $event.target.value)"
				class="flex-1 md:flex-none min-w-[130px] px-3 py-2 rounded-xl border border-app-border bg-white text-sm font-semibold text-[#1D1D1F] outline-none hover:border-[#86868B] transition dark:bg-app-card dark:border-app-border dark:text-white dark:hover:border-white/30"
			>
				<option v-for="option in statusFilterOptions" :key="option.value" :value="option.value">
					{{ option.label }}
				</option>
			</select>
			<button type="button" class="w-9 h-9 flex items-center justify-center rounded-xl border border-app-border bg-white text-[#1D1D1F] transition hover:bg-app-secondary disabled:opacity-40 disabled:hover:bg-white dark:bg-app-card dark:border-app-border dark:text-white dark:hover:bg-app-secondary dark:disabled:hover:bg-app-card shadow-sm" @click="$emit('prev')" :disabled="!canNavigateFiltered" title="Anterior">◀</button>
			<button type="button" class="w-9 h-9 flex items-center justify-center rounded-xl border border-app-border bg-white text-[#1D1D1F] transition hover:bg-app-secondary disabled:opacity-40 disabled:hover:bg-white dark:bg-app-card dark:border-app-border dark:text-white dark:hover:bg-app-secondary dark:disabled:hover:bg-app-card shadow-sm" @click="$emit('next')" :disabled="!canNavigateFiltered" title="Siguiente">▶</button>
			
			<!-- Botón Modo Calor -->
			<button 
				type="button" 
				class="inline-flex items-center px-3 h-9 rounded-xl border border-app-border bg-white text-[#4A4A4D] transition hover:bg-app-secondary dark:bg-app-card dark:border-app-border dark:text-[#A1A1A6] dark:hover:bg-app-secondary shadow-sm" 
				:class="[isHeatmapActive ? '!bg-brand-red !text-white !border-brand-red dark:!bg-brand-red dark:!text-[#1C1C1E] dark:!border-brand-red' : '']"
				@click="$emit('toggle-heatmap')" 
				title="Modo Calor"
			>
				<i class="pi pi-map"></i>
				<span class="ml-1.5 text-xs font-bold hidden sm:inline">Modo Calor</span>
			</button>
		</div>
	</div>
</template>

<script>
import { statusInlineSvg } from '@/utils/mapUtils.js'

export default {
	name: 'MapControlsBar',

	props: {
		selectedStatusFilter: { type: String, default: 'all' },
		canNavigateFiltered:  { type: Boolean, default: false },
		leyendaEstatus:       { type: Array, default: () => [] },
		statusFilterOptions:  { type: Array, default: () => [] },
		activeFilterLabel:    { type: String, default: '' },
		isHeatmapActive:      { type: Boolean, default: false },
	},

	emits: ['update:selectedStatusFilter', 'prev', 'next', 'toggle-heatmap'],

	methods: {
		svgForType(type) {
			return statusInlineSvg(type)
		},
	},
}
</script>

<style scoped>
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
</style>
