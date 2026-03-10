<template>
  <Teleport to="body">
    <!-- Overlay -->
    <Transition name="fade">
      <div v-if="isOpen" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-[100]" @click="close"></div>
    </Transition>

    <!-- Search Modal -->
    <Transition name="search-modal">
      <div v-if="isOpen" class="fixed inset-0 z-[101] flex items-start justify-center pt-[15vh]">
        <div class="w-full max-w-xl mx-4 bg-white dark:bg-[#1C1C1E] rounded-2xl shadow-2xl shadow-black/30 border border-app-border overflow-hidden" @click.stop>
          
          <!-- Search Input -->
          <div class="flex items-center gap-3 px-5 py-4 border-b border-app-border">
            <i class="pi pi-search text-brand-red text-lg"></i>
            <input
              ref="searchInput"
              v-model="query"
              type="text"
              placeholder="Buscar incidencias, usuarios, órdenes..."
              class="flex-1 bg-transparent text-base font-medium text-[#1D1D1F] dark:text-white placeholder-[#86868B] outline-none"
              @keydown.escape="close"
              @keydown.down.prevent="moveDown"
              @keydown.up.prevent="moveUp"
              @keydown.enter.prevent="selectItem"
            />
            <kbd class="hidden sm:inline-flex items-center gap-0.5 px-2 py-1 text-[10px] font-bold text-[#86868B] bg-app-secondary dark:bg-white/10 rounded-md border border-app-border">
              ESC
            </kbd>
          </div>

          <!-- Quick Actions -->
          <div v-if="!query" class="px-3 py-3 border-b border-app-border/50">
            <p class="text-[10px] font-black text-[#86868B] dark:text-[#A1A1A6] uppercase tracking-widest px-2 mb-2">Acciones Rápidas</p>
            <div class="space-y-0.5">
              <button 
                v-for="action in quickActions" 
                :key="action.label"
                class="w-full flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-semibold text-[#1D1D1F] dark:text-white hover:bg-app-secondary dark:hover:bg-white/5 transition-colors text-left"
                @click="action.handler"
              >
                <div class="w-8 h-8 rounded-lg bg-app-secondary dark:bg-white/10 flex items-center justify-center shrink-0">
                  <i :class="[action.icon, 'text-brand-red text-sm']"></i>
                </div>
                <span>{{ action.label }}</span>
                <kbd v-if="action.shortcut" class="ml-auto text-[10px] text-[#86868B] bg-app-secondary dark:bg-white/10 px-1.5 py-0.5 rounded border border-app-border">
                  {{ action.shortcut }}
                </kbd>
              </button>
            </div>
          </div>

          <!-- Results -->
          <div v-if="query && filteredResults.length > 0" class="max-h-[300px] overflow-y-auto px-3 py-3">
            <p class="text-[10px] font-black text-[#86868B] dark:text-[#A1A1A6] uppercase tracking-widest px-2 mb-2">
              {{ filteredResults.length }} resultado{{ filteredResults.length > 1 ? 's' : '' }}
            </p>
            <div class="space-y-0.5">
              <button 
                v-for="(result, index) in filteredResults" 
                :key="result.id"
                class="w-full flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm transition-colors text-left"
                :class="index === activeIndex ? 'bg-brand-red text-white' : 'text-[#1D1D1F] dark:text-white hover:bg-app-secondary dark:hover:bg-white/5'"
                @click="goTo(result)"
                @mouseenter="activeIndex = index"
              >
                <div 
                  class="w-8 h-8 rounded-lg flex items-center justify-center shrink-0"
                  :class="index === activeIndex ? 'bg-white/20' : 'bg-app-secondary dark:bg-white/10'"
                >
                  <i :class="[result.icon, index === activeIndex ? 'text-white' : 'text-brand-red', 'text-sm']"></i>
                </div>
                <div class="flex-1 min-w-0">
                  <p class="font-bold text-sm truncate" :class="index === activeIndex ? 'text-white' : ''">{{ result.title }}</p>
                  <p class="text-xs truncate" :class="index === activeIndex ? 'text-white/70' : 'text-[#86868B]'">{{ result.subtitle }}</p>
                </div>
                <span 
                  class="text-[10px] font-black uppercase px-2 py-0.5 rounded-full"
                  :class="index === activeIndex ? 'bg-white/20 text-white' : result.tagClass"
                >
                  {{ result.tag }}
                </span>
              </button>
            </div>
          </div>

          <!-- No Results -->
          <div v-if="query && filteredResults.length === 0" class="px-5 py-10 text-center">
            <i class="pi pi-search text-3xl text-[#86868B]/30 mb-2"></i>
            <p class="text-sm font-bold text-[#86868B]">Sin resultados para "{{ query }}"</p>
            <p class="text-xs text-[#A1A1A6] mt-1">Intenta con otro término de búsqueda</p>
          </div>

          <!-- Footer -->
          <div class="flex items-center justify-between px-5 py-3 border-t border-app-border/50 bg-app-secondary/20 dark:bg-white/5">
            <div class="flex items-center gap-3 text-[10px] text-[#86868B] font-bold">
              <span class="flex items-center gap-1"><kbd class="px-1.5 py-0.5 bg-app-secondary dark:bg-white/10 rounded border border-app-border text-[10px]">↑↓</kbd> Navegar</span>
              <span class="flex items-center gap-1"><kbd class="px-1.5 py-0.5 bg-app-secondary dark:bg-white/10 rounded border border-app-border text-[10px]">↵</kbd> Seleccionar</span>
            </div>
            <span class="text-[10px] font-black text-brand-red uppercase tracking-widest">SIGIU Search</span>
          </div>
        </div>
      </div>
    </Transition>
  </Teleport>
