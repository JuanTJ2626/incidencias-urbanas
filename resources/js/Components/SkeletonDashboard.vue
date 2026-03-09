<template>
  <div class="animate-fade-in space-y-8">
    <!-- Header Skeleton -->
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-6">
      <div>
        <div class="skeleton skeleton-title"></div>
        <div class="skeleton skeleton-text w-64"></div>
      </div>
      <div class="skeleton w-40 h-10 rounded-2xl"></div>
    </div>

    <!-- Stats Cards Skeleton -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
      <div v-for="n in 4" :key="n" class="bg-white/50 dark:bg-white/5 rounded-[2rem] border border-app-border p-6">
        <div class="flex items-center justify-between mb-6">
          <div class="skeleton w-14 h-14 rounded-2xl"></div>
          <div class="skeleton w-12 h-6 rounded-xl"></div>
        </div>
        <div class="skeleton skeleton-title w-20"></div>
        <div class="skeleton skeleton-text w-32"></div>
      </div>
    </div>

    <!-- Charts Skeleton -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
      <div v-for="n in 2" :key="n" class="bg-white/50 dark:bg-white/5 rounded-[2.5rem] border border-app-border overflow-hidden">
        <div class="px-8 py-6">
          <div class="skeleton skeleton-title w-40"></div>
          <div class="skeleton skeleton-text w-56"></div>
        </div>
        <div class="px-8 pb-8">
          <div class="skeleton skeleton-chart"></div>
        </div>
      </div>
    </div>

    <!-- Map + Activity Skeleton -->
    <div class="grid grid-cols-1 xl:grid-cols-12 gap-6">
      <div class="xl:col-span-5 bg-white/50 dark:bg-white/5 rounded-[2rem] border border-app-border p-6">
        <div class="skeleton skeleton-title w-36 mb-4"></div>
        <div class="space-y-3">
          <div v-for="n in 5" :key="n" class="flex items-center gap-3">
            <div class="skeleton w-10 h-10 rounded-xl shrink-0"></div>
            <div class="flex-1">
              <div class="skeleton skeleton-text w-3/4"></div>
              <div class="skeleton skeleton-text w-1/2"></div>
            </div>
          </div>
        </div>
      </div>
      <div class="xl:col-span-7">
        <div class="skeleton w-full h-80 rounded-[2.5rem]"></div>
      </div>
    </div>
  </div>
</template>

<script setup>
// Componente puramente visual, sin props ni lógica
</script>

<style scoped>
@keyframes fadeIn {
  from { opacity: 0; transform: translateY(20px); }
  to   { opacity: 1; transform: translateY(0); }
}
.animate-fade-in {
  animation: fadeIn 0.8s cubic-bezier(0.16, 1, 0.3, 1) both;
}
</style>
