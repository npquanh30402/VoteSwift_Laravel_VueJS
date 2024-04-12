<template>
    <div class="notification-list_content">
        <Link
            :href="route('user.profile', notification.data.recipient_id)"
            class="notification-list_img"
        >
            <img :src="notification.data.recipient_avatar" alt="user" />
        </Link>
        <div class="notification-list_detail">
            <p>
                <b>{{ notification.data.recipient_username }}</b>
                have accepted your friend request.
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

defineProps(["notification"]);
const helper = useHelper();
const formattedDate = helper.formattedDate;
</script>
