<template>
    
    <!-- Estado de Carga (Skeleton) -->
    <div v-if="loading" class="p-4">
      <!-- Header Skeleton -->
      <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
        <div class="flex items-center gap-3">
          <Skeleton shape="circle" size="3rem" class="mr-2"></Skeleton>
          <div>
            <Skeleton width="10rem" class="mb-2"></Skeleton>
            <Skeleton width="5rem"></Skeleton>
          </div>
        </div>
        <Skeleton width="15rem" height="2.5rem" borderRadius="16px"></Skeleton>
      </div>

      <!-- Table Skeleton -->
      <div class="border border-gray-100 rounded-xl overflow-hidden">
        <!-- Cabecera Skeleton -->
        <div class="flex bg-gray-50 p-4 border-b border-gray-100">
          <Skeleton width="20%" height="1.5rem" class="mr-4"></Skeleton>
          <Skeleton width="20%" height="1.5rem" class="mr-4"></Skeleton>
          <Skeleton width="20%" height="1.5rem" class="mr-4"></Skeleton>
          <Skeleton width="20%" height="1.5rem" class="mr-4"></Skeleton>
          <Skeleton width="20%" height="1.5rem"></Skeleton>
        </div>
        
        <!-- Filas Skeleton -->
        <div v-for="i in 5" :key="i" class="flex p-4 border-b border-gray-50 items-center">
          <Skeleton width="20%" height="1.2rem" class="mr-4"></Skeleton>
          <Skeleton width="20%" height="1.2rem" class="mr-4"></Skeleton>
          <Skeleton width="20%" height="1.2rem" class="mr-4"></Skeleton>
          <Skeleton width="20%" height="2rem" borderRadius="16px" class="mr-4"></Skeleton>
          <Skeleton shape="circle" size="2.5rem"></Skeleton>
        </div>
      </div>
    </div>

    <!-- Tabla Real -->
    <DataTable
      v-else
      :value="value"
      :paginator="paginator"
      :rows="rows"
      :rowsPerPageOptions="rowsPerPageOptions"
      :tableStyle="tableStyle"
      class="p-datatable-lg modern-datatable"
      hoverableRows
      responsiveLayout="scroll"
      paginatorTemplate="FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink RowsPerPageDropdown"
    >
      <template #header>
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 p-4 bg-white/80 backdrop-blur-md border-b border-gray-100">
          <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-[#850D12] flex items-center justify-center shadow-lg" style="box-shadow: 0 10px 30px rgba(133,13,18,0.18);">
              <i class="pi pi-table text-white text-lg"></i>
            </div>
            <div>
              <h3 class="text-xl font-bold text-[#1D1D1F] m-0 tracking-tight">
                <slot name="title">Listado de Registros</slot>
              </h3>
              <p class="text-sm text-[#86868B] m-0 mt-0.5 font-medium">Gestiona y visualiza la información</p>
            </div>
          </div>
          
          <div class="relative w-full sm:w-auto group">
            <i class="pi pi-search absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 group-focus-within:text-[#850D12] transition-colors duration-300" />
            <InputText 
              v-model="globalFilter" 
              placeholder="Buscar en todos los campos..." 
              class="w-full sm:w-72 pl-11 pr-4 py-2.5 bg-gray-50/50 border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-[#850D12]/20 focus:border-[#850D12] transition-all duration-300" 
            />
          </div>
        </div>
      </template>

      <template #paginatorleft>
            <div class="flex items-center gap-2 px-2">
          <div class="w-2 h-2 rounded-full bg-[#850D12] animate-pulse"></div>
          <span class="text-sm font-medium text-[#6B7280]">
            Total: <span class="font-bold text-[#1D1D1F]">{{ totalCount }}</span> registros
          </span>
        </div>
      </template>

      <Column
        v-for="col in columns"
        :key="col"
        :field="col"
        :header="prettyHeader(col)"
        sortable
      >
        <template #body="{ data }">
          <template v-if="col === 'foto' && data[col]">
            <div class="flex items-center">
              <div class="relative group cursor-pointer">
                <div class="absolute inset-0 bg-black/20 rounded-xl opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-center justify-center">
                  <i class="pi pi-eye text-white"></i>
                </div>
                <img 
                  :src="`/storage/${data[col]}`" 
                  alt="Foto" 
                  class="w-14 h-14 object-cover rounded-xl shadow-sm border border-gray-100 transition-transform duration-300 group-hover:scale-105"
                />
              </div>
            </div>
          </template>
          <template v-else-if="col === 'estatus'">
            <div class="flex items-center">
              <span :class="getStatusBadgeClass(data[col])" class="px-3.5 py-1.5 rounded-full text-xs font-bold tracking-wide shadow-sm flex items-center gap-1.5">
                <span class="w-1.5 h-1.5 rounded-full bg-current opacity-75"></span>
                {{ formatCell(data[col]) }}
              </span>
            </div>
          </template>
          <template v-else>
            <span class="text-[15px] font-medium text-[#1D1D1F]">{{ formatCell(data[col]) }}</span>
          </template>
        </template>
      </Column>

      <template #empty>
        <div class="flex flex-col items-center justify-center py-16 px-4 text-center">
          <div class="w-24 h-24 bg-gray-50 rounded-full flex items-center justify-center mb-6 shadow-inner">
            <i class="pi pi-folder-open text-4xl text-gray-300"></i>
          </div>
          <h4 class="text-xl font-bold text-gray-800 mb-2">No se encontraron datos</h4>
          <p class="text-gray-500 max-w-sm">Aún no hay registros disponibles para mostrar en esta tabla. Los nuevos datos aparecerán aquí.</p>
        </div>
      </template>
    </DataTable>
</template>

<script setup>
import { computed, ref } from 'vue'
import InputText from 'primevue/inputtext'
import Skeleton from 'primevue/skeleton'

const props = defineProps({
  value: { type: Array, default: () => [] },
  rows: { type: Number, default: 10 },
  paginator: { type: Boolean, default: true },
  rowsPerPageOptions: { type: Array, default: () => [5, 10, 20, 50] },
  tableStyle: { type: String, default: 'min-width: 50rem' },
  loading: { type: Boolean, default: false },
})

const globalFilter = ref('')

const totalCount = computed(() => props.value.length)

const columns = computed(() => {
  if (!props.value || !props.value.length) return []
  return Object.keys(props.value[0]).filter(key => !['created_at', 'updated_at'].includes(key))
})

function prettyHeader(key) {
  return String(key)
    .replace(/_/g, ' ')
    .replace(/\b\w/g, (c) => c.toUpperCase())
}

function formatCell(val) {
  if (val === null || val === '' || typeof val === 'undefined') return '—'
  if (typeof val === 'boolean') return val ? 'Sí' : 'No'
  if (typeof val === 'object') return JSON.stringify(val)
  
  const dateRegex = /^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}/;
  if (typeof val === 'string' && dateRegex.test(val)) {
    return new Date(val).toLocaleDateString('es-MX', {
      year: 'numeric', month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit'
    })
  }
  
  return String(val)
}

