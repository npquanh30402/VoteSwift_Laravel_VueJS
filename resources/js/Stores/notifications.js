import { defineStore } from "pinia";
import { ref } from "vue";
import { route } from "ziggy-js";
import axios from "axios";

export const useNotificationStore = defineStore("notification", () => {
    const notifications = ref({});
    const currentPage = ref(1);
    const unreadCount = ref(0);
    let isEchoSetup = false;

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
        if (Object.keys(notifications.value).length !== 0) {
            return;
        }

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
        if (isEchoSetup) {
            return;
        }

        Echo.private("App.Models.User." + userId).notification(
            (notification) => {
                addNotification(notification, 1);
            },
        );

        isEchoSetup = true;
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

    const markAllAsRead = async (userId) => {
        try {
            const response = await axios.put(
                route("api.notification.all.read", userId),
            );

            if (response.status === 200) {
                for (const targetKey in notifications.value) {
                    const target = notifications.value[targetKey];

                    for (const index in target.data) {
                        const data = target.data[index];

                        if (data.read_at === null) {
                            data.read_at = new Date().toISOString();
                        }
                    }
                }

                unreadCount.value = 0;
            }

            return response;
        } catch (error) {
            console.log(error);
        }
    };

    return {
        notifications,
        currentPage,
        unreadCount,
        fetchNotifications,
        addNotification,
        markAsRead,
        markAllAsRead,
        setupEchoListeners,
        fetchUnreadNotificationsCount,
    };
});
