<template>
    <div v-if="notifications" class="my-3 mx-5">
        <div class="d-flex justify-content-between">
            <h3 class="m-b-50 heading-line">
                Notifications <i class="bi bi-bell-fill"></i>
            </h3>

            <button
                v-if="notificationCount > 1"
                class="btn btn-secondary"
                @click="handleMarkAllAsRead"
            >
                Mark All as Read
            </button>
        </div>

        <div class="notification-ui_dd-content">
            <NotificationList
                :currentPage="currentPage"
                :notifications="notifications"
            />
            <div v-if="notifications.data?.length > 0">
                <Pagination
                    :links="notifications.links"
                    @loadPage="handleLoadPage"
                />
            </div>
        </div>
    </div>
</template>

<script setup>
import { usePage } from "@inertiajs/vue3";
import { computed, onMounted, onUpdated, ref, watch } from "vue";
import Pagination from "@/Components/Pagination.vue";
import { useNotificationStore } from "@/Stores/notifications.js";
import { useToast } from "vue-toast-notification";
import NotificationList from "@/Pages/Users/Notification/NotificationList.vue";

const authUser = computed(() => usePage().props.authUser.user);
const toast = useToast();
const notificationStore = useNotificationStore();

const notificationCount = computed(() => notificationStore.unreadCount);
const currentPage = ref(1);
const notifications = ref({});

watch([currentPage, notificationStore.notifications], () => {
    notifications.value = notificationStore.notifications[currentPage.value];
});

const handleLoadPage = (url, page) => {
    notificationStore.fetchNotifications(url, page).then(() => {
        currentPage.value = page;
    });
};

const handleMarkAllAsRead = async () => {
    await notificationStore.markAllAsRead(authUser.value.id);
};

onMounted(() => {
    notificationStore.fetchNotifications();
});

function scrollToTop() {
    window.scrollTo({
        top: 0,
        behavior: "smooth",
    });
}

onUpdated(() => scrollToTop());
</script>

<style scoped>
.m-b-50 {
    margin-bottom: 50px;
}

.heading-line {
    position: relative;
    padding-bottom: 5px;
}

.heading-line:after {
    content: "";
    height: 4px;
    width: 75px;
    background-color: #29b6f6;
    position: absolute;
    bottom: 0;
    left: 0;
}

.notification-ui_dd-content {
    margin-bottom: 30px;
}
</style>
