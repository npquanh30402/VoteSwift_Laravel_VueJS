<template>
    <header>
        <nav
            class="navbar navbar-dark navbar-expand-lg bg-dark shadow small mb-3"
        >
            <div class="container-fluid">
                <Link class="navbar-brand" :href="route('homepage')">
                    VoteSwift
                </Link>
                <button
                    class="navbar-toggler border-0"
                    type="button"
                    data-bs-toggle="collapse"
                    data-bs-target="#navbarHeaderContent"
                    aria-controls="navbarHeaderContent"
                    aria-expanded="false"
                    aria-label="Toggle navigation"
                >
                    <i class="bi bi-list"></i>
                </button>
                <div
                    class="collapse navbar-collapse hstack justify-content-between"
                    id="navbarHeaderContent"
                >
                    <div>
                        <ul
                            class="navbar-nav me-auto mb-2 mb-lg-0"
                            v-if="authUser"
                        >
                            <li class="nav-item">
                                <Link class="nav-link" :href="route('homepage')"
                                    >Home
                                </Link>
                            </li>
                            <li class="nav-item">
                                <Link
                                    class="nav-link"
                                    :href="route('public.room')"
                                    >Public Rooms
                                </Link>
                            </li>
                        </ul>
                    </div>

                    <div class="d-flex">
                        <div class="hstack gap-3">
                            <div
                                class="me-3 hstack align-items-center"
                                v-if="authUser"
                            >
                                <MusicPlayer
                                    :music="music"
                                    class="me-5"
                                    v-if="isMusicPlayerEnable"
                                    style="transform: scale(0.8)"
                                />

                                <div class="d-flex flex-column mt-3">
                                    <Clock class="me-4"></Clock>
                                    <p class="text-white">
                                        {{
                                            Intl.DateTimeFormat().resolvedOptions()
                                                .timeZone
                                        }}
                                        ({{ format(new Date(), "OOOO") }})
                                    </p>
                                </div>

                                <VMenu>
                                    <div>
                                        <Link
                                            :href="route('notification.index')"
                                            class="mx-3 position-relative"
                                        >
                                            <i
                                                class="bi bi-bell text-white fs-4"
                                            ></i>
                                            <span
                                                v-if="notificationCount"
                                                class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger"
                                                >{{ notificationCount }}</span
                                            >
                                        </Link>
                                    </div>

                                    <template #popper>
                                        <NotificationList
                                            :notifications="notifications"
                                            :currentPage="currentPage"
                                        />
                                    </template>
                                </VMenu>

                                <Link
                                    :href="route('dashboard.user')"
                                    class="d-flex align-items-center"
                                >
                                    <img
                                        :src="authUser.avatar"
                                        class="rounded-circle"
                                        style="width: 3rem"
                                        alt="Avatar"
                                    />
                                    <span class="fs-4 mx-3 text-white">{{
                                        authUser.username
                                    }}</span>
                                </Link>
                            </div>
                            <div class="vr text-white"></div>
                            <div v-if="authUser">
                                <button
                                    class="btn btn-sm btn-warning"
                                    @click="handleLogout"
                                >
                                    <i class="bi bi-power"></i>
                                </button>
                            </div>
                            <div class="d-flex gap-3" v-else>
                                <Link
                                    :href="route('register')"
                                    @click="registerOrLoginShow"
                                    v-if="registerOrLogin"
                                    class="btn btn-sm btn-success"
                                    preserve-state
                                    preserve-scroll
                                >
                                    <i class="bi bi-box-arrow-in-right"></i>
                                </Link>
                                <Link
                                    :href="route('login')"
                                    @click="registerOrLoginShow"
                                    v-if="!registerOrLogin"
                                    class="btn btn-sm btn-secondary"
                                    preserve-state
                                    preserve-scroll
                                >
                                    <i class="bi bi-door-open"></i>
                                </Link>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </nav>
    </header>
</template>

<script setup>
import { Link, router, usePage } from "@inertiajs/vue3";
import { route } from "ziggy-js";
import { computed, onMounted, ref, watch } from "vue";
import MusicPlayer from "@/Components/MusicPlayer.vue";
import Clock from "@/Components/Clock.vue";
import { format } from "date-fns";
import { useToast } from "vue-toast-notification";
import { useNotificationStore } from "@/Stores/notifications.js";
import NotificationList from "@/Pages/Users/Notification/NotificationList.vue";

const props = defineProps(["authUser"]);
const notificationStore = useNotificationStore();

const music = computed(() => usePage().props.authUser.music);
const $toast = useToast();

const currentPage = ref(1);
const notifications = ref({});
const notificationCount = computed(() => {
    const dbCount = notificationStore.unreadCount;

    if (dbCount > 9) return "9+";

    return dbCount;
});

const isMusicPlayerEnable = computed(
    () => props.authUser.settings?.music_player_enabled === 1,
);
const registerOrLogin = ref(false);

function registerOrLoginShow() {
    registerOrLogin.value = !registerOrLogin.value;
}

const handleLogout = () => {
    router.post(route("logout"));
    $toast.success("Logout successfully");
};

onMounted(() => {
    notificationStore.setupEchoListeners(props.authUser.id);
    notificationStore.fetchUnreadNotificationsCount();
    notificationStore.fetchNotifications();
});

watch(notificationStore.notifications, () => {
    const notification_page =
        notificationStore.notifications[currentPage.value].data;

    notifications.value = {
        data: notification_page ? notification_page.slice(0, 5) : [],
    };
});
</script>
