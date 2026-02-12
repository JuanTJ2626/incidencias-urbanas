<template>
  <DataTable
    :value="value"
    :paginator="true"
    :rows="rows"
    class="p-datatable-lg"
    scrollable
    tableStyle="min-width:900px"
    paginatorTemplate="FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink RowsPerPageDropdown"
    :rowsPerPageOptions="rowsPerPageOptions"
  >
    <Column field="name" header="NOMBRE DEL USUARIO" sortable :style="{ minWidth: '320px' }">
      <template #body="{ data }">
        <div class="flex items-center gap-4 py-2">
          <Avatar
            :label="data.name.charAt(0)"
            shape="circle"
            class="!bg-gray-100 !text-gray-600 !font-bold !border !border-gray-200"
            size="large"
          />
          <div>
            <span class="block font-bold text-gray-800 text-base">{{ data.name }}</span>
            <span class="block text-sm text-gray-400">{{ data.email }}</span>
          </div>
        </div>
      </template>
    </Column>

    <Column field="rol" header="ROL ASIGNADO" sortable :style="{ minWidth: '220px' }">
      <template #body="{ data }">
        <Tag
          :value="data.rol"
          :severity="getRolSeverity(data.rol)"
          class="!px-3 !py-1 !text-xs !uppercase !tracking-widest !font-bold"
          rounded
        />
      </template>
    </Column>

    <Column field="created_at" header="FECHA REGISTRO" sortable :style="{ minWidth: '240px' }">
      <template #body="{ data }">
        <span class="text-sm font-medium text-gray-500">{{ new Date(data.created_at).toLocaleDateString() }}</span>
      </template>
    </Column>

    <Column header="ACCIONES" bodyClass="text-right" :style="{ minWidth: '140px' }">
      <template #body="{ data }">
        <div class="flex justify-end gap-2">
          <Button
            icon="pi pi-pencil"
            text
            rounded
            severity="info"
            @click="$emit('edit', data)"
            v-tooltip.top="'Editar'"
            class="hover:bg-blue-50"
          />
          <Button
            icon="pi pi-trash"
            text
            rounded
            severity="danger"
            :loading="deletingId === data.id"
            @click="$emit('delete', data)"
            v-tooltip.top="'Eliminar'"
            class="hover:bg-red-50"
          />
        </div>
      </template>
    </Column>
  </DataTable>
</template>

<script setup>
const props = defineProps({
  value: { type: Array, default: () => [] },
  rows: { type: Number, default: 5 },
  rowsPerPageOptions: { type: Array, default: () => [5, 10, 20] },
  deletingId: { type: [Number, String], default: null },
})

const emits = defineEmits(['edit', 'delete'])

const getRolSeverity = (rol) => {
  const r = (rol || '').toLowerCase()
  if (r.includes('admin')) return 'danger'
  if (r.includes('super')) return 'warning'
  if (r.includes('empleado')) return 'success'
  return 'info'
}
</script>

<style scoped>
/* Mobile-specific adjustments so DataTable stays legible without external wrappers */
@media (max-width: 768px) {
  :deep(.p-datatable table) {
    min-width: 860px !important;
    max-width: 1000px !important;
  }

  :deep(.p-datatable th),
  :deep(.p-datatable td) {
    white-space: nowrap !important;
    padding: 0.5rem 0.75rem !important;
    font-size: 0.98rem !important;
  }

  :deep(.p-paginator .p-paginator-prev),
  :deep(.p-paginator .p-paginator-next),
  :deep(.p-paginator .p-paginator-page) {
    min-width: 38px !important;
    height: 38px !important;
  }
}

/* Avoid PrimeVue small-text shrinking inside table */
:deep(.p-datatable .text-sm) {
  font-size: 0.98rem !important;
}
</style>
