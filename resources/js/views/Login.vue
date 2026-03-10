<template>
  <div class="min-h-screen w-full flex overflow-hidden bg-app-bg transition-colors duration-500">
    <Toast position="top-right" />

    <!-- Left Side: Visual / Branding (TUYO ORIGINAL) -->
    <div
      class="hidden lg:flex lg:w-1/2 relative bg-black justify-center items-center overflow-hidden"
    >
      <!-- Background Image (Urban / City) -->
      <div
        class="absolute inset-0 bg-cover bg-center opacity-90 transition-transform duration-[30s] hover:scale-105"
        style="background-image: url('https://images.unsplash.com/photo-1477959858617-67f85cf4f1df?q=80&w=2644&auto=format&fit=crop');"
      ></div>

      <!-- Gradient Overlay -->
      <div class="absolute inset-0 bg-gradient-to-t from-black via-black/50 to-black/30"></div>

      <!-- Content -->
      <div class="relative z-10 text-white max-w-xl px-12">
        <!-- Logo / Brand Header -->
        <div class="flex items-center gap-4 mb-10">
          <div class="w-14 h-14 bg-white/10 backdrop-blur-md rounded-2xl flex items-center justify-center border border-white/10">
            <i class="pi pi-building text-2xl text-brand-red"></i>
          </div>
          <div class="flex flex-col">
            <span class="text-[11px] tracking-[0.25em] font-medium text-[#D1A7B0] uppercase">Plataforma Municipal</span>
            <span class="text-xl font-bold tracking-wide">Incidencias Urbanas</span>
          </div>
        </div>

        <!-- Main Headline -->
        <h2 class="text-4xl font-extrabold mb-5 leading-tight">
          <span class="block">Reporta, Supervisa</span>
          <span class="block">y Mejora tu</span>
          <span class="text-transparent bg-clip-text bg-gradient-to-r from-white to-brand-red">Ciudad</span>
        </h2>

        <!-- Accent Line -->
        <div class="flex gap-1 mb-6">
          <div class="h-1 w-12 bg-brand-red rounded-full"></div>
          <div class="h-1 w-6 bg-gray-300 rounded-full"></div>
          <div class="h-1 w-6 bg-gray-100 rounded-full"></div>
        </div>

        <p class="text-base text-gray-200 font-light leading-relaxed max-w-md">
          Plataforma digital para el registro, seguimiento y resolución de incidencias en infraestructura y servicios públicos de tu comunidad.
        </p>

        <!-- Feature Highlights -->
        <div class="mt-10 space-y-4">
          <div class="flex items-center gap-3">
            <div class="w-9 h-9 bg-white/10 rounded-lg flex items-center justify-center shrink-0">
              <i class="pi pi-map-marker text-sm text-brand-red"></i>
            </div>
            <span class="text-sm text-gray-300">Geolocaliza y reporta problemas urbanos al instante</span>
          </div>
          <div class="flex items-center gap-3">
            <div class="w-9 h-9 bg-white/10 rounded-lg flex items-center justify-center shrink-0">
              <i class="pi pi-chart-line text-sm text-brand-red"></i>
            </div>
            <span class="text-sm text-gray-300">Seguimiento en tiempo real del estado de tus reportes</span>
          </div>
          <div class="flex items-center gap-3">
            <div class="w-9 h-9 bg-white/10 rounded-lg flex items-center justify-center shrink-0">
              <i class="pi pi-users text-sm text-brand-red"></i>
            </div>
            <span class="text-sm text-gray-300">Comunicación directa con las autoridades responsables</span>
          </div>
        </div>
      </div>
    </div>

    <!-- Right Side: Form (TU FORM ORIGINAL CON LOGO 3D Y HEADER MEJORADO) -->
    <div
      class="w-full lg:w-1/2 flex items-center justify-center p-6 lg:p-12 relative overflow-y-auto"
    >
      <div class="w-full max-w-md">
        <!-- Mobile Header (Hidden because 3D Logo is now visible on mobile) -->
        <div class="hidden text-center mb-10">
          <div class="flex justify-center mb-3">
            <div class="w-12 h-12 bg-app-secondary rounded-xl flex items-center justify-center border border-app-border">
              <i class="pi pi-building text-xl text-brand-red"></i>
            </div>
          </div>
          <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Incidencias Urbanas</h1>
          <p class="text-gray-500 dark:text-gray-400 text-sm mt-1">Acceso al Sistema</p>
        </div>

        <!-- Form Card -->
        <div class="bg-white dark:bg-app-card p-8 lg:p-10 rounded-3xl shadow-xl border border-app-border relative transition-colors">
          <!-- Form Header (MEJORADO CON LOGO 3D Y TEXTO SIGIU) -->
          <div class="text-center mb-10">
            <div class="flex items-center justify-center gap-5 mb-8">
              <!-- LOGO 3D CON ORBITA (Tamaño Ajustado para formato horizontal) -->
              <div class="logo-3d-container">
                <div class="logo-orbit"></div>
                <img src="/public/img/jordan.png" alt="Logo Jordan" class="logo-3d" />
              </div>
              
              <!-- TEXTO SIGIU -->
              <div class="flex flex-col text-left">
                <span class="text-[2.75rem] font-black tracking-tight text-gray-900 dark:text-white leading-none">
                  SIG<span class="text-brand-red">IU</span>
                </span>
                <span class="text-[10px] sm:text-xs font-bold text-brand-red uppercase tracking-[0.2em] mt-1.5 whitespace-nowrap">
                  Sistema de Gestión
                </span>
              </div>
            </div>

            <!-- TITULO CAMBIADO A BIENVENIDO -->
            <h1 class="text-3xl font-black text-gray-900 dark:text-white mb-3 tracking-tight">
              Bienvenido
            </h1>
            <!-- LINEAS DECORATIVAS MEJORADAS -->
            <div class="flex justify-center items-center gap-1.5 mb-4">
              <div class="h-1 w-10 bg-gradient-to-r from-brand-red to-[#D1A7B0] rounded-full"></div>
              <div class="h-1 w-3 bg-gray-400/50 rounded-full"></div>
              <div class="h-1 w-3 bg-gray-200/50 rounded-full"></div>
            </div>
            <!-- TEXTO MEJORADO -->
            <p class="text-gray-400 dark:text-white/70 font-medium text-[15px]">
              Ingresa tus credenciales para acceder al sistema
            </p>
          </div>

          <!-- FORMULARIO EXACTAMENTE COMO LO TENIAS -->
          <form @submit.prevent="submit" class="space-y-8">
            <!-- Email Field with FloatLabel -->
            <div class="flex flex-col gap-1">
              <FloatLabel>
                <InputText
                  id="email"
                  v-model="form.email"
                  class="w-full !pl-4 !pr-4 !py-3 !bg-white dark:!bg-white/5 !border-app-border !rounded-xl !text-gray-900 dark:!text-white !text-sm hover:!border-brand-red/50 focus:!border-brand-red focus:!bg-white dark:focus:!bg-white/10 !transition-all !duration-200 shadow-sm"
                  :class="{ '!border-red-500': validationErrors.email || form.errors.email }"
                />
                <label for="email" class="text-gray-500">Correo Electrónico</label>
              </FloatLabel>
              <small v-if="validationErrors.email" class="text-red-500 text-xs ml-1 flex items-center gap-1">
                <i class="pi pi-exclamation-circle text-[10px]"></i>
                {{ validationErrors.email }}
              </small>
            </div>

            <!-- Password Field with FloatLabel -->
            <div class="flex flex-col gap-1">
              <FloatLabel>
                <Password
                  id="password"
                  v-model="form.password"
                  :feedback="false"
                  toggleMask
                  class="w-full"
                  inputClass="w-full !pl-4 !pr-10 !py-3 !bg-white dark:!bg-white/5 !border-app-border !rounded-xl !text-gray-900 dark:!text-white !text-sm hover:!border-brand-red/50 focus:!border-brand-red focus:!bg-white dark:focus:!bg-white/10 !transition-all !duration-200 shadow-sm"
                  :class="{ '!border-red-500': validationErrors.password || form.errors.password }"
                />
                <label for="password" class="text-gray-500 dark:text-white/70">Contraseña</label>
              </FloatLabel>
              <small v-if="validationErrors.password" class="text-red-500 text-xs ml-1 flex items-center gap-1">
                <i class="pi pi-exclamation-circle text-[10px]"></i>
                {{ validationErrors.password }}
              </small>
            </div>

            <!-- Options Row -->
            <div class="flex items-center justify-between pt-1">
              <div class="flex items-center gap-2.5">
                <Checkbox v-model="form.remember" binary inputId="remember" />
                <label for="remember" class="text-sm text-gray-600 dark:text-white/70 cursor-pointer select-none">Recuérdame</label>
              </div>
            </div>

            <!-- Submit Button -->
            <Button
              type="submit"
              label="INGRESAR AL SISTEMA"
              class="w-full !bg-brand-red hover:!bg-black !border-brand-red hover:!border-black !text-white !font-medium !tracking-wide !py-3.5 !rounded-xl !shadow-lg !transition-all !duration-200 hover:!shadow-xl hover:!-translate-y-0.5 active:!translate-y-0"
              :loading="form.processing"
            />
          </form>
        </div>

        <!-- Footer -->
        <div class="text-center mt-6">
          <p class="text-gray-400 text-xs">
            © 2026 Plataforma de Incidencias Urbanas · Gobierno Municipal
          </p>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { useForm } from "@inertiajs/inertia-vue3";
