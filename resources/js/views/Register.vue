<script setup>
import { useForm, Link } from '@inertiajs/inertia-vue3'

const props = defineProps({
  roles: Array,
  errors: Object
})

const form = useForm({
  name: '',
  email: '',
  password: '',
  password_confirmation: '',
  rol: '', 
})

const submit = () => {
  form.post('/register')
}
</script>

<template>
  <div class="min-h-screen w-full flex bg-[#F5F5F7] font-sans selection:bg-[#607C88]/30">
    <!-- Left Side: Visual / Branding (Apple Style) -->
    <div class="hidden lg:flex lg:w-5/12 relative bg-black justify-center items-center overflow-hidden">
      <!-- Background Image with sophisticated blur/overlay -->
      <div 
        class="absolute inset-0 bg-cover bg-center opacity-100 scale-105"
        style="background-image: url('https://images.unsplash.com/photo-1518105779142-d975f22f1b0a?q=80&w=2670&auto=format&fit=crop');"
      ></div>
      
      <!-- Glass Overlay -->
      <div class="absolute inset-0 bg-black/20 backdrop-blur-[1px]"></div>

      <div class="relative z-10 p-12 w-full max-w-lg">
        <!-- Minimalist Brand Header -->
        <div class="flex items-center gap-4 mb-12">
            <div class="w-10 h-10 bg-white/10 backdrop-blur-xl rounded-2xl flex items-center justify-center border border-white/20 shadow-lg">
                <i class="pi pi-shield text-lg text-[#BFDDEA]"></i>
            </div>
            <div>
                <h3 class="text-white text-sm font-medium tracking-wide uppercase opacity-90">República Digital</h3>
                <p class="text-[#A7C5D2] text-xs tracking-wider">Gestión Ciudadana</p>
            </div>
        </div>

        <!-- Typography Headline -->
        <h1 class="text-6xl font-semibold text-white mb-6 tracking-tight leading-tight">
          Un nuevo <br />
          <span class="text-transparent bg-clip-text bg-gradient-to-br from-white via-[#BFDDEA] to-[#607C88]">estándar.</span>
        </h1>
        
        <p class="text-lg text-white/80 font-normal leading-relaxed max-w-sm mb-12">
          La plataforma diseñada para transformar la manera en que conectamos contigo. Simple. Segura. Eficiente.
        </p>

        <!-- Subtle Slate Indicator -->
        <div class="flex gap-2 items-center opacity-80">
            <div class="h-1 w-8 bg-[#607C88] rounded-full blur-[1px]"></div>
            <div class="h-1 w-8 bg-white rounded-full blur-[1px]"></div>
            <div class="h-1 w-8 bg-[#90ADB9] rounded-full blur-[1px]"></div>
        </div>
      </div>
    </div>

    <!-- Right Side: Form (Clean, Spacious, Airy) -->
    <div class="w-full lg:w-7/12 flex items-center justify-center p-6 lg:p-16 bg-white relative">
      <div class="w-full max-w-[420px] animate-fade-in-up">
        
        <!-- Header -->
        <div class="text-center mb-10">
           <div class="inline-block p-3 rounded-full bg-gray-50 mb-4 lg:hidden">
                <i class="pi pi-shield text-xl text-[#607C88]"></i>
           </div>
           <h2 class="text-3xl font-semibold text-gray-900 mb-2 tracking-tight">Crear una cuenta</h2>
           <p class="text-gray-500 text-[15px]">Ingresa tus datos para comenzar.</p>
        </div>

        <form @submit.prevent="submit" class="space-y-5">
            
            <!-- Rol Selection -->
            <div class="group">
                <label class="block text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1.5 ml-1">Tipo de Usuario</label>
                <Dropdown 
                    v-model="form.rol" 
                    :options="roles" 
                    optionLabel="nombre"
                    optionValue="nombre"
                    placeholder="Selecciona tu rol" 
                    class="w-full apple-dropdown"
                    :class="{'p-invalid': form.errors.rol}"
                />
                <small v-if="form.errors.rol" class="text-red-500 text-xs ml-1 mt-1 block">{{ form.errors.rol }}</small>
            </div>

            <!-- Name Field -->
            <div class="group">
                <label for="name" class="block text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1.5 ml-1">Nombre Completo</label>
                <InputText 
                  id="name" 
                  v-model="form.name" 
                  class="apple-input" 
                  :class="{'p-invalid': form.errors.name}" 
                  placeholder="Juan Pérez"
                />
                <small v-if="form.errors.name" class="text-red-500 text-xs ml-1 mt-1 block">{{ form.errors.name }}</small>
            </div>

            <!-- Email Field -->
            <div class="group">
                <label for="email" class="block text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1.5 ml-1">Correo Electrónico</label>
                <InputText 
                  id="email" 
                  v-model="form.email" 
                  class="apple-input" 
                  :class="{'p-invalid': form.errors.email}" 
                  placeholder="nombre@ejemplo.com"
                />
                <small v-if="form.errors.email" class="text-red-500 text-xs ml-1 mt-1 block">{{ form.errors.email }}</small>
            </div>

            <!-- Password Field -->
            <div class="group">
                <label for="password" class="block text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1.5 ml-1">Contraseña</label>
                <Password 
                  id="password" 
                  v-model="form.password" 
                  toggleMask 
                  class="w-full" 
                  inputClass="apple-input w-full" 
                  :class="{'p-invalid': form.errors.password}"
                  placeholder="••••••••"
                  :feedback="true"
                />
                <small v-if="form.errors.password" class="text-red-500 text-xs ml-1 mt-1 block">{{ form.errors.password }}</small>
            </div>

            <!-- Password Confirmation -->
            <div class="group">
                <label for="password_confirmation" class="block text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1.5 ml-1">Confirmar Contraseña</label>
                <Password 
                  id="password_confirmation" 
                  v-model="form.password_confirmation" 
                  :feedback="false" 
                  toggleMask 
                  class="w-full" 
                  inputClass="apple-input w-full" 
                  :class="{'p-invalid': form.errors.password_confirmation}"
                  placeholder="••••••••"
                />
            </div>

            <!-- Submit Button (Slate Blue Theme) -->
            <div class="pt-2">
                <Button 
                    type="submit" 
                    label="Continuar" 
                    class="w-full !bg-[#607C88] hover:!bg-black !border-[#607C88] hover:!border-black !text-white !font-medium !text-[15px] !py-3.5 !rounded-lg !shadow-none transition-all duration-300 transform active:scale-[0.98]" 
                    :loading="form.processing"
                />
            </div>

            <div class="mt-8 text-center">
                <p class="text-[13px] text-gray-400">
                    ¿Ya tienes una cuenta? 
                    <Link href="/login" class="text-[#607C88] hover:text-[#4a6069] hover:underline font-medium ml-1 transition-colors">
                        Inicia sesión
                    </Link>
                </p>
            </div>
        </form>

        <!-- Terms Footer -->
        <div class="mt-12 pt-6 border-t border-gray-100 text-center">
            <p class="text-[11px] text-gray-400 leading-normal">
                Al registrarte, aceptas nuestros <a href="#" class="underline hover:text-gray-600">Términos de Servicio</a> y <a href="#" class="underline hover:text-gray-600">Política de Privacidad</a>.
                <br>Protegido por reCAPTCHA.
            </p>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
