<template>

    <!-- Estado de Carga (Skeleton) -->
    <div v-if="loading" class="p-4 bg-white dark:bg-app-card">
      <!-- Header Skeleton -->
      <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
        <div class="flex items-center gap-3">
          <Skeleton shape="circle" size="3rem" class="mr-2 dark:bg-white/5"></Skeleton>
          <div>
            <Skeleton width="10rem" class="mb-2 dark:bg-white/5"></Skeleton>
            <Skeleton width="5rem" class="dark:bg-white/5"></Skeleton>
          </div>
        </div>
        <Skeleton width="15rem" height="2.5rem" borderRadius="16px" class="dark:bg-white/5"></Skeleton>
      </div>

      <!-- Table Skeleton -->
      <div class="border border-gray-100 dark:border-app-border rounded-xl overflow-hidden">
        <!-- Cabecera Skeleton -->
        <div class="flex bg-gray-50 dark:bg-app-secondary p-4 border-b border-gray-100 dark:border-app-border">
          <Skeleton width="20%" height="1.5rem" class="mr-4 dark:bg-white/5"></Skeleton>
          <Skeleton width="20%" height="1.5rem" class="mr-4 dark:bg-white/5"></Skeleton>
          <Skeleton width="20%" height="1.5rem" class="mr-4 dark:bg-white/5"></Skeleton>
          <Skeleton width="20%" height="1.5rem" class="mr-4 dark:bg-white/5"></Skeleton>
          <Skeleton width="20%" height="1.5rem" class="dark:bg-white/5"></Skeleton>
        </div>
        
        <!-- Filas Skeleton -->
        <div v-for="i in 5" :key="i" class="flex p-4 border-b border-gray-50 dark:border-app-border items-center">
          <Skeleton width="20%" height="1.2rem" class="mr-4 dark:bg-white/5"></Skeleton>
          <Skeleton width="20%" height="1.2rem" class="mr-4 dark:bg-white/5"></Skeleton>
          <Skeleton width="20%" height="1.2rem" class="mr-4 dark:bg-white/5"></Skeleton>
          <Skeleton width="20%" height="2rem" borderRadius="16px" class="mr-4 dark:bg-white/5"></Skeleton>
          <Skeleton shape="circle" size="2.5rem" class="dark:bg-white/5"></Skeleton>
        </div>
      </div>
    </div>

    <!-- Tabla Real -->
    <DataTable
      v-else
      :value="filteredValue"
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
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 p-4 bg-white/80 dark:bg-app-card/80 backdrop-blur-md border-b border-gray-100 dark:border-app-border">
          <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-brand-red flex items-center justify-center shadow-lg" style="box-shadow: 0 10px 30px rgba(133,13,18,0.18);">
              <i class="pi pi-table text-white text-lg"></i>
            </div>
            <div>
              <h3 class="text-xl font-bold text-[#1D1D1F] dark:text-white m-0 tracking-tight">
                <slot name="title">Listado de Registros</slot>
              </h3>
              <p class="text-sm text-[#86868B] dark:text-[#A1A1A6] m-0 mt-0.5 font-medium">Gestiona y visualiza la información</p>
            </div>
          </div>
          
          <div class="flex items-center gap-3">
            <!-- Slot para botones extra en el toolbar -->
            <slot name="toolbar" />
            <div class="relative group">
              <i class="pi pi-search absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 group-focus-within:text-brand-red dark:group-focus-within:text-brand-red transition-colors duration-300" />
              <InputText
                v-model="globalFilter"
                placeholder="Buscar en todos los campos..."
                class="w-full sm:w-72 pl-11 pr-4 py-2.5 bg-gray-50/50 dark:bg-white/5 border-gray-200 dark:border-app-border rounded-xl text-sm dark:text-white focus:ring-2 focus:ring-brand-red/20 dark:focus:ring-white/10 focus:border-brand-red dark:focus:border-white/30 transition-all duration-300"
              />
            </div>
          </div>
        </div>
      </template>

      <template #paginatorleft>
            <div class="flex items-center gap-2 px-2">
          <div class="w-2 h-2 rounded-full bg-brand-red animate-pulse"></div>
          <span class="text-sm font-medium text-[#6B7280] dark:text-[#A1A1A6]">
            Total: <span class="font-bold text-[#1D1D1F] dark:text-white">{{ totalCount }}</span> registros
          </span>
        </div>
      </template>

      <!-- Columnas auto-detectadas -->
      <Column
        v-for="col in columns"
        :key="col"
        :field="col"
        :header="prettyHeader(col)"
        sortable
      >
        <template #body="{ data }">
          <!-- Slot override: #col-{fieldname} -->
          <slot v-if="$slots['col-' + col]" :name="'col-' + col" :data="data" :value="data[col]" />

          <!-- Renderizado por defecto: foto -->
          <template v-else-if="isFotoField(col) && data[col]">
            <div class="relative group cursor-pointer w-fit">
              <div class="absolute inset-0 bg-black/20 dark:bg-black/50 rounded-xl opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-center justify-center">
                <i class="pi pi-eye text-white"></i>
              </div>
              <img
                :src="`/storage/${data[col]}`"
                alt="Foto"
                class="w-14 h-14 object-cover rounded-xl shadow-sm border border-gray-100 dark:border-app-border transition-transform duration-300 group-hover:scale-105"
              />
            </div>
          </template>

          <!-- Renderizado por defecto: campos tipo estatus -->
          <template v-else-if="isStatusField(col)">
            <span :class="getStatusBadgeClass(data[col])" class="px-3.5 py-1.5 rounded-full text-xs font-bold tracking-wide shadow-sm flex items-center gap-1.5 w-fit">
              <span class="w-1.5 h-1.5 rounded-full bg-current opacity-75"></span>
              {{ formatCell(data[col]) }}
            </span>
          </template>

          <!-- Renderizado genérico -->
          <template v-else>
            <span class="text-[15px] font-medium text-[#1D1D1F] dark:text-gray-200">{{ formatCell(data[col]) }}</span>
          </template>
        </template>
      </Column>

      <!-- Columna de acciones (solo si se pasa el slot #actions) -->
      <Column v-if="$slots.actions" :header="actionsHeader" :style="{ minWidth: actionsWidth }">
        <template #body="{ data }">
          <slot name="actions" :data="data" />
        </template>
      </Column>

      <template #empty>
        <div class="flex flex-col items-center justify-center py-16 px-4 text-center">
          <div class="w-24 h-24 bg-gray-50 dark:bg-white/5 rounded-full flex items-center justify-center mb-6 shadow-inner">
            <i class="pi pi-folder-open text-4xl text-gray-300 dark:text-gray-600"></i>
          </div>
          <h4 class="text-xl font-bold text-gray-800 dark:text-gray-200 mb-2">No se encontraron datos</h4>
          <p class="text-gray-500 dark:text-gray-400 max-w-sm">Aún no hay registros disponibles para mostrar en esta tabla. Los nuevos datos aparecerán aquí.</p>
        </div>
      </template>
    </DataTable>
</template>

<script setup>
import { computed, ref } from 'vue'
import InputText from 'primevue/inputtext'
import Skeleton from 'primevue/skeleton'

// ─── Palabras clave que identifican campos de imagen / estatus ─────────
const FOTO_KEYWORDS   = ['foto', 'imagen', 'image', 'photo', 'picture', 'thumbnail']
const STATUS_KEYWORDS = ['estatus', 'status', 'estado', 'estado_actual']

const props = defineProps({
  value:             { type: Array,   default: () => [] },
  rows:              { type: Number,  default: 10 },
  paginator:         { type: Boolean, default: true },
  rowsPerPageOptions:{ type: Array,   default: () => [5, 10, 20, 50] },
  tableStyle:        { type: String,  default: 'min-width: 50rem' },
  loading:           { type: Boolean, default: false },
  /** Columnas a excluir del auto-detect */
  excludeColumns:    { type: Array,   default: () => ['created_at', 'updated_at'] },
  /** Encabezado de la columna de acciones */
  actionsHeader:     { type: String,  default: 'Acciones' },
  /** Ancho mínimo de la columna de acciones */
  actionsWidth:      { type: String,  default: '200px' },
})

const globalFilter = ref('')

const totalCount = computed(() => props.value.length)

/** Columnas auto-detectadas respetando excludeColumns */
const columns = computed(() => {
  if (!props.value?.length) return []
  return Object.keys(props.value[0]).filter(key => !props.excludeColumns.includes(key))
})

/** Filtra filas según el texto buscado */
const filteredValue = computed(() => {
  if (!globalFilter.value) return props.value
  const q = globalFilter.value.toLowerCase()
  return props.value.filter(row =>
    Object.values(row).some(v => v != null && String(v).toLowerCase().includes(q))
  )
})

/** Detecta si un campo es de tipo imagen */
function isFotoField(key) {
  return FOTO_KEYWORDS.some(k => key.toLowerCase().includes(k))
}

/** Detecta si un campo es de tipo estatus */
function isStatusField(key) {
  return STATUS_KEYWORDS.some(k => key.toLowerCase().includes(k))
}

/** Convierte snake_case / camelCase en título legible */
function prettyHeader(key) {
  return String(key)
    .replace(/_/g, ' ')
    .replace(/([a-z])([A-Z])/g, '$1 $2')
    .replace(/\b\w/g, c => c.toUpperCase())
}

/** Formatea cualquier valor para mostrarlo en la celda */
function formatCell(val) {
  if (val === null || val === '' || typeof val === 'undefined') return '—'
  if (typeof val === 'boolean') return val ? 'Sí' : 'No'
  if (typeof val === 'object') return JSON.stringify(val)
  const dateRegex = /^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}/
  if (typeof val === 'string' && dateRegex.test(val)) {
    return new Date(val).toLocaleDateString('es-MX', {
      year: 'numeric', month: 'short', day: 'numeric',
      hour: '2-digit', minute: '2-digit',
    })
  }
  return String(val)
}

