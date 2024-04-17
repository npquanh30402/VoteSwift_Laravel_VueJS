<template>
    <div v-for="message in UserMessage" class="notification-list_content">
        <Link :href="null" class="notification-list_img">
            <img :src="message.user.avatar" alt="user" />
        </Link>
        <div class="notification-list_detail">
            <p>
                <b>{{ message.user.username }}</b>
                : {{ message.message.content }}
            </p>
        </div>
    </div>
</template>
<script setup>
import { Link } from "@inertiajs/vue3";
import { useHelper } from "@/Services/helper.js";
import { useToast } from "vue-toast-notification";
import { useFriendStore } from "@/Stores/friends.js";

const props = defineProps(["UserMessage"]);

const helper = useHelper();
const friendStore = useFriendStore();
const toast = useToast();
const formattedDate = helper.formattedDate;
</script>
