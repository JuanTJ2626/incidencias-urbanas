<template>
  <div class="bg-white/70 dark:bg-app-card backdrop-blur-xl rounded-2xl border border-app-border shadow-sm overflow-hidden transition-all duration-300 relative">
    
    <!-- Acciones en lote (Bulk Actions) -->
    <Transition name="slide-up">
      <div v-if="selection.length > 0" class="fixed bottom-10 left-1/2 -translate-x-1/2 z-[60] flex items-center gap-6 px-8 py-4 bg-[#1D1D1F] dark:bg-white text-white dark:text-black rounded-[2rem] shadow-2xl shadow-black/30 border border-white/10">
        <div class="flex items-center gap-3 pr-6 border-r border-white/20 dark:border-black/10">
          <div class="w-8 h-8 rounded-full bg-brand-red flex items-center justify-center text-xs font-black text-white">
            {{ selection.length }}
          </div>
          <span class="text-sm font-black tracking-tight uppercase">Seleccionados</span>
        </div>
        
        <div class="flex items-center gap-4">
          <slot name="bulk-actions" :selection="selection"></slot>
          <button @click="clearSelection" class="text-xs font-bold hover:text-brand-red dark:hover:text-brand-red transition-colors flex items-center gap-2">
            <i class="pi pi-times-circle"></i>
            Cancelar
          </button>
        </div>
      </div>
    </Transition>

    <!-- Header de la tabla -->
    <div v-if="title || subtitle" class="flex flex-col md:flex-row md:items-center justify-between gap-4 px-6 py-5 border-b border-app-secondary dark:border-app-border">
      <div class="flex items-center gap-3">
        <div v-if="icon" class="w-10 h-10 rounded-xl bg-app-secondary flex items-center justify-center shrink-0">
          <i :class="[icon, 'text-brand-red text-lg']"></i>
        </div>
        <div>
          <h3 v-if="title" class="text-base font-black text-[#1D1D1F] dark:text-white leading-tight">{{ title }}</h3>
          <p v-if="subtitle" class="text-[11px] text-[#86868B] dark:text-[#A1A1A6] font-bold uppercase tracking-wider mt-0.5">{{ subtitle }}</p>
        </div>
      </div>
      
      <!-- Slot para acciones (buscadores, botones, etc) -->
      <div class="flex items-center gap-2">
        <slot name="actions"></slot>
      </div>
    </div>

    <!-- Cuerpo de la Tabla -->
    <div class="overflow-x-auto">
      <table class="min-w-full text-sm border-separate border-spacing-0">
        <thead>
          <tr class="bg-app-secondary/50 dark:bg-app-bg/20">
            <!-- Columna Selección -->
            <th v-if="selectable" class="w-12 px-6 py-4 border-b border-app-secondary dark:border-app-border first:pl-6">
              <div class="flex items-center">
                <input 
                  type="checkbox" 
                  :checked="isAllSelected" 
                  @change="toggleSelectAll"
                  class="w-5 h-5 rounded-md border-gray-300 text-brand-red focus:ring-brand-red cursor-pointer transition-all"
                >
              </div>
            </th>

            <th 
              v-for="col in columns" 
              :key="col.key"
              :style="{ width: col.width || 'auto' }"
              class="text-left text-[11px] font-black text-[#86868B] dark:text-[#A1A1A6] uppercase tracking-widest px-6 py-4 border-b border-app-secondary dark:border-app-border last:pr-6"
            >
              {{ col.label }}
            </th>
          </tr>
        </thead>
        <tbody class="divide-y divide-app-secondary dark:divide-app-border">
          <tr 
            v-for="(row, index) in paginatedData" 
            :key="index"
            class="group hover:bg-app-secondary/30 dark:hover:bg-white/5 transition-colors duration-150"
            :class="{ 'bg-brand-red/5 dark:bg-brand-red/10': isSelected(row) }"
          >
            <!-- Checkbox de Fila -->
            <td v-if="selectable" class="px-6 py-4 align-middle border-none">
               <input 
                  type="checkbox" 
                  :checked="isSelected(row)" 
                  @change="toggleSelection(row)"
                  class="w-5 h-5 rounded-md border-gray-300 text-brand-red focus:ring-brand-red cursor-pointer transition-all"
                >
            </td>

            <td 
              v-for="col in columns" 
              :key="col.key"
              class="px-6 py-4 whitespace-nowrap align-middle"
            >
              <!-- Slot dinámico por columna: cell-NOMBRE_COLUMNA -->
              <slot :name="`cell-${col.key}`" :row="row" :value="row[col.key]">
                <span class="font-bold text-[#1D1D1F] dark:text-gray-200">{{ row[col.key] }}</span>
              </slot>
            </td>
          </tr>
          
          <!-- Empty State -->
          <tr v-if="!data || data.length === 0">
            <td :colspan="selectable ? columns.length + 1 : columns.length" class="px-6 py-12 text-center">
              <div class="flex flex-col items-center gap-2 text-[#86868B] dark:text-[#A1A1A6]">
                <i class="pi pi-inbox text-3xl opacity-20"></i>
                <p class="font-bold">No hay registros disponibles</p>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Footer con Paginación -->
    <div class="px-6 py-4 border-t border-app-secondary dark:border-app-border bg-app-secondary/10 flex flex-col md:flex-row md:items-center justify-between gap-4">
      <!-- Estadísticas de página -->
      <div class="text-[11px] font-black text-[#86868B] dark:text-[#A1A1A6] uppercase tracking-widest">
        Mostrando <span class="text-brand-red">{{ startItem }}</span> a <span class="text-brand-red">{{ endItem }}</span> de <span class="text-brand-red">{{ data.length }}</span> registros
      </div>

      <!-- Controles de paginación -->
      <div v-if="totalPages > 1" class="flex items-center gap-1">
        <button 
          @click="currentPage = 1" 
          :disabled="currentPage === 1"
          class="w-8 h-8 rounded-lg flex items-center justify-center transition-all bg-white dark:bg-app-bg border border-app-border text-[#1D1D1F] dark:text-white disabled:opacity-30 hover:bg-app-secondary"
        >
          <i class="pi pi-angle-double-left text-[10px]"></i>
        </button>
        <button 
          @click="currentPage--" 
          :disabled="currentPage === 1"
          class="w-8 h-8 rounded-lg flex items-center justify-center transition-all bg-white dark:bg-app-bg border border-app-border text-[#1D1D1F] dark:text-white disabled:opacity-30 hover:bg-app-secondary"
        >
          <i class="pi pi-angle-left text-[10px]"></i>
        </button>

        <!-- Páginas numéricas (simplificado) -->
        <span class="px-4 text-[11px] font-black text-[#1D1D1F] dark:text-white uppercase tracking-widest">
          Página {{ currentPage }} de {{ totalPages }}
        </span>

        <button 
          @click="currentPage++" 
          :disabled="currentPage === totalPages"
          class="w-8 h-8 rounded-lg flex items-center justify-center transition-all bg-white dark:bg-app-bg border border-app-border text-[#1D1D1F] dark:text-white disabled:opacity-30 hover:bg-app-secondary"
        >
          <i class="pi pi-angle-right text-[10px]"></i>
        </button>
        <button 
          @click="currentPage = totalPages" 
          :disabled="currentPage === totalPages"
          class="w-8 h-8 rounded-lg flex items-center justify-center transition-all bg-white dark:bg-app-bg border border-app-border text-[#1D1D1F] dark:text-white disabled:opacity-30 hover:bg-app-secondary"
        >
          <i class="pi pi-angle-double-right text-[10px]"></i>
        </button>
      </div>

      <!-- Slot para footer adicional -->
      <div v-if="$slots.footer">
        <slot name="footer"></slot>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, watch } from 'vue'

