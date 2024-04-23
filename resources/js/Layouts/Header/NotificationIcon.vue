<template>
    <VMenu>
        <div>
            <Link
                :href="route('notification.index')"
                class="mx-3 position-relative"
            >
                <i class="bi bi-bell text-white fs-4"></i>
                <span
                    v-if="notificationCount > 0"
                    class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger"
                    >{{ notificationCount }}</span
                >
            </Link>
        </div>

        <template #popper>
            <NotificationList
                :currentPage="currentPage"
                :notifications="notifications"
            />
        </template>
    </VMenu>
</template>
<script setup>
import { route } from "ziggy-js";
import { Link, usePage } from "@inertiajs/vue3";
import NotificationList from "@/Pages/Users/Notification/NotificationList.vue";
import { useNotificationStore } from "@/Stores/notifications.js";
import { computed, onMounted, ref } from "vue";
import { useHelper } from "@/Services/helper.js";

const authUser = computed(() => usePage().props.authUser.user);
const helper = useHelper();
const notificationStore = useNotificationStore();

const currentPage = ref(1);
const notificationCount = computed(() =>
    helper.formatWithThreshold(notificationStore.unreadCount, 9),
);

const initializeNotification = async () => {
    notificationStore.setupEchoListeners(authUser.value.id);
    await notificationStore.fetchUnreadNotificationsCount(authUser.value.id);
    await notificationStore.fetchNotifications(null, currentPage.value);
};

onMounted(initializeNotification);

const notifications = computed(() => {
    const notificationPage =
        notificationStore.notifications[currentPage.value]?.data;

    return {
        data: notificationPage ? notificationPage.slice(0, 5) : [],
    };
});
</script>
