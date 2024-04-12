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
                have sent you an invitation link to his voting room. Please
                check your email for more details.
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
