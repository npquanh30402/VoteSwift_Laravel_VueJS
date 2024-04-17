<template>
    <header>
        <nav
            class="navbar navbar-dark navbar-expand-lg bg-dark shadow small mb-3"
        >
            <div class="container-fluid">
                <Link :href="route('homepage')" class="navbar-brand">
                    VoteSwift
                </Link>
                <button
                    aria-controls="navbarHeaderContent"
                    aria-expanded="false"
                    aria-label="Toggle navigation"
                    class="navbar-toggler border-0"
                    data-bs-target="#navbarHeaderContent"
                    data-bs-toggle="collapse"
                    type="button"
                >
                    <i class="bi bi-list"></i>
                </button>
                <div
                    id="navbarHeaderContent"
                    class="collapse navbar-collapse hstack justify-content-between"
                >
                    <div>
                        <ul
                            v-if="authUser"
                            class="navbar-nav me-auto mb-2 mb-lg-0"
                        >
                            <li class="nav-item">
                                <Link :href="route('homepage')" class="nav-link"
                                    >Home
                                </Link>
                            </li>
                            <li class="nav-item">
                                <Link
                                    :href="route('public.room')"
                                    class="nav-link"
                                    >Public Rooms
                                </Link>
                            </li>
                        </ul>
                    </div>

                    <div class="d-flex">
                        <div class="hstack gap-3">
                            <div
                                v-if="authUser"
                                class="me-3 hstack align-items-center"
                            >
                                <MusicPlayer
                                    v-if="isMusicPlayerEnable"
                                    :music="music"
                                    class="me-5"
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
                                            :currentPage="currentPage"
                                            :notifications="notifications"
                                        />
                                    </template>
                                </VMenu>

                                <VMenu :skidding="-100">
                                    <div>
                                        <Link
                                            :href="route('chat.index')"
                                            class="mx-3 position-relative"
                                        >
                                            <i
                                                class="bi bi-chat-dots text-white fs-4"
                                            ></i>
                                            <span
                                                v-if="totalUnreadMessages"
                                                class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger"
                                                >{{ totalUnreadMessages }}</span
                                            >
                                        </Link>
                                    </div>

                                    <template #popper>
                                        <KeepAlive>
                                            <UserChatPopup
                                                :currentReceiver="
                                                    currentReceiver
                                                "
                                                @change-user="
                                                    handleChangeReceiver
                                                "
                                            />
                                        </KeepAlive>
                                    </template>
                                </VMenu>

                                <Link
                                    :href="route('dashboard.user')"
                                    class="d-flex align-items-center"
                                >
                                    <img
                                        :src="authUser.avatar"
                                        alt="Avatar"
                                        class="rounded-circle"
                                        style="width: 3rem"
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
                            <div v-else class="d-flex gap-3">
                                <Link
                                    v-if="registerOrLogin"
                                    :href="route('register')"
                                    class="btn btn-sm btn-success"
                                    preserve-scroll
                                    preserve-state
                                    @click="registerOrLoginShow"
                                >
                                    <i class="bi bi-box-arrow-in-right"></i>
                                </Link>
                                <Link
                                    v-if="!registerOrLogin"
                                    :href="route('login')"
                                    class="btn btn-sm btn-secondary"
                                    preserve-scroll
                                    preserve-state
                                    @click="registerOrLoginShow"
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
import { computed, onMounted, onUpdated, ref, watch } from "vue";
import MusicPlayer from "@/Components/MusicPlayer.vue";
import Clock from "@/Components/Clock.vue";
import { format } from "date-fns";
import { useToast } from "vue-toast-notification";
import { useNotificationStore } from "@/Stores/notifications.js";
import NotificationList from "@/Pages/Users/Notification/NotificationList.vue";
import { useUserChatStore } from "@/Stores/user-chat.js";
import { useFriendStore } from "@/Stores/friends.js";
import UserChatNotification from "@/Pages/Users/Chat/UserChatNotification.vue";
import UserChatPopup from "@/Pages/Users/Chat/UserChatPopup.vue";

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

const initializeNotification = () => {
    if (props.authUser) {
        notificationStore.setupEchoListeners(props.authUser.id);
        notificationStore.fetchUnreadNotificationsCount();
        notificationStore.fetchNotifications(null, currentPage.value);
    }
};

onMounted(() => initializeNotification());

watch(
    () => props.authUser,
    () => initializeNotification(),
);

watch(notificationStore.notifications, () => {
    const notification_page =
        notificationStore.notifications[currentPage.value].data;

    notifications.value = {
        data: notification_page ? notification_page.slice(0, 5) : [],
    };
});

const friendStore = useFriendStore();
const friends = computed(() => friendStore.friends.friends);

const userChatStore = useUserChatStore();
const totalUnreadMessages = computed(() => {
    const dbCount = userChatStore.totalUnreadMessages;

    if (dbCount > 9) return "9+";

    return dbCount;
});

const currentReceiver = ref(null);
const handleChangeReceiver = (receiver) => {
    currentReceiver.value = receiver;
};

onMounted(() => {
    if (props.authUser) {
        const friendResponse = friendStore.fetchFriends();

        friendStore.setupEchoPrivateListener(props.authUser.id);
        if (friendResponse.status === 200) {
            friends.value.forEach((friend) => {
                userChatStore.setupEchoPrivateListener(friend.id);
            });
        }

        userChatStore.setupEchoPrivateListener(props.authUser.id);

        userChatStore.fetchUnreadMessages(props.authUser.id);
    }
});
</script>
