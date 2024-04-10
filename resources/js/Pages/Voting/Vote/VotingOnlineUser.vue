<template>
    <div>
        <div class="popup-chat" v-if="!showOnlineUser">
            <button class="btn btn-primary fs-5 position-relative" @click="toggleUserOnline">
                <i class="bi bi-people-fill"></i>
                <!--                <span-->
                <!--                    class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger"-->
                <!--                    v-if="unreadMessagesCount && unreadMessagesCount[room.id] > 0">-->
                <!--                {{-->
                <!--                        unreadMessagesCount[room.id]-->
                <!--                    }}-->
                <!--            </span>-->
            </button>
        </div>
        <div class="popup-chat" style="z-index: 999">
            <transition name="fade">
                <div v-if="showOnlineUser" class="card shadow">
                    <div class="card-header d-flex justify-content-between">
                        <div class="d-flex justify-content-center align-items-center gap-3">
                            <span>Online Users</span>
                            <span>|</span>
                            <span style="font-size: 0.8rem">
                                <i class="bi bi-circle-fill text-success animate__animated animate__flash animate__infinite animate__slow"
                                   style="font-size: 0.6rem"></i>
                                {{ onlineUsers.length }} Online / {{ invitedUsers.length }} Invited
                            </span>
                        </div>
                        <span style="cursor: pointer" @click="toggleUserOnline"><i class="bi bi-x-lg"></i></span>
                    </div>
                    <div class="card-body">
                        <div class="mb-3" style="min-height: 20vh; min-width: 20vw">
                            <div v-for="user in invitedUsers" :key="user.id"
                                 :class="{ 'opacity-100': isUserOnline(user), 'opacity-50': !isUserOnline(user) }"
                                 class="mb-4">
                                <a :href="route('user.profile', user.id)" target="_blank"
                                   class="d-flex text-decoration-none align-items-center text-dark">
                                    <img class="rounded-circle me-2 img-fluid" :src="user.avatar" alt="" width="48">
                                    <p><strong>{{ user.username }}</strong></p>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </transition>
        </div>
    </div>
</template>

<script setup>
import {computed, ref} from "vue";
import {usePage} from "@inertiajs/vue3";
import {route} from "ziggy-js";

const props = defineProps(['room', 'roomSettings', 'invitedUsers', 'onlineUsers', 'isUserOnline'])

const showOnlineUser = ref(false)
const authUser = computed(() => usePage().props.authUser.user);

function toggleUserOnline() {
    showOnlineUser.value = !showOnlineUser.value;
}
</script>

<style scoped>
.popup-chat {
    position: fixed;
    top: 20vh;
    right: 3vw;
}
</style>
