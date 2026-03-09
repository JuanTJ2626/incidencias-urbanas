<template>
	<Dialog
		:visible="visible"
		:modal="true"
		:style="{ width: '860px', maxWidth: '96vw' }"
		:draggable="false"
		maximizable
		dismissableMask
		@update:visible="$emit('update:visible', $event)"
		@hide="$emit('hide')"
	>
		<!-- Header personalizado del Dialog con PrimeVue -->
		<template #header>
			<div class="flex items-center gap-3">
				<span class="font-bold text-xl text-[#1D1D1F] dark:text-white">
					{{ inc ? `${inc.tipo_incidencia || 'Incidencia'} — #${inc.id}` : 'Detalle de incidencia' }}
				</span>
			</div>
		</template>

		<div v-if="inc" class="flex flex-col gap-5 py-2">
			<!-- Encabezado: ícono + nombre + asignación -->
			<div class="flex flex-wrap items-start justify-between gap-4">
				<div class="flex items-start gap-4">
					<div
						class="status-card-icon !w-16 !h-16 !rounded-2xl"
						:style="{ backgroundColor: colorForType(inc.estatus) }"
						v-html="svgForType(inc.estatus)"
					></div>
					<div>
						<div class="flex flex-wrap items-center gap-2 mb-1">
							<p class="text-xl font-bold text-[#1D1D1F] dark:text-white">{{ inc.tipo_incidencia || 'Incidencia' }}</p>
							<Tag :value="inc.estatus || 'sin estatus'" :severity="getTagSeverity(inc.estatus)" rounded class="font-bold px-3"></Tag>
						</div>
						<p class="text-sm font-medium text-[#4A4A4D] dark:text-[#EBEBF5]">{{ inc.direccion || 'Sin dirección registrada' }}</p>
						<p class="text-xs text-[#86868B] dark:text-[#A1A1A6] mt-1 font-semibold flex items-center gap-1">
							<i class="pi pi-calendar text-[10px]"></i> {{ inc.created_at || 'Sin fecha disponible' }}
						</p>
					</div>
				</div>

				<div class="rounded-2xl bg-app-secondary border border-app-border px-5 py-4 min-w-[240px] dark:bg-app-card">
					<p class="text-[10px] font-black text-[#86868B] uppercase tracking-[0.2em] dark:text-[#A1A1A6]">Asignación Actual</p>
					<p class="text-[15px] font-bold text-[#1D1D1F] mt-1 dark:text-white">{{ inc.trabajador_nombre || 'Sin asignar' }}</p>
					<p class="text-xs font-semibold text-[#86868B] mt-0.5 dark:text-[#A1A1A6]">{{ inc.trabajador_email || 'Aún no hay trabajador asignado' }}</p>
				</div>
			</div>

			<!-- Cuerpo: detalles + fotos -->
			<div class="grid grid-cols-1 lg:grid-cols-[minmax(0,1fr)_340px] gap-6 mt-2">
				<div class="flex flex-col gap-5">
					<!-- Ciudadano + Ubicación -->
					<div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
						<div class="rounded-2xl border border-app-border bg-app-secondary p-5 shadow-sm dark:bg-app-card">
							<p class="text-[10px] font-black text-[#86868B] uppercase tracking-[0.2em] mb-2 flex items-center dark:text-[#A1A1A6]"><i class="pi pi-user mr-2 text-[#1D1D1F] dark:text-white"></i>Ciudadano</p>
							<p class="font-bold text-[#1D1D1F] text-[15px] dark:text-white">{{ inc.nombre_ciudadano || 'No especificado' }}</p>
							<p class="text-[#86868B] mt-1 font-medium dark:text-[#A1A1A6]">{{ inc.email || 'Sin correo registrado' }}</p>
						</div>

						<div class="rounded-2xl border border-app-border bg-app-secondary p-5 shadow-sm dark:bg-app-card">
							<p class="text-[10px] font-black text-[#86868B] uppercase tracking-[0.2em] mb-2 flex items-center dark:text-[#A1A1A6]"><i class="pi pi-map-marker mr-2 text-[#1D1D1F] dark:text-white"></i>Ubicación GPS</p>
							<div class="space-y-1 text-[#1D1D1F] font-bold dark:text-white">
								<p>Lat <span class="font-mono text-[#86868B] ml-2 dark:text-[#A1A1A6]">{{ coord(inc.latitud) }}</span></p>
								<p>Lng <span class="font-mono text-[#86868B] ml-2 dark:text-[#A1A1A6]">{{ coord(inc.longitud) }}</span></p>
							</div>
							<p class="text-[11px] font-bold text-[#8A1538] mt-3 bg-rose-50 px-2 py-1 rounded inline-block dark:bg-rose-500/20 dark:text-rose-400">
								{{ distancia ? `A ${distancia} de ti` : 'Distancia desconocida' }}
							</p>
						</div>
					</div>

					<!-- Descripción -->
					<div class="rounded-2xl border border-[#E8E8ED] bg-white p-5 shadow-sm dark:bg-[#1C1C1E] dark:border-white/10">
						<p class="text-[10px] font-black text-[#86868B] uppercase tracking-[0.2em] mb-3 dark:text-[#A1A1A6]">Descripción</p>
						<p class="text-[15px] leading-relaxed text-[#1D1D1F] whitespace-pre-line font-medium dark:text-[#EBEBF5]">{{ inc.descripcion || 'Sin descripción adicional reportada por el ciudadano.' }}</p>
					</div>

					<!-- Seguimiento + Notas de cierre -->
					<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
						<div class="rounded-2xl border border-[#E8E8ED] bg-[#FAFAFB] p-5 shadow-sm dark:bg-[#1C1C1E] dark:border-white/10">
							<p class="text-[10px] font-black text-[#86868B] uppercase tracking-[0.2em] mb-3 dark:text-[#A1A1A6]">Seguimiento</p>
							<div class="space-y-3 text-sm text-[#1D1D1F] dark:text-white">
								<p><span class="font-bold block text-xs text-[#86868B] mb-0.5 dark:text-[#A1A1A6]">Estatus actual</span> {{ inc.estatus || 'Sin estatus' }}</p>
								<p><span class="font-bold block text-xs text-[#86868B] mb-0.5 dark:text-[#A1A1A6]">Cierre registrado</span> {{ inc.cerrado_en || '--' }}</p>
								<p v-if="inc.motivo_rechazo"><span class="font-bold block text-xs text-rose-500 mb-0.5 dark:text-rose-400">Motivo de rechazo</span> {{ inc.motivo_rechazo }}</p>
							</div>
						</div>

						<div class="rounded-2xl border border-[#E8E8ED] bg-[#FAFAFB] p-5 shadow-sm dark:bg-[#1C1C1E] dark:border-white/10">
							<p class="text-[10px] font-black text-[#86868B] uppercase tracking-[0.2em] mb-3 dark:text-[#A1A1A6]">Notas de cierre</p>
							<p class="text-sm leading-relaxed text-[#1D1D1F] whitespace-pre-line font-medium dark:text-[#EBEBF5]">{{ inc.notas_cierre || 'Aún no hay notas de cierre operativas.' }}</p>
						</div>
					</div>

					<!-- Acciones PrimeVue -->
					<div class="flex flex-wrap gap-3 mt-2">
						<Button 
							label="Ver mi ubicación" 
							icon="pi pi-compass" 
							severity="secondary" 
							outlined 
							rounded 
							class="!font-bold hover:!bg-gray-50"
							@click="$emit('ver-ubicacion')" 
						/>
						
						<Button 
							label="Volver al marcador" 
							icon="pi pi-map-marker" 
							severity="secondary" 
							outlined 
							rounded 
							class="!font-bold hover:!bg-gray-50"
							@click="$emit('enfocar', inc)" 
						/>

						<Button 
							label="Abrir en Google Maps" 
							icon="pi pi-external-link" 
							rounded 
							class="!bg-[#1D1D1F] !border-none hover:!bg-black !font-bold"
							@click="openInMaps(inc)" 
						/>
					</div>
				</div>

				<!-- Fotos con PrimeVue Image (Preview) -->
				<div class="flex flex-col gap-5">
					<div class="rounded-3xl border border-[#E8E8ED] bg-[#F5F5F7] overflow-hidden shadow-sm flex flex-col items-center dark:bg-[#1C1C1E] dark:border-white/10">
						<div class="w-full bg-[#1D1D1F] text-white text-[10px] font-black uppercase tracking-widest text-center py-2 dark:bg-[#2C2C2E]">
							Evidencia Inicial
						</div>
						<Image
							v-if="inc.foto"
							:src="`/storage/${inc.foto}`"
							alt="Foto de la incidencia"
							preview
							imageClass="w-full h-auto min-h-[220px] max-h-[280px] object-cover cursor-zoom-in hover:opacity-90 transition"
							class="w-full flex-1 flex"
						/>
						<div v-else class="flex-1 min-h-[220px] w-full flex flex-col items-center justify-center text-[#B0B0B6] gap-3 dark:text-[#A1A1A6]">
							<i class="pi pi-image text-5xl"></i>
							<p class="text-sm font-bold">Sin foto del reporte</p>
						</div>
					</div>

					<div class="rounded-3xl border border-[#E8E8ED] bg-[#F5F5F7] overflow-hidden shadow-sm flex flex-col items-center dark:bg-[#1C1C1E] dark:border-white/10">
						<div class="w-full bg-[#8A1538] text-white text-[10px] font-black uppercase tracking-widest text-center py-2 dark:bg-[#ff453a] dark:text-[#1C1C1E]">
							Evidencia de Cierre
						</div>
						<Image
							v-if="inc.foto_despues"
							:src="`/storage/${inc.foto_despues}`"
							alt="Foto de cierre"
							preview
							imageClass="w-full h-auto min-h-[220px] max-h-[280px] object-cover cursor-zoom-in hover:opacity-90 transition"
							class="w-full flex-1 flex"
						/>
						<div v-else class="flex-1 min-h-[220px] w-full flex flex-col items-center justify-center text-[#B0B0B6] gap-3 px-4 text-center dark:text-[#A1A1A6]">
							<i class="pi pi-camera text-5xl"></i>
							<p class="text-sm font-bold">Sin evidencia posterior</p>
						</div>
					</div>
				</div>
			</div>
		</div>
	</Dialog>
