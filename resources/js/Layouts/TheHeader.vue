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
                                <Link class="nav-link active" aria-current="page" :href="route('dashboard.user')">Home
                                </Link>
                            </li>
                            <li class="nav-item">
                                <Link class="nav-link active" aria-current="page" :ref="route('public.room')">Public
                                    Rooms
                                </Link>
                            </li>
                        </ul>
                    </div>

                    <div class="d-flex">
                        <div class="hstack gap-3">
                            <div class="me-3" v-if="user">
                                <a :href="route('dashboard.user')" class="d-flex align-items-center">
                                    <img :src="user.avatar" class="rounded-circle"
                                         style="width: 3rem;"
                                         alt="Avatar"/>
                                    <span class="fs-4 mx-3 text-white">{{ user.username }}</span>
                                </a>
                            </div>
                            <div class="vr text-white"></div>
                            <div v-if="user">
                                <button type="button" class="btn btn-sm btn-warning"
                                        data-bs-toggle="modal"
                                        :data-bs-target="'#' + logoutModalId">
                                    <i class="bi bi-power"></i>
                                </button>
                                <teleport to="body">
                                    <BaseModal
                                        :modalId="logoutModalId"
                                        title="Confirm Logout"
                                    >
                                        <div>Do you want to logout?</div>
                                        <template #footer>
                                            <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Yes
                                            </button>
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">No
                                            </button>
                                        </template>
                                    </BaseModal>
                                </teleport>
                            </div>
                            <div class="d-flex gap-3" v-else>
                                <Link :href="route('register')" @click="registerOrLoginShow" v-if="registerOrLogin"
                                      class="btn btn-sm btn-success">
                                    <i class="bi bi-box-arrow-in-right"></i>
                                </Link>
                                <Link :href="route('login')" @click="registerOrLoginShow" v-if="!registerOrLogin"
                                      class="btn btn-sm btn-secondary">
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
import BaseModal from "../Components/BaseModal.vue";
import {ref} from "vue";
import {route} from "ziggy-js";

const registerOrLogin = ref(false);

defineProps(
    {'user': Object}
);

function registerOrLoginShow() {
    registerOrLogin.value = !registerOrLogin.value;
}

const logoutModalId = 'logoutModal';
</script>
