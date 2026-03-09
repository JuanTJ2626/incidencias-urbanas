<template>
  <div class="flex flex-col items-center justify-center py-10 text-[#86868B] gap-2">
    <i :class="[icono, 'text-3xl opacity-30']"></i>
    <p class="text-sm font-semibold">{{ mensaje }}</p>
  </div>
</template>

<script>
export default {
  props: {
    mensaje: { type: String, default: 'Sin datos' },
    icono: { type: String, default: 'pi pi-inbox' },
  },
}
</script>
