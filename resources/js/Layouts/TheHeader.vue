<template>
    <header v-if="isReady">
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

                                <FriendSetup />

                                <NotificationIcon />

                                <ChatIcon />

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
import { computed, onMounted, ref } from "vue";
import MusicPlayer from "@/Components/MusicPlayer.vue";
import Clock from "@/Components/Clock.vue";
import { format } from "date-fns";
import { useToast } from "vue-toast-notification";
import { useHelper } from "@/Services/helper.js";
import NotificationIcon from "@/Layouts/Header/NotificationIcon.vue";
import ChatIcon from "@/Layouts/Header/ChatIcon.vue";
import FriendSetup from "@/Layouts/Header/FriendSetup.vue";

const props = defineProps(["authUser", "userSettings"]);

const $toast = useToast();

const helper = useHelper();

const music = computed(() => usePage().props.authUser.music);
const isMusicPlayerEnable = computed(
    () => props.userSettings.music_player_enabled === 1,
);

const registerOrLogin = ref(false);
const isReady = ref(false);

function registerOrLoginShow() {
    registerOrLogin.value = !registerOrLogin.value;
}

const handleLogout = () => {
    router.post(route("logout"));
    $toast.success("Logout successfully");
};

const initializeComponent = async () => {
    isReady.value = true;
};

onMounted(initializeComponent);
</script>