/* Apple-style smooth inputs */
.apple-input {
    width: 100%;
    background-color: #f5f5f7; /* Apple light gray */
    border: 1px solid transparent;
    border-radius: 12px; /* Smooth rounded corners */
    padding: 0.8rem 1rem;
    font-size: 15px;
    color: #1d1d1f;
    transition: all 0.2s ease;
}

.apple-input:hover {
    background-color: #e8e8ed;
}

.apple-input:focus {
    background-color: #ffffff;
    border-color: #607C88; /* Slate Blue Focus */
    box-shadow: 0 0 0 4px rgba(96, 124, 136, 0.1);
    outline: none;
}

.apple-input::placeholder {
    color: #86868b;
}

/* Dropdown Customization */
:deep(.p-dropdown) {
    background: #f5f5f7;
    border: 1px solid transparent;
    border-radius: 12px;
    transition: all 0.2s ease;
}
:deep(.p-dropdown:not(.p-disabled):hover) {
    background: #e8e8ed;
    border-color: transparent;
}
:deep(.p-dropdown:not(.p-disabled).p-focus) {
    background: #ffffff;
    border-color: #607C88;
    box-shadow: 0 0 0 4px rgba(96, 124, 136, 0.1);
}
:deep(.p-dropdown-label) {
    padding: 0.8rem 1rem;
    font-size: 15px;
}
:deep(.p-inputtext) {
    font-family: inherit;
}

/* Custom Animation */
@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.animate-fade-in-up {
    animation: fadeInUp 0.6s cubic-bezier(0.16, 1, 0.3, 1) forwards;
}
</style>

<script>
export default {
  layout: null,
}
</script>
