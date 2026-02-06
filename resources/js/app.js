import './bootstrap'
import { createApp, h } from 'vue'
import { createInertiaApp } from '@inertiajs/inertia-vue3'
import PrimeVue from 'primevue/config'
import 'primevue/resources/themes/saga-blue/theme.css'
import 'primevue/resources/primevue.min.css'
import 'primeicons/primeicons.css'
import Button from 'primevue/button'

// Glob para importar todas las vistas automáticamente
const pages = import.meta.glob('./views/**/*.vue')

createInertiaApp({
  resolve: name => pages[`./views/${name}.vue`]().then(module => module.default),
  setup({ el, App, props, plugin }) {
    const vueApp = createApp({ render: () => h(App, props) })
    vueApp.use(plugin)
    vueApp.use(PrimeVue)
    vueApp.component('Button', Button)
    vueApp.mount(el)
  }
})