</template>

<script>
import Dialog from 'primevue/dialog'
import Button from 'primevue/button'
import Tag from 'primevue/tag'
import Image from 'primevue/image'
import { statusType, statusColor, statusInlineSvg, formatearCoord, googleMapsUrl } from '@/utils/mapUtils.js'

export default {
	name: 'MapIncidenciaModal',

	components: { Dialog, Button, Tag, Image },

	props: {
		visible:   { type: Boolean, default: false },
		inc:       { type: Object, default: null },
		distancia: { type: String, default: '' },
	},

	emits: ['update:visible', 'hide', 'enfocar', 'ver-ubicacion'],

	methods: {
		colorForType(estatus) { return statusColor(statusType(estatus)) },
		svgForType(estatus)   { return statusInlineSvg(statusType(estatus)) },
		coord(value)          { return formatearCoord(value) },
		openInMaps(inc)       { window.open(googleMapsUrl(inc), '_blank') },
		
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

</style>

<style>
/* Modificadores globales (sin scope) para el Modal interpolado en el Body */
.dark .p-dialog {
	background-color: #1C1C1E !important;
	border: 1px solid rgba(255, 255, 255, 0.1) !important;
	color: #EBEBF5 !important;
}
.dark .p-dialog .p-dialog-header {
	background-color: #1C1C1E !important;
	border-bottom: 1px solid rgba(255, 255, 255, 0.1) !important;
	color: white !important;
}
.dark .p-dialog .p-dialog-content {
	background-color: #1C1C1E !important;
	color: #EBEBF5 !important;
}
.dark .p-dialog .p-dialog-header-icons .p-dialog-header-icon {
	color: #A1A1A6 !important;
	transition: background-color 0.2s, color 0.2s;
}
.dark .p-dialog .p-dialog-header-icons .p-dialog-header-icon:hover {
	background-color: #2C2C2E !important;
	color: white !important;
}
</style>
