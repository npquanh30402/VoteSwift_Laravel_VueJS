import { defineStore } from "pinia";
import { ref } from "vue";
import { route } from "ziggy-js";
import axios from "axios";

export const useNotificationStore = defineStore("notification", () => {
    const notifications = ref({});
    const currentPage = ref(1);
    const unreadCount = ref(0);

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

    const fetchUnreadNotificationsCount = async () => {
        const response = await axios.get(
            route("api.notifications.unreadCount"),
        );
        if (response.status === 200) {
            unreadCount.value = response.data;
        }
    };

    const addNotification = (notification, page) => {
        try {
            if (!notifications.value[page]) {
                notifications.value[page] = {
                    data: [],
                };
            }

            notifications.value[page].data.unshift(notification);
            unreadCount.value++;
        } catch (error) {
            console.log(error);
        }
    };

    const setupEchoListeners = (userId) => {
        Echo.private("App.Models.User." + userId).notification(
            (notification) => {
                addNotification(notification, 1);
            },
        );
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
                    unreadCount.value--;
                }
            } else {
                message = "Notification not found. Please try again.";
            }
        } catch (error) {
            console.log(error);
        }

        return message;
    };

    return {
        notifications,
        currentPage,
        unreadCount,
        fetchNotifications,
        addNotification,
        markAsRead,
        setupEchoListeners,
        fetchUnreadNotificationsCount,
    };
});
