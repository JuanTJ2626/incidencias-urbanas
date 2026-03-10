<template>
  <!-- Backdrop for Mobile -->
  <div
    v-if="visible"
    class="lg:hidden fixed inset-0 bg-black/40 backdrop-blur-sm z-40 transition-opacity duration-500"
    @click="toggle"
  ></div>

  <!-- Sidebar Premium Glass -->
  <aside
    class="bg-white/80 dark:bg-app-bg backdrop-blur-[60px] backdrop-saturate-[200%] border-r border-white/50 dark:border-app-border z-50 transition-all duration-500 ease-[cubic-bezier(0.23,1,0.32,1)] flex flex-col overflow-hidden h-screen fixed top-0 left-0 group/sidebar rounded-r-2xl"
    :class="[
      isHovered || visible ? 'w-72 shadow-[0_0_80px_rgba(0,0,0,0.35)] dark:shadow-[0_0_80px_rgba(0,0,0,0.6)]' : 'w-[68px] shadow-xl',
      visible ? 'translate-x-0 max-lg:w-[300px]' : 'max-lg:-translate-x-full',
    ]"
    :style="[
      {
        backgroundColor: 'var(--sidebar-bg)',
        backgroundImage: 'var(--sidebar-gradient)'
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
    <div class="absolute top-0 left-0 right-0 h-px bg-gradient-to-r from-transparent via-brand-red/30 to-transparent"></div>

    <!-- Header - Alineación tipo Apple -->
    <div
      class="h-21 flex items-center shrink-0 overflow-hidden relative border-b border-white/20 dark:border-app-border transition-all duration-300"
      :class="isHovered || visible ? 'px-6' : 'justify-center px-0'"
    >
      <div
        class="flex items-center gap-4 transition-all duration-500 w-full"
        :class="isHovered || visible ? '' : 'justify-center'"
      >
        <!-- Logo Container with Glow -->
        <div
          class="relative w-[46px] h-[46px] flex items-center justify-center shrink-0 transition-all duration-700 group-hover/sidebar:scale-105"
          :class="!(isHovered || visible) ? 'translate-x-[11px]' : ''"
        >
          <div class="absolute inset-0 bg-gradient-to-br from-brand-red/20 to-transparent rounded-xl blur-xl opacity-0 group-hover/sidebar:opacity-100 transition-opacity duration-500"></div>
          <img
            src="/public/img/jordan.png"
            alt="Jordan Logo"
            class="w-full h-full object-contain relative z-10 drop-shadow-xl"
          />
        </div>

        <!-- Logo Text con efecto BOUNCE al aparecer -->
        <div
          class="flex flex-col overflow-hidden transition-all duration-500"
          :class="isHovered || visible ? 'opacity-100 translate-x-0 w-auto' : 'opacity-0 -translate-x-6 pointer-events-none w-0 h-0'"
        >
          <span 
            class="text-xl font-black tracking-tight text-[#1D1D1F] dark:text-white leading-none whitespace-nowrap"
            :class="{ 'animate-bounce-in': isHovered || visible }"
          >
            SIG<span class="text-brand-red">IU</span>
          </span>
          <span 
            class="text-[10px] font-bold text-brand-red dark:text-[#D1A7B0] uppercase tracking-[0.15em] mt-1.5 whitespace-nowrap"
            :class="{ 'animate-bounce-in-delayed': isHovered || visible }"
          >
            Sistema de Gestión
          </span>
        </div>
      </div>

      <!-- Close button for mobile -->
      <Button
        v-if="visible"
        class="lg:hidden absolute top-4 right-4 !p-0 !w-8 !h-8 text-gray-500 hover:text-brand-red bg-white/80 dark:bg-gray-800 rounded-lg shadow-lg border border-gray-200 dark:border-gray-700 transition-all hover:scale-110"
        icon="pi pi-times"
        text
        rounded
        @click="toggle"
      />
    </div>

    <!-- Menu Navigation - Alineación tipo Apple -->
    <div class="flex-1 mt-4 py-4 px-3 overflow-y-auto overflow-x-hidden custom-scrollbar relative">
      <!-- Section Indicator -->
      <div class="absolute left-0 top-3 bottom-3 w-0.5 bg-gradient-to-b from-transparent via-brand-red/20 to-transparent opacity-0 group-hover/sidebar:opacity-100 transition-opacity duration-500"></div>

      <nav class="space-y-8">
        <div v-for="(item, sectionIndex) in menuItems" :key="sectionIndex">
          <!-- Section Label -->
          <div
            v-if="item.label"
            class="px-3 mb-3 flex items-center gap-2 overflow-hidden"
            :class="isHovered || visible ? 'opacity-100' : 'opacity-0 h-0 overflow-hidden'"
          >
            <div class="h-px flex-1 bg-gradient-to-r from-brand-red/30 to-transparent"></div>
            <span class="text-[10px] font-bold text-brand-red/70 dark:text-[#D1A7B0]/70 uppercase tracking-[0.15em]">
              {{ item.label }}
            </span>
            <div class="h-px flex-1 bg-gradient-to-l from-brand-red/30 to-transparent"></div>
          </div>

          <!-- Menu Items con efecto BOUNCE escalonado -->
          <div class="space-y-[4px]">
            <Link
              v-for="(subItem, itemIndex) in item.items"
              :key="subItem.label"
              :href="subItem.url || '#'"
              class="flex items-center h-[46px] rounded-2xl transition-all duration-300 group/item relative overflow-hidden"
              :class="[
                isHovered || visible ? 'px-4 gap-4' : 'px-2 justify-center',
                $page.url === subItem.url
                  ? 'bg-gradient-to-r from-brand-red to-[#a01a45] text-white shadow-lg shadow-brand-red/25'
                  : 'text-gray-700 dark:text-gray-400 hover:bg-brand-red/5 dark:hover:bg-white/5 hover:text-brand-red dark:hover:text-white transition-all'
              ]"
              @click="handleMenuClick(subItem.url)"
            >
              <!-- Active Indicator Line -->
              <div 
                v-if="$page.url === subItem.url"
                class="absolute left-0 top-1/2 -translate-y-1/2 w-[3px] h-5 bg-white rounded-r-full shadow-[0_0_10px_rgba(255,255,255,0.5)]"
              ></div>

              <!-- Icon Container -->
              <div class="w-5 h-5 flex items-center justify-center shrink-0 relative">
                <div 
                  v-if="$page.url === subItem.url"
                  class="absolute inset-0 bg-white/20 rounded-lg blur-md"
                ></div>
                <!-- Spinner while loading -->
                <i
                  v-if="loadingItem === subItem.url"
                  class="pi pi-spinner animate-spin text-[17px] relative z-10"
                  :class="$page.url === subItem.url ? 'text-white' : 'text-brand-red'"
                ></i>
                <i
                  v-else
                  :class="[
                    subItem.icon,
                    'text-[17px] transition-all duration-300 relative z-10',
                    $page.url === subItem.url
                      ? 'text-white scale-110'
                      : 'text-gray-500 dark:text-gray-400 group-hover/item:text-brand-red group-hover/item:scale-110',
                  ]"
                ></i>
              </div>

              <!-- Label con efecto BOUNCE al aparecer -->
              <span
                class="font-semibold text-[13px] tracking-tight whitespace-nowrap flex-1 transition-all duration-400"
                :class="[
                  isHovered || visible ? 'opacity-100 translate-x-0' : 'opacity-0 -translate-x-4 pointer-events-none absolute',
                  $page.url === subItem.url ? 'text-white' : 'text-gray-700 dark:text-gray-200',
                  (isHovered || visible) && 'animate-menu-bounce'
                ]"
                :style="{ animationDelay: ((sectionIndex * 0.1) + (itemIndex * 0.06)) + 's' }"
              >
                {{ subItem.label }}
              </span>

              <!-- Active Dot -->
              <div
                v-if="$page.url === subItem.url && (isHovered || visible)"
                class="w-1.5 h-1.5 bg-white rounded-full shadow-[0_0_8px_rgba(255,255,255,0.8)] animate-pulse z-20"
              ></div>

              <!-- Tooltip (Collapsed State) -->
              <div
                v-if="!isHovered && !visible"
                class="absolute left-11 bg-brand-red text-white text-[11px] font-bold px-3 py-2 rounded-xl opacity-0 translate-x-2 scale-95 group-hover/item:opacity-100 group-hover/item:translate-x-0 group-hover/item:scale-100 transition-all duration-300 pointer-events-none whitespace-nowrap z-50 shadow-2xl shadow-brand-red/30"
              >
                <div class="absolute left-0 top-1/2 -translate-x-1 -translate-y-1/2 w-2 h-2 bg-brand-red rotate-45"></div>
                {{ subItem.label }}
              </div>
            </Link>
          </div>
        </div>
      </nav>
    </div>

    <!-- Footer: User Profile - Alineación tipo Apple -->
    <div class="p-3 mt-auto relative">
      <!-- Top Border -->
      <div class="absolute top-0 left-3 right-3 h-px bg-gradient-to-r from-transparent via-gray-300/50 dark:via-gray-600/50 to-transparent"></div>

      <div
        class="flex items-center bg-gradient-to-br from-white/90 to-white/50 dark:from-gray-800/90 dark:to-gray-800/50 backdrop-blur-xl rounded-xl border border-white/60 dark:border-gray-700/50 transition-all duration-500 hover:shadow-xl hover:shadow-brand-red/10 hover:border-brand-red/30 group/user overflow-hidden relative"
        :class="isHovered || visible ? 'p-2 gap-2' : 'p-1.5 justify-center'"
      >
        <!-- User Glow -->
        <div class="absolute inset-0 bg-gradient-to-r from-brand-red/5 to-transparent opacity-0 group-hover/user:opacity-100 transition-opacity duration-500"></div>

        <div class="relative shrink-0">
          <div class="absolute inset-0 bg-brand-red rounded-full blur-lg opacity-20 group-hover/user:opacity-40 transition-opacity"></div>
          <Avatar
            :label="displayInitial"
            shape="circle"
            class="!bg-gradient-to-br !from-brand-red !to-[#a01a45] !text-white !font-bold !w-8 !h-8 shadow-lg shadow-brand-red/30 relative z-10 transition-transform duration-300 group-hover/user:scale-105 !text-xs"
          />
          <!-- Online Status -->
          <div class="absolute -bottom-0.5 -right-0.5 w-2.5 h-2.5 bg-green-500 border-2 border-white dark:border-gray-800 rounded-full shadow-sm z-20">
            <div class="absolute inset-0 bg-green-500 rounded-full animate-ping opacity-75"></div>
          </div>
        </div>

        <!-- User Info con efecto BOUNCE -->
        <div
          class="flex-1 min-w-0 overflow-hidden transition-all duration-500 relative z-10"
          :class="isHovered || visible ? 'opacity-100' : 'opacity-0 w-0'"
        >
          <h4 
            class="text-[13px] font-bold text-gray-900 dark:text-white truncate leading-tight"
            :class="{ 'animate-bounce-in': isHovered || visible }"
          >
            {{ displayUser.name }}
          </h4>
          <p 
            class="text-[10px] text-brand-red dark:text-[#D1A7B0] font-semibold uppercase tracking-wide truncate"
            :class="{ 'animate-bounce-in-delayed': isHovered || visible }"
          >
            {{ displayUser.rol }}
          </p>
        </div>

        <Button
          v-if="isHovered || visible"
          icon="pi pi-power-off"
          text
          rounded
          :loading="isLoggingOut"
          class="!text-gray-400 hover:!text-red-500 hover:!bg-red-50 dark:hover:!bg-red-900/20 !w-7 !h-7 transition-all duration-300 hover:scale-110 relative z-10"
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
const loadingItem = ref(null);