import { reactive } from "vue";
import { useToast } from "primevue/usetoast";

const props = defineProps({
  errors: Object,
});

const toast = useToast();

const form = useForm({
  email: "",
  password: "",
  remember: false,
});

const validationErrors = reactive({
  email: "",
  password: "",
});

const validateEmail = () => {
  if (!form.email.trim()) {
    validationErrors.email = "El correo electrónico es obligatorio.";
    return false;
  }
  const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  if (!emailRegex.test(form.email)) {
    validationErrors.email = "Ingresa un correo electrónico válido.";
    return false;
  }
  validationErrors.email = "";
  return true;
};

const validatePassword = () => {
  if (!form.password.trim()) {
    validationErrors.password = "La contraseña es obligatoria.";
    return false;
  }
  if (form.password.length < 6) {
    validationErrors.password = "La contraseña debe tener al menos 6 caracteres.";
    return false;
  }
  validationErrors.password = "";
  return true;
};

const showToast = (severity, summary, detail) => {
  toast.add({ severity: severity, summary: summary, detail: detail, life: 5000 });
};

const submit = () => {
  const emailValid = validateEmail();
  const passwordValid = validatePassword();

  if (!emailValid) {
    showToast("warn", "Atención", validationErrors.email);
    return;
  }
  if (!passwordValid) {
    showToast("warn", "Atención", validationErrors.password);
    return;
  }

  form.post("/login", {
    onError: (errors) => {
      if (errors.email) {
        showToast("error", "Error de Acceso", errors.email);
      } else {
        showToast("error", "Error", "Ocurrió un error al intentar iniciar sesión.");
      }
    },
  });
};
</script>

