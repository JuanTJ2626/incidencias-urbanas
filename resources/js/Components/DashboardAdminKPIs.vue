<template>
  <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
    <div v-for="stat in stats" :key="stat.label" class="premium-stat-card group">
      <div :class="['stat-icon-wrapper', stat.colorClass]">
        <i :class="stat.icon"></i>
      </div>
      <div class="mt-4">
        <p class="text-[10px] font-black uppercase tracking-[0.2em] text-[#86868B] dark:text-[#A1A1A6]">{{ stat.label }}</p>
        <h4 class="text-3xl font-black mt-1 text-[#1D1D1F] dark:text-white">{{ stat.value }}</h4>
      </div>
      <div class="absolute bottom-0 left-0 w-0 h-1 bg-brand-red transition-all duration-500 group-hover:w-full"></div>
    </div>
  </div>
</template>

<script setup>
defineProps({
  stats: { type: Array, required: true }
})
</script>

<style scoped>
@reference "../../css/app.css";

.premium-stat-card {
  @apply bg-white/70 dark:bg-app-card/70 backdrop-blur-3xl rounded-[2rem] border border-app-border p-7 
         shadow-[0_8px_30px_rgba(0,0,0,0.04)] relative overflow-hidden transition-all duration-500 
         hover:shadow-2xl hover:-translate-y-1;
}

.stat-icon-wrapper {
  @apply w-12 h-12 rounded-2xl flex items-center justify-center text-xl shadow-inner;
}
</style>
