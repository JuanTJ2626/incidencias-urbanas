<template>
  <!-- Overlay Backdrop - Glass Premium -->
  <div
    v-if="visible"
    class="lg:hidden fixed inset-0 z-40 transition-all duration-500"
    style="
      background: linear-gradient(
        180deg,
        rgba(0, 0, 0, 0.4),
        rgba(0, 0, 0, 0.6)
      );
      backdrop-filter: blur(12px) saturate(180%);
      -webkit-backdrop-filter: blur(12px) saturate(180%);
    "
    @click="toggle"
  ></div>

  <!-- Sidebar Premium Glass -->
  <aside
    class="bg-white/80 dark:bg-gray-900/95 backdrop-blur-[60px] backdrop-saturate-[200%] border-r border-white/50 dark:border-gray-700/50 z-50 transition-all duration-500 ease-[cubic-bezier(0.23,1,0.32,1)] flex flex-col overflow-hidden h-screen fixed top-0 left-0 group/sidebar"
    :class="[
      isHovered || visible ? 'w-80 shadow-[0_0_80px_rgba(138,21,56,0.15)]' : 'w-24 shadow-xl',
      visible ? 'translate-x-0 max-lg:w-full' : 'max-lg:-translate-x-full',
    ]"
    :style="[
      {
        backgroundImage: isDark
          ? 'linear-gradient(165deg, rgba(17,24,39,0.95) 0%, rgba(138,21,56,0.05) 50%, rgba(17,24,39,0.9) 100%)'
          : 'linear-gradient(165deg, rgba(255,255,255,0.9) 0%, rgba(138,21,56,0.03) 50%, rgba(255,255,255,0.85) 100%)'
      },
      visible ? { height: '100dvh' } : {}
    ]"
    @mouseenter="isHovered = true"
    @mouseleave="isHovered = false"
  >
    <!-- Shine Effect -->
    <div 
      v-if="!isDark" 
      class="absolute inset-0 pointer-events-none opacity-30"
      style="background: linear-gradient(135deg, rgba(255,255,255,0.4) 0%, transparent 50%, rgba(138,21,56,0.05) 100%);"
    ></div>

    <!-- Decorative Line -->
    <div class="absolute top-0 left-0 right-0 h-px bg-gradient-to-r from-transparent via-[#8A1538]/30 to-transparent"></div>

    <!-- Header -->
    <div
      class="h-32 flex items-center shrink-0 overflow-hidden relative border-b border-white/20 dark:border-gray-700/30"
      :class="isHovered || visible ? 'px-6' : 'justify-center'"
    >
      <div
        class="flex items-center gap-4 transition-all duration-500"
        :class="isHovered || visible ? 'min-w-[280px]' : 'min-w-0'"
      >
        <!-- Logo Container with Glow -->
        <div
          class="relative w-16 h-16 flex items-center justify-center shrink-0 transition-all duration-700 group-hover/sidebar:scale-110"
        >
          <div class="absolute inset-0 bg-gradient-to-br from-[#8A1538]/20 to-transparent rounded-2xl blur-xl opacity-0 group-hover/sidebar:opacity-100 transition-opacity duration-500"></div>
          <img
            src="/public/img/jordan.png"
            alt="Jordan Logo"
            class="w-14 h-14 object-contain relative z-10 drop-shadow-lg"
          />
        </div>

        <div
          class="flex flex-col transition-all duration-500"
          :class="
            isHovered || visible
              ? 'opacity-100 translate-x-0'
              : 'opacity-0 -translate-x-6 pointer-events-none w-0 h-0 overflow-hidden'
          "
        >
          <span class="text-2xl font-black tracking-tight text-[#1D1D1F] dark:text-white leading-none whitespace-nowrap">
            SIG<span class="text-[#8A1538]">IU</span>
          </span>
          <span class="text-[10px] font-semibold text-[#8A1538] dark:text-[#D1A7B0] uppercase tracking-[0.15em] mt-1.5 whitespace-nowrap">
            Sistema de Gestión
          </span>
          <span class="text-[9px] text-gray-400 dark:text-gray-500 mt-0.5 tracking-wide">Incidencias Urbanas</span>
        </div>
      </div>

      <!-- Close button for mobile -->
      <Button
        v-if="visible"
        class="lg:hidden absolute top-5 right-5 !p-0 !w-10 !h-10 text-gray-500 hover:text-[#8A1538] bg-white/80 dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 transition-all hover:scale-110"
        icon="pi pi-times"
        text
        rounded
        @click="toggle"
      />
    </div>

    <!-- Menu Navigation -->
    <div class="flex-1 py-6 px-3 overflow-y-auto overflow-x-hidden custom-scrollbar relative">
      <!-- Section Indicator -->
      <div class="absolute left-0 top-6 bottom-6 w-0.5 bg-gradient-to-b from-transparent via-[#8A1538]/20 to-transparent opacity-0 group-hover/sidebar:opacity-100 transition-opacity duration-500"></div>

      <nav class="space-y-8">
        <div v-for="(item, index) in menuItems" :key="index">
          <!-- Section Label -->
          <div
            v-if="item.label"
            class="px-4 mb-3 flex items-center gap-2"
            :class="isHovered || visible ? 'opacity-100' : 'opacity-0 h-0 overflow-hidden'"
          >
            <div class="h-px flex-1 bg-gradient-to-r from-[#8A1538]/30 to-transparent"></div>
            <span class="text-[10px] font-bold text-[#8A1538]/70 dark:text-[#D1A7B0]/70 uppercase tracking-[0.2em]">
              {{ item.label }}
            </span>
            <div class="h-px flex-1 bg-gradient-to-l from-[#8A1538]/30 to-transparent"></div>
          </div>

          <!-- Menu Items -->
          <div class="space-y-1.5 px-2">
            <Link
              v-for="subItem in item.items"
              :key="subItem.label"
              :href="subItem.url || '#'"
              class="flex items-center h-12 px-4 rounded-2xl transition-all duration-300 group/item relative overflow-hidden"
              :class="[
                $page.url === subItem.url
                  ? 'bg-gradient-to-r from-[#8A1538] to-[#a01a45] text-white shadow-lg shadow-[#8A1538]/25'
                  : 'text-gray-700 dark:text-gray-300 hover:bg-white/60 dark:hover:bg-gray-800/60 hover:shadow-md'
              ]"
            >
              <!-- Active Indicator Line -->
              <div 
                v-if="$page.url === subItem.url"
                class="absolute left-0 top-1/2 -translate-y-1/2 w-1 h-8 bg-white rounded-r-full shadow-[0_0_10px_rgba(255,255,255,0.5)]"
              ></div>

              <!-- Icon Container (moved more left) -->
              <div class="w-8 flex items-center justify-start pl-0 -ml-2 shrink-0 relative">
                <div 
                  v-if="$page.url === subItem.url"
                  class="absolute inset-0 bg-white/20 rounded-lg blur-md"
                ></div>
                <i
                  :class="[
                    subItem.icon,
                    'text-lg transition-all duration-300 relative z-10',
                    $page.url === subItem.url
                      ? 'text-white scale-110'
                      : 'text-gray-500 dark:text-gray-400 group-hover/item:text-[#8A1538] group-hover/item:scale-110',
                  ]"
                ></i>
              </div>

              <!-- Label -->
              <span
                class="ml-2 font-semibold text-[13px] tracking-tight whitespace-nowrap transition-all duration-400"
                :class="[
                  isHovered || visible ? 'opacity-100 translate-x-0' : 'opacity-0 -translate-x-4 pointer-events-none absolute',
                  $page.url === subItem.url ? 'text-white' : 'text-gray-700 dark:text-gray-200'
                ]"
              >
                {{ subItem.label }}
              </span>

              <!-- Active Dot -->
              <div
                v-if="$page.url === subItem.url && (isHovered || visible)"
                class="absolute right-3 w-2 h-2 bg-white rounded-full shadow-[0_0_8px_rgba(255,255,255,0.8)] animate-pulse"
              ></div>

              <!-- Tooltip (Collapsed State) -->
              <div
                v-if="!isHovered && !visible"
                class="absolute left-10 bg-[#8A1538] text-white text-[11px] font-bold px-3 py-2 rounded-xl opacity-0 translate-x-2 scale-95 group-hover/item:opacity-100 group-hover/item:translate-x-0 group-hover/item:scale-100 transition-all duration-300 pointer-events-none whitespace-nowrap z-50 shadow-2xl shadow-[#8A1538]/30"
              >
                <div class="absolute left-0 top-1/2 -translate-x-1 -translate-y-1/2 w-2 h-2 bg-[#8A1538] rotate-45"></div>
                {{ subItem.label }}
              </div>
            </Link>
          </div>
        </div>
      </nav>
    </div>

    <!-- Footer: User Profile -->
    <div class="p-4 mt-auto relative">
      <!-- Top Border -->
      <div class="absolute top-0 left-4 right-4 h-px bg-gradient-to-r from-transparent via-gray-300/50 dark:via-gray-600/50 to-transparent"></div>

      <div
        class="flex items-center bg-gradient-to-br from-white/90 to-white/50 dark:from-gray-800/90 dark:to-gray-800/50 backdrop-blur-xl rounded-2xl border border-white/60 dark:border-gray-700/50 transition-all duration-500 hover:shadow-xl hover:shadow-[#8A1538]/10 hover:border-[#8A1538]/30 group/user overflow-hidden relative"
        :class="isHovered || visible ? 'p-3 gap-3' : 'p-2 justify-center'"
      >
        <!-- User Glow -->
        <div class="absolute inset-0 bg-gradient-to-r from-[#8A1538]/5 to-transparent opacity-0 group-hover/user:opacity-100 transition-opacity duration-500"></div>

        <div class="relative shrink-0">
          <div class="absolute inset-0 bg-[#8A1538] rounded-full blur-lg opacity-20 group-hover/user:opacity-40 transition-opacity"></div>
          <Avatar
            :label="displayInitial"
            shape="circle"
            class="!bg-gradient-to-br !from-[#8A1538] !to-[#a01a45] !text-white !font-bold !w-11 !h-11 shadow-lg shadow-[#8A1538]/30 relative z-10 transition-transform duration-300 group-hover/user:scale-105"
          />
          <!-- Online Status -->
          <div class="absolute -bottom-0.5 -right-0.5 w-3.5 h-3.5 bg-green-500 border-2 border-white dark:border-gray-800 rounded-full shadow-sm z-20">
            <div class="absolute inset-0 bg-green-500 rounded-full animate-ping opacity-75"></div>
          </div>
        </div>

        <div
          class="flex-1 overflow-hidden transition-all duration-500 relative z-10"
          :class="isHovered || visible ? 'opacity-100 max-w-full' : 'opacity-0 max-w-0'"
        >
          <h4 class="text-sm font-bold text-gray-900 dark:text-white truncate">
            {{ displayUser.name }}
          </h4>
          <p class="text-[10px] text-[#8A1538] dark:text-[#D1A7B0] font-semibold uppercase tracking-wide truncate">
            {{ displayUser.rol }}
          </p>
        </div>

        <Button
          v-if="isHovered || visible"
          icon="pi pi-power-off"
          text
          rounded
          :loading="isLoggingOut"
          class="!text-gray-400 hover:!text-red-500 hover:!bg-red-50 dark:hover:!bg-red-900/20 !w-9 !h-9 transition-all duration-300 hover:scale-110 relative z-10"
          @click="logout"
        />
      </div>
    </div>
  </aside>
