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
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8 mb-12">
      <div
        v-for="(card, idx) in statCards"
        :key="idx"
        v-ripple
        class="p-ripple bg-white p-8 rounded-[2.2rem] shadow-[0_10px_40px_rgba(0,0,0,0.02)] border border-[#E8E8ED] flex flex-col gap-6 hover:-translate-y-2 transition-all duration-500 group cursor-pointer overflow-hidden relative"
        :class="[card.hoverColor, card.rippleClass]"
      >
        <div
          class="absolute top-0 right-0 p-4 opacity-5 group-hover:opacity-20 group-hover:scale-150 group-hover:rotate-12 transition-all duration-700"
        >
          <i :class="[card.icon, 'text-6xl text-[#1D1D1F] group-hover:text-white']"></i>
        </div>
        <div
          class="w-14 h-14 rounded-2xl flex items-center justify-center text-2xl shadow-sm transition-all duration-500 group-hover:bg-white/20 group-hover:scale-110 group-hover:shadow-none"
          :class="card.bgClass"
        >
          <i
            :class="[
              card.icon,
              card.textClass,
              'group-hover:text-white transition-colors',
            ]"
          ></i>
        </div>
        <div>
          <span
            class="block text-[#86868B] text-[10px] font-black uppercase tracking-[0.2em] group-hover:text-white/70 transition-colors"
            >{{ card.label }}</span
          >
          <span
            class="block text-4xl font-black text-[#1D1D1F] tracking-tighter mt-1 group-hover:text-white transition-all transform origin-left"
            >{{ card.value }}</span
          >
        </div>
      </div>
    </div>

    <!-- Directory Section -->
    <div
      class="bg-white border border-[#E8E8ED] rounded-[2.5rem] shadow-[0_20px_60px_rgba(0,0,0,0.03)] overflow-hidden flex flex-col h-auto"
    >
      <div
        class="px-10 py-10 border-b border-[#F5F5F7] flex flex-col sm:flex-row justify-between items-center gap-6"
      >
        <div>
          <h3 class="text-2xl font-black text-[#1D1D1F] tracking-tight">
            Directorio de Usuarios
          </h3>
          <p class="text-sm font-bold text-[#86868B] mt-1.5">
            Gestiona los accesos y privilegios del sistema.
          </p>
        </div>
        <div class="flex items-center gap-4 w-full sm:w-auto">
          <div class="relative w-full sm:w-72 group">
            <i
              class="pi pi-search absolute left-4 top-1/2 -translate-y-1/2 text-[#86868B] group-focus-within:text-[#1D1D1F] transition-colors"
            ></i>
            <InputText
              v-model="searchQuery"
              placeholder="Buscar..."
              class="!w-full !rounded-2xl !border-transparent !bg-[#F5F5F7] !pl-12 !py-3.5 !font-bold focus:!bg-white focus:!border-[#E8E8ED] transition-all"
            />
          </div>
          <Button
            label="Nuevo Usuario"
            icon="pi pi-plus"
            class="!bg-[#1D1D1F] !border-[#1D1D1F] !rounded-2xl !px-8 !py-3.5 !font-black !shadow-xl hover:!scale-105 active:!scale-95 transition-all"
            @click="openCreateDialog"
          />
        </div>
      </div>

      <div class="p-2">
        <UsersTable
          :value="filteredUsers"
          :rows="5"
          :rowsPerPageOptions="[5, 10, 20]"
          :deletingId="deletingId"
          @edit="openEditDialog"
          @delete="deleteUser"
        />
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
import UsersTable from "../../Components/UsersTable.vue";
import UserFormModal from "../../Components/UserFormModal.vue";

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
