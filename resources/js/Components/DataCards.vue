
<template>
  <div>
    <!-- Toolbar / buscador -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-5">
      <slot name="toolbar" />
      <div class="relative w-full sm:w-72 group ml-auto">
        <i class="pi pi-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 group-focus-within:text-[#850D12] transition-colors" />
        <InputText
          v-model="search"
          placeholder="Buscar..."
          class="w-full pl-9 pr-4 py-2 text-sm rounded-xl border-gray-200 bg-gray-50 focus:ring-2 focus:ring-[#850D12]/20 focus:border-[#850D12] transition-all"
        />
      </div>
    </div>

    <!-- Skeleton -->
    <div v-if="loading" :class="gridClass" class="grid gap-5">
      <div v-for="i in cols * 2" :key="i" class="bg-white rounded-2xl overflow-hidden border border-[#E8E8ED]">
        <Skeleton height="10rem" borderRadius="0" />
        <div class="p-4 flex flex-col gap-2">
          <Skeleton width="60%" height="1.1rem" />
          <Skeleton width="40%" height="0.85rem" />
          <Skeleton width="80%" height="0.85rem" />
        </div>
      </div>
    </div>

    <!-- Empty state -->
    <div v-else-if="!filtered.length" class="w-full">
      <slot name="empty">
        <div class="flex flex-col items-center py-16 text-center bg-white rounded-2xl border border-[#E8E8ED]">
          <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center mb-4">
            <i class="pi pi-folder-open text-4xl text-gray-300"></i>
          </div>
          <p class="text-gray-500 font-medium">No se encontraron registros.</p>
        </div>
      </slot>
    </div>

    <!-- Grid de tarjetas (PrimeVue Card) -->
    <div v-else :class="gridClass" class="grid gap-5">
      <Card
        v-for="item in filtered"
        :key="item.id ?? JSON.stringify(item)"
        class="overflow-hidden shadow-sm hover:shadow-md transition-shadow duration-200 border border-[#E8E8ED] rounded-2xl"
      >
        <!-- Cabecera: imagen auto-detectada -->
        <template #header>
          <slot name="card-header" :data="item">
            <div v-if="resolvedImageField" class="relative h-44 bg-gray-100">
              <img
                v-if="item[resolvedImageField]"
                :src="`/storage/${item[resolvedImageField]}`"
                class="w-full h-full object-cover"
                alt="Imagen"
              />
              <div v-else class="w-full h-full flex items-center justify-center">
                <i class="pi pi-image text-4xl text-gray-300"></i>
              </div>
              <!-- Badge estatus sobre la imagen -->
              <span
                v-if="resolvedBadgeField && item[resolvedBadgeField]"
                :class="badgeClass(item[resolvedBadgeField])"
                class="absolute top-3 right-3 px-2.5 py-1 rounded-full text-xs font-bold shadow"
              >
                {{ item[resolvedBadgeField] }}
              </span>
            </div>
          </slot>
        </template>

        <!-- Título auto-detectado -->
        <template #title>
          <slot v-if="resolvedTitleField && $slots['card-' + resolvedTitleField]" :name="'card-' + resolvedTitleField" :data="item" :value="item[resolvedTitleField]" />
          <template v-else-if="resolvedTitleField">{{ formatVal(item[resolvedTitleField]) }}</template>
        </template>

        <!-- Subtítulo auto-detectado -->
        <template #subtitle>
          <slot v-if="resolvedSubtitleField && $slots['card-' + resolvedSubtitleField]" :name="'card-' + resolvedSubtitleField" :data="item" :value="item[resolvedSubtitleField]" />
          <template v-else-if="resolvedSubtitleField">{{ formatVal(item[resolvedSubtitleField]) }}</template>
        </template>

        <!-- Contenido: resto de campos visibles -->
        <template #content>
          <dl class="flex flex-col gap-1.5">
            <template v-for="key in bodyFields(item)" :key="key">
              <div class="flex flex-wrap items-baseline gap-1">
                <dt class="text-[10px] font-semibold text-[#86868B] uppercase tracking-wide shrink-0">
                  {{ prettyKey(key) }}:
                </dt>
                <dd class="text-xs text-[#1D1D1F] font-medium break-all">
                  <slot v-if="$slots['card-' + key]" :name="'card-' + key" :data="item" :value="item[key]" />
                  <template v-else>{{ formatVal(item[key]) }}</template>
                </dd>
              </div>
            </template>
          </dl>
        </template>

        <!-- Pie de tarjeta -->
        <template v-if="$slots['card-footer']" #footer>
          <slot name="card-footer" :data="item" />
        </template>
      </Card>
    </div>
  </div>
</template>

<script setup>
import { computed, ref } from 'vue'
import Card from 'primevue/card'
import InputText from 'primevue/inputtext'
import Skeleton from 'primevue/skeleton'

