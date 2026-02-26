<template>
  <div class="animate-fade-in py-6 pl-4 pr-6">
    <PageHeader
      title="Reportes - Ciudadanos"
      subtitle="Administra y da seguimiento a las incidencias reportadas."
    >
      <template #actions>
        <!-- ejemplo: botón para crear o filtros -->
        <div class="flex items-center gap-3">
          <slot name="header-actions" />
        </div>
      </template>
    </PageHeader>

    <div class="bg-white border border-[#E8E8ED] rounded-2xl shadow-[0_20px_60px_rgba(0,0,0,0.03)] p-4">
      <div v-if="error" class="text-red-600 mb-4">Error: {{ error }}</div>

      <AutoTable :value="customers" :rows="10" :loading="loading">
        <template #title>
          Listado de Registros
        </template>
      </AutoTable>
    </div>
  </div>
</template>

<script>
import AutoTable from '@/Components/AutoTable.vue'

export default {
  components: { AutoTable },
  data() {
    return {
      customers: [],
      loading: false,
      error: null,
    };
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
