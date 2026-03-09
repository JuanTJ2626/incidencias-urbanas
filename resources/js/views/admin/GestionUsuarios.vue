<template>
  <div class="animate-fade-in py-6 pl-4 pr-6">
    <PageHeader
      title="Gestión de Usuarios"
      subtitle="Gestiona los usuarios del sistema, asigna roles y administra permisos."
    >
      <template #actions>
        <!-- ejemplo: botón para crear o filtros -->
        <div class="flex items-center gap-3">
          <slot name="header-actions" />
        </div>
      </template>
    </PageHeader>

    <!-- Stats Grid -->
    <StatsGrid :stats="statCards" />

    <!-- Directory Section -->
    <div
      class="bg-white dark:bg-app-card border border-app-border rounded-[2.5rem] shadow-[0_20px_60px_rgba(0,0,0,0.03)] overflow-hidden flex flex-col h-auto"
    >
      <div
        class="px-10 py-10 border-b border-app-secondary dark:border-app-border flex flex-col sm:flex-row justify-between items-center gap-6"
      >
        <div>
          <h3 class="text-2xl font-black text-[#1D1D1F] dark:text-white tracking-tight">
            Directorio de Usuarios
          </h3>
          <p class="text-sm font-bold text-[#86868B] dark:text-[#A1A1A6] mt-1.5">
            Gestiona los accesos y privilegios del sistema.
          </p>
        </div>
        <div class="flex items-center gap-4 w-full sm:w-auto">
          <div class="relative w-full sm:w-72 group">
            <i
              class="pi pi-search absolute left-4 top-1/2 -translate-y-1/2 text-[#86868B] dark:text-[#A1A1A6] group-focus-within:text-[#1D1D1F] dark:group-focus-within:text-white transition-colors"
            ></i>
            <InputText
              v-model="searchQuery"
              placeholder="Buscar..."
              class="!w-full !rounded-2xl !border-transparent !bg-app-secondary dark:!bg-white/5 !pl-12 !py-3.5 !font-bold focus:!bg-white dark:focus:!bg-white/10 focus:!border-app-border dark:focus:!border-white/10 dark:!text-white transition-all"
            />
          </div>
          <Button
            label="Nuevo Usuario"
            icon="pi pi-plus"
            class="!bg-[#1D1D1F] dark:!bg-white !border-[#1D1D1F] dark:!border-white dark:!text-black !rounded-2xl !px-8 !py-3.5 !font-black !shadow-xl hover:!scale-105 active:!scale-95 transition-all"
            @click="openCreateDialog"
          />
        </div>
      </div>

      <div class="px-6 pb-6">
        <GenericPremiumTable
          :columns="[
            { key: 'id', label: 'ID', width: '70px' },
            { key: 'name', label: 'Nombre' },
            { key: 'email', label: 'Email' },
            { key: 'rol', label: 'Rol' },
            { key: 'activo', label: 'Estado', width: '120px' },
            { key: 'actions', label: 'Acciones', width: '150px' }
          ]"
          :data="filteredUsers"
          selectable
          v-model:selection="selectedItems"
        >
          <!-- Acciones en lote -->
          <template #bulk-actions="{ selection }">
            <button 
              @click="bulkDelete(selection)"
              class="flex items-center gap-2 px-6 py-2 bg-rose-600 hover:bg-rose-700 text-white rounded-xl font-black transition-all active:scale-95 shadow-lg shadow-rose-500/20"
            >
              <i class="pi pi-trash"></i>
              Eliminar Seleccionados
            </button>
          </template>

          <!-- Slot ID -->
          <template #cell-id="{ value }">
            <span class="text-xs font-black text-[#86868B] dark:text-[#A1A1A6]">#{{ value }}</span>
          </template>

          <!-- Slot Nombre (con avatar ficticio) -->
          <template #cell-name="{ value }">
             <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-full bg-app-secondary flex items-center justify-center text-[10px] font-black text-brand-red">
                   {{ value.charAt(0) }}
                </div>
                <span class="font-bold text-[#1D1D1F] dark:text-white">{{ value }}</span>
             </div>
          </template>

          <!-- Slot Rol -->
          <template #cell-rol="{ value }">
             <span class="text-xs font-bold px-2 py-1 rounded bg-app-secondary dark:bg-app-secondary text-[#1D1D1F] dark:text-white uppercase tracking-wider">
                {{ value }}
             </span>
          </template>

          <!-- Slot Estado -->
          <template #cell-activo="{ row }">
             <div class="flex items-center gap-2">
                <div :class="['w-2 h-2 rounded-full animate-pulse', row.activo ? 'bg-emerald-500 shadow-[0_0_8px_#10b981]' : 'bg-rose-500 shadow-[0_0_8px_#f43f5e]']"></div>
                <span :class="['text-[10px] font-black uppercase tracking-widest', row.activo ? 'text-emerald-600 dark:text-emerald-400' : 'text-rose-600 dark:text-rose-400']">
                   {{ row.activo ? 'Activo' : 'Inactivo' }}
                </span>
             </div>
          </template>

          <!-- Slot Acciones -->
          <template #cell-actions="{ row }">
            <div class="flex items-center gap-2">
              <button @click="openEditDialog(row)" class="w-8 h-8 rounded-lg bg-app-secondary hover:bg-app-border dark:hover:bg-white/10 transition-colors flex items-center justify-center text-[#1D1D1F] dark:text-white">
                 <i class="pi pi-pencil text-xs"></i>
              </button>
              <button @click="toggleActivo(row)" :class="['w-8 h-8 rounded-lg transition-colors flex items-center justify-center', row.activo ? 'bg-rose-50 text-rose-600 hover:bg-rose-100 dark:bg-rose-500/10 dark:text-rose-400 dark:hover:bg-rose-500/20' : 'bg-emerald-50 text-emerald-600 hover:bg-emerald-100 dark:bg-emerald-500/10 dark:text-emerald-400 dark:hover:bg-emerald-500/20']">
                 <i :class="['pi text-xs', row.activo ? 'pi-ban' : 'pi-check-circle']"></i>
              </button>
              <button @click="deleteUser(row)" class="w-8 h-8 rounded-lg bg-app-secondary hover:bg-rose-500 hover:text-white dark:hover:bg-rose-600 transition-colors flex items-center justify-center text-[#1D1D1F] dark:text-white">
                 <i class="pi pi-trash text-xs"></i>
              </button>
            </div>
          </template>
        </GenericPremiumTable>
      </div>
    </div>

    <!-- Modals (Global components handle Toast and Confirmation) -->
    <UserFormModal
      v-model:visible="showCreateDialog"
      mode="create"
      :roles="roles"
      @saved="onModalSaved"
    />
    <UserFormModal
      v-model:visible="showEditDialog"
      mode="edit"
      :user="selectedUser"
      :roles="roles"
      @saved="onModalSaved"
    />
  </div>
