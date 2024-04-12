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

    const markAsRead = async (notificationId, page) => {
        let message = "";
        try {
            const notificationsPage = notifications.value[page]?.data || [];
            const foundNotification = notificationsPage.find(
                (n) => n.id === notificationId,
            );

            if (foundNotification) {
                foundNotification.read_at = new Date().toISOString();

                const response = await axios.put(
                    route("api.notification.read", notificationId),
                );

                if (response.status === 204) {
                    message = "Notification marked as read successfully.";
                }
            } else {
                message = "Notification not found. Please try again.";
            }
        } catch (error) {
            console.log(error);
        }

        return message;
    };

    const addNotification = (notification, page) => {
        notifications.value[page].data.unshift(notification);
    };

    return {
        notifications,
        currentPage,
        fetchNotifications,
        addNotification,
        markAsRead,
    };
});
