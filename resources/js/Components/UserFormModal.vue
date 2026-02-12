<template>
  <Dialog 
    v-model:visible="internalVisible" 
    :modal="true" 
    :draggable="false" 
    :header="dialogTitle" 
    class="!w-full !max-w-md !rounded-[2.5rem] !border-none !shadow-2xl overflow-hidden"
  >
    <form @submit.prevent="onSubmit" class="flex flex-col gap-8 pt-8 px-2">
      <FloatLabel>
        <InputText 
          id="f-name" 
          v-model="form.name" 
          class="w-full !rounded-2xl !py-4 !bg-[#F5F5F7] !border-transparent focus:!bg-white focus:!border-[#E8E8ED] !font-bold transition-all" 
        />
        <label for="f-name" class="!font-bold !text-[#86868B]">Nombre Completo</label>
      </FloatLabel>

      <FloatLabel>
        <InputText 
          id="f-email" 
          v-model="form.email" 
          class="w-full !rounded-2xl !py-4 !bg-[#F5F5F7] !border-transparent focus:!bg-white focus:!border-[#E8E8ED] !font-bold transition-all" 
        />
        <label for="f-email" class="!font-bold !text-[#86868B]">Correo Electrónico</label>
      </FloatLabel>

      <FloatLabel>
        <Password 
          id="f-pass" 
          v-model="form.password" 
          toggleMask 
          :feedback="false" 
          class="w-full" 
          inputClass="w-full !rounded-2xl !py-4 !bg-[#F5F5F7] !border-transparent focus:!bg-white focus:!border-[#E8E8ED] !font-bold transition-all" 
        />
        <label for="f-pass" class="!font-bold !text-[#86868B]">Contraseña</label>
      </FloatLabel>

      <div class="flex flex-col gap-3">
        <label class="text-[10px] font-black text-[#86868B] uppercase tracking-[0.2em] ml-2">Seleccionar Rol</label>
        <Dropdown 
          v-model="form.rol" 
          :options="roles" 
          optionLabel="nombre" 
          optionValue="nombre" 
          placeholder="Elegir..." 
          class="w-full !rounded-2xl !border-transparent !bg-[#F5F5F7] !py-1 focus:!bg-white focus:!border-[#E8E8ED] !font-bold transition-all" 
        />
      </div>

      <div class="flex justify-end gap-3 pt-6 border-t border-[#F5F5F7] mt-4">
        <Button 
          label="Cancelar" 
          text 
          class="!text-[#86868B] !font-bold hover:!text-[#1D1D1F] !px-6" 
          @click="close" 
        />
        <Button 
          :label="primaryLabel" 
          :loading="loading"
          class="!bg-[#1D1D1F] !border-[#1D1D1F] !rounded-2xl !px-10 !py-4 !font-black !shadow-xl hover:!scale-105 active:!scale-95 transition-all" 
          type="submit" 
        />
      </div>
    </form>
  </Dialog>
</template>

<script setup>
import { reactive, watch, computed } from 'vue'

const props = defineProps({
  visible: { type: Boolean, default: false },
  mode: { type: String, default: 'create' }, 
  user: { type: Object, default: () => ({}) },
  roles: { type: Array, default: () => [] },
  loading: { type: Boolean, default: false }
})

const emit = defineEmits(['update:visible','save'])

const internalVisible = computed({
  get: () => props.visible,
  set: (v) => emit('update:visible', v)
})

const form = reactive({ name: '', email: '', password: '', rol: '' })

watch(() => props.user, (u) => {
  if (props.mode === 'edit' && u) {
    form.name = u.name || ''
    form.email = u.email || ''
    form.rol = u.rol || ''
    form.password = ''
  }
}, { immediate: true })

watch(() => props.mode, (m) => {
  if (m === 'create') {
    form.name = ''
    form.email = ''
    form.password = ''
    form.rol = ''
  }
})

const dialogTitle = computed(() => props.mode === 'edit' ? 'Editar Usuario' : 'Nuevo Usuario')
const primaryLabel = computed(() => props.mode === 'edit' ? 'Guardar Cambios' : 'Crear Usuario')

function close() {
  emit('update:visible', false)
}

function onSubmit() {
  emit('save', { name: form.name, email: form.email, password: form.password, rol: form.rol })
}
</script>

<style scoped>
:deep(.p-dialog-header) {
  padding: 2.5rem 2rem 0;
  background: white;
}
:deep(.p-dialog-title) {
  font-weight: 900;
  font-size: 1.5rem;
  letter-spacing: -0.025em;
  color: #1D1D1F;
}
:deep(.p-dialog-content) {
  background: white;
  padding: 0 1rem;
}
:deep(.p-float-label label) {
  margin-left: 0.5rem;
}
</style>
