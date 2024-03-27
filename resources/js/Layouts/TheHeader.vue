<template>
    <header>
        <nav class="navbar navbar-dark navbar-expand-lg bg-dark shadow small mb-3">
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
                    aria-label="Toggle navigation">
                    <i class="bi bi-list"></i>
                </button>
                <div class="collapse navbar-collapse hstack justify-content-between" id="navbarHeaderContent">
                    <div>
                        <ul class="navbar-nav me-auto mb-2 mb-lg-0" v-if="authUser">
                            <li class="nav-item">
                                <Link class="nav-link" :href="route('homepage')">Home
                                </Link>
                            </li>
                            <li class="nav-item">
                                <Link class="nav-link" :href="route('public.room')">Public
                                    Rooms
                                </Link>
                            </li>
                        </ul>
                    </div>

                    <div class="d-flex">
                        <div class="hstack gap-3">
                            <div class="me-3 hstack" v-if="authUser">
                                <MusicPlayer class="me-5" v-if="isMusicPlayerEnable"></MusicPlayer>
                                
                                <Clock class="me-4"></Clock>
                                <Link :href="route('dashboard.user')" class="d-flex align-items-center">
                                    <img :src="authUser.avatar" class="rounded-circle"
                                         style="width: 3rem;"
                                         alt="Avatar"/>
                                    <span class="fs-4 mx-3 text-white">{{ authUser.username }}</span>
                                </Link>
                            </div>
                            <div class="vr text-white"></div>
                            <div v-if="authUser">
                                <Link :href="route('logout')" class="btn btn-sm btn-warning" as="button" method="POST">
                                    <i class="bi bi-power"></i>
                                </Link>
                            </div>
                            <div class="d-flex gap-3" v-else>
                                <Link :href="route('register')" @click="registerOrLoginShow" v-if="registerOrLogin"
                                      class="btn btn-sm btn-success" preserve-state preserve-scroll>
                                    <i class="bi bi-box-arrow-in-right"></i>
                                </Link>
                                <Link :href="route('login')" @click="registerOrLoginShow" v-if="!registerOrLogin"
                                      class="btn btn-sm btn-secondary" preserve-state preserve-scroll>
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
import {Link} from "@inertiajs/vue3";
import {route} from "ziggy-js";
import {ref} from "vue";
import MusicPlayer from "@/Components/MusicPlayer.vue";
import Clock from "@/Components/Clock.vue";

const props = defineProps(['authUser', 'authUserSettings'])

const isMusicPlayerEnable = ref(props.authUserSettings?.music_player_enabled === 1);

const registerOrLogin = ref(false);

function registerOrLoginShow() {
    registerOrLogin.value = !registerOrLogin.value;
}
</script>
