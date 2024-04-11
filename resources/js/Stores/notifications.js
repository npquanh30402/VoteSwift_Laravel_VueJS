import { defineStore } from "pinia";
import { ref } from "vue";
import { route } from "ziggy-js";
import axios from "axios";

export const useNotificationStore = defineStore("notification", () => {
    const notifications = ref({});
    const currentPage = ref(1);

    const fetchNotifications = async (url = null, page = null) => {
        if (!!notifications.value[page]) {
            return;
        }

        if (url) {
            const response = await axios.get(url);
            if (response.status === 200) {
                notifications.value[response.data.current_page] = response.data;
            }
        } else {
            const response = await axios.get(route("api.notifications.index"));
            if (response.status === 200) {
                notifications.value[response.data.current_page] = response.data;
            }
        }
    };

    return { notifications, currentPage, fetchNotifications };
});
