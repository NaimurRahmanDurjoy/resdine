import './bootstrap';

import { createApp, h } from 'vue'
import { createInertiaApp, Link, Head } from '@inertiajs/vue3'
import { route } from 'ziggy-js' // Ziggy for use route helper in Vue components

const pages = import.meta.glob('./Pages/**/*.vue');

createInertiaApp({
    title: (title) => title ? `${title} | Resdine Admin` : 'Resdine Admin',
    resolve: name => {
        const importPage = pages[`./Pages/${name}.vue`];
        if (!importPage) throw new Error(`Page ${name} not found`);
        return importPage();
    },
    setup({ el, App, props, plugin }) {
        const app = createApp({ render: () => h(App, props) })
        app.use(plugin)
        app.component('Link', Link)
        app.component('Head', Head)

        app.config.globalProperties.route = (name, params, absolute) => route(name, params, absolute) // optional ziggy
        app.mount(el)
    },
})
