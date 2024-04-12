<template>
    <div class="notification-list_content">
        <Link
            :href="route('user.profile', notification.data.sender_id)"
            class="notification-list_img"
        >
            <img :src="notification.data.sender_avatar" alt="user" />
        </Link>
        <div class="notification-list_detail">
            <p>
                <b>{{ notification.data.sender_username }}</b>
                have sent you a friend request.
            </p>
            <p class="text-muted">
                Click
                <span @click="acceptFriendRequest(notification.data.sender_id)">
                    Here
                </span>
                to accept.
            </p>
            <p class="text-muted">
                Or
                <span @click="rejectFriendRequest(notification.data.sender_id)">
                    Here
                </span>
                to reject.
            </p>
            <p class="text-muted">
                <small>{{ formattedDate(notification.created_at) }}</small>
            </p>
        </div>
    </div>
</template>
<script setup>
import { route } from "ziggy-js";
import { Link } from "@inertiajs/vue3";
import { useHelper } from "@/Services/helper.js";
import { useToast } from "vue-toast-notification";
import { useFriendStore } from "@/Stores/friends.js";

defineProps(["notification"]);
const helper = useHelper();
const friendStore = useFriendStore();
const toast = useToast();
const formattedDate = helper.formattedDate;

const acceptFriendRequest = (id) => {
    friendStore.acceptFriendRequest(id);
    toast.success("Friend request accepted");
};

const rejectFriendRequest = (id) => {
    friendStore.rejectFriendRequest(id);
    toast.success("Friend request rejected");
};
</script>
