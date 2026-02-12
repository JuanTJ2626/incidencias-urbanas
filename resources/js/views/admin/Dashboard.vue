<script setup>
import { useForm } from '@inertiajs/inertia-vue3'
import { ref, computed } from 'vue'
import { Inertia } from '@inertiajs/inertia'
import { useToast } from 'primevue/usetoast'
import AppSidebar from '../../Components/AppSidebar.vue'

const props = defineProps({
  users: Array,
  roles: Array,
  errors: Object
})

const toast = useToast()

// ---- State ----
const showCreateDialog = ref(false)
const showEditDialog = ref(false)
const searchQuery = ref('')
const selectedUser = ref(null)
const sidebarVisible = ref(false)

// ---- Menu Data (passed to AppSidebar) ----
const menuItems = ref([
 {
    label: 'GENERAL',
    items: [
        { label: 'Dashboard', icon: 'pi pi-home', command: () => Inertia.visit('/admin/dashboard'), class: 'text-green-600' },
        { label: 'Usuarios', icon: 'pi pi-users', command: () => Inertia.visit('/admin/dashboard') }
    ]
 },
 {
    label: 'GESTIÓN',
    items: [
        { label: 'Reportes', icon: 'pi pi-chart-bar', command: () => {} },
        { label: 'Configuración', icon: 'pi pi-cog', command: () => {} }
    ]
 }
])

// ---- Forms ----
const createForm = useForm({ name: '', email: '', password: '', rol: '' })
const editForm = useForm({ name: '', email: '', password: '', rol: '' })

// ---- Computed ----
const filteredUsers = computed(() => {
  if (!searchQuery.value) return props.users
  const q = searchQuery.value.toLowerCase()
  return props.users.filter(u =>
    u.name.toLowerCase().includes(q) ||
    u.email.toLowerCase().includes(q) ||
    u.rol.toLowerCase().includes(q)
  )
})

const totalUsers = computed(() => props.users.length)
const adminCount = computed(() => props.users.filter(u => u.rol.toLowerCase().includes('admin')).length)

// ---- Methods ----
const openCreateDialog = () => {
  createForm.reset(); createForm.clearErrors(); showCreateDialog.value = true
}

const submitCreate = () => {
  createForm.post('/admin/users', {
    onSuccess: () => {
      showCreateDialog.value = false; createForm.reset()
      toast.add({ severity: 'success', summary: 'Usuario Creado', life: 3000 })
    }
  })
}

const openEditDialog = (user) => {
  selectedUser.value = user
  editForm.name = user.name
  editForm.email = user.email
  editForm.rol = user.rol
  editForm.password = ''
  showEditDialog.value = true
}

const submitEdit = () => {
  editForm.put(`/admin/users/${selectedUser.value.id}`, {
    onSuccess: () => {
      showEditDialog.value = false
      toast.add({ severity: 'success', summary: 'Usuario Actualizado', life: 3000 })
    }
  })
}

const deleteUser = (user) => {
  if (confirm('¿Eliminar usuario?')) {
    Inertia.delete(`/admin/users/${user.id}`, {
      onSuccess: () => toast.add({ severity: 'info', summary: 'Usuario Eliminado', life: 3000 })
    })
  }
}

const getRolSeverity = (rol) => {
  const r = rol.toLowerCase()
  if (r.includes('admin')) return 'danger'
  if (r.includes('super')) return 'warning'
  if (r.includes('empleado')) return 'success'
  return 'info'
}
</script>