</template>

<script setup>
import { ref, computed, watch, onMounted, onBeforeUnmount, nextTick } from 'vue'
import { Inertia } from '@inertiajs/inertia'

const isOpen = ref(false)
const query = ref('')
const activeIndex = ref(0)
const searchInput = ref(null)

// Páginas y secciones indexadas para búsqueda
const searchableItems = ref([
  { id: 1, title: 'Dashboard Ejecutivo', subtitle: 'Vista general del sistema', icon: 'pi pi-chart-bar', tag: 'Página', tagClass: 'bg-sky-100 text-sky-700', route: '/admin/dashboard' },
  { id: 2, title: 'Mapa de Incidencias', subtitle: 'Visualización geoespacial', icon: 'pi pi-map', tag: 'Página', tagClass: 'bg-emerald-100 text-emerald-700', route: '/admin/mapa' },
  { id: 3, title: 'Gestión de Incidencias', subtitle: 'Administrar reportes ciudadanos', icon: 'pi pi-exclamation-triangle', tag: 'Página', tagClass: 'bg-amber-100 text-amber-700', route: '/admin/incidencias' },
  { id: 4, title: 'Gestión de Usuarios', subtitle: 'Control de cuentas y roles', icon: 'pi pi-users', tag: 'Página', tagClass: 'bg-purple-100 text-purple-700', route: '/admin/usuarios' },
  { id: 5, title: 'Analíticas y Gráficas', subtitle: 'Métricas y reportes avanzados', icon: 'pi pi-chart-line', tag: 'Página', tagClass: 'bg-sky-100 text-sky-700', route: '/admin/graficas' },
  { id: 6, title: 'Reportes y Exportación', subtitle: 'Generar Excel y PDF', icon: 'pi pi-file-export', tag: 'Página', tagClass: 'bg-rose-100 text-rose-700', route: '/admin/reportes' },
  { id: 7, title: 'Bacheo', subtitle: 'Categoría de incidencias de baches', icon: 'pi pi-car', tag: 'Categoría', tagClass: 'bg-orange-100 text-orange-700', route: '/admin/incidencias' },
  { id: 8, title: 'Luminaria', subtitle: 'Categoría de alumbrado público', icon: 'pi pi-bolt', tag: 'Categoría', tagClass: 'bg-yellow-100 text-yellow-700', route: '/admin/incidencias' },
  { id: 9, title: 'Fuga de Agua', subtitle: 'Categoría de fugas y drenaje', icon: 'pi pi-cloud', tag: 'Categoría', tagClass: 'bg-cyan-100 text-cyan-700', route: '/admin/incidencias' },
  { id: 10, title: 'Seguridad', subtitle: 'Categoría de seguridad pública', icon: 'pi pi-shield', tag: 'Categoría', tagClass: 'bg-red-100 text-red-700', route: '/admin/incidencias' },
])

const filteredResults = computed(() => {
  if (!query.value.trim()) return []
  const q = query.value.toLowerCase()
  return searchableItems.value.filter(item =>
    item.title.toLowerCase().includes(q) ||
    item.subtitle.toLowerCase().includes(q) ||
    item.tag.toLowerCase().includes(q)
  )
})

const quickActions = [
  { label: 'Ir al Dashboard', icon: 'pi pi-chart-bar', shortcut: 'D', handler: () => navigate('/admin/dashboard') },
  { label: 'Ver Mapa', icon: 'pi pi-map', shortcut: 'M', handler: () => navigate('/admin/mapa') },
  { label: 'Gestionar Incidencias', icon: 'pi pi-exclamation-triangle', shortcut: 'I', handler: () => navigate('/admin/incidencias') },
  { label: 'Ver Usuarios', icon: 'pi pi-users', shortcut: 'U', handler: () => navigate('/admin/usuarios') },
]

watch(query, () => { activeIndex.value = 0 })

const open = () => {
  isOpen.value = true
  query.value = ''
  activeIndex.value = 0
  nextTick(() => searchInput.value?.focus())
}

const close = () => {
  isOpen.value = false
  query.value = ''
}

const navigate = (route) => {
  close()
  Inertia.visit(route)
}

const goTo = (result) => {
  navigate(result.route)
}

const moveDown = () => {
  const max = filteredResults.value.length - 1
  if (activeIndex.value < max) activeIndex.value++
}

const moveUp = () => {
  if (activeIndex.value > 0) activeIndex.value--
}

const selectItem = () => {
  if (filteredResults.value[activeIndex.value]) {
    goTo(filteredResults.value[activeIndex.value])
  }
}

const handleKeydown = (e) => {
  if ((e.metaKey || e.ctrlKey) && e.key === 'k') {
    e.preventDefault()
    isOpen.value ? close() : open()
  }
}

onMounted(() => {
  document.addEventListener('keydown', handleKeydown)
})

onBeforeUnmount(() => {
  document.removeEventListener('keydown', handleKeydown)
})
</script>

<style scoped>
/* Fade overlay */
.fade-enter-active, .fade-leave-active { transition: opacity 0.2s ease; }
.fade-enter-from, .fade-leave-to { opacity: 0; }

/* Search modal slide */
.search-modal-enter-active {
  transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
}
.search-modal-leave-active {
  transition: all 0.15s ease-in;
}
.search-modal-enter-from {
  opacity: 0;
  transform: scale(0.95) translateY(-20px);
}
.search-modal-leave-to {
  opacity: 0;
  transform: scale(0.97) translateY(-10px);
}
</style>
