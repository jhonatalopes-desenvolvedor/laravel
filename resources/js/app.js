import './bootstrap'

import { initFlowbite } from 'flowbite'
import { HSStaticMethods } from 'preline'
import { createApp, h } from 'vue'
import { createPinia } from 'pinia'
import { createInertiaApp, router } from '@inertiajs/vue3'
import { ZiggyVue } from 'ziggy-js'
import { Ziggy } from '@/ziggy.js'

import Toast from 'vue-toastification'

Ziggy.url = window.location.origin

createInertiaApp({
    resolve: (name) => {
        const pages = import.meta.glob('./Pages/**/*.vue')
        return pages[`./Pages/${name}.vue`]()
    },
    setup({ el, App, props, plugin }) {
        const app = createApp({ render: () => h(App, props) })

        app.use(plugin)
        app.use(createPinia())
        app.use(ZiggyVue, Ziggy)
        app.use(Toast, {})
        app.mount(el)
    },
})

router.on('navigate', () => {
    initFlowbite()
    HSStaticMethods.autoInit()
})