<template>
  <div class="flex h-screen bg-gray-50 font-sans text-gray-700 overflow-hidden">
    <Toast position="top-right" />

    <!-- 
      Reusable Push Sidebar Component 
      It sits here as a flex item, so it pushes the main content naturally.
    -->
    <!-- ============================================== 
         MAIN CONTENT (Static, not pushed)
         ============================================== -->
    <div class="flex-1 flex flex-col h-screen overflow-hidden relative">
      
      <!-- Top Header -->
      <header class="h-20 bg-white/80 backdrop-blur-md border-b border-gray-100 flex items-center justify-between px-8 z-10 shrink-0">
        <div class="flex items-center gap-4">
          <!-- Toggle Button for Overlay Sidebar -->
          <Button icon="pi pi-bars" text rounded severity="secondary" @click="sidebarVisible = !sidebarVisible" class="!w-10 !h-10 transition-transform active:scale-95" />
          
          <div>
            <h1 class="text-2xl font-bold text-gray-800">Dashboard General</h1>
            <p class="text-xs text-gray-400 hidden sm:block">Bienvenido al panel de control</p>
          </div>
        </div>
        <div class="flex items-center gap-3">
           <span class="p-input-icon-left hidden sm:block">
              <i class="pi pi-search text-gray-400" />
              <InputText placeholder="Buscar..." class="!bg-gray-50 !border-gray-200 !rounded-full !py-2 !pl-10 !text-sm focus:!bg-white focus:!border-green-500 !w-64" />
           </span>
           <Button icon="pi pi-bell" text rounded severity="secondary" v-badge.danger="'3'" v-tooltip.bottom="'Notificaciones'" />
        </div>
      </header>

      <!-- Scrollable Content -->
      <main class="flex-1 overflow-y-auto p-4 sm:p-8">
        
        <!-- Stats Cards Layout -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex items-center gap-5 hover:-translate-y-1 transition-transform duration-300 group cursor-pointer">
                <div class="w-14 h-14 rounded-2xl bg-blue-50 text-blue-600 group-hover:bg-blue-600 group-hover:text-white transition-colors flex items-center justify-center text-2xl shadow-sm">
                    <i class="pi pi-users"></i>
                </div>
                <div>
                    <span class="block text-gray-400 text-xs font-bold uppercase tracking-wider">Usuarios</span>
                    <span class="block text-3xl font-bold text-gray-800">{{ totalUsers }}</span>
                </div>
            </div>

            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex items-center gap-5 hover:-translate-y-1 transition-transform duration-300 group cursor-pointer">
                <div class="w-14 h-14 rounded-2xl bg-red-50 text-red-600 group-hover:bg-red-600 group-hover:text-white transition-colors flex items-center justify-center text-2xl shadow-sm">
                    <i class="pi pi-shield"></i>
                </div>
                <div>
                    <span class="block text-gray-400 text-xs font-bold uppercase tracking-wider">Admins</span>
                    <span class="block text-3xl font-bold text-gray-800">{{ adminCount }}</span>
                </div>
            </div>

            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex items-center gap-5 hover:-translate-y-1 transition-transform duration-300 group cursor-pointer">
                <div class="w-14 h-14 rounded-2xl bg-amber-50 text-amber-600 group-hover:bg-amber-600 group-hover:text-white transition-colors flex items-center justify-center text-2xl shadow-sm">
                    <i class="pi pi-tag"></i>
                </div>
                <div>
                    <span class="block text-gray-400 text-xs font-bold uppercase tracking-wider">Roles</span>
                    <span class="block text-3xl font-bold text-gray-800">{{ roles.length }}</span>
                </div>
            </div>
             
             <!-- Fake Stat -->
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex items-center gap-5 hover:-translate-y-1 transition-transform duration-300 group cursor-pointer">
                <div class="w-14 h-14 rounded-2xl bg-green-50 text-green-600 group-hover:bg-green-600 group-hover:text-white transition-colors flex items-center justify-center text-2xl shadow-sm">
                    <i class="pi pi-check-circle"></i>
                </div>
                <div>
                    <span class="block text-gray-400 text-xs font-bold uppercase tracking-wider">Activos</span>
                    <span class="block text-3xl font-bold text-gray-800">100%</span>
                </div>
            </div>
        </div>

        <!-- USERS TABLE CARD -->
        <div class="bg-white border border-gray-100 rounded-3xl shadow-sm overflow-hidden flex flex-col h-auto animate-fade-in-up">
            <div class="px-8 py-6 border-b border-gray-100 flex flex-col sm:flex-row justify-between items-center gap-4">
                <div>
                   <h3 class="text-xl font-bold text-gray-800">Directorio de Usuarios</h3>
                   <p class="text-sm text-gray-400 mt-1">Gestión completa de accesos y roles</p>
                </div>
                <div class="flex gap-3 w-full sm:w-auto">
                    <Button label="Nuevo Usuario" icon="pi pi-plus" class="!bg-green-600 !border-green-600 hover:!bg-green-700 !rounded-xl !px-6 !font-bold !shadow-lg hover:!shadow-xl transition-all" @click="openCreateDialog" />
                </div>
            </div>
            
            <DataTable 
              :value="filteredUsers" 
              :paginator="true" 
              :rows="5" 
              class="p-datatable-lg" 
              responsiveLayout="scroll"
              paginatorTemplate="FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink RowsPerPageDropdown"
              :rowsPerPageOptions="[5, 10, 20]"
            >
                <Column field="name" header="NOMBRE DEL USUARIO" sortable>
                    <template #body="{ data }">
                        <div class="flex items-center gap-4 py-2">
                            <Avatar :label="data.name.charAt(0)" shape="circle" class="!bg-gray-100 !text-gray-600 !font-bold !border !border-gray-200" size="large" />
                            <div>
                                <span class="block font-bold text-gray-800 text-base">{{ data.name }}</span>
                                <span class="block text-sm text-gray-400">{{ data.email }}</span>
                            </div>
                        </div>
                    </template>
                </Column>
                
                <Column field="rol" header="ROL ASIGNADO" sortable>
                    <template #body="{ data }">
                        <Tag :value="data.rol" :severity="getRolSeverity(data.rol)" class="!px-3 !py-1 !text-xs !uppercase !tracking-widest !font-bold" rounded></Tag>
                    </template>
                </Column>
                
                <Column field="created_at" header="FECHA REGISTRO" sortable>
                    <template #body="{ data }">
                        <span class="text-sm font-medium text-gray-500">{{ new Date(data.created_at).toLocaleDateString() }}</span>
                    </template>
                </Column>

                <Column header="ACCIONES" bodyClass="text-right">
                    <template #body="{ data }">
                        <div class="flex justify-end gap-2">
                            <Button icon="pi pi-pencil" text rounded severity="info" @click="openEditDialog(data)" v-tooltip.top="'Editar'" class="hover:bg-blue-50" />
                            <Button icon="pi pi-trash" text rounded severity="danger" @click="deleteUser(data)" v-tooltip.top="'Eliminar'" class="hover:bg-red-50" />
                        </div>
                    </template>
                </Column>
            </DataTable>
        </div>
      </main>
    </div>

    <!-- 
      Reusable Sidebar Component (Overlay) 
      Placed outside the flex container flow to ensure absolute overlay behavior
    -->
    <AppSidebar 
      v-model:visible="sidebarVisible" 
      :items="menuItems" 
      :user="{ name: 'Administrador', rol: 'Super Admin' }"
      @logout="Inertia.post('/logout')" 
    />

    <!-- DIALOGS (Usando FloatLabels y validaciones) -->
    <Dialog v-model:visible="showCreateDialog" modal header="Nuevo Usuario" class="!w-full !max-w-md !rounded-3xl" :draggable="false">
        <form @submit.prevent="submitCreate" class="flex flex-col gap-6 pt-4">
            <FloatLabel>
                <InputText id="n-name" v-model="createForm.name" class="w-full !rounded-xl !py-3 !border-gray-300" />
                <label for="n-name">Nombre Completo</label>
            </FloatLabel>
            
            <FloatLabel>
                <InputText id="n-email" v-model="createForm.email" class="w-full !rounded-xl !py-3 !border-gray-300" />
                <label for="n-email">Correo Electrónico</label>
            </FloatLabel>

            <FloatLabel>
                <Password id="n-pass" v-model="createForm.password" toggleMask :feedback="false" class="w-full" inputClass="w-full !rounded-xl !py-3 !border-gray-300" />
                <label for="n-pass">Contraseña</label>
            </FloatLabel>
            
            <div class="flex flex-col gap-2">
                 <label class="text-xs font-bold text-gray-400 uppercase ml-2">Asignar Rol</label>
                 <Dropdown v-model="createForm.rol" :options="roles" optionLabel="nombre" optionValue="nombre" placeholder="Seleccione..." class="w-full !rounded-xl !border-gray-300" />
            </div>

            <div class="flex justify-end gap-3 pt-4 border-t border-gray-100 mt-2">
                <Button label="Cancelar" text severity="secondary" @click="showCreateDialog = false" class="!px-4 !py-2.5 !rounded-xl" />
                <Button label="Crear Usuario" icon="pi pi-check" class="!bg-green-600 !border-green-600 !rounded-xl !px-6 !py-2.5 !font-bold !shadow-md hover:!shadow-lg hover:!-translate-y-0.5 transition-all" type="submit" :loading="createForm.processing" />
            </div>
        </form>
    </Dialog>

    <Dialog v-model:visible="showEditDialog" modal header="Editar Usuario" class="!w-full !max-w-md !rounded-3xl" :draggable="false">
        <form @submit.prevent="submitEdit" class="flex flex-col gap-6 pt-4">
             <FloatLabel>
                <InputText id="e-name" v-model="editForm.name" class="w-full !rounded-xl !py-3 !border-gray-300" />
                <label for="e-name">Nombre Completo</label>
            </FloatLabel>
            
            <FloatLabel>
                <InputText id="e-email" v-model="editForm.email" class="w-full !rounded-xl !py-3 !border-gray-300" />
                <label for="e-email">Correo Electrónico</label>
            </FloatLabel>

            <FloatLabel>
                <InputText id="e-pass" v-model="editForm.password" class="w-full !rounded-xl !py-3 !border-gray-300" />
                <label for="e-pass">Nueva Contraseña (Opcional)</label>
            </FloatLabel>

             <div class="flex flex-col gap-2">
                 <label class="text-xs font-bold text-gray-400 uppercase ml-2">Rol</label>
                 <Dropdown v-model="editForm.rol" :options="roles" optionLabel="nombre" optionValue="nombre" class="w-full !rounded-xl !border-gray-300" />
            </div>

            <div class="flex justify-end gap-3 pt-4 border-t border-gray-100 mt-2">
                <Button label="Cancelar" text severity="secondary" @click="showEditDialog = false" class="!px-4 !py-2.5 !rounded-xl" />
                <Button label="Guardar Cambios" icon="pi pi-save" class="!bg-blue-600 !border-blue-600 !rounded-xl !px-6 !py-2.5 !font-bold !shadow-md hover:!shadow-lg hover:!-translate-y-0.5 transition-all" type="submit" :loading="editForm.processing" />
            </div>
        </form>
    </Dialog>

  </div>
</template>

<style scoped>
/* Overrides for specific styling if needed, but Tailwind handles most */
:deep(.p-datatable .p-datatable-header) {
  padding: 0;
  border: none;
}
:deep(.p-dialog-content) {
  overflow-y: visible !important; /* Allow dropdowns to overflow */
}
</style>