/** Clase CSS del badge de estatus */
function getStatusBadgeClass(status) {
  const s = String(status ?? '').toLowerCase()
  if (s.includes('pendiente') || s.includes('espera'))     return 'bg-amber-50 dark:bg-amber-500/10 text-amber-600 dark:text-amber-400 border border-amber-200/50'
  if (s.includes('resuelto')  || s.includes('completado') || s.includes('activo'))  return 'bg-emerald-50 dark:bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-200/50'
  if (s.includes('rechazado') || s.includes('cancelado')  || s.includes('inactivo')) return 'bg-rose-50 dark:bg-rose-500/10 text-rose-600 dark:text-rose-400 border border-rose-200/50'
  if (s.includes('proceso'))  return 'bg-sky-50 dark:bg-sky-500/10 text-sky-600 dark:text-sky-400 border border-sky-200/50'
  return 'bg-gray-50 dark:bg-white/10 text-gray-600 dark:text-gray-400 border border-gray-200/50 dark:border-app-border'
}
</script>

<style scoped>
/* Estilos Modernos y Limpios */
:deep(.modern-datatable) {
  background-color: transparent !important;
}

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

:global(.dark) :deep(.modern-datatable .p-datatable-thead > tr > th) {
  background-color: var(--app-secondary) !important;
  color: #a1a1a6 !important;
  border-bottom-color: var(--app-border) !important;
}

