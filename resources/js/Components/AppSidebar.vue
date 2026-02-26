<template>
  <!-- Overlay Backdrop - Estilo Humo Apple (glass) -->
  <div
    v-if="visible"
    class="lg:hidden fixed inset-0 z-40 transition-opacity"
    style="
      background: linear-gradient(
        180deg,
        rgba(255, 255, 255, 0.02),
        rgba(13, 18, 23, 0.18)
      );
      backdrop-filter: blur(6px) saturate(120%);
      -webkit-backdrop-filter: blur(6px) saturate(120%);
    "
    @click="toggle"
  ></div>

  <!-- Sidebar Rail Expandible (Super Liquid Glass iOS Style) -->
  <aside
    class="bg-white/10 dark:bg-gray-900/95 backdrop-blur-[50px] backdrop-saturate-[180%] border-r border-white/40 dark:border-gray-800 z-50 transition-all duration-700 ease-[cubic-bezier(0.16,1,0.3,1)] flex flex-col overflow-hidden h-screen fixed top-0 left-0 group/sidebar rounded-br-3xl"
    :class="[
      isHovered || visible ? 'w-72 shadow-[0_0_120px_rgba(0,0,0,0.1)]' : 'w-20 shadow-sm',
      visible ? 'translate-x-0 max-lg:w-full' : 'max-lg:-translate-x-full',
    ]"
    :style="[
      {
        backgroundImage: isDark
          ? 'linear-gradient(170deg, rgba(17,24,39,0.9) 0%, rgba(17,24,39,0.6) 50%, rgba(17,24,39,0.8) 100%)'
          : 'linear-gradient(170deg, rgba(255,255,255,0.28) 0%, rgba(255,255,255,0) 50%, rgba(255,255,255,0.08) 100%)'
      },
      visible ? { height: '100dvh' } : {}
    ]"
    @mouseenter="isHovered = true"
    @mouseleave="isHovered = false"
  >
    <!-- Sutil overlay blanco desvanecido (modo claro) -->
    <div v-if="!isDark" class="absolute inset-0 pointer-events-none" style="background: linear-gradient(180deg, rgba(255,255,255,0.06) 0%, rgba(255,255,255,0.03) 40%, rgba(255,255,255,0.02) 100%); mix-blend-mode: overlay;"></div>
    <!-- Header: Apple Brand Identity -->
    <div
      class="h-36 flex items-center shrink-0 overflow-hidden relative"
      :class="isHovered || visible ? 'px-4' : 'justify-center'"
    >
      <div
        class="flex items-center gap-3 transition-all duration-500"
        :class="isHovered || visible ? 'min-w-[280px]' : 'min-w-0'"
      >
        <div
          class="w-20 h-20 flex items-center justify-center shrink-0 transition-all duration-1000 group-hover/sidebar:rotate-[360deg] group-hover/sidebar:scale-110"
        >
          <img
            src="/public/img/jordan.png"
            alt="Jordan Logo"
            class="w-18 h-18 object-contain"
          />
        </div>
        <div
          class="flex flex-col transition-all duration-500"
          :class="
            isHovered || visible
              ? 'opacity-100 translate-x-0'
              : 'opacity-0 -translate-x-10 pointer-events-none w-0 h-0 overflow-hidden'
          "
        >
          <span
            class="text-3xl font-black tracking-tighter text-[#1D1D1F] dark:text-white leading-none whitespace-nowrap"
            >SIG<span class="text-[#607C88]">IU</span></span
          >
          <span
            class="text-[11px] font-bold text-[#86868B] dark:text-gray-400 uppercase tracking-[0.1em] mt-2 whitespace-nowrap"
            >Sistema de Gestión
          </span>
        </div>
      </div>

      <!-- Close button for mobile -->
      <Button
        v-if="visible"
        class="lg:hidden absolute top-6 right-4 !p-0 !w-10 !h-10 text-[#86868B] hover:text-[#1D1D1F] dark:hover:text-white bg-[#F5F5F7] dark:bg-gray-800 rounded-xl border border-[#E8E8ED] dark:border-gray-700 transition-all"
        icon="pi pi-times"
        text
        rounded
        @click="toggle"
      />
    </div>

    <!-- Menú de Navegación Apple Minimal -->
    <div class="flex-1 py-6 px-3 overflow-y-auto overflow-x-hidden custom-scrollbar">
      <nav class="space-y-10">
        <div v-for="(item, index) in menuItems" :key="index">
          <!-- iOS Style Header -->
          <div
            v-if="item.label"
            class="px-5 mb-4 text-[11px] font-red text-[#86868B]/50 dark:text-gray-500 uppercase tracking-[0.2em] transition-all duration-500"
            :class="
              isHovered || visible ? 'opacity-100' : 'opacity-0 h-0 overflow-hidden'
            "
          >
            {{ item.label }}
          </div>

          <!-- Items del Menú -->
          <div class="space-y-2">
            <Link
              v-for="subItem in item.items"
              :key="subItem.label"
              :href="subItem.url || '#'"
              class="flex items-center h-12 px-2 rounded-[1rem] transition-all duration-500 group/item relative overflow-hidden"
              :class="
                $page.url === subItem.url
                  ? 'bg-[#850D12] text-white shadow-lg shadow-[#F04A4B]/20'
                  : 'text-[#1D1D1F] dark:text-gray-300 hover:bg-[#E0DCDC] dark:hover:bg-gray-800 hover:text-[#000000] dark:hover:text-white'
              "
            >
              <!-- Icono con Animación Elastic Pop Centrado -->
              <div class="w-10 flex justify-center items-center shrink-0">
                <i
                  :class="[
                    subItem.icon,
                    'text-xl transition-all duration-500 cubic-bezier-elastic group-hover/item:scale-135 group-hover/item:rotate-[12deg]',
                    $page.url === subItem.url
                      ? 'text-[#FFFFFF]'
                      : 'group-hover/item:text-[#000000]',
                  ]"
                ></i>
              </div>

              <!-- Texto más balanceado -->
              <span
                class="ml-4 font-bold text-[13px] tracking-tight whitespace-nowrap transition-all duration-500"
                :class="
                  isHovered || visible
                    ? 'opacity-100 translate-x-0'
                    : 'opacity-0 -translate-x-8 pointer-events-none'
                "
              >
                {{ subItem.label }}
              </span>

              <!-- Active state indicator bar -->
              <div
                v-if="$page.url === subItem.url && (isHovered || visible)"
                class="absolute right-4 w-1.5 h-1.5 bg-[#FFFFFF] rounded-full shadow-[0_0_8px_#607C88]"
              ></div>

              <!-- Tooltip Minimalista (Negro Premium) -->
              <div
                v-if="!isHovered && !visible"
                class="absolute left-20 bg-[#850D12] text-white text-[10px] font-bold px-3 py-1.5 rounded-lg opacity-0 translate-x-4 group-hover/item:opacity-100 group-hover/item:translate-x-0 transition-all duration-300 pointer-events-none whitespace-nowrap z-50 shadow-2xl"
              >
                {{ subItem.label }}
              </div>
            </Link>
          </div>
        </div>
      </nav>
    </div>

    <!-- Footer: Perfil Control Center -->
    <div class="p-4 mt-auto">
      <div
        class="flex items-center bg-white/10 backdrop-blur-xl backdrop-saturate-[180%] rounded-[1.8rem] border border-white/30 transition-all duration-500 hover:shadow-lg hover:bg-white/20 group/user"
        :class="isHovered || visible ? 'p-3' : 'p-2 justify-center'"
      >
        <div class="relative shrink-0">
          <Avatar
            :label="displayInitial"
            shape="circle"
            class="!bg-[#1D1D1F] !text-white !font-bold !w-10 !h-10 shadow-sm transition-transform duration-500 group-hover/user:scale-105"
          />
          <div
            class="absolute -bottom-0.5 -right-0.5 w-3.5 h-3.5 bg-green-500 border-2 border-white/20 rounded-full"
          ></div>
        </div>

        <div
          class="ml-3 flex-1 overflow-hidden transition-all duration-500"
          :class="isHovered || visible ? 'opacity-100 max-w-full' : 'opacity-0 max-w-0'"
        >
          <h4 class="text-sm font-bold text-[#1D1D1F] dark:text-white truncate tracking-tight">
            {{ displayUser.name }}
          </h4>
          <p
            class="text-[10px] text-[#86868B] dark:text-gray-400 font-bold uppercase tracking-tight truncate"
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
          class="!text-[#86868B] hover:!text-red-500 hover:!bg-red-50 !w-10 !h-10 transition-all duration-300"
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
@keyframes fadeIn {
  from {
    opacity: 0;
    transform: translateX(-10px);
  }
  to {
    opacity: 1;
    transform: translateX(0);
  }
}

.cubic-bezier-elastic {
  transition-timing-function: cubic-bezier(0.175, 0.885, 0.32, 1.275);
}

.animate-fade-in {
  animation: fadeIn 0.4s ease-out forwards;
}

.custom-scrollbar::-webkit-scrollbar {
  width: 0px;
}
.custom-scrollbar:hover::-webkit-scrollbar {
  width: 3px;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
  background: #e8e8ed;
  border-radius: 10px;
}
</style>
