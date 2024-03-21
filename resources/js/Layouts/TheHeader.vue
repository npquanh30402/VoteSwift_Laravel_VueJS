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
                        <ul class="navbar-nav me-auto mb-2 mb-lg-0" v-if="user">
                            <li class="nav-item">
                                <Link class="nav-link active" aria-current="page" href="">Home
                                </Link>
                            </li>
                            <li class="nav-item">
                                <Link class="nav-link active" aria-current="page" :href="route('public.room')">Public
                                    Rooms
                                </Link>
                            </li>
                        </ul>
                    </div>

                    <div class="d-flex">
                        <div class="hstack gap-3">
                            <div class="me-3" v-if="user">
                                <Link :href="route('dashboard.user')" class="d-flex align-items-center">
                                    <img :src="user.avatar" class="rounded-circle"
                                         style="width: 3rem;"
                                         alt="Avatar"/>
                                    <span class="fs-4 mx-3 text-white">{{ user.username }}</span>
                                </Link>
                            </div>
                            <div class="vr text-white"></div>
                            <div v-if="user">
                                <button type="button" class="btn btn-sm btn-warning" @click="showLogoutModal">
                                    <i class="bi bi-power"></i>
                                </button>
                                <teleport to="body">
                                    <BaseModal title="Confirm Logout" id="logoutModal">
                                        Do you want to logout?
                                        <template #footer>
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">No
                                            </button>
                                            <button type="button" class="btn btn-danger" @click="logout">Yes
                                            </button>
                                        </template>
                                    </BaseModal>
                                </teleport>
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
import {Link, router} from "@inertiajs/vue3";
import BaseModal from "../Components/BaseModal.vue";
import {onMounted, ref} from "vue";
import {route} from "ziggy-js";
import * as bootstrap from "bootstrap";

defineProps(
    {'user': Object}
);


const registerOrLogin = ref(false);

function registerOrLoginShow() {
    registerOrLogin.value = !registerOrLogin.value;
}

let modal;

onMounted(() => {
    modal = new bootstrap.Modal(document.getElementById('logoutModal'));
});

const showLogoutModal = () => {
    modal.show();
};

const logout = () => {
    router.post(route("logout"));
    modal.hide();
};
</script>
