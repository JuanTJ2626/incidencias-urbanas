<template>
  <Dialog
    v-model:visible="visible"
    header="Rechazar evidencia del trabajador"
    :modal="true"
    :style="{ width: '480px', maxWidth: '95vw' }"
    :draggable="false"
    :closable="!loading"
  >
    <div class="flex flex-col gap-4 py-2">
      <!-- Vista previa evidencia -->
      <div v-if="inc" class="bg-app-secondary dark:bg-app-bg rounded-xl p-3 border border-app-border">
        <p class="text-sm font-bold text-[#1D1D1F] dark:text-white">{{ inc.tipo_incidencia }} — #{{ inc.id }}</p>
        <p class="text-xs text-[#86868B] dark:text-[#A1A1A6]">{{ inc.direccion }}</p>
        <div v-if="inc.foto_despues" class="mt-2">
          <p class="text-[10px] font-bold text-[#86868B] dark:text-[#A1A1A6] uppercase tracking-wide mb-1">Foto enviada por el trabajador:</p>
          <img :src="`/storage/${inc.foto_despues}`" class="h-32 w-full object-cover rounded-lg border border-app-border" />
        </div>
      </div>

      <div class="flex flex-col gap-1.5">
        <label class="text-xs font-bold text-[#1D1D1F] dark:text-white uppercase tracking-wide flex items-center gap-1">
          <i class="pi pi-comment text-brand-red"></i>
          Motivo del rechazo <span class="text-rose-500">*</span>
        </label>
        <Textarea
          v-model="motivo"
          :rows="3"
          placeholder="Explica al trabajador qué debe corregir..."
          class="w-full text-sm resize-none"
          :class="{ '!border-rose-400': !motivo && error }"
        />
        <p v-if="error" class="text-xs text-rose-500">{{ error }}</p>
      </div>

      <div class="bg-amber-50 dark:bg-amber-500/10 border border-amber-200 dark:border-amber-500/30 rounded-xl px-3 py-2 text-xs text-amber-700 dark:text-amber-400 flex items-start gap-2">
        <i class="pi pi-info-circle mt-0.5 shrink-0 text-amber-500"></i>
        El trabajador verá este motivo y deberá corregir y reenviar la evidencia.
      </div>
    </div>

    <template #footer>
      <div class="flex gap-2 justify-end">
        <Button label="Cancelar" severity="secondary" outlined @click="visible = false" :disabled="loading" />
        <Button
          label="Confirmar rechazo" icon="pi pi-times"
          severity="danger" :loading="loading"
          :disabled="!motivo"
          @click="confirmar"
        />
      </div>
    </template>
  </Dialog>
</template>

<script>
import Textarea from 'primevue/textarea'

export default {
  components: { Textarea },

  props: {
    modelValue: { type: Boolean, default: false },
    inc: { type: Object, default: null },
  },
  emits: ['update:modelValue', 'confirmed'],

  data() {
    return {
      motivo: '',
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
        this.motivo = ''
        this.error = null
      }
    },
  },

  methods: {
    confirmar() {
      if (!this.motivo.trim()) {
        this.error = 'El motivo es obligatorio.'
        return
      }
      this.loading = true
      this.$emit('confirmed', {
        incId: this.inc.id,
        motivo: this.motivo,
        done: (err) => {
          this.loading = false
          if (err) {
            this.error = err
          } else {
            this.visible = false
          }
        },
      })
    },
  },
}
</script>
