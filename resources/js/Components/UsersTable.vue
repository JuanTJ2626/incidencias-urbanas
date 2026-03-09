<template>
  <DataTable
    :value="value"
    :paginator="true"
    :rows="rows"
    class="p-datatable-lg"
    scrollable
    :rowClass="rowClass"
    tableStyle="min-width:900px"
    paginatorTemplate="FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink RowsPerPageDropdown"
    :rowsPerPageOptions="rowsPerPageOptions"
  >
    <template #paginatorleft>
      <div class="text-sm font-bold px-3 text-sidebar-primary">Mostrando {{ totalCount }} usuarios</div>
    </template>
    <Column
      field="name"
      header="NOMBRE DEL USUARIO"
      sortable
      :style="{ minWidth: '320px' }"
    >
      <template #body="{ data }">
        <div class="flex items-center gap-4 py-2">
          <Avatar
            :label="data.name.charAt(0)"
            shape="circle"
            class="!bg-gray-100 dark:!bg-white/5 !text-gray-600 dark:!text-gray-400 !font-bold !border !border-gray-200 dark:!border-white/10"
            size="large"
          />
          <div>
            <span class="block font-bold text-gray-800 dark:text-gray-200 text-base">{{ data.name }}</span>
            <span class="block text-sm text-gray-400 dark:text-gray-500">{{ data.email }}</span>
          </div>
        </div>
      </template>
    </Column>

    <Column field="rol" header="ROL ASIGNADO" sortable :style="{ minWidth: '220px' }">
      <template #body="{ data }">
        <Tag
          :value="data.rol"
          class="role-tag !px-3 !py-1 !text-xs !uppercase !tracking-widest !font-bold"
          :class="getRolClass(data.rol)"
          rounded
        />
      </template>
    </Column>

    <Column
      field="created_at"
      header="FECHA REGISTRO"
      sortable
      :style="{ minWidth: '240px' }"
    >
      <template #body="{ data }">
        <span class="text-sm font-medium text-gray-500 dark:text-gray-400">{{
          new Date(data.created_at).toLocaleDateString()
        }}</span>
      </template>
    </Column>

    <Column field="activo" header="ESTADO" :style="{ minWidth: '130px' }">
      <template #body="{ data }">
        <button
          @click="$emit('toggle-activo', data)"
          v-tooltip.top="data.activo ? 'Clic para desactivar' : 'Clic para activar'"
          :class="[
            'flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-bold transition-all',
            data.activo
              ? 'bg-emerald-100 dark:bg-emerald-500/10 text-emerald-700 dark:text-emerald-400 hover:bg-emerald-200 dark:hover:bg-emerald-500/20'
              : 'bg-rose-100 dark:bg-rose-500/10 text-rose-700 dark:text-rose-400 hover:bg-rose-200 dark:hover:bg-rose-500/20'
          ]"
        >
          <i :class="data.activo ? 'pi pi-check-circle' : 'pi pi-ban'"></i>
          {{ data.activo ? 'Activo' : 'Inactivo' }}
        </button>
      </template>
    </Column>

    <Column header="ACCIONES" bodyClass="text-right" :style="{ minWidth: '140px' }">
      <template #body="{ data }">
        <div class="flex justify-end gap-2">
          <Button
            icon="pi pi-pencil"
            text
            rounded
            @click="$emit('edit', data)"
            v-tooltip.top="'Editar'"
            class="hover:bg-[#607C88]/10 text-[#607C88] dark:text-gray-400 dark:hover:bg-white/5"
          />
          <Button
            icon="pi pi-trash"
            text
            rounded
            :loading="deletingId === data.id"
            @click="$emit('delete', data)"
            v-tooltip.top="'Eliminar'"
            class="hover:bg-[#850D12]/10 dark:hover:bg-[#ff453a]/10 text-[#850D12] dark:text-[#ff453a]"
          />
        </div>
      </template>
    </Column>
    <template #empty>
      <div class="p-6 text-center text-gray-500">No hay usuarios para mostrar.</div>
    </template>
  </DataTable>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
  value: { type: Array, default: () => [] },
  rows: { type: Number, default: 5 },
  rowsPerPageOptions: { type: Array, default: () => [5, 10, 20] },
  deletingId: { type: [Number, String], default: null },
});

const emits = defineEmits(["edit", "delete", "toggle-activo"]);

// Map roles to sidebar palette classes
const getRolClass = (rol) => {
  const r = (rol || '').toLowerCase();
  if (r.includes('admin')) return 'tag-admin';
  if (r.includes('super')) return 'tag-super';
  if (r.includes('empleado')) return 'tag-employee';
  return 'tag-default';
};

const rowClass = (data) => {
  return props.deletingId === data.id ? 'row-deleting' : 'row-hover';
};

const totalCount = computed(() => props.value.length);
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

/* Sidebar palette classes */
.text-sidebar-primary {
  color: #850D12; /* sidebar primary red */
}
:global(.dark) .text-sidebar-primary {
  color: #ff453a;
}

.role-tag {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  border-radius: 9999px;
}

.tag-admin {
  background: linear-gradient(180deg, #850D12, #F04A4B);
  color: #fff !important;
}

.tag-super {
  background: linear-gradient(180deg, #607C88, #91A3AD);
  color: #fff !important;
}

.tag-employee {
  background: linear-gradient(180deg, #34C759, #2CB24E);
  color: #06371b !important;
}

.tag-default {
  background: #F5F5F7;
  color: #1D1D1F !important;
}
:global(.dark) .tag-default {
  background: rgba(255, 255, 255, 0.05);
  color: #ebebf5 !important;
}

/* Row hover and deleting states (subtle, using sidebar tones) */
:deep(.row-hover:hover) {
  background: rgba(133,13,18,0.04) !important;
}
:global(.dark) :deep(.row-hover:hover) {
  background: rgba(255, 255, 255, 0.05) !important;
}

:deep(.row-deleting) {
  opacity: 0.6 !important;
  pointer-events: none !important;
}

/* PrimeVue Table Dark Overrides */
:global(.dark) :deep(.p-datatable) {
  background: transparent !important;
}
:global(.dark) :deep(.p-datatable-thead > tr > th) {
  background: #1C1C1E !important;
  border-bottom-color: rgba(255, 255, 255, 0.05) !important;
  color: #a1a1a6 !important;
}
:global(.dark) :deep(.p-datatable-tbody > tr) {
  background: #111112 !important;
  color: #ebebf5 !important;
}
:global(.dark) :deep(.p-datatable-tbody > tr > td) {
  border-bottom-color: rgba(255, 255, 255, 0.05) !important;
}
:global(.dark) :deep(.p-paginator) {
  background: #111112 !important;
  border-top-color: rgba(255, 255, 255, 0.05) !important;
}
:global(.dark) :deep(.p-paginator .p-paginator-page) {
    color: #a1a1a6 !important;
}
:global(.dark) :deep(.p-paginator .p-paginator-page.p-highlight) {
    background: #ff453a !important;
    color: white !important;
}
</style>
