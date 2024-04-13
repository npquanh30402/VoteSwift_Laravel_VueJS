<template>
    <div>
        <div v-if="!showOnlineUser" class="popup-chat">
            <button
                class="btn btn-primary fs-5 position-relative"
                @click="toggleUserOnline"
            >
                <i class="bi bi-people-fill"></i>
            </button>
        </div>
        <div>
            <transition name="fade">
                <div
                    v-if="showOnlineUser"
                    ref="el"
                    :style="style"
                    class="card shadow"
                    style="position: fixed; z-index: 1"
                >
                    <div class="card-header d-flex justify-content-between">
                        <div
                            class="d-flex justify-content-center align-items-center gap-3"
                        >
                            <span>Online Users</span>
                            <span>|</span>
                            <div
                                class="hstack align-items-center justify-content-between gap-3"
                                style="font-size: 0.8rem"
                            >
                                <div>
                                    <i
                                        class="bi bi-circle-fill text-success animate__animated animate__flash animate__infinite animate__slow me-2"
                                        style="font-size: 0.6rem"
                                    ></i>
                                    <span
                                        >{{ onlineUsers.length }} In the
                                        room</span
                                    >
                                </div>
                                <div v-if="roomSettings.invitation_only">
                                    <i class="bi bi-people-fill me-2"></i>
                                    <span
                                        >{{ onlineUsersLengthExcludeOwner }}
                                        Online /
                                        {{ invitedUsersLength }} Invited</span
                                    >
                                </div>
                            </div>
                        </div>
                        <span style="cursor: pointer" @click="toggleUserOnline"
                            ><i class="bi bi-x-lg"></i
                        ></span>
                    </div>
                    <div class="card-body">
                        <div
                            class="mb-3"
                            style="min-height: 20vh; min-width: 30vw"
                        >
                            <template v-if="roomSettings.invitation_only">
                                <div
                                    v-for="user in invitedUsers"
                                    :key="user.id"
                                    :class="{
                                        'opacity-100': isUserOnline(user),
                                        'opacity-50': !isUserOnline(user),
                                    }"
                                    class="mb-4 border-bottom border-black pb-3"
                                >
                                    <div
                                        class="d-flex text-decoration-none align-items-center justify-content-between text-dark"
                                    >
                                        <div
                                            class="d-flex gap-2 align-items-center"
                                        >
                                            <img
                                                :src="user.avatar"
                                                alt=""
                                                class="rounded-circle me-2 img-fluid"
                                                width="48"
                                            />
                                            <p v-if="authUser.id === user.id">
                                                <strong>You</strong>
                                            </p>
                                            <p v-else>
                                                <strong
                                                    >{{ user.username }}
                                                    <span
                                                        v-if="
                                                            owner.id === user.id
                                                        "
                                                        >(Owner)</span
                                                    ></strong
                                                >
                                            </p>
                                        </div>
                                        <a
                                            v-if="authUser.id !== user.id"
                                            :href="
                                                route('user.profile', user.id)
                                            "
                                            class="btn btn-primary"
                                            target="_blank"
                                            ><i class="bi bi-person"></i
                                        ></a>
                                    </div>
                                </div>
                            </template>
                            <template v-else>
                                <div
                                    v-for="user in onlineUsers"
                                    :key="user.id"
                                    :class="{
                                        'opacity-100': isUserOnline(user),
                                        'opacity-50': !isUserOnline(user),
                                    }"
                                    class="mb-4 border-bottom border-black pb-3"
                                >
                                    <div
                                        class="d-flex text-decoration-none align-items-center justify-content-between text-dark"
                                    >
                                        <div
                                            class="d-flex gap-2 align-items-center"
                                        >
                                            <img
                                                :src="user.avatar"
                                                alt=""
                                                class="rounded-circle me-2 img-fluid"
                                                width="48"
                                            />
                                            <p v-if="authUser.id === user.id">
                                                <strong>You</strong>
                                            </p>
                                            <p v-else>
                                                <strong
                                                    >{{ user.username }}
                                                    <span
                                                        v-if="
                                                            owner.id === user.id
                                                        "
                                                        >(Owner)</span
                                                    ></strong
                                                >
                                            </p>
                                        </div>
                                        <a
                                            v-if="authUser.id !== user.id"
                                            :href="
                                                route('user.profile', user.id)
                                            "
                                            class="btn btn-primary"
                                            target="_blank"
                                            ><i class="bi bi-person"></i
                                        ></a>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>
            </transition>
        </div>
    </div>
</template>

<script setup>
import { computed, ref } from "vue";
import { usePage } from "@inertiajs/vue3";
import { route } from "ziggy-js";
import { useDraggable } from "@vueuse/core";

const props = defineProps([
    "room",
    "invitedUsers",
    "onlineUsers",
    "isUserOnline",
    "owner",
    "roomSettings",
]);

const el = ref();
const { x, y, style } = useDraggable(el, {
    initialValue: { x: 1000, y: 200 },
});

const showOnlineUser = ref(false);
const authUser = computed(() => usePage().props.authUser.user);

const onlineUsersLengthExcludeOwner = computed(() => {
    for (let obj of props.onlineUsers) {
        if (obj.id === props.owner.id) {
            return props.onlineUsers.length - 1;
        }
    }
    return props.onlineUsers.length;
});

const invitedUsersLength = computed(() => {
    for (let obj of props.onlineUsers) {
        if (obj.id === props.owner.id) {
            return props.invitedUsers.length - 1;
        }
    }
    return props.invitedUsers.length;
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
