<template>
  <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-8">
    <div class="flex items-center gap-4">
      <div v-if="icon" class="w-12 h-12 rounded-xl bg-gradient-to-br from-[#850D12] to-[#F04A4B] flex items-center justify-center shadow-md">
        <i :class="icon + ' text-white text-xl'"></i>
      </div>
      <div>
        <h1 class="text-4xl font-black tracking-tight text-[#1D1D1F] dark:text-white">{{ title }}</h1>
        <p v-if="subtitle" class="text-[#86868B] dark:text-gray-300 font-semibold mt-1 text-sm">{{ subtitle }}</p>
      </div>
    </div>

    <div v-if="$slots.actions" class="self-start sm:self-auto">
      <slot name="actions" />
    </div>
  </div>
</template>

<script setup>
defineProps({
  title: { type: String, required: true },
  subtitle: { type: String, default: '' },
  icon: { type: String, default: '' },
})
</script>

<style scoped>
.rounded-xl i { line-height: 1; }
</style>