</template>

<script setup>
import { Inertia } from "@inertiajs/inertia";
import { usePage } from "@inertiajs/inertia-vue3";
import { computed, ref, watch, onBeforeUnmount } from "vue";
import { Link } from "@inertiajs/inertia-vue3";
import { useDarkMode } from "@/composables/useDarkMode";

const { isDark } = useDarkMode();
const props = defineProps({
  visible: { type: Boolean, default: false },
  items: { type: Array, default: () => [] },
  user: {
    type: Object,
  },
});

const isHovered = ref(false);
const emit = defineEmits(["update:visible", "logout"]);
const page = usePage();

const menuItems = computed(() => page.props.value?.menu ?? props.items);
const displayUser = computed(() => page.props.value?.auth?.user ?? props.user);
const displayInitial = computed(() => {
  const name = displayUser.value?.name || displayUser.value?.email || "U";
  return name.charAt(0).toUpperCase();
});

const isLoggingOut = ref(false);
const toggle = () => emit("update:visible", !props.visible);
const logout = () => {
  isLoggingOut.value = true;
  Inertia.post(
    "/logout",
    {},
    {
      onFinish: () => (isLoggingOut.value = false),
    }
  );
};

const setBodyLock = (lock) => {
  try {
    if (lock) {
      document.documentElement.classList.add("overflow-hidden");
      document.body.classList.add("overflow-hidden");
    } else {
      document.documentElement.classList.remove("overflow-hidden");
      document.body.classList.remove("overflow-hidden");
    }
  } catch (e) {
    // ignore when server-side rendering or document not available
  }
};

