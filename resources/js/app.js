import 'bootstrap/dist/css/bootstrap.min.css';
import 'bootstrap/dist/js/bootstrap.min.js';
import 'bootstrap-icons/font/bootstrap-icons.min.css'

import {createApp, h} from "vue";
import {createInertiaApp} from "@inertiajs/vue3";
import {ZiggyVue} from "ziggy-js";
import MainLayout from "./Layouts/MainLayout.vue";

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
            .mount(el);
    },
});

// Echo.private('status-notifications').listen('UserActivity', (e) => {
//     const notificationElement = document.getElementById('status-notification');

//     e.users.forEach(user => {
//         const userId = user.id;
//         const message = e.message;
//         const type = e.type;

//         if (userId === currentUserId) {
//             notificationElement.innerText = message;
//             notificationElement.classList.remove('d-none');
//             notificationElement.classList.add('alert-' + type);
//             setTimeout(() => {
//                 notificationElement.classList.add('d-none');
//                 notificationElement.classList.remove('alert-' + type);
//             }, 5000);
//         }
//     });
// })
