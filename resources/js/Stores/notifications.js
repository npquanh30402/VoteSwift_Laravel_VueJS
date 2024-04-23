import { defineStore } from "pinia";
import { ref } from "vue";
import { route } from "ziggy-js";
import axios from "axios";
import { useToast } from "vue-toast-notification";

const toast = useToast();

export const useNotificationStore = defineStore("notification", () => {
    const notifications = ref({});
    const currentPage = ref(1);
    const unreadCount = ref(0);

    const fetchNotifications = async (url = null, page = null) => {
        if (!!notifications.value[page]) {
            return;
        }

        try {
            if (url) {
                const response = await axios.get(url);
                if (response.status === 200) {
                    notifications.value[response.data.data.current_page] =
                        response.data.data;
                }
            } else {
                const response = await axios.get(
                    route("api.notifications.index"),
                );
                if (response.status === 200) {
                    notifications.value[response.data.data.current_page] =
                        response.data.data;
                }
            }
        } catch (error) {
            toast.error(error.response.data.message);
        }
    };

    const fetchUnreadNotificationsCount = async (userId) => {
        if (Object.keys(notifications.value).length !== 0) {
            return;
        }

        try {
            const response = await axios.get(
                route("api.notifications.unread.count", userId),
            );

            if (response.status === 200) {
                unreadCount.value = response.data.data;
            }
        } catch (error) {
            toast.error(error.response.data.message);
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
        try {
            Echo.private("App.Models.User." + userId).notification(
                (notification) => {
                    addNotification(notification, 1);
                },
            );
        } catch (error) {
            console.log(error);
        }
    };

    const markAsRead = async (notificationId, page) => {
        const notificationsPage = notifications.value[page]?.data || [];
        const foundNotification = notificationsPage.find(
            (n) => n.id === notificationId,
        );

        if (foundNotification) {
            try {
                const response = await axios.put(
                    route("api.notification.mark.as.read", notificationId),
                );

                foundNotification.read_at = new Date().toISOString();

                if (response.status === 200) {
                    toast.success(response.data.message);
                }

                if (response.status === 204) {
                    message = "Notification marked as read successfully.";
                    unreadCount.value--;
                }
            } catch (error) {
                toast.error(error.response.data.message);
            }
        }
    };

    const markAllAsRead = async (userId) => {
        try {
            const response = await axios.put(
                route("api.notifications.mark.all.as.read", userId),
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

                toast.success(response.data.message);
            }
        } catch (error) {
            toast.error(error.response.data.message);
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