watch(
  () => props.visible,
  (val) => setBodyLock(!!val)
);
onBeforeUnmount(() => setBodyLock(false));
</script>

<style scoped>
/* Smooth transitions */
.transition-all {
  transition-property: all;
  transition-timing-function: cubic-bezier(0.23, 1, 0.32, 1);
}

/* Custom Scrollbar */
.custom-scrollbar::-webkit-scrollbar {
  width: 4px;
}
.custom-scrollbar::-webkit-scrollbar-track {
  background: transparent;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
  background: linear-gradient(180deg, #8A1538, #D1A7B0);
  border-radius: 10px;
  opacity: 0.5;
}
.custom-scrollbar::-webkit-scrollbar-thumb:hover {
  opacity: 1;
}

/* Active Item Glow */
@keyframes activeGlow {
  0%, 100% { box-shadow: 0 4px 20px rgba(138, 21, 56, 0.25); }
  50% { box-shadow: 0 4px 30px rgba(138, 21, 56, 0.4); }
}

:deep(.bg-gradient-to-r.from-\[\#8A1538\]) {
  animation: activeGlow 3s ease-in-out infinite;
}

/* Tooltip Arrow Animation */
.group-hover\/item\:translate-x-0 {
  transform: translateX(0);
}

/* Duration utilities */
.duration-400 {
  transition-duration: 400ms;
}

/* Elastic easing */
.ease-\[cubic-bezier\(0\.23\2c 1\2c 0\.32\2c 1\)\] {
  transition-timing-function: cubic-bezier(0.23, 1, 0.32, 1);
}
</style>