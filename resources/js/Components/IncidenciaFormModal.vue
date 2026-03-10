<template>
  <Dialog
    :visible="visible"
    @update:visible="$emit('update:visible', $event)"
    :header="mode === 'create' ? 'Nueva Incidencia' : 'Editar Incidencia'"
    :modal="true"
    :style="{ width: '680px', maxWidth: '96vw' }"
    :draggable="false"
    :closable="!saving"
  >
    <form @submit.prevent="submit" class="flex flex-col gap-4 py-2">

      <!-- Fila 1: Nombre + Email -->
      <div class="grid grid-cols-2 gap-4">
        <div class="flex flex-col gap-1.5">
          <label class="field-label">Nombre ciudadano <span class="text-rose-500">*</span></label>
          <InputText v-model="form.nombre_ciudadano" placeholder="Juan Pérez" class="w-full" />
          <p v-if="errors.nombre_ciudadano" class="field-error">{{ errors.nombre_ciudadano }}</p>
        </div>
        <div class="flex flex-col gap-1.5">
          <label class="field-label">Email</label>
          <InputText v-model="form.email" placeholder="correo@ejemplo.com" type="email" class="w-full" />
          <p v-if="errors.email" class="field-error">{{ errors.email }}</p>
        </div>
      </div>

      <!-- Fila 2: Dirección -->
      <div class="flex flex-col gap-1.5">
        <label class="field-label">Dirección <span class="text-rose-500">*</span></label>
        <InputText v-model="form.direccion" placeholder="Calle, colonia, municipio..." class="w-full" />
        <p v-if="errors.direccion" class="field-error">{{ errors.direccion }}</p>
      </div>

      <!-- Fila 3: Tipo + Estatus -->
      <div class="grid grid-cols-2 gap-4">
        <div class="flex flex-col gap-1.5">
          <label class="field-label">Tipo de incidencia <span class="text-rose-500">*</span></label>
          <Dropdown
            v-model="form.tipo_incidencia"
            :options="tiposIncidencia"
            placeholder="— Selecciona tipo —"
            class="w-full"
            :editable="true"
          />
          <p v-if="errors.tipo_incidencia" class="field-error">{{ errors.tipo_incidencia }}</p>
        </div>
        <div class="flex flex-col gap-1.5">
          <label class="field-label">Estatus <span class="text-rose-500">*</span></label>
          <Dropdown
            v-model="form.estatus"
            :options="estatusOpciones"
            placeholder="— Selecciona estatus —"
            class="w-full"
          />
          <p v-if="errors.estatus" class="field-error">{{ errors.estatus }}</p>
        </div>
      </div>

      <!-- Descripción -->
      <div class="flex flex-col gap-1.5">
        <label class="field-label">Descripción</label>
        <Textarea v-model="form.descripcion" :rows="3" placeholder="Describe la incidencia..." class="w-full resize-none text-sm" />
        <p v-if="errors.descripcion" class="field-error">{{ errors.descripcion }}</p>
      </div>

      <!-- Fila 4: Coordenadas -->
      <div class="grid grid-cols-2 gap-4">
        <div class="flex flex-col gap-1.5">
          <label class="field-label">Latitud</label>
          <InputText v-model="form.latitud" placeholder="20.123456" class="w-full font-mono" />
          <p v-if="errors.latitud" class="field-error">{{ errors.latitud }}</p>
        </div>
        <div class="flex flex-col gap-1.5">
          <label class="field-label">Longitud</label>
          <InputText v-model="form.longitud" placeholder="-103.123456" class="w-full font-mono" />
          <p v-if="errors.longitud" class="field-error">{{ errors.longitud }}</p>
        </div>
      </div>

      <!-- Foto -->
      <div class="flex flex-col gap-2">
        <label class="field-label">Foto {{ mode === 'edit' ? '(dejar vacío para mantener la actual)' : '' }}</label>
        <div class="flex items-center gap-4">
          <!-- Preview si ya existe -->
          <div v-if="mode === 'edit' && user_original_foto && !fotoPreview" class="h-20 w-20 rounded-xl overflow-hidden bg-app-secondary border border-app-border shrink-0">
            <img :src="`/storage/${user_original_foto}`" class="w-full h-full object-cover" />
          </div>
          <div v-if="fotoPreview" class="h-20 w-20 rounded-xl overflow-hidden bg-app-secondary border border-emerald-200 shrink-0">
            <img :src="fotoPreview" class="w-full h-full object-cover" />
          </div>

          <label class="flex items-center gap-2 cursor-pointer px-4 py-2.5 rounded-xl border border-dashed border-app-border bg-app-secondary hover:bg-white dark:hover:bg-white/5 transition text-sm font-bold text-[#86868B]">
            <i class="pi pi-upload"></i>
            {{ fotoPreview ? 'Cambiar foto' : 'Subir foto' }}
            <input type="file" accept="image/*" class="hidden" @change="onFotoChange" />
          </label>
          <button v-if="fotoPreview" type="button" @click="fotoPreview = null; form.foto = null" class="text-rose-400 hover:text-rose-600 text-xs">✕ quitar</button>
        </div>
        <p v-if="errors.foto" class="field-error">{{ errors.foto }}</p>
      </div>

    </form>

    <template #footer>
      <div class="flex justify-end gap-2 pt-2">
        <Button label="Cancelar" text @click="$emit('update:visible', false)" :disabled="saving" class="dark:text-[#A1A1A6]" />
        <Button
          :label="mode === 'create' ? 'Crear incidencia' : 'Guardar cambios'"
          :icon="saving ? 'pi pi-spin pi-spinner' : 'pi pi-check'"
          :loading="saving"
          @click="submit"
          class="!bg-[#1D1D1F] !border-[#1D1D1F] dark:!bg-white dark:!text-black dark:!border-white !rounded-xl !font-bold"
        />
      </div>
    </template>
  </Dialog>
