<script setup>
import { computed, onMounted } from "vue";
import { useFriendStore } from "@/Stores/friends.js";
import { usePage } from "@inertiajs/vue3";

const authUser = computed(() => usePage().props.authUser.user);
const friendStore = useFriendStore();

const initializeFriend = async () => {
    await friendStore.fetchFriends();
    friendStore.setupEchoPrivateListener(authUser.value.id);
};

onMounted(initializeFriend);
</script>

<template></template>
