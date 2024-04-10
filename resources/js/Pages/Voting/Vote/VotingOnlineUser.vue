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
                            <div style="font-size: 0.8rem"
                                 class="hstack align-items-center justify-content-between gap-3">
                                <div>
                                    <i class="bi bi-circle-fill text-success animate__animated animate__flash animate__infinite animate__slow me-2"
                                       style="font-size: 0.6rem"></i>
                                    <span>{{ onlineUsers.length }} In the room</span>
                                </div>
                                <div>
                                    <i class="bi bi-people-fill me-2"></i>
                                    <span>{{ onlineUsersLengthExcludeOwner }} Online / {{
                                            invitedUsersLength
                                        }} Invited</span>
                                </div>
                            </div>
                        </div>
                        <span style="cursor: pointer" @click="toggleUserOnline"><i class="bi bi-x-lg"></i></span>
                    </div>
                    <div class="card-body">
                        <div class="mb-3" style="min-height: 20vh; min-width: 30vw">
                            <div v-for="user in invitedUsers" :key="user.id"
                                 :class="{ 'opacity-100': isUserOnline(user), 'opacity-50': !isUserOnline(user) }"
                                 class="mb-4 border-bottom border-black pb-3">
                                <div
                                    class="d-flex text-decoration-none align-items-center justify-content-between text-dark">
                                    <div
                                        class="d-flex gap-2 align-items-center">
                                        <img class="rounded-circle me-2 img-fluid" :src="user.avatar" alt="" width="48">
                                        <p v-if="authUser.id === user.id"><strong>You</strong></p>
                                        <p v-else>
                                            <strong>{{ user.username }}
                                                <span v-if="owner.id === user.id">(Owner)</span>
                                            </strong>
                                        </p>
                                    </div>
                                    <a class="btn btn-primary" :href="route('user.profile', user.id)" target="_blank"
                                       v-if="authUser.id !== user.id"><i
                                        class="bi bi-person"></i></a>
                                </div>
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

const props = defineProps(['room', 'invitedUsers', 'onlineUsers', 'isUserOnline', 'owner'])

const showOnlineUser = ref(false)
const authUser = computed(() => usePage().props.authUser.user);

const onlineUsersLengthExcludeOwner = computed(() => {
    for (let obj of props.onlineUsers) {
        if (obj.id === props.owner.id) {
            return props.onlineUsers.length - 1
        }
    }
    return props.onlineUsers.length
});

const invitedUsersLength = computed(() => {
    for (let obj of props.onlineUsers) {
        if (obj.id === props.owner.id) {
            return props.invitedUsers.length - 1
        }
    }
    return props.invitedUsers.length
});

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
