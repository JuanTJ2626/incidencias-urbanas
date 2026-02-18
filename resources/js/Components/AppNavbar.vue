<template>
  <header class="bg-white/80 backdrop-blur-md border-b border-gray-100 sticky top-0 z-30">
    <div class="w-full px-6 py-3 flex items-center justify-between">
      <!-- Left: menu + brand -->
        <div class="flex items-center gap-4"> 
          <Button
            v-if="isMobile"
            class="lg:hidden !p-0 !w-10 !h-10 text-gray-400 hover:text-[#607C88] hover:bg-[#607C88]/5 transition-all"
            icon="pi pi-bars"
            text
            rounded
            @click="emitToggle"
            aria-label="Toggle sidebar"
          />
          <div class="flex flex-col leading-none">
            <span class="text-base font-semibold text-gray-900 tracking-tight">Gestión Ciudadana</span>
            <span class="text-[11px] font-medium text-gray-400 uppercase tracking-widest mt-0.5">Panel de Control</span>
          </div>
        </div>

      <!-- Right: actions + user -->
      <div class="flex items-center gap-4">
        <div class="flex items-center gap-3 pl-4 border-l border-gray-100">
          <Avatar 
            :label="initial" 
            shape="circle" 
            class="!bg-white !text-[#607C88] shadow-sm !font-bold border border-gray-100 !w-9 !h-9" 
          />
          <div class="hidden sm:flex flex-col leading-none items-end">
            <span class="text-sm font-semibold text-gray-900 uppercase">{{ userNameUpper }}</span>
            <span class="text-[10px] text-green-600 font-medium flex items-center gap-2">
              <span class="w-2 h-2 bg-green-500 rounded-full inline-block"></span>
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
// InputText and Badge removed (search and notifications removed)

const emit = defineEmits(['toggleSidebar'])

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
</script>

<style scoped>
.pi { vertical-align: middle }
</style>