</template>

<script setup>
import { ref, computed } from "vue";
import { Inertia } from "@inertiajs/inertia";
import { useToast } from "primevue/usetoast";
import { useConfirm } from "primevue/useconfirm";
import GenericPremiumTable from "../../Components/GenericPremiumTable.vue";
import UserFormModal from "../../Components/UserFormModal.vue";
import StatsGrid     from "../../Components/StatsGrid.vue";

const props = defineProps({
  users: Array,
  roles: Array,
  errors: Object,
});

const toast = useToast();
const confirm = useConfirm();

const showCreateDialog = ref(false);
const showEditDialog = ref(false);
const searchQuery = ref("");
const selectedUser = ref(null);
const deletingId = ref(null);
const selectedItems = ref([]); // Para selección múltiple

/**
 * Eliminar múltiples usuarios
 */
const bulkDelete = (items) => {
  confirm.require({
    message: `¿Estás seguro de eliminar ${items.length} usuarios seleccionados? Esta acción es irreversible.`,
    header: "Eliminación Masiva",
    icon: "pi pi-exclamation-triangle",
    acceptLabel: "Sí, eliminar todos",
    rejectLabel: "Cancelar",
    acceptClass: "p-button-danger !rounded-xl !font-black",
    rejectClass: "p-button-secondary p-button-text !rounded-xl",
    accept: () => {
      const ids = items.map(i => i.id);
      Inertia.post('/admin/users/bulk-delete', { ids }, {
        onSuccess: () => {
          toast.add({ severity: 'success', summary: 'Éxito', detail: 'Usuarios eliminados correctamente', life: 3000 });
          selectedItems.value = []; // Limpiar selección
        },
        onError: () => toast.add({ severity: 'error', summary: 'Error', detail: 'No se pudieron eliminar algunos usuarios', life: 3000 })
      });
    }
  });
};

