<template>
  <div class="flex h-screen bg-[#F5F5F7] overflow-hidden">
    <!-- Sidebar Global -->
    <AppSidebar v-model:visible="sidebarVisible" />

    <!-- Content Area with Dynamic Padding to avoid overlap -->
    <div 
      class="flex-1 flex flex-col min-w-0 transition-all duration-700 ease-[cubic-bezier(0.16,1,0.3,1)]"
      :class="[sidebarVisible ? 'lg:pl-72' : 'lg:pl-20']"
    >
      <!-- Navbar Global -->
      <AppNavbar @toggleSidebar="sidebarVisible = !sidebarVisible" />

      <!-- Main Content Container -->
      <main class="flex-1 overflow-y-auto overflow-x-hidden p-4 md:p-8">
        <AppToast />
        <ConfirmDialog />
        <slot />
      </main>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, onBeforeUnmount } from 'vue'
import AppSidebar from '@/Components/AppSidebar.vue'
import AppNavbar from '@/Components/AppNavbar.vue'
import AppToast from '@/Components/AppToast.vue'

const sidebarVisible = ref(false)

// Listener para eventos globales si se necesitara disparar el toggle desde fuera
const onToggleEvent = () => {
  sidebarVisible.value = !sidebarVisible.value
}

onMounted(() => {
  window.addEventListener('toggle-sidebar', onToggleEvent)
})

onBeforeUnmount(() => {
  window.removeEventListener('toggle-sidebar', onToggleEvent)
})
</script>

<style scoped>
/* Evitamos scroll horizontal innecesario durante transiciones de ancho */
.overflow-x-hidden {
  overflow-x: hidden;
}
</style>