:deep(.modern-datatable .p-datatable-thead > tr > th:hover) {
  background-color: #f1f5f9;
}

:global(.dark) :deep(.modern-datatable .p-datatable-thead > tr > th:hover) {
  background-color: #3f3f46 !important;
}

:deep(.modern-datatable .p-datatable-tbody > tr) {
  background-color: #ffffff;
  transition: all 0.2s ease;
}

:global(.dark) :deep(.modern-datatable .p-datatable-tbody > tr) {
  background-color: var(--app-card) !important;
  color: #ebebf5 !important;
}

:deep(.modern-datatable .p-datatable-tbody > tr:hover) {
  background-color: #f8fafc !important;
}

:global(.dark) :deep(.modern-datatable .p-datatable-tbody > tr:hover) {
  background-color: var(--app-secondary) !important;
}

:deep(.modern-datatable .p-datatable-tbody > tr > td) {
  padding: 1.25rem 1.5rem;
  border-bottom: 1px solid #f1f5f9;
}

:global(.dark) :deep(.modern-datatable .p-datatable-tbody > tr > td) {
  border-bottom-color: var(--app-border) !important;
}

/* Paginador Moderno */
:deep(.modern-datatable .p-paginator) {
  background-color: #ffffff;
  border-top: 1px solid #f1f5f9;
  padding: 1.25rem;
  border-bottom-left-radius: 1rem;
  border-bottom-right-radius: 1rem;
}

:global(.dark) :deep(.modern-datatable .p-paginator) {
  background-color: var(--app-card) !important;
  border-top-color: var(--app-border) !important;
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

:global(.dark) :deep(.modern-datatable .p-paginator .p-paginator-pages .p-paginator-page) {
  color: #a1a1a6 !important;
}

:deep(.modern-datatable .p-paginator .p-paginator-pages .p-paginator-page:hover) {
  background-color: #f1f5f9;
  color: #0f172a;
}

:global(.dark) :deep(.modern-datatable .p-paginator .p-paginator-pages .p-paginator-page:hover) {
  background-color: var(--app-secondary) !important;
  color: white !important;
}

:deep(.modern-datatable .p-paginator .p-paginator-pages .p-paginator-page.p-highlight) {
  background-color: #850D12;
  color: #ffffff;
  box-shadow: 0 4px 14px 0 rgba(133, 13, 18, 0.39);
}

:global(.dark) :deep(.modern-datatable .p-paginator .p-paginator-pages .p-paginator-page.p-highlight) {
  background-color: var(--brand-red) !important;
  box-shadow: 0 4px 14px 0 rgba(255, 69, 58, 0.2) !important;
}

:deep(.modern-datatable .p-dropdown) {
  border-radius: 0.75rem;
  border-color: #e2e8f0;
}

:global(.dark) :deep(.modern-datatable .p-dropdown) {
  background-color: var(--app-secondary) !important;
  border-color: var(--app-border) !important;
  color: white !important;
}

:deep(.modern-datatable .p-dropdown:not(.p-disabled):hover) {
  border-color: #cbd5e1;
}

:deep(.modern-datatable .p-dropdown:not(.p-disabled).p-focus) {
  box-shadow: 0 0 0 2px rgba(133, 13, 18, 0.2);
  border-color: #850D12;
}

:global(.dark) :deep(.modern-datatable .p-dropdown:not(.p-disabled).p-focus) {
  box-shadow: 0 0 0 2px rgba(255, 255, 255, 0.1) !important;
  border-color: white !important;
}

@media (max-width: 768px) {
  :deep(.p-datatable table) { min-width: 800px !important; }
  :deep(.modern-datatable .p-datatable-tbody > tr > td),
  :deep(.modern-datatable .p-datatable-thead > tr > th) { 
    padding: 1rem !important; 
  }
}
</style>
