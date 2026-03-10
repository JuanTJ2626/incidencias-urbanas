<template>
  <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
    <div
      v-for="card in cards"
      :key="card.label"
      class="group relative bg-app-card rounded-2xl border border-app-border shadow-sm p-5 flex flex-col gap-4 overflow-hidden hover:-translate-y-1 hover:shadow-lg transition-all duration-300 cursor-default"
      :class="card.hover"
    >
      <div class="absolute -top-2 -right-2 w-20 h-20 rounded-full opacity-5 group-hover:opacity-20 transition-opacity duration-500"
        :class="card.deco"></div>

      <div class="flex items-start justify-between">
        <div :class="['w-11 h-11 rounded-xl flex items-center justify-center text-xl shrink-0', card.iconBg]">
          <i :class="[card.icon, card.iconColor]"></i>
        </div>
        <span :class="['text-[10px] font-black uppercase tracking-widest px-2 py-0.5 rounded-full', card.badgeBg, card.badgeText]">
          {{ card.pct }}%
        </span>
      </div>

      <div>
        <p class="text-3xl font-black text-[#1D1D1F] dark:text-white tracking-tight group-hover:text-white transition-colors duration-300">
          {{ card.value }}
        </p>
        <p class="text-[11px] font-bold text-[#86868B] dark:text-[#A1A1A6] uppercase tracking-[0.12em] mt-1 group-hover:text-white/70 transition-colors duration-300">
          {{ card.label }}
        </p>
      </div>
    </div>
  </div>
</template>

<script setup>
defineProps({
  cards: { type: Array, required: true }
})
</script>
