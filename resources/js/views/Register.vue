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
  <div class="min-h-screen w-full flex overflow-hidden bg-gray-50">
    <!-- Left Side: Visual / Branding (Mexico Theme) -->
    <div class="hidden lg:flex lg:w-1/2 relative bg-gray-900 justify-center items-center overflow-hidden">
      <!-- Background Image -->
      <div 
        class="absolute inset-0 bg-cover bg-center opacity-70 transform hover:scale-105 transition-transform duration-[30s]"
        style="background-image: url('https://images.unsplash.com/photo-1518105779142-d975f22f1b0a?q=80&w=2670&auto=format&fit=crop');"
      ></div>
      
      <!-- Gradient Overlay -->
      <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/40 to-black/60"></div>

      <!-- Content -->
      <div class="relative z-10 text-white max-w-xl px-12">
        <!-- Logo / Brand Header -->
        <div class="flex items-center gap-4 mb-8">
             <div class="w-12 h-12 bg-white/10 backdrop-blur-md rounded-full flex items-center justify-center border border-white/20">
                <i class="pi pi-shield text-2xl"></i>
            </div>
            <div class="flex flex-col">
                <span class="text-sm tracking-[0.2em] font-light text-gray-300">REPÚBLICA DIGITAL</span>
                <span class="text-xl font-bold tracking-wider">GESTIÓN CIUDADANA</span>
            </div>
        </div>

        <!-- Main Headline -->
        <h2 class="text-5xl font-extrabold mb-6 leading-tight">
          <span class="block">Únete a la</span>
          <span class="text-transparent bg-clip-text bg-gradient-to-r from-green-400 via-white to-red-400">Transformación</span>
        </h2>
        
        <div class="w-24 h-1.5 bg-gradient-to-r from-green-600 via-white to-red-600 rounded-full mb-8"></div>

        <p class="text-lg text-gray-200 font-light leading-relaxed max-w-lg">
          Sé parte del cambio. Regístrate en la plataforma nacional para el reporte y atención de servicios públicos.
        </p>

        <!-- Stats or Badges -->
        <div class="mt-12 flex gap-8 border-t border-white/10 pt-8">
             <div>
                <span class="block text-3xl font-bold">100%</span>
                <span class="text-xs text-gray-400 uppercase tracking-wider">Transparencia</span>
             </div>
             <div>
                <span class="block text-3xl font-bold">24/7</span>
                <span class="text-xs text-gray-400 uppercase tracking-wider">Atención Digital</span>
             </div>
        </div>
      </div>
    </div>

    <!-- Right Side: Form -->
    <div class="w-full lg:w-1/2 flex items-center justify-center p-6 lg:p-12 relative overflow-y-auto">
      <div class="w-full max-w-md">
        
        <!-- Mobile Header (Visible only on small screens) -->
        <div class="lg:hidden text-center mb-10">
           <h1 class="text-2xl font-bold text-gray-800">Gestión Ciudadana</h1>
           <p class="text-gray-500 text-sm">Registro de Usuarios</p>
        </div>

        <div class="bg-white p-8 lg:p-10 rounded-3xl shadow-2xl border border-gray-100">
            <!-- Form Header inside card -->
            <div class="text-center mb-10">
                <h1 class="text-3xl font-bold text-gray-800 mb-2">Crear Cuenta</h1>
                 <div class="flex justify-center gap-1 mb-2">
                    <div class="h-1 w-6 bg-green-600 rounded-full"></div>
                    <div class="h-1 w-6 bg-gray-300 rounded-full"></div>
                    <div class="h-1 w-6 bg-red-600 rounded-full"></div>
                </div>
                <p class="text-gray-500 text-sm">Ingresa tus datos para registrarte</p>
            </div>

            <form @submit.prevent="submit" class="space-y-6">
            
            <!-- Rol Selection -->
            <div class="flex flex-col gap-2">
                <label class="text-xs font-bold text-gray-500 uppercase tracking-wider ml-1">Tipo de Usuario</label>
                <Dropdown 
                    v-model="form.rol" 
                    :options="roles" 
                    optionLabel="nombre"
                    optionValue="nombre"
                    placeholder="Selecciona tu rol" 
                    class="w-full p-inputtext-sm"
                    :class="{'p-invalid': form.errors.rol}"
                />
                <small v-if="form.errors.rol" class="text-red-500 text-xs ml-1">{{ form.errors.rol }}</small>
            </div>

            <!-- Name Field -->
            <div class="flex flex-col gap-2">
                <label for="name" class="text-xs font-bold text-gray-500 uppercase tracking-wider ml-1">Nombre Completo</label>
                <div class="relative">
                     <i class="pi pi-user absolute left-5 top-1/2 -translate-y-1/2 text-gray-400 z-10"></i>
                    <InputText 
                      id="name" 
                      v-model="form.name" 
                      class="w-full pl-14 bg-gray-50 border-gray-200 focus:bg-white transition-colors" 
                      :class="{'p-invalid': form.errors.name}" 
                      placeholder="Ej. Juan Pérez"
                    />
                </div>
                <small v-if="form.errors.name" class="text-red-500 text-xs ml-1">{{ form.errors.name }}</small>
            </div>

            <!-- Email Field -->
            <div class="flex flex-col gap-2">
                <label for="email" class="text-xs font-bold text-gray-500 uppercase tracking-wider ml-1">Correo Electrónico</label>
                <div class="relative">
                    <i class="pi pi-envelope absolute left-5 top-1/2 -translate-y-1/2 text-gray-400 z-10"></i>
                    <InputText 
                      id="email" 
                      v-model="form.email" 
                      class="w-full pl-14 bg-gray-50 border-gray-200 focus:bg-white transition-colors" 
                      :class="{'p-invalid': form.errors.email}" 
                      placeholder="correo@ejemplo.com"
                    />
                </div>
                <small v-if="form.errors.email" class="text-red-500 text-xs ml-1">{{ form.errors.email }}</small>
            </div>

            <!-- Password Field -->
            <div class="flex flex-col gap-2">
                <label for="password" class="text-xs font-bold text-gray-500 uppercase tracking-wider ml-1">Contraseña</label>
                <div class="relative">
                     <i class="pi pi-lock absolute left-5 top-1/2 -translate-y-1/2 text-gray-400 z-10"></i>
                    <Password 
                      id="password" 
                      v-model="form.password" 
                      toggleMask 
                      class="w-full" 
                      inputClass="w-full pl-14 bg-gray-50 border-gray-200 focus:bg-white" 
                      :class="{'p-invalid': form.errors.password}"
                      placeholder="••••••••"
                      :feedback="true"
                    />
                </div>
                <small v-if="form.errors.password" class="text-red-500 text-xs ml-1">{{ form.errors.password }}</small>
            </div>

            <!-- Password Confirmation -->
            <div class="flex flex-col gap-2">
                <label for="password_confirmation" class="text-xs font-bold text-gray-500 uppercase tracking-wider ml-1">Confirmar Contraseña</label>
                 <div class="relative">
                    <i class="pi pi-check-circle absolute left-5 top-1/2 -translate-y-1/2 text-gray-400 z-10"></i>
                    <Password 
                      id="password_confirmation" 
                      v-model="form.password_confirmation" 
                      :feedback="false" 
                      toggleMask 
                      class="w-full" 
                      inputClass="w-full pl-14 bg-gray-50 border-gray-200 focus:bg-white" 
                      :class="{'p-invalid': form.errors.password_confirmation}"
                      placeholder="••••••••"
                    />
                </div>
            </div>

            <!-- Submit Button -->
            <Button 
                type="submit" 
                label="REGISTRARME" 
                class="w-full !bg-green-700 hover:!bg-green-800 !border-green-700 !text-white font-bold tracking-wide py-3 shadow-lg shadow-green-700/30 transition-all transform hover:-translate-y-0.5" 
                :loading="form.processing"
            />

            <div class="mt-8 text-center text-sm text-gray-500">
                ¿Ya tienes cuenta? 
                <Link href="/login" class="font-bold text-green-700 hover:text-green-900 transition-colors">
                Ingresa aquí
                </Link>
            </div>
            </form>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
/* PrimeVue Overrides for this theme */
:deep(.p-dropdown) {
    background: #f9fafb;
    border-color: #e5e7eb;
}
:deep(.p-dropdown:not(.p-disabled).p-focus) {
    background: #ffffff;
    border-color: #15803d; /* Green-700 */
    box-shadow: 0 0 0 1px #15803d;
}
:deep(.p-inputtext) {
    padding-top: 0.75rem;
    padding-bottom: 0.75rem;
}
:deep(.p-inputtext:enabled:focus) {
    border-color: #15803d; /* Green-700 */
    box-shadow: 0 0 0 1px #15803d;
}
:deep(.p-password input) {
    width: 100%;
}

/* Ensure PrimeVue input doesn't override our left padding */
:deep(.p-inputtext) {
  padding-left: 3.5rem; /* pl-14 */
}
</style>
