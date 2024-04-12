<template>
    <div class="notification-list_content">
        <div class="notification-list_detail">
            <p>You have created a new <b>Voting Room</b></p>
            <p class="text-muted">
                Click
                <Link :href="route('room.dashboard', notification.data.room_id)"
                    >here
                </Link>
                to visit its dashboard.
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
