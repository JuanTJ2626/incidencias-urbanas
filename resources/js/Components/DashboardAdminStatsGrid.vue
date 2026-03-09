<template>
  <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
    <div
      v-for="card in cards"
      :key="card.label"
      class="group relative bg-white dark:bg-app-card rounded-[2rem] border border-app-border shadow-[0_10px_30px_rgba(0,0,0,0.02)] p-6 flex flex-col gap-6 overflow-hidden transition-all duration-500 hover:-translate-y-2 hover:shadow-2xl hover:shadow-black/5"
      :class="card.hover"
    >
      <!-- Decoración de fondo premium -->
      <div class="absolute -top-10 -right-10 w-32 h-32 rounded-full opacity-[0.03] group-hover:opacity-10 transition-all duration-700 blur-3xl scale-150"
        :class="card.deco"></div>

      <div class="flex items-center justify-between z-10">
        <div :class="['w-14 h-14 rounded-2xl flex items-center justify-center text-2xl shrink-0 shadow-inner group-hover:scale-110 transition-transform duration-500', card.iconBg]">
          <i :class="[card.icon, card.iconColor, 'group-hover:drop-shadow-sm icon-interactive icon-wiggle']"></i>
        </div>
        <div :class="['flex flex-col items-end px-3 py-1 rounded-xl group-hover:bg-white/20 transition-colors', card.badgeBg]">
          <span :class="['text-[10px] font-black uppercase tracking-widest', card.badgeText]">
            {{ card.pct }}%
          </span>
        </div>
      </div>

      <div class="z-10">
        <div class="flex items-baseline gap-1">
          <p class="text-4xl font-black text-[#1D1D1F] dark:text-white tracking-tighter group-hover:text-white transition-colors duration-300">
            {{ card.value }}
          </p>
        </div>
        <p class="text-[11px] font-black text-[#86868B] dark:text-[#A1A1A6] uppercase tracking-[0.2em] mt-2 group-hover:text-white/80 transition-colors duration-300">
          {{ card.label }}
        </p>
      </div>
      
      <!-- Línea de progreso sutil al fondo -->
      <div class="absolute bottom-0 left-0 h-1 bg-current opacity-10 group-hover:opacity-30 transition-all duration-500" :style="{ width: card.pct + '%' }"></div>
    </div>
  </div>
</template>

<script setup>
defineProps({
  cards: { type: Array, required: true }
})
</script>
