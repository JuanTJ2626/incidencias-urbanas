<template>
  <DataTable
    :value="value"
    :paginator="paginator"
    :rows="rows"
    :rowsPerPageOptions="rowsPerPageOptions"
    :tableStyle="tableStyle"
    class="p-datatable-lg"
  >
    <template #paginatorleft>
      <div class="text-sm font-bold px-3 text-sidebar-primary">Mostrando {{ totalCount }} registros</div>
    </template>

    <Column
      v-for="col in columns"
      :key="col"
      :field="col"
      :header="prettyHeader(col)"
    >
      <template #body="{ data }">
        {{ formatCell(data[col]) }}
      </template>
    </Column>

    <template #empty>
      <div class="p-6 text-center text-gray-500">No hay registros para mostrar.</div>
    </template>
  </DataTable>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  value: { type: Array, default: () => [] },
  rows: { type: Number, default: 10 },
  paginator: { type: Boolean, default: true },
  rowsPerPageOptions: { type: Array, default: () => [5, 10, 20] },
  tableStyle: { type: String, default: 'min-width: 50rem' },
})

const totalCount = computed(() => props.value.length)

const columns = computed(() => {
  if (!props.value || !props.value.length) return []
  return Object.keys(props.value[0])
})

function prettyHeader(key) {
  return String(key).replace(/_/g, ' ').replace(/\b\w/g, (c) => c.toUpperCase())
}

function formatCell(val) {
  if (val === null || typeof val === 'undefined') return '-'
  if (typeof val === 'boolean') return val ? 'Sí' : 'No'
  if (typeof val === 'object') return JSON.stringify(val)
  const date = Date.parse(val)
  if (!isNaN(date) && typeof val === 'string' && val.length >= 8) return new Date(val).toLocaleString()
  return String(val)
}
</script>

<style scoped>
.text-sidebar-primary { color: #850D12; }
:deep(.p-datatable .text-sm) { font-size: 0.98rem !important; }
@media (max-width: 768px) {
  :deep(.p-datatable table) { min-width: 860px !important; max-width: 1000px !important; }
  :deep(.p-datatable th), :deep(.p-datatable td) { white-space: nowrap !important; padding: 0.5rem 0.75rem !important; font-size: 0.98rem !important; }
}
</style>
