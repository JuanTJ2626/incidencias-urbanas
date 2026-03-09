<template>
  <div class="animate-fade-in py-6 pl-4 pr-6">
    <PageHeader
      title="Reportes - Ciudadanos"
      subtitle="Administra y da seguimiento a las incidencias reportadas."
    >
      <template #actions>
        <div class="flex items-center gap-3">
          <div class="relative w-64 group hidden md:block">
            <i class="pi pi-search absolute left-4 top-1/2 -translate-y-1/2 text-[#86868B] dark:text-[#A1A1A6]"></i>
            <InputText
              v-model="searchQuery"
              placeholder="Buscar reporte..."
              class="!w-full !rounded-2xl !border-transparent !bg-app-secondary dark:!bg-white/5 !pl-11 !py-2.5 !text-xs !font-bold focus:!bg-white dark:focus:!bg-white/10 focus:!border-app-border transition-all"
            />
          </div>
        </div>
      </template>
    </PageHeader>

    <!-- Tabla Premium con Paginación -->
    <GenericPremiumTable
      title="Listado de Registros"
      :subtitle="`${filteredData.length} reportes registrados`"
      icon="pi-comment"
      :columns="[
        { key: 'id', label: 'ID', width: '80px' },
        { key: 'tipo_incidencia', label: 'Incidencia' },
        { key: 'direccion', label: 'Ubicación' },
        { key: 'estatus', label: 'Estado', width: '130px' },
        { key: 'created_at', label: 'Fecha de Reporte' }
      ]"
      :data="filteredData"
      :rowsPerPage="10"
    >
      <!-- Slot ID -->
      <template #cell-id="{ value }">
        <span class="text-xs font-black text-[#86868B] dark:text-[#A1A1A6]">#{{ value }}</span>
      </template>

      <!-- Slot Incidencia -->
      <template #cell-tipo_incidencia="{ row }">
        <div class="flex items-center gap-3">
          <div class="w-8 h-8 rounded-lg bg-app-secondary flex items-center justify-center shrink-0">
            <i :class="[iconoCategoria(row.tipo_incidencia), 'text-brand-red text-xs']"></i>
          </div>
          <span class="font-bold text-[#1D1D1F] dark:text-white capitalize leading-tight">{{ row.tipo_incidencia }}</span>
        </div>
      </template>

      <!-- Slot Estado -->
      <template #cell-estatus="{ value }">
         <span :class="['text-[10px] font-black uppercase tracking-widest px-2.5 py-1 rounded-full border shadow-sm', badgeClass(value)]">
            {{ value }}
         </span>
      </template>

      <!-- Slot Fecha -->
      <template #cell-created_at="{ value }">
         <span class="text-xs font-bold text-[#4A4A4D] dark:text-gray-300">
            {{ formatDate(value) }}
         </span>
      </template>
    </GenericPremiumTable>
  </div>
</template>

<script>
import GenericPremiumTable from '@/Components/GenericPremiumTable.vue'
import PageHeader from '@/Components/PageHeader.vue'
import InputText from 'primevue/inputtext'

export default {
  components: { GenericPremiumTable, PageHeader, InputText },
  data() {
    return {
      customers: [],
      loading: false,
      error: null,
      searchQuery: '',
    };
  },
  computed: {
    filteredData() {
      if (!this.searchQuery) return this.customers
      const q = this.searchQuery.toLowerCase()
      return this.customers.filter(i => 
        i.tipo_incidencia?.toLowerCase().includes(q) ||
        i.direccion?.toLowerCase().includes(q) ||
        i.id.toString().includes(q)
      )
    },
    badgeClass() {
      return (estatus) => {
        const m = {
          'pendiente':   'bg-gray-100 text-gray-600 border-gray-200 dark:bg-white/5 dark:text-gray-400 dark:border-white/10',
          'en proceso':  'bg-sky-50 text-sky-700 border-sky-200 dark:bg-sky-500/10 dark:text-sky-400 dark:border-sky-500/20',
          'en revisión': 'bg-amber-50 text-amber-700 border-amber-200 dark:bg-amber-500/10 dark:text-amber-400 dark:border-amber-500/20',
          'resuelto':    'bg-emerald-50 text-emerald-700 border-emerald-200 dark:bg-emerald-500/10 dark:text-emerald-400 dark:border-emerald-500/20',
          'rechazado':   'bg-rose-50 text-rose-700 border-rose-200 dark:bg-rose-500/10 dark:text-rose-400 dark:border-rose-500/20',
        }
        return m[estatus] || 'bg-gray-100 text-gray-600'
      }
    }
  },
  methods: {
    iconoCategoria(nombre) {
      const n = (nombre || '').toLowerCase()
      if (n.includes('bache') || n.includes('paviment')) return 'pi pi-exclamation-triangle'
      if (n.includes('alumbr') || n.includes('luz')) return 'pi pi-sun'
      if (n.includes('basura') || n.includes('residuo')) return 'pi pi-trash'
      if (n.includes('agua') || n.includes('fuga')) return 'pi pi-tint'
      if (n.includes('árbol') || n.includes('verde')) return 'pi pi-apple'
      if (n.includes('graffiti') || n.includes('vand')) return 'pi pi-palette'
      if (n.includes('ruido') || n.includes('sonido')) return 'pi pi-volume-up'
      return 'pi pi-map-marker'
    },
    formatDate(str) {
      if (!str) return '—'
      const d = new Date(str)
      return d.toLocaleDateString('es-MX', { day: '2-digit', month: 'short', year: 'numeric' })
    }
  },
  async mounted() {
    this.loading = true;
    try {
      const res = await axios.get('/incidencias', { headers: { Accept: 'application/json' } });
      // If API returns pagination or data array, normalize
      this.customers = Array.isArray(res.data) ? res.data : res.data.data || res.data;
      console.log('Incidencias fetched:', this.customers)
    } catch (e) {
      this.error = e.response?.data || e.message;
      console.error('Error cargando incidencias', e);
    } finally {
      this.loading = false;
    }
  },
};
</script>
