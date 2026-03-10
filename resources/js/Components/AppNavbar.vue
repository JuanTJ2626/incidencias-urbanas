<template>
  <header 
    class="backdrop-blur-2xl border-b border-brand-red/20 sticky top-0 z-30 transition-all duration-300 shadow-sm rounded-b-2xl overflow-hidden"
    :style="{
      backgroundColor: 'var(--navbar-bg)',
      backgroundImage: 'var(--navbar-gradient)'
    }"
  >
    <div class="w-full px-6 py-3.5 flex items-center justify-between">
      <!-- Left: menu + brand -->
      <div class="flex items-center gap-4">
        <Button
          v-if="isMobile"
          class="lg:hidden !p-0 !w-10 !h-10 text-gray-500 hover:text-brand-red hover:bg-brand-red/10 transition-all duration-300 rounded-xl"
          icon="pi pi-bars"
          text
          rounded
          @click="emitToggle"
          aria-label="Toggle sidebar"
        />
        <!-- <div class="flex flex-col leading-none">
          <span class="text-lg font-bold text-gray-900 dark:text-white tracking-tight transition-colors duration-300">
            Gestión <span class="text-[#8A1538]">Ciudadana</span>
          </span>
          <span class="text-[10px] font-semibold text-[#8A1538]/70 dark:text-[#D1A7B0]/70 uppercase tracking-[0.2em] mt-1">Panel de Control</span>
        </div> -->
      </div>

      <!-- Right: actions + user -->
      <div class="flex items-center gap-3">
        <!-- Search Trigger (Ctrl+K) -->
        <button
          @click="openSearch"
          class="hidden md:flex items-center gap-3 px-4 py-2 rounded-xl bg-app-secondary/50 dark:bg-white/5 border border-app-border hover:border-brand-red/30 transition-all duration-200 group"
        >
          <i class="pi pi-search text-sm text-[#86868B] group-hover:text-brand-red transition-colors"></i>
          <span class="text-xs text-[#86868B] font-medium">Buscar...</span>
          <kbd class="ml-2 text-[10px] font-bold text-[#86868B] bg-white dark:bg-white/10 px-1.5 py-0.5 rounded border border-app-border">⌘K</kbd>
        </button>

        <!-- Dark Mode Toggle -->
        <Button
          :icon="isDark ? 'pi pi-sun' : 'pi pi-moon'"
          text
          rounded
          class="!p-0 !w-10 !h-10 text-gray-400 dark:text-gray-400 hover:text-brand-red hover:bg-brand-red/10 transition-all duration-300 rounded-xl"
          :aria-label="isDark ? 'Modo claro' : 'Modo oscuro'"
          @click="toggleDark"
        />

        <!-- Divider -->
        <div class="h-8 w-px bg-gradient-to-b from-transparent via-gray-300 dark:via-gray-600 to-transparent mx-1"></div>

        <!-- User Profile -->
        <div class="flex items-center gap-3 pl-1">
          <div class="relative group">
            <div class="absolute inset-0 bg-gradient-to-br from-brand-red to-rose-400 rounded-full blur-md opacity-0 group-hover:opacity-40 transition-opacity duration-300"></div>
            <Avatar 
              :label="initial" 
              shape="circle" 
              class="!bg-gradient-to-br !from-brand-red !to-rose-600 !text-white !font-bold shadow-lg shadow-brand-red/20 border-2 border-white dark:border-gray-800 !w-10 !h-10 relative z-10 transition-transform duration-300 group-hover:scale-105" 
            />
            <!-- Online Indicator -->
            <div class="absolute -bottom-0.5 -right-0.5 w-3.5 h-3.5 bg-green-500 border-2 border-white dark:border-gray-800 rounded-full z-20">
              <div class="absolute inset-0 bg-green-500 rounded-full animate-ping opacity-60"></div>
            </div>
          </div>
          
          <div class="hidden sm:flex flex-col leading-none">
            <span class="text-sm font-bold text-gray-900 dark:text-white uppercase tracking-wide transition-colors duration-300">{{ userNameUpper }}</span>
            <span class="text-[10px] text-green-600 dark:text-green-400 font-semibold flex items-center gap-1.5 mt-0.5">
              <span class="w-1.5 h-1.5 bg-green-500 rounded-full"></span>
              En línea
            </span>
          </div>
        </div>
      </div>
    </div>
  </header>
</template>

<script setup>
import { computed, ref, onMounted, onBeforeUnmount } from 'vue'
import { usePage } from '@inertiajs/inertia-vue3'
import Button from 'primevue/button'
import Avatar from 'primevue/avatar'
import { useDarkMode } from '@/composables/useDarkMode'

const emit = defineEmits(['toggleSidebar'])

const { isDark, toggle: toggleDark } = useDarkMode()

// Control para render condicional del botón hamburguesa (solo en pantallas < lg)
const isMobile = ref(false)
const LG_BREAKPOINT = 1024 // coincide con Tailwind `lg`

const handleResize = () => {
  try { isMobile.value = window.innerWidth < LG_BREAKPOINT } catch (e) { isMobile.value = false }
}

onMounted(() => {
  handleResize()
  window.addEventListener('resize', handleResize)
})

onBeforeUnmount(() => window.removeEventListener('resize', handleResize))

const page = usePage()
const user = computed(() => page.props.value.auth?.user ?? { name: 'Usuario' })
const userName = computed(() => user.value.name ?? 'Usuario')
const userNameUpper = computed(() => (userName.value || 'Usuario').toUpperCase())
const initial = computed(() => (user.value.name ? user.value.name.charAt(0).toUpperCase() : 'U'))

const emitToggle = () => emit('toggleSidebar')

const openSearch = () => {
  document.dispatchEvent(new KeyboardEvent('keydown', { key: 'k', ctrlKey: true, bubbles: true }))
}
</script>

<style scoped>
.pi { vertical-align: middle }

/* Animación suave para el header al hacer scroll */
header {
  transition: box-shadow 0.3s ease, background-color 0.3s ease;
}

header.scrolled {
  box-shadow: 0 4px 20px rgba(138, 21, 56, 0.08);
}
</style>