function getStatusBadgeClass(status) {
  const s = String(status).toLowerCase()
  if (s.includes('pendiente') || s.includes('espera')) return 'bg-amber-50 text-amber-600 border border-amber-200/50'
  if (s.includes('resuelto') || s.includes('completado') || s.includes('activo')) return 'bg-emerald-50 text-emerald-600 border border-emerald-200/50'
  if (s.includes('rechazado') || s.includes('cancelado') || s.includes('inactivo')) return 'bg-rose-50 text-rose-600 border border-rose-200/50'
  if (s.includes('proceso')) return 'bg-sky-50 text-sky-600 border border-sky-200/50'
  return 'bg-gray-50 text-gray-600 border border-gray-200/50'
}
</script>

<style scoped>
/* Estilos Modernos y Limpios */
:deep(.modern-datatable .p-datatable-thead > tr > th) {
  background-color: #f8fafc;
  color: #64748b;
  font-weight: 700;
  font-size: 0.75rem;
  text-transform: uppercase;
  letter-spacing: 0.05em;
  padding: 1.25rem 1.5rem;
  border-bottom: 1px solid #e2e8f0;
  border-top: none;
  transition: background-color 0.2s;
}

:deep(.modern-datatable .p-datatable-thead > tr > th:hover) {
  background-color: #f1f5f9;
}

:deep(.modern-datatable .p-datatable-tbody > tr) {
  background-color: #ffffff;
  transition: all 0.2s ease;
}

:deep(.modern-datatable .p-datatable-tbody > tr:hover) {
  background-color: #f8fafc !important;
  /* Removed translate and shadow for a flat hover effect */
}

:deep(.modern-datatable .p-datatable-tbody > tr > td) {
  padding: 1.25rem 1.5rem;
  border-bottom: 1px solid #f1f5f9;
}

/* Paginador Moderno */
:deep(.modern-datatable .p-paginator) {
  background-color: #ffffff;
  border-top: 1px solid #f1f5f9;
  padding: 1.25rem;
  border-bottom-left-radius: 1rem;
  border-bottom-right-radius: 1rem;
}

:deep(.modern-datatable .p-paginator .p-paginator-pages .p-paginator-page) {
  border-radius: 0.5rem;
  min-width: 2.5rem;
  height: 2.5rem;
  color: #64748b;
  font-weight: 600;
  transition: all 0.2s;
  border: 1px solid transparent;
}

:deep(.modern-datatable .p-paginator .p-paginator-pages .p-paginator-page:hover) {
  background-color: #f1f5f9;
  color: #0f172a;
}

:deep(.modern-datatable .p-paginator .p-paginator-pages .p-paginator-page.p-highlight) {
  background-color: #850D12;
  color: #ffffff;
  box-shadow: 0 4px 14px 0 rgba(133, 13, 18, 0.39);
}

:deep(.modern-datatable .p-dropdown) {
  border-radius: 0.75rem;
  border-color: #e2e8f0;
}

:deep(.modern-datatable .p-dropdown:not(.p-disabled):hover) {
  border-color: #cbd5e1;
}

:deep(.modern-datatable .p-dropdown:not(.p-disabled).p-focus) {
  box-shadow: 0 0 0 2px rgba(133, 13, 18, 0.2);
  border-color: #850D12;
}

@media (max-width: 768px) {
  :deep(.p-datatable table) { min-width: 800px !important; }
  :deep(.modern-datatable .p-datatable-tbody > tr > td),
  :deep(.modern-datatable .p-datatable-thead > tr > th) { 
    padding: 1rem !important; 
  }
}
</style>