</template>

<script>
import InputText from 'primevue/inputtext'
import Textarea  from 'primevue/textarea'
import Dropdown  from 'primevue/dropdown'

export default {
  components: { InputText, Textarea, Dropdown },

  props: {
    visible:     { type: Boolean, default: false },
    mode:        { type: String, default: 'create' }, // 'create' | 'edit'
    incidencia:  { type: Object, default: null },       // para modo edit
  },

  emits: ['update:visible', 'saved'],

  data() {
    return {
      saving:    false,
      errors:    {},
      fotoPreview: null,
      form: {
        nombre_ciudadano: '',
        email:            '',
        direccion:        '',
        tipo_incidencia:  '',
        descripcion:      '',
        estatus:          'pendiente',
        latitud:          '',
        longitud:         '',
        foto:             null,
      },
      tiposIncidencia: [
        'Bache', 'Alumbrado público', 'Fuga de agua', 'Drenaje', 'Basura',
        'Semáforo dañado', 'Árbol caído', 'Grafiti', 'Otro',
      ],
      estatusOpciones: ['pendiente', 'en proceso', 'en revisión', 'resuelto', 'rechazado'],
    }
  },

  computed: {
    user_original_foto() {
      return this.incidencia?.foto ?? null
    },
  },

  watch: {
    visible(val) {
      if (val) this.reset()
    },
  },

  methods: {
    reset() {
      this.errors      = {}
      this.fotoPreview = null
      this.form = {
        nombre_ciudadano: this.incidencia?.nombre_ciudadano ?? '',
        email:            this.incidencia?.email            ?? '',
        direccion:        this.incidencia?.direccion        ?? '',
        tipo_incidencia:  this.incidencia?.tipo_incidencia  ?? '',
        descripcion:      this.incidencia?.descripcion      ?? '',
        estatus:          this.incidencia?.estatus          ?? 'pendiente',
        latitud:          this.incidencia?.latitud          ?? '',
        longitud:         this.incidencia?.longitud         ?? '',
        foto:             null,
      }
    },

    onFotoChange(e) {
      const f = e.target.files[0]
      if (!f) return
      this.form.foto = f
      const reader = new FileReader()
      reader.onload = ev => { this.fotoPreview = ev.target.result }
      reader.readAsDataURL(f)
    },

    submit() {
      this.errors  = {}
      this.saving  = true

      const fd = new FormData()
      Object.entries(this.form).forEach(([k, v]) => {
        if (v !== null && v !== '') fd.append(k, v)
      })

      const csrf   = document.querySelector('meta[name="csrf-token"]')?.content
      const isEdit = this.mode === 'edit'

      const url    = isEdit ? `/admin/incidencias/${this.incidencia.id}` : '/admin/incidencias'
      if (isEdit) fd.append('_method', 'PUT')

      axios.post(url, fd, {
        headers: { 'X-CSRF-TOKEN': csrf, 'Content-Type': 'multipart/form-data' },
      }).then(res => {
        this.$emit('saved', res.data?.incidencia ?? null)
        this.$emit('update:visible', false)
        this.$toast?.add({ severity: 'success', summary: isEdit ? 'Actualizado' : 'Creado', detail: isEdit ? 'Incidencia actualizada.' : 'Incidencia creada.', life: 3000 })
      }).catch(e => {
        const errs = e.response?.data?.errors
        if (errs) this.errors = Object.fromEntries(Object.entries(errs).map(([k, v]) => [k, v[0]]))
        else this.errors = { general: 'Error inesperado. Intenta de nuevo.' }
      }).finally(() => { this.saving = false })
    },
  },
}
</script>

<style scoped>
.field-label {
  font-size: 0.75rem;
  font-weight: 700;
  color: var(--app-text);
  text-transform: uppercase;
  letter-spacing: 0.05em;
  opacity: 0.9;
}

.field-error {
  font-size: 0.75rem;
  color: #ef4444;
  margin-top: 0.125rem;
}
</style>
