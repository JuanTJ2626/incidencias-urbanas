<template>
  <div>
    <h2>REPORTES Incidencias</h2>

    <div v-if="loading" class="py-6">Cargando incidencias...</div>
    <div v-else>
      <div v-if="error" class="text-red-600 mb-4">Error: {{ error }}</div>

      <AutoTable :value="customers" :rows="5" />
    </div>
  </div>
</template>

<script>
import AutoTable from '@/Components/AutoTable.vue'

export default {
  components: {
    AutoTable,
  },
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