const props = defineProps({
  title: { type: String, default: '' },
  subtitle: { type: String, default: '' },
  icon: { type: String, default: '' },
  columns: {
    type: Array,
    required: true,
  },
  data: {
    type: Array,
    default: () => []
  },
  rowsPerPage: {
    type: Number,
    default: 10
  },
  selectable: {
    type: Boolean,
    default: false
  },
  selection: {
    type: Array,
    default: () => []
  }
})

const emit = defineEmits(['update:selection'])

const currentPage = ref(1)

// Resetear página si los datos cambian
watch(() => props.data, () => {
  currentPage.value = 1
  clearSelection()
})

const totalPages = computed(() => {
  return Math.ceil(props.data.length / props.rowsPerPage) || 1
})

const paginatedData = computed(() => {
  const start = (currentPage.value - 1) * props.rowsPerPage
  const end = start + props.rowsPerPage
  return props.data.slice(start, end)
})

const startItem = computed(() => props.data.length === 0 ? 0 : (currentPage.value - 1) * props.rowsPerPage + 1)
const endItem = computed(() => Math.min(currentPage.value * props.rowsPerPage, props.data.length))

// ── Selección ──
const isSelected = (row) => props.selection.some(item => item.id === row.id)

const isAllSelected = computed(() => {
  if (paginatedData.value.length === 0) return false
  return paginatedData.value.every(row => isSelected(row))
})

const toggleSelection = (row) => {
  const newSelection = [...props.selection]
  const index = newSelection.findIndex(item => item.id === row.id)
  
  if (index === -1) {
    newSelection.push(row)
  } else {
    newSelection.splice(index, 1)
  }
  emit('update:selection', newSelection)
}

const toggleSelectAll = () => {
  let newSelection = [...props.selection]
  
  if (isAllSelected.value) {
    // Deseleccionar los de esta página
    const currentIds = paginatedData.value.map(r => r.id)
    newSelection = newSelection.filter(item => !currentIds.includes(item.id))
  } else {
    // Seleccionar los de esta página (evitando duplicados si ya había selección de otras páginas)
    paginatedData.value.forEach(row => {
      if (!newSelection.some(s => s.id === row.id)) {
        newSelection.push(row)
      }
    })
  }
  emit('update:selection', newSelection)
}

const clearSelection = () => {
  emit('update:selection', [])
}
</script>

<style scoped>
@reference "../../css/app.css";

/* Estilos extra si fueran necesarios, pero usamos Tailwind + Variables Globales */
table {
  width: 100%;
}

/* Animación para la barra de acciones */
.slide-up-enter-active,
.slide-up-leave-active {
  transition: all 0.4s cubic-bezier(0.23, 1, 0.32, 1);
}

.slide-up-enter-from,
.slide-up-leave-to {
  opacity: 0;
  transform: translate(-50%, 20px) scale(0.9);
}
</style>
