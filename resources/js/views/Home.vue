<template>
  <div class="max-w-7xl mx-auto space-y-8 animate-fade-in">
    <!-- Hero / Welcome Section -->
    <div class="bg-white p-10 rounded-[2.5rem] shadow-sm border border-gray-100 flex flex-col md:flex-row items-center gap-10 overflow-hidden relative">
      <div class="absolute top-0 right-0 w-64 h-64 bg-[#607C88]/5 rounded-full -mr-32 -mt-32 blur-3xl"></div>
      
      <div class="relative z-10 flex-1 space-y-4 text-center md:text-left">
        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-[#607C88]/10 text-[#607C88] text-xs font-bold uppercase tracking-widest">
           <i class="pi pi-sparkles text-[10px]"></i>
           <span>Resumen del Sistema</span>
        </div>
        <h1 class="text-4xl md:text-5xl font-semibold text-gray-900 tracking-tight leading-tight">
          Hola, <span class="text-[#607C88]">{{ userName }}</span>.
        </h1>
        <p class="text-lg text-gray-400 font-medium max-w-xl">
          Bienvenido a la plataforma de gestión ciudadana. Aquí tienes un vistazo rápido al estado actual de las incidencias en tu comunidad.
        </p>
        <div class="pt-4 flex flex-wrap gap-3 justify-center md:justify-start">
           <Button label="Nuevo Reporte" icon="pi pi-plus" class="!bg-[#607C88] !border-[#607C88] !rounded-2xl !px-6 !py-3 hover:!bg-black hover:!border-black transition-all" />
           <Button label="Ver Mapa" icon="pi pi-map" text class="!text-[#607C88] hover:!bg-[#607C88]/5 !rounded-2xl !px-6" />
        </div>
      </div>

      <div class="hidden lg:block relative shrink-0">
          <div class="w-72 h-72 bg-[#F5F5F7] rounded-[3rem] border border-gray-100 flex items-center justify-center p-8 rotate-3 shadow-xl transform-gpu hover:rotate-0 transition-transform duration-500">
             <i class="pi pi-building text-9xl text-[#BFDDEA]"></i>
          </div>
          <div class="absolute -bottom-4 -left-4 w-32 h-32 bg-[#607C88] rounded-[2rem] shadow-xl flex items-center justify-center -rotate-12 transform-gpu hover:rotate-0 transition-transform duration-500">
             <i class="pi pi-chart-bar text-4xl text-white"></i>
          </div>
      </div>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
      <div v-for="(stat, index) in stats" :key="index" 
        class="bg-white p-8 rounded-[2rem] border border-gray-100 shadow-sm hover:shadow-md transition-shadow group">
        <div class="flex items-start justify-between mb-4">
          <div :class="`w-12 h-12 rounded-2xl flex items-center justify-center ${stat.bg} group-hover:scale-110 transition-transform duration-300`">
            <i :class="`pi ${stat.icon} ${stat.color} text-xl`"></i>
          </div>
          <span class="text-xs font-bold text-gray-400 uppercase tracking-widest">Global</span>
        </div>
        <div class="space-y-1">
          <h3 class="text-sm font-medium text-gray-400">{{ stat.label }}</h3>
          <p class="text-3xl font-bold text-gray-900 tracking-tight">{{ stat.value }}</p>
        </div>
        <div class="mt-6 flex items-center gap-2">
           <div class="flex-1 h-1.5 bg-gray-50 rounded-full overflow-hidden">
              <div :class="`h-full ${stat.progressColor} rounded-full`" :style="`width: ${stat.percent}%`"></div>
           </div>
           <span class="text-[10px] font-bold text-gray-400">{{ stat.percent }}%</span>
        </div>
      </div>
    </div>

    <!-- Recent Activity / News (Placeholder for now) -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
       <div class="bg-white p-8 rounded-[2.5rem] border border-gray-100 shadow-sm">
          <div class="flex items-center justify-between mb-8">
             <h2 class="text-xl font-bold text-gray-900">Actividad Reciente</h2>
             <Button label="Ver Todo" text class="!text-[#607C88] !text-xs !font-bold" />
          </div>
          <div class="space-y-6">
             <div v-for="i in 3" :key="i" class="flex gap-4 group cursor-pointer">
                <div class="w-10 h-10 rounded-xl bg-gray-50 flex items-center justify-center shrink-0 border border-gray-100 group-hover:bg-[#607C88]/10 transition-colors">
                   <i class="pi pi-bell text-gray-400 group-hover:text-[#607C88]"></i>
                </div>
                <div class="space-y-1 flex-1">
                   <div class="flex items-center justify-between">
                      <h4 class="text-sm font-semibold text-gray-800">Actualización de Incidencia #129{{i}}</h4>
                      <span class="text-[10px] text-gray-400">Hace 2 horas</span>
                   </div>
                   <p class="text-xs text-gray-500 leading-relaxed">El estado del reporte de alumbrado público ha cambiado a "En Proceso" por el departamento técnico.</p>
                </div>
             </div>
          </div>
       </div>

       <div class="bg-[#607C88] p-8 rounded-[2.5rem] shadow-xl relative overflow-hidden group">
          <div class="absolute top-0 right-0 w-64 h-64 bg-white/5 rounded-full -mr-20 -mt-20 blur-2xl"></div>
          <div class="relative z-10 h-full flex flex-col">
             <div class="w-12 h-12 bg-white/10 rounded-2xl flex items-center justify-center mb-6">
                <i class="pi pi-info-circle text-white text-xl"></i>
             </div>
             <h2 class="text-2xl font-bold text-white mb-4">¿Necesitas ayuda?</h2>
             <p class="text-white/80 text-sm leading-relaxed mb-8 flex-1">
                Consulta nuestra guía rápida de usuario para aprender a utilizar todas las herramientas de la plataforma y mejorar tu eficiencia.
             </p>
             <Button label="Consultar Guía" class="!bg-white !text-[#607C88] !rounded-xl !font-bold !border-none hover:!bg-black hover:!text-white transition-all" />
          </div>
       </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'
import { usePage } from '@inertiajs/inertia-vue3'
import Button from "primevue/button";

const page = usePage()
const userName = computed(() => page.props.value.auth?.user?.name?.split(' ')[0] ?? 'Usuario')

const stats = [
  { label: 'Total Incidencias', value: '1,284', icon: 'pi-folder', color: 'text-[#607C88]', bg: 'bg-[#607C88]/10', progressColor: 'bg-[#607C88]', percent: 85 },
  { label: 'En Resolución', value: '432', icon: 'pi-sync', color: 'text-[#A7C5D2]', bg: 'bg-[#A7C5D2]/10', progressColor: 'bg-[#A7C5D2]', percent: 45 },
  { label: 'Resueltas hoy', value: '28', icon: 'pi-check-circle', color: 'text-gray-400', bg: 'bg-gray-100', progressColor: 'bg-gray-400', percent: 62 },
]
</script>

<style scoped>
@keyframes fadeIn {
  from { opacity: 0; transform: translateY(10px); }
  to { opacity: 1; transform: translateY(0); }
}
.animate-fade-in {
  animation: fadeIn 0.8s cubic-bezier(0.16, 1, 0.3, 1) forwards;
}
</style>