const statCards = computed(() => [
  {
    label: "Usuarios",
    value: props.users.length,
    icon: "pi pi-users",
    bgClass: "bg-[#F5F5F7]",
    textClass: "text-[#1D1D1F]",
    hoverColor:
      "hover:bg-[#007AFF] hover:shadow-[0_20px_50px_rgba(0,122,255,0.3)] hover:border-[#007AFF]",
  },
  {
    label: "Administradores",
    value: props.users.filter((u) => u.rol.toLowerCase().includes("admin")).length,
    icon: "pi pi-shield",
    bgClass: "bg-[#F5F5F7]",
    textClass: "text-[#607C88]",
    hoverColor:
      "hover:bg-[#607C88] hover:shadow-[0_20px_50px_rgba(96,124,136,0.3)] hover:border-[#607C88]",
  },
  {
    label: "Roles",
    value: props.roles.length,
    icon: "pi pi-tag",
    bgClass: "bg-[#F5F5F7]",
    textClass: "text-[#1D1D1F]",
    hoverColor:
      "hover:bg-[#FF9500] hover:shadow-[0_20px_50px_rgba(255,149,0,0.3)] hover:border-[#FF9500]",
  },
  {
    label: "Sistema",
    value: "Activo",
    icon: "pi pi-check-circle",
    bgClass: "bg-green-50",
    textClass: "text-green-600",
    hoverColor:
      "hover:bg-[#34C759] hover:shadow-[0_20px_50px_rgba(52,199,89,0.3)] hover:border-[#34C759]",
  },
]);

// create/edit form logic moved into UserFormModal

const filteredUsers = computed(() => {
  if (!searchQuery.value) return props.users;
  const q = searchQuery.value.toLowerCase();
  return props.users.filter(
    (u) =>
      u.name.toLowerCase().includes(q) ||
      u.email.toLowerCase().includes(q) ||
      u.rol.toLowerCase().includes(q)
  );
});

const openCreateDialog = () => {
  selectedUser.value = null;
  showCreateDialog.value = true;
};

// Create handled by UserFormModal

const openEditDialog = (user) => {
  selectedUser.value = user;
  showEditDialog.value = true;
};

// Edit handled by UserFormModal

const onModalSaved = () => {
  // reload page data to reflect changes
  Inertia.reload({ only: ['users'] })
}

const toggleActivo = (user) => {
  const accion = user.activo ? 'desactivar' : 'activar'
  confirm.require({
    message: `¿${accion.charAt(0).toUpperCase() + accion.slice(1)} la cuenta de ${user.name}?`,
    header: `${accion.charAt(0).toUpperCase() + accion.slice(1)} usuario`,
    icon: user.activo ? 'pi pi-ban' : 'pi pi-check-circle',
    acceptLabel: accion.charAt(0).toUpperCase() + accion.slice(1),
    rejectLabel: 'Cancelar',
    acceptClass: user.activo ? 'p-button-danger !rounded-xl !font-bold' : 'p-button-success !rounded-xl !font-bold',
    rejectClass: 'p-button-secondary p-button-text !rounded-xl !font-bold',
    accept: () => {
      Inertia.patch(`/admin/users/${user.id}/toggle-activo`, {}, {
        onSuccess: () => toast.add({
          severity: user.activo ? 'warn' : 'success',
          summary: user.activo ? 'Desactivado' : 'Activado',
          detail: `La cuenta de ${user.name} fue ${user.activo ? 'desactivada' : 'activada'}.`,
          life: 3500,
        }),
        onError: () => toast.add({ severity: 'error', summary: 'Error', detail: 'No se pudo cambiar el estado.', life: 3500 }),
      })
    },
  })
}

const deleteUser = (user) => {
  confirm.require({
    message: `¿Estás seguro de eliminar a ${user.name}? Esta acción no se puede deshacer.`,
    header: "Confirmar Eliminación",
    icon: "pi pi-exclamation-triangle",
    acceptLabel: "Eliminar",
    rejectLabel: "Cancelar",
    acceptClass: "p-button-danger !rounded-xl !font-bold",
    rejectClass: "p-button-secondary p-button-text !rounded-xl !font-bold",
    accept: () => {
      deletingId.value = user.id;
      Inertia.delete(`/admin/users/${user.id}`, {
        onSuccess: () =>
          toast.add({
            severity: "info",
            summary: "Eliminado",
            detail: "El usuario ya no existe.",
            life: 3500,
          }),
        onError: () =>
          toast.add({
            severity: "error",
            summary: "Error",
            detail: "No se pudo eliminar el usuario.",
            life: 3500,
          }),
        onFinish: () => (deletingId.value = null),
      });
    },
  });
};
</script>

<style scoped>
@keyframes fadeIn {
  from {
    opacity: 0;
    transform: translateY(20px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}
.animate-fade-in {
  animation: fadeIn 0.8s cubic-bezier(0.16, 1, 0.3, 1) forwards;
}

/* Ripple effect color customization */
:deep(.p-ink) {
  background: rgba(255, 255, 255, 0.5);
}
</style>
