<template>
    <VMenu :skidding="-100">
        <div>
            <Link :href="route('chat.index')" class="mx-3 position-relative">
                <i class="bi bi-chat-dots text-white fs-4"></i>
                <span
                    v-if="totalUnreadMessages > 0"
                    class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger"
                    >{{ totalUnreadMessages }}</span
                >
            </Link>
        </div>

        <template #popper>
            <KeepAlive>
                <UserChatPopup
                    :currentReceiver="currentReceiver"
                    @change-user="handleChangeReceiver"
                />
            </KeepAlive>
        </template>
    </VMenu>
</template>
<script setup>
import { route } from "ziggy-js";
import { Link, usePage } from "@inertiajs/vue3";
import UserChatPopup from "@/Pages/Users/Chat/UserChatPopup.vue";
import { useUserChatStore } from "@/Stores/user-chat.js";
import { computed, onMounted, ref } from "vue";
import { useHelper } from "@/Services/helper.js";
import { useFriendStore } from "@/Stores/friends.js";

const authUser = computed(() => usePage().props.authUser.user);
const helper = useHelper();
const friendStore = useFriendStore();
const userChatStore = useUserChatStore();

const friends = computed(() => friendStore.friends.friends);
const totalUnreadMessages = computed(() =>
    helper.formatWithThreshold(userChatStore.totalUnreadMessages, 9),
);

const currentReceiver = ref(null);
const handleChangeReceiver = (receiver) => {
    currentReceiver.value = receiver;
};

const initializeChat = () => {
    friends.value?.forEach((friend) => {
        userChatStore.setupEchoPrivateListener(friend.id);
    });

    userChatStore.setupEchoPrivateListener(authUser.value.id);
    userChatStore.fetchUnreadMessages(authUser.value.id);
};

onMounted(initializeChat);
</script>