<style scoped>
@keyframes orbit {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

@keyframes pulsar {
  0% { transform: translateX(-50%) scale(1); opacity: 1; }
  50% { transform: translateX(-50%) scale(1.5); opacity: 0.7; }
  100% { transform: translateX(-50%) scale(1); opacity: 1; }
}

/* LOGO 3D Container (Ajustado para formato Horizontal) */
.logo-3d-container {
  position: relative;
  width: 100px;
  height: 100px;
  display: flex;
  align-items: center;
  justify-content: center;
  perspective: 1000px;
}

@media (max-width: 640px) {
  .logo-3d-container {
    width: 80px;
    height: 80px;
  }
}

.logo-orbit {
  position: absolute;
  inset: 0;
  border: 2px solid transparent;
  background: linear-gradient(var(--app-card), var(--app-card)) padding-box,
              conic-gradient(from 0deg, var(--brand-red), var(--brand-red-light), var(--brand-red)) border-box;
  opacity: 1;
  border-radius: 50%;
  animation: orbit 10s linear infinite;
  mask: linear-gradient(#fff 0 0) content-box, linear-gradient(#fff 0 0);
  -webkit-mask: linear-gradient(#fff 0 0) content-box, linear-gradient(#fff 0 0);
  mask-composite: exclude;
  -webkit-mask-composite: destination-out;
  filter: drop-shadow(0 0 5px rgba(138, 21, 56, 0.4));
}

.logo-orbit::before {
  content: '';
  position: absolute;
  top: -4px;
  left: 50%;
  width: 8px;
  height: 8px;
  background: var(--brand-red);
  border-radius: 50%;
  transform: translateX(-50%);
  box-shadow: 0 0 10px var(--brand-red), 0 0 20px var(--brand-red);
  animation: pulsar 1.5s ease-in-out infinite;
  z-index: 2;
  border: 1.5px solid white;
}

.logo-3d {
  width: 75px;
  height: 75px;
  object-fit: contain;
  filter: drop-shadow(0 10px 20px rgba(138, 21, 56, 0.4));
  opacity: 1;
  transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
}

@media (max-width: 640px) {
  .logo-3d {
    width: 60px;
    height: 60px;
  }
}

.logo-3d-container:hover .logo-3d {
  transform: scale(1.05) rotateY(10deg);
  filter: drop-shadow(0 20px 50px var(--brand-red));
}

:deep(.p-inputtext:enabled:focus) { box-shadow: 0 0 0 3px var(--brand-red); }
:deep(.p-checkbox-box.p-highlight) { background: var(--brand-red); border-color: var(--brand-red); }
:deep(.p-password input) { width: 100%; }
:deep(.p-float-label label) { margin-left: 0.5rem; color: #6b7280; transition: color 0.3s ease; }
.dark :deep(.p-float-label label) { color: rgba(255, 255, 255, 0.7); }
.dark :deep(input::placeholder) { color: rgba(255, 255, 255, 0.4); }
:deep(.p-inputtext:focus ~ label),
:deep(.p-inputtext.p-filled ~ label),
:deep(.p-inputwrapper-focus ~ label),
:deep(.p-inputwrapper-filled ~ label) {
  background-color: transparent;
  color: var(--brand-red);
  font-weight: 600;
  transition: color 0.3s ease;
}
</style>

<script>
export default {
  layout: null,
};
</script>