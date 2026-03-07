<template>
  <div class="animate-fade-in py-6 pl-4 pr-6">
    <PageHeader
      title="Historial de Órdenes Cerradas"
      subtitle="Registro de trabajos completados con evidencias fotográficas."
    />

    <!-- Vacío -->
    <div v-if="!cerradas.length" class="bg-white rounded-2xl p-14 text-center border border-[#E8E8ED]">
      <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4">
        <i class="pi pi-history text-4xl text-gray-300"></i>
      </div>
      <p class="text-gray-500 font-medium">Aún no tienes órdenes cerradas.</p>
    </div>

    <!-- Grid de evidencias -->
    <div v-else class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-5">
      <div
        v-for="inc in cerradas"
        :key="inc.id"
        class="bg-white rounded-2xl border border-[#E8E8ED] shadow-sm overflow-hidden hover:shadow-md transition"
      >
        <!-- Fotos Antes / Después -->
        <div class="grid grid-cols-2 h-36">
          <div class="relative bg-gray-100">
            <img v-if="inc.foto_url" :src="inc.foto_url" class="w-full h-full object-cover" alt="Antes" />
            <div v-else class="w-full h-full flex items-center justify-center"><i class="pi pi-image text-2xl text-gray-300"></i></div>
            <span class="absolute bottom-1.5 left-1.5 bg-black/60 text-white text-[9px] font-bold px-1.5 py-0.5 rounded">ANTES</span>
          </div>
          <div class="relative bg-emerald-50 border-l border-[#E8E8ED]">
            <img v-if="inc.foto_despues_url" :src="inc.foto_despues_url" class="w-full h-full object-cover" alt="Después" />
            <div v-else class="w-full h-full flex items-center justify-center"><i class="pi pi-image text-2xl text-gray-300"></i></div>
            <span class="absolute bottom-1.5 right-1.5 bg-emerald-600/80 text-white text-[9px] font-bold px-1.5 py-0.5 rounded">DESPUÉS</span>
          </div>
        </div>

        <!-- Detalle -->
        <div class="p-4 flex flex-col gap-2">
          <div class="flex items-start justify-between gap-2">
            <div>
              <p class="text-xs text-[#86868B] font-bold uppercase tracking-wide">{{ inc.tipo_incidencia }}</p>
              <p class="text-sm font-bold text-[#1D1D1F] leading-snug mt-0.5">{{ inc.direccion }}</p>
            </div>
            <span class="text-xs text-gray-400 shrink-0 font-mono">#{{ inc.id }}</span>
          </div>

          <p v-if="inc.notas_cierre" class="text-xs text-[#86868B] italic bg-[#F5F5F7] rounded-lg p-2">
            <i class="pi pi-comment mr-1"></i>{{ inc.notas_cierre }}
          </p>

          <!-- Mapa de cierre -->
          <div v-if="inc.lat_cierre && inc.lng_cierre" class="mt-1">
            <iframe
              :src="osmUrl(inc.lat_cierre, inc.lng_cierre)"
              class="w-full h-28 rounded-xl border border-[#E8E8ED]" frameborder="0" scrolling="no" loading="lazy"
            ></iframe>
          </div>

          <div class="flex items-center gap-1 text-xs text-emerald-600 font-bold mt-1">
            <i class="pi pi-check-circle"></i>
            Cerrado el {{ formatDate(inc.cerrado_en) }}
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  props: {
    cerradas: { type: Array, default: () => [] },
  },
  methods: {
    formatDate(val) {
      if (!val) return '—'
      return new Date(val).toLocaleDateString('es-MX', { day: '2-digit', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit' })
    },
    osmUrl(lat, lng) {
      const d = 0.006
      return `https://www.openstreetmap.org/export/embed.html?bbox=${lng-d},${lat-d},${lng+d},${lat+d}&layer=mapnik&marker=${lat},${lng}`
    },
  },
}
</script>

<style scoped>
@keyframes fadeIn { from { opacity:0; transform:translateY(8px) } to { opacity:1; transform:translateY(0) } }
.animate-fade-in { animation: fadeIn .35s ease-out forwards }
</style>
