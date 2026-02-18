<template>
  <div class="min-h-screen w-full flex overflow-hidden bg-[#F5F5F7]">
    <Toast position="top-right" />

    <!-- Left Side: Visual / Branding -->
    <div
      class="hidden lg:flex lg:w-1/2 relative bg-black justify-center items-center overflow-hidden"
    >
      <!-- Background Image (Urban / City) -->
      <div
        class="absolute inset-0 bg-cover bg-center opacity-90 transition-transform duration-[30s] hover:scale-105"
        style="
          background-image: url('https://images.unsplash.com/photo-1477959858617-67f85cf4f1df?q=80&w=2644&auto=format&fit=crop');
        "
      ></div>

      <!-- Gradient Overlay -->
      <div
        class="absolute inset-0 bg-gradient-to-t from-black via-black/50 to-black/30"
      ></div>

      <!-- Content -->
      <div class="relative z-10 text-white max-w-xl px-12">
        <!-- Logo / Brand Header -->
        <div class="flex items-center gap-4 mb-10">
          <div
            class="w-14 h-14 bg-white/10 backdrop-blur-md rounded-2xl flex items-center justify-center border border-white/10"
          >
            <i class="pi pi-building text-2xl text-[#BFDDEA]"></i>
          </div>
          <div class="flex flex-col">
            <span
              class="text-[11px] tracking-[0.25em] font-medium text-[#A7C5D2] uppercase"
              >Plataforma Municipal</span
            >
            <span class="text-xl font-bold tracking-wide">Incidencias Urbanas</span>
          </div>
        </div>

        <!-- Main Headline -->
        <h2 class="text-4xl font-extrabold mb-5 leading-tight">
          <span class="block">Reporta, Supervisa</span>
          <span class="block">y Mejora tu</span>
          <span
            class="text-transparent bg-clip-text bg-gradient-to-r from-white to-[#607C88]"
            >Ciudad</span
          >
        </h2>

        <!-- Accent Line -->
        <div class="flex gap-1 mb-6">
          <div class="h-1 w-12 bg-[#607C88] rounded-full"></div>
          <div class="h-1 w-6 bg-white/30 rounded-full"></div>
          <div class="h-1 w-6 bg-white/10 rounded-full"></div>
        </div>

        <p class="text-base text-gray-400 font-light leading-relaxed max-w-md">
          Plataforma digital para el registro, seguimiento y resolución de incidencias en
          infraestructura y servicios públicos de tu comunidad.
        </p>

        <!-- Feature Highlights -->
        <div class="mt-10 space-y-4">
          <div class="flex items-center gap-3">
            <div
              class="w-9 h-9 bg-white/10 rounded-lg flex items-center justify-center shrink-0"
            >
              <i class="pi pi-map-marker text-sm text-[#BFDDEA]"></i>
            </div>
            <span class="text-sm text-gray-300"
              >Geolocaliza y reporta problemas urbanos al instante</span
            >
          </div>
          <div class="flex items-center gap-3">
            <div
              class="w-9 h-9 bg-white/10 rounded-lg flex items-center justify-center shrink-0"
            >
              <i class="pi pi-chart-line text-sm text-[#BFDDEA]"></i>
            </div>
            <span class="text-sm text-gray-300"
              >Seguimiento en tiempo real del estado de tus reportes</span
            >
          </div>
          <div class="flex items-center gap-3">
            <div
              class="w-9 h-9 bg-white/10 rounded-lg flex items-center justify-center shrink-0"
            >
              <i class="pi pi-users text-sm text-[#BFDDEA]"></i>
            </div>
            <span class="text-sm text-gray-300"
              >Comunicación directa con las autoridades responsables</span
            >
          </div>
        </div>
      </div>
    </div>

    <!-- Right Side: Form -->
    <div
      class="w-full lg:w-1/2 flex items-center justify-center p-6 lg:p-12 relative overflow-y-auto"
    >
      <div class="w-full max-w-md">
        <!-- Mobile Header -->
        <div class="lg:hidden text-center mb-10">
          <div class="flex justify-center mb-3">
            <div
              class="w-12 h-12 bg-gray-100 rounded-xl flex items-center justify-center border border-gray-200"
            >
              <i class="pi pi-building text-xl text-[#607C88]"></i>
            </div>
          </div>
          <h1 class="text-2xl font-bold text-gray-900">Incidencias Urbanas</h1>
          <p class="text-gray-500 text-sm mt-1">Acceso al Sistema</p>
        </div>

        <!-- Form Card -->
        <div
          class="bg-white p-8 lg:p-10 rounded-3xl shadow-xl border border-gray-100 relative"
        >
          <!-- Form Header -->
          <div class="text-center mb-10">
            <div class="hidden lg:flex justify-center mb-6">
              <div class="relative group animate-float">
                <!-- Logo Image - animado -->
                <img
                  src="/public/img/jordan.png"
                  alt="Logo Jordan"
                  class="w-48 h-auto object-contain relative z-10 drop-shadow-2xl transition-all duration-500 group-hover:scale-110 active:scale-95"
                />

                <!-- Sutil resplandor de fondo pulsante -->
                <div
                  class="absolute inset-x-0 -bottom-2 bg-[#607C88]/20 h-4 w-32 mx-auto blur-xl rounded-full animate-shadow-pulse"
                ></div>
              </div>
            </div>

            <h1 class="text-3xl font-bold text-gray-900 mb-3 tracking-tight">
              Iniciar Sesión
            </h1>

            <div class="flex justify-center items-center gap-1.5 mb-4">
              <div class="h-1 w-10 bg-[#607C88] rounded-full"></div>
              <div class="h-1 w-3 bg-[#90ADB9]/50 rounded-full"></div>
              <div class="h-1 w-3 bg-[#BFDDEA]/50 rounded-full"></div>
            </div>

            <p class="text-gray-400 font-medium text-[15px]">
              Bienvenido de nuevo, por favor ingresa tus datos.
            </p>
          </div>

          <form @submit.prevent="submit" class="space-y-8">
            <!-- Email Field with FloatLabel -->
            <div class="flex flex-col gap-1">
              <FloatLabel>
                <InputText
                  id="email"
                  v-model="form.email"
                  class="w-full !pl-4 !pr-4 !py-3 !bg-gray-50 !border-gray-200 !rounded-xl !text-gray-900 !text-sm hover:!border-[#90ADB9] focus:!border-[#607C88] focus:!bg-white !transition-all !duration-200"
                  :class="{
                    '!border-red-500': validationErrors.email || form.errors.email,
                  }"
                />
                <label for="email" class="text-gray-500">Correo Electrónico</label>
              </FloatLabel>
              <small
                v-if="validationErrors.email"
                class="text-red-500 text-xs ml-1 flex items-center gap-1"
              >
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
                  inputClass="w-full !pl-4 !pr-10 !py-3 !bg-gray-50 !border-gray-200 !rounded-xl !text-gray-900 !text-sm hover:!border-[#90ADB9] focus:!border-[#607C88] focus:!bg-white !transition-all !duration-200"
                  :class="{
                    '!border-red-500': validationErrors.password || form.errors.password,
                  }"
                />
                <label for="password" class="text-gray-500">Contraseña</label>
              </FloatLabel>
              <small
                v-if="validationErrors.password"
                class="text-red-500 text-xs ml-1 flex items-center gap-1"
              >
                <i class="pi pi-exclamation-circle text-[10px]"></i>
                {{ validationErrors.password }}
              </small>
            </div>

            <!-- Options Row -->
            <div class="flex items-center justify-between pt-1">
              <div class="flex items-center gap-2.5">
                <Checkbox v-model="form.remember" binary inputId="remember" />
                <label
                  for="remember"
                  class="text-sm text-gray-600 cursor-pointer select-none"
                  >Recuérdame</label
                >
              </div>
            </div>

            <!-- Submit Button -->
            <Button
              type="submit"
              label="INGRESAR AL SISTEMA"
              class="w-full !bg-[#607C88] hover:!bg-black !border-[#607C88] hover:!border-black !text-white !font-medium !tracking-wide !py-3.5 !rounded-xl !shadow-lg !transition-all !duration-200 hover:!shadow-xl hover:!-translate-y-0.5 active:!translate-y-0"
              :loading="form.processing"
            />

            <!-- Register Link -->
            <!-- <div class="text-center pt-2 text-sm text-gray-500">
              ¿No tienes cuenta? 
              <Link href="/register" class="font-bold text-[#607C88] hover:text-[#4a6069] transition-colors">
                Regístrate aquí
              </Link>
            </div> -->
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
@keyframes float {
  0% {
    transform: translateY(0px);
  }
  50% {
    transform: translateY(-10px);
  }
  100% {
    transform: translateY(0px);
  }
}

