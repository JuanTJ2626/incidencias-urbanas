<template>
  <!-- Overlay Backdrop (Always Visible when open) -->
  <div
    v-if="visible"
    class="fixed inset-0 bg-black/50 z-40 transition-opacity"
    @click="toggle"
  ></div>

  <!-- Sidebar (Always Fixed Overlay) -->
  <aside
    class="bg-white border-r border-gray-100 shadow-2xl z-50 transition-transform duration-300 ease-in-out flex flex-col overflow-hidden whitespace-nowrap h-screen fixed top-0 left-0"
    :class="visible ? 'translate-x-0 w-72' : '-translate-x-full w-72'"
  >
    <!-- Logo Header -->
    <div
      class="h-20 flex items-center px-8 border-b border-gray-50 min-w-[18rem] shrink-0"
    >
      <i class="pi pi-building text-2xl text-green-600 mr-2"></i>
      <span class="text-xl font-bold tracking-tight text-gray-800"
        >Incidencias<span class="text-green-600">App</span></span
      >
      <!-- Close button always accessible -->
      <Button
        icon="pi pi-times"
        text
        rounded
        severity="secondary"
        class="ml-auto"
        @click="toggle"
      />
    </div>

    <!-- Menu Items -->
    <div class="flex-1 py-6 px-4 overflow-y-auto min-w-[18rem]">
      <Menu :model="items" class="!w-full !border-none !bg-transparent" />
    </div>

    <!-- User Footer -->
    <div class="p-4 border-t border-gray-50 bg-gray-50/50 min-w-[18rem] shrink-0">
      <div class="flex items-center gap-3">
        <Avatar
          :label="user.name.charAt(0).toUpperCase()"
          shape="circle"
          class="!bg-white !text-green-700 shadow-sm !font-bold"
        />
        <div class="flex-1 overflow-hidden">
          <h4 class="text-sm font-bold text-gray-800 truncate">{{ user.name }}</h4>
          <p class="text-xs text-gray-500 truncate capitalize">{{ user.rol }}</p>
        </div>
        <Button
          icon="pi pi-sign-out"
          text
          rounded
          severity="secondary"
          @click="logout"
          v-tooltip="'Cerrar Sesión'"
        />
      </div>
    </div>
  </aside>
</template>

<script setup>
import { Link } from "@inertiajs/inertia-vue3";
import { Inertia } from "@inertiajs/inertia";

const props = defineProps({
  visible: Boolean,
  items: {
    type: Array,
    required: true,
  },
  user: {
    type: Object,
    default: () => ({ name: "Usuario", email: "usuario@sistema.com", rol: "Invitado" }),
  },
});

const emit = defineEmits(["update:visible", "logout"]);

const toggle = () => {
  emit("update:visible", !props.visible);
};

const logout = () => {
  Inertia.post("/logout");
};
</script>

<style scoped>
/* Override de PrimeVueMenu para que se vea limpio dentro del Sidebar */
:deep(.p-menu) {
  background: transparent !important;
  border: none !important;
  padding: 0 !important;
  width: 100% !important;
}
:deep(.p-menu .p-menuitem-link) {
  padding: 0.75rem 1rem !important;
  border-radius: 0.75rem !important;
  margin-bottom: 0.25rem !important;
  color: #4b5563 !important; /* gray-600 */
  font-weight: 500 !important;
}
:deep(.p-menu .p-menuitem-link:hover) {
  background-color: #f0fdf4 !important; /* green-50 */
  color: #15803d !important; /* green-700 */
}
:deep(.p-menu .p-menuitem-link .p-menuitem-icon) {
  color: #9ca3af !important; /* gray-400 */
}
:deep(.p-menu .p-menuitem-link:hover .p-menuitem-icon) {
  color: #16a34a !important; /* green-600 */
}
:deep(.p-submenu-header) {
  background: transparent !important;
  font-size: 0.75rem !important;
  font-weight: 800 !important;
  color: #9ca3af !important;
  letter-spacing: 0.05em !important;
  padding: 1rem 1rem 0.5rem 1rem !important;
}
</style>
