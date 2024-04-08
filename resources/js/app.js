import 'bootstrap/dist/css/bootstrap.min.css';
import 'bootstrap/dist/js/bootstrap.bundle.min.js';
import 'bootstrap-icons/font/bootstrap-icons.min.css'
import 'animate.css';

import './realtime-setup.js'

import {createApp, h} from "vue";
import {createInertiaApp} from "@inertiajs/vue3";
import {ZiggyVue} from "ziggy-js";
import MainLayout from "./Layouts/MainLayout.vue";
import AudioPlayer from '@liripeng/vue-audio-player'
import {createPinia} from 'pinia'
import ToastPlugin from 'vue-toast-notification';
import 'vue-toast-notification/dist/theme-bootstrap.css';
import FloatingVue from 'floating-vue'
import 'floating-vue/dist/style.css'

var elem = document.getElementById("app");
elem.className += "d-flex flex-column min-vh-100";

const pinia = createPinia()

createInertiaApp({
    resolve: (name) => {
        const pages = import.meta.glob("./Pages/**/*.vue", {eager: true});
        const page = pages[`./Pages/${name}.vue`];

        page.default.layout = page.default.layout || MainLayout;

        return page;
    },
    setup({el, App, props, plugin}) {
        createApp({render: () => h(App, props)})
            .use(plugin)
            .use(ZiggyVue)
            .use(ToastPlugin)
            .use(FloatingVue)
            .use(pinia)
            .use(AudioPlayer)
            .mount(el);
    },
});
