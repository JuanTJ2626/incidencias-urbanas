<template>
  <Dialog
    v-model:visible="visible"
    modal
    header="Asignar Trabajador"
    :style="{ width: '420px' }"
    :closable="true"
  >
    <div class="flex flex-col gap-4 pt-2">
      <p class="text-sm text-gray-600 dark:text-gray-300">
        Incidencia <strong>#{{ inc?.id }}</strong> — {{ inc?.tipo_incidencia }}<br/>
        <span class="text-xs text-gray-400 dark:text-[#A1A1A6]">{{ inc?.direccion }}</span>
      </p>
      <div class="flex flex-col gap-1">
        <label class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Selecciona un trabajador</label>
        <Dropdown
          v-model="trabajadorId"
          :options="trabajadores"
          optionLabel="name"
          optionValue="id"
          placeholder="— Elige trabajador —"
          class="w-full"
          showClear
        >
          <template #option="{ option }">
            <div class="flex flex-col leading-tight py-0.5">
              <span class="text-sm font-semibold">{{ option.name }}</span>
              <span class="text-xs text-gray-400">{{ option.email }} · {{ option.rol }}</span>
            </div>
          </template>
        </Dropdown>
      </div>
      <Message v-if="error" severity="error" :closable="false">{{ error }}</Message>
    </div>

    <template #footer>
      <div class="flex justify-end gap-2 pt-2">
        <Button label="Cancelar" text @click="cancelar" />
        <Button
          label="Asignar y poner en proceso"
          icon="pi pi-check"
          :loading="loading"
          :disabled="!trabajadorId"
          @click="confirmar"
          class="!bg-brand-red !border-brand-red"
        />
      </div>
    </template>
  </Dialog>
</template>

<script>
export default {
  props: {
    modelValue: { type: Boolean, default: false },
    inc: { type: Object, default: null },
    trabajadores: { type: Array, default: () => [] },
  },
  emits: ['update:modelValue', 'confirmed'],

  data() {
    return {
      trabajadorId: null,
      loading: false,
      error: null,
    }
  },

  computed: {
    visible: {
      get() { return this.modelValue },
      set(val) { this.$emit('update:modelValue', val) },
    },
  },

  watch: {
    modelValue(open) {
      if (open) {
        this.trabajadorId = this.inc?.asignado_a ?? null
        this.error = null
      }
    },
  },

  methods: {
    cancelar() {
      this.visible = false
      this.trabajadorId = null
    },

    confirmar() {
      if (!this.trabajadorId) return
      this.loading = true
      this.error = null

      this.$emit('confirmed', {
        incId: this.inc.id,
        trabajadorId: this.trabajadorId,
        done: (err) => {
          this.loading = false
          if (err) {
            this.error = err
          } else {
            this.cancelar()
          }
        },
      })
    },
  },
}
</script>
