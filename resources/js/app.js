import 'bootstrap/dist/css/bootstrap.min.css';
import 'bootstrap/dist/js/bootstrap.min.js';
import 'bootstrap-icons/font/bootstrap-icons.min.css'
import 'animate.css';

import './realtime-setup.js'

import {createApp, h} from "vue";
import {createInertiaApp} from "@inertiajs/vue3";
import {ZiggyVue} from "ziggy-js";
import MainLayout from "./Layouts/MainLayout.vue";
import AudioPlayer from '@liripeng/vue-audio-player'

var elem = document.getElementById("app");
elem.className += "d-flex flex-column min-vh-100";

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
            .use(AudioPlayer)
            .mount(el);
    },
});