// Palabras clave para auto-detección de campos especiales
const FOTO_KEYWORDS     = ['foto', 'imagen', 'image', 'photo', 'picture', 'thumbnail']
const STATUS_KEYWORDS   = ['estatus', 'status', 'estado']
const TITLE_KEYWORDS    = ['nombre', 'name', 'titulo', 'title', 'tipo_incidencia', 'tipo', 'asunto']
const SUBTITLE_KEYWORDS = ['direccion', 'address', 'descripcion', 'description', 'subtitulo']
const DATE_KEYWORDS     = ['_at', 'fecha', 'date']

const props = defineProps({
  value:          { type: Array,   default: () => [] },
  loading:        { type: Boolean, default: false },
  cols:           { type: Number,  default: 3 },
  imageField:     { type: String,  default: null },
  titleField:     { type: String,  default: null },
  subtitleField:  { type: String,  default: null },
  badgeField:     { type: String,  default: null },
  excludeFields:  { type: Array,   default: () => ['created_at', 'updated_at'] },
})

const search = ref('')

/** Clase dinámica del grid según `cols` */
const gridClass = computed(() => {
  const map = { 1: 'grid-cols-1', 2: 'grid-cols-1 md:grid-cols-2', 3: 'grid-cols-1 md:grid-cols-2 xl:grid-cols-3', 4: 'grid-cols-1 md:grid-cols-2 xl:grid-cols-4' }
  return map[props.cols] ?? 'grid-cols-1 md:grid-cols-2 xl:grid-cols-3'
})

/** Claves del primer item */
const allKeys = computed(() => {
  if (!props.value?.length) return []
  return Object.keys(props.value[0])
})

/** Auto-detecta campo de imagen */
const resolvedImageField = computed(() =>
  props.imageField ?? allKeys.value.find(k => FOTO_KEYWORDS.some(kw => k.toLowerCase().includes(kw))) ?? null
)

/** Auto-detecta campo de badge/estatus */
const resolvedBadgeField = computed(() =>
  props.badgeField ?? allKeys.value.find(k => STATUS_KEYWORDS.some(kw => k.toLowerCase().includes(kw))) ?? null
)

/** Auto-detecta campo de título */
const resolvedTitleField = computed(() =>
  props.titleField ?? allKeys.value.find(k => TITLE_KEYWORDS.some(kw => k.toLowerCase().includes(kw))) ?? null
)

/** Auto-detecta campo de subtítulo */
const resolvedSubtitleField = computed(() => {
  if (props.subtitleField) return props.subtitleField
  const cand = allKeys.value.find(k =>
    SUBTITLE_KEYWORDS.some(kw => k.toLowerCase().includes(kw)) && k !== resolvedTitleField.value
  )
  return cand ?? null
})

/** Campos mostrados en el cuerpo (excluidos: especiales + excluidos por prop) */
function bodyFields(item) {
  const special = [
    resolvedImageField.value,
    resolvedBadgeField.value,
    resolvedTitleField.value,
    resolvedSubtitleField.value,
  ].filter(Boolean)
  return Object.keys(item).filter(k => !props.excludeFields.includes(k) && !special.includes(k))
}

/** Búsqueda genérica en todos los campos */
const filtered = computed(() => {
  if (!search.value) return props.value
  const q = search.value.toLowerCase()
  return props.value.filter(row =>
    Object.values(row).some(v => v != null && String(v).toLowerCase().includes(q))
  )
})

/** Badge visual según valor de estatus */
function badgeClass(val) {
  const s = String(val ?? '').toLowerCase()
  if (s.includes('resuelto')  || s.includes('completado')) return 'bg-emerald-500 text-white'
  if (s.includes('rechazado') || s.includes('cancelado'))  return 'bg-rose-500 text-white'
  if (s.includes('proceso'))                                return 'bg-sky-500 text-white'
  if (s.includes('pendiente'))                              return 'bg-amber-400 text-white'
  return 'bg-gray-400 text-white'
}

/** Formatea cualquier valor para mostrar */
function formatVal(val) {
  if (val === null || val === '' || val === undefined) return '—'
  if (typeof val === 'boolean') return val ? 'Sí' : 'No'
  if (typeof val === 'object') return JSON.stringify(val)
  const dateRegex = /^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}/
  if (typeof val === 'string' && dateRegex.test(val)) {
    return new Date(val).toLocaleDateString('es-MX', {
      year: 'numeric', month: 'short', day: 'numeric',
      hour: '2-digit', minute: '2-digit',
    })
  }
  return String(val)
}

/** snake_case → Título legible */
function prettyKey(key) {
  return String(key)
    .replace(/_/g, ' ')
    .replace(/([a-z])([A-Z])/g, '$1 $2')
    .replace(/\b\w/g, c => c.toUpperCase())
}
</script>