@keyframes bounceIn {
  0% {
    opacity: 0;
    transform: scale(0.3);
  }
  50% {
    opacity: 0.9;
    transform: scale(1.1);
  }
  80% {
    opacity: 1;
    transform: scale(0.89);
  }
  100% {
    opacity: 1;
    transform: scale(1);
  }
}

@keyframes shadowPulse {
  0% {
    transform: scale(1);
    opacity: 0.2;
  }
  50% {
    transform: scale(1.3);
    opacity: 0.4;
  }
  100% {
    transform: scale(1);
    opacity: 0.2;
  }
}

.animate-float {
  animation: float 4s ease-in-out infinite;
}

.animate-bounce-in {
  animation: bounceIn 0.8s cubic-bezier(0.68, -0.55, 0.265, 1.55) forwards;
}

.animate-shadow-pulse {
  animation: shadowPulse 4s ease-in-out infinite;
}

/* Solo estilos críticos irremplazables por Tailwind */
:deep(.p-inputtext:enabled:focus) {
  box-shadow: 0 0 0 3px rgba(96, 124, 136, 0.1);
}
:deep(.p-checkbox-box.p-highlight) {
  background: #607c88;
  border-color: #607c88;
}
:deep(.p-password input) {
  width: 100%;
}
/* Estilo para que el FloatLabel se vea bien con el diseño específico */
:deep(.p-float-label label) {
  margin-left: 0.5rem;
  color: #6b7280; /* text-gray-500 */
}
:deep(.p-inputtext:focus ~ label),
:deep(.p-inputtext.p-filled ~ label) {
  background-color: transparent;
  /* Ajuste para que la etiqueta flotante se destaque al subir */
  color: #607c88;
  font-weight: 600;
}
</style>
<script>
export default {
  layout: null,
};
</script>
