import './bootstrap';

import { createApp, h } from 'vue'
import { createInertiaApp } from '@inertiajs/vue3'

const pages = import.meta.glob('./Pages/**/*.vue');

createInertiaApp({
    resolve: name => {
        const importPage = pages[`./Pages/${name}.vue`];
        if (!importPage) throw new Error(`Page ${name} not found`);
        return importPage();
    },
    setup({ el, App, props, plugin }) {
        createApp({ render: () => h(App, props) })
            .use(plugin)
            .mount(el)
    },
})