const handleMenuClick = (url) => {
  if (url && url !== '#') {
    loadingItem.value = url;
  }
};

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

// Clear loading when Inertia finishes navigation
let removeFinishListener = null;
if (typeof window !== 'undefined') {
  removeFinishListener = Inertia.on('finish', () => {
    loadingItem.value = null;
  });
}

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
onBeforeUnmount(() => {
  setBodyLock(false);
  if (removeFinishListener) removeFinishListener();
});
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
  background: linear-gradient(180deg, var(--brand-red), #D1A7B0);
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

:deep(.bg-gradient-to-r.from-brand-red) {
  animation: activeGlow 3s ease-in-out infinite;
}

/* ANIMACIÓN DE REBOTE PARA MENÚS */
@keyframes menuBounce {
  0% {
    opacity: 0;
    transform: translateX(-20px) scale(0.9);
  }
  50% {
    opacity: 1;
    transform: translateX(5px) scale(1.02);
  }
  70% {
    transform: translateX(-2px) scale(0.99);
  }
  100% {
    opacity: 1;
    transform: translateX(0) scale(1);
  }
}

.animate-menu-bounce {
  animation: menuBounce 0.5s cubic-bezier(0.34, 1.56, 0.64, 1) both;
}

/* ANIMACIÓN DE REBOTE PARA EL NOMBRE (SIGIU) */
@keyframes bounceIn {
  0% {
    opacity: 0;
    transform: translateY(-15px) scale(0.8);
  }
  50% {
    opacity: 1;
    transform: translateY(3px) scale(1.05);
  }
  70% {
    transform: translateY(-2px) scale(0.98);
  }
  100% {
    opacity: 1;
    transform: translateY(0) scale(1);
  }
}

.animate-bounce-in {
  animation: bounceIn 0.6s cubic-bezier(0.34, 1.56, 0.64, 1) both;
}

.animate-bounce-in-delayed {
  animation: bounceIn 0.6s cubic-bezier(0.34, 1.56, 0.64, 1) 0.1s both;
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
