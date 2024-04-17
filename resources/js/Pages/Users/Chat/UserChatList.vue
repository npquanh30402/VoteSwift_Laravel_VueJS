<template>
    <div
        v-for="friend in friends"
        :key="friend.id"
        :class="{ active: currentUser === friend }"
        class="list-group-item list-group-item-action"
        @click="changeUser(friend)"
    >
        <div
            class="d-flex align-items-center justify-content-between overflow-auto"
        >
            <div class="d-flex align-items-center">
                <img
                    :src="friend.avatar"
                    alt=""
                    class="rounded-circle me-3"
                    style="width: 50px"
                />
                <span class="fs-5"
                    ><strong>{{ friend.username }}</strong></span
                >
            </div>
            <span
                v-if="unreadMessages && unreadMessages[friend.id]?.length > 0"
                class="badge text-bg-danger"
                >{{ unreadMessages[friend.id]?.length }}</span
            >
        </div>
    </div>
</template>

<script setup>
import { computed, ref } from "vue";
import { useUserChatStore } from "@/Stores/user-chat.js";
import { usePage } from "@inertiajs/vue3";

const props = defineProps(["friends", "currentReceiver"]);
const authUser = computed(() => usePage().props.authUser.user);
const emit = defineEmits(["change-user"]);

const userChatStore = useUserChatStore();
const unreadMessages = computed(() => userChatStore.unreadMessages);

const currentUser = ref(
    props.currentReceiver ? props.currentReceiver : authUser,
);
const changeUser = (user) => {
    currentUser.value = user;
    userChatStore.markRead(authUser.value.id, user.id);
    emit("change-user", user);
};
</script>
