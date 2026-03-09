import './bootstrap'
import '../css/app.css'
import { createApp, h } from 'vue'
import { createInertiaApp } from '@inertiajs/inertia-vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import PrimeVue from 'primevue/config'
import 'primevue/resources/themes/lara-light-blue/theme.css'
import 'primevue/resources/primevue.min.css'
import 'primeicons/primeicons.css'

// PrimeVue Components
import Button from 'primevue/button'
import InputText from 'primevue/inputtext'
import Password from 'primevue/password'
import Checkbox from 'primevue/checkbox'
import Dropdown from 'primevue/dropdown'
import ToastService from 'primevue/toastservice'
import Toast from 'primevue/toast'
import ConfirmationService from 'primevue/confirmationservice'
import ConfirmDialog from 'primevue/confirmdialog'
import Ripple from 'primevue/ripple'
import Tooltip from 'primevue/tooltip'
import DataTable from 'primevue/datatable'
import Column from 'primevue/column'
import Dialog from 'primevue/dialog'
import Tag from 'primevue/tag'
import Message from 'primevue/message'
import FloatLabel from 'primevue/floatlabel'
import Sidebar from 'primevue/sidebar'
import Avatar from 'primevue/avatar'
import Menu from 'primevue/menu'
import Badge from 'primevue/badge'
import Chart from 'primevue/chart'
import PageHeader from '@/Components/PageHeader.vue'
import DataCards from '@/Components/DataCards.vue'
import StatsGrid from '@/Components/StatsGrid.vue'

const pages = import.meta.glob('./views/**/*.vue')

createInertiaApp({
  resolve: name => pages[`./views/${name}.vue`]().then(module => {
    const page = module.default
    if (typeof page.layout === 'undefined') {
      page.layout = AppLayout
    }
    return page
  }),
  setup({ el, App, props, plugin }) {
    const vueApp = createApp({ render: () => h(App, props) })

    vueApp.use(plugin)
    vueApp.use(PrimeVue, { ripple: true })
    vueApp.use(ToastService)
    vueApp.use(ConfirmationService)

    // Global Component Registration
    vueApp.component('Button', Button)
    vueApp.component('InputText', InputText)
    vueApp.component('Password', Password)
    vueApp.component('Checkbox', Checkbox)
    vueApp.component('Dropdown', Dropdown)
    vueApp.component('Toast', Toast)
    vueApp.component('ConfirmDialog', ConfirmDialog)
    vueApp.component('DataTable', DataTable)
    vueApp.component('Column', Column)
    vueApp.component('Dialog', Dialog)
    vueApp.component('Tag', Tag)
    vueApp.component('Message', Message)
    vueApp.component('FloatLabel', FloatLabel)
    vueApp.component('Sidebar', Sidebar)
    vueApp.component('Avatar', Avatar)
    vueApp.component('Menu', Menu)
    vueApp.component('Badge', Badge)
    vueApp.component('Chart', Chart)
    vueApp.component('PageHeader', PageHeader)
    vueApp.component('DataCards', DataCards)
    vueApp.component('StatsGrid', StatsGrid)

    vueApp.directive('ripple', Ripple)
    vueApp.directive('tooltip', Tooltip)

    vueApp.mount(el)
  },
})