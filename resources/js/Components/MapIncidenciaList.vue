<template>
	<div class="flex flex-col h-full">
		<!-- Lista -->
		<div class="bg-white border border-app-border rounded-3xl shadow-sm flex flex-col h-full overflow-hidden dark:bg-app-card">
			<!-- Header -->
			<div class="p-5 border-b border-app-secondary dark:border-app-border">
				<div class="flex items-start justify-between gap-3">
					<div>
						<p class="text-[15px] font-black text-[#1D1D1F] dark:text-white">Directorio Geográfico</p>
						<p class="text-xs font-semibold text-[#86868B] mt-0.5 dark:text-[#A1A1A6]">Reportes en tu filtro de mapa.</p>
					</div>
					<span class="text-xs font-black text-white bg-brand-red rounded-full px-3 py-1.5 shadow-sm dark:bg-brand-red dark:text-app-bg">
						{{ incidencias.length }}
					</span>
				</div>
			</div>

			<!-- Contenido Scrollable -->
			<div v-if="incidencias.length" class="flex-1 overflow-y-auto p-4 space-y-3 custom-scrollbar max-h-[68vh] xl:max-h-[75vh]">
				<button
					v-for="inc in incidencias"
					:key="inc.id"
					@click="$emit('enfocar', inc)"
					class="w-full text-left rounded-2xl border transition-all duration-200 p-4 group"
					:class="activa?.id === inc.id
						? 'border-transparent bg-[#1D1D1F] text-white shadow-md transform scale-[1.02] dark:bg-white dark:text-[#1D1D1F]'
						: 'border-app-border bg-white hover:border-[#1D1D1F]/30 hover:shadow-sm dark:bg-app-secondary dark:hover:border-white/30'"
				>
					<div class="flex items-start gap-4">
						<div
							class="status-card-icon"
							:style="{ backgroundColor: colorForType(inc.estatus) }"
							v-html="svgForType(inc.estatus)"
						></div>

						<div class="flex-1 min-w-0">
							<div class="flex flex-wrap items-center justify-between gap-2 mb-1.5">
								<p class="text-[15px] font-bold truncate leading-tight" :class="activa?.id === inc.id ? 'text-white dark:text-[#1D1D1F]' : 'text-[#1D1D1F] dark:text-white'">
									{{ inc.tipo_incidencia || 'Incidencia' }}
								</p>
							</div>

							<div class="flex flex-wrap items-center gap-2 mb-2">
								<Tag :value="inc.estatus || 'sin estatus'" :severity="getTagSeverity(inc.estatus)" rounded class="!text-[10px] !px-2 py-0.5 font-bold"></Tag>
								<span class="text-[10px] font-bold px-2 py-0.5 rounded-full" :class="activa?.id === inc.id ? 'bg-white/20 text-white dark:bg-[#1D1D1F]/10 dark:text-[#1D1D1F]' : 'bg-app-secondary text-[#4A4A4D] dark:bg-app-bg dark:text-[#A1A1A6]'">
									#{{ inc.id }}
								</span>
							</div>

							<p class="text-xs font-medium line-clamp-2 leading-relaxed" :class="activa?.id === inc.id ? 'text-white/80 dark:text-[#1D1D1F]/80' : 'text-[#4A4A4D] dark:text-[#A1A1A6]'">
								<i class="pi pi-map-marker text-[10px] mr-1"></i>{{ inc.direccion || 'Sin dirección registrada' }}
							</p>

							<div class="mt-3 flex items-center justify-between">
								<p class="text-[10px] font-bold" :class="activa?.id === inc.id ? 'text-white/70 dark:text-[#1D1D1F]/70' : 'text-[#86868B] dark:text-[#86868B]'">
									<i class="pi pi-calendar mr-1"></i>{{ inc.created_at?.split(' ')[0] || 'Sin fecha' }}
								</p>
								<i class="pi pi-arrow-right text-[11px] transition-transform group-hover:translate-x-1" :class="activa?.id === inc.id ? 'text-white dark:text-[#1D1D1F]' : 'text-[#1D1D1F] dark:text-white'"></i>
							</div>
						</div>
					</div>
				</button>
			</div>

			<!-- Empty State -->
			<div v-else class="flex-1 flex flex-col items-center justify-center p-8 text-center bg-app-secondary dark:bg-app-secondary">
				<div class="w-16 h-16 rounded-full bg-white border border-app-border shadow-sm flex items-center justify-center mb-4 text-[#C7C7CC] dark:bg-app-card dark:text-[#86868B]">
					<i class="pi pi-map text-2xl"></i>
				</div>
				<p class="text-[15px] font-bold text-[#1D1D1F] dark:text-white">Sin incidencias a la vista</p>
				<p class="text-xs font-medium text-[#86868B] mt-1.5 max-w-[200px] dark:text-[#A1A1A6]">Cambia el modo del mapa o tu filtro de estado para revelar los marcadores ocultos.</p>
			</div>
		</div>
	</div>
</template>

<script>
import Tag from 'primevue/tag'
import { statusType, statusColor, statusInlineSvg } from '@/utils/mapUtils.js'

export default {
	name: 'MapIncidenciaList',

	components: { Tag },

	props: {
		incidencias:  { type: Array,   default: () => [] },
		activa:       { type: Object,  default: null },
	},

	emits: ['enfocar'],

	methods: {
		colorForType(estatus) { return statusColor(statusType(estatus)) },
		svgForType(estatus)   { return statusInlineSvg(statusType(estatus)) },
		
		getTagSeverity(estatus) {
			const type = statusType(estatus)
			if (type === 'resolved') return 'success'
			if (type === 'rejected') return 'danger'
			if (type === 'inprogress') return 'info'
			return 'warning'
		}
	},
}
</script>

<style scoped>
.status-card-icon {
	width: 44px;
	height: 44px;
	min-width: 44px;
	border-radius: 14px;
	display: inline-flex;
	align-items: center;
	justify-content: center;
	color: white;
	box-shadow: 0 10px 22px rgba(15, 23, 42, 0.18);
}

.custom-scrollbar::-webkit-scrollbar {
	width: 4px;
}
.custom-scrollbar::-webkit-scrollbar-track {
	background: transparent;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
	background-color: #E8E8ED;
	border-radius: 10px;
}
</style>
