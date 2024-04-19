<template>
    <TheHeader :authUser="authUser" :userSettings="userSettings"></TheHeader>
    <FlashMessages></FlashMessages>
    <transition mode="out-in" name="fade">
        <div :key="$page.url">
            <slot></slot>
        </div>
    </transition>
    <TheFooter></TheFooter>

    <ScrollToTop />
</template>

<script setup>
import TheFooter from "./TheFooter.vue";
import TheHeader from "./TheHeader.vue";
import { usePage } from "@inertiajs/vue3";
import FlashMessages from "@/Components/FlashMessages.vue";
import { computed } from "vue";
import ScrollToTop from "@/Components/ScrollToTop.vue";

const authUser = computed(() => usePage().props.authUser.user);
const userSettings = computed(() => usePage().props.authUser.settings);
</script>

<style>
.fade-enter-active,
.fade-leave-active {
    transition: opacity 0.3s ease-out;
}

.fade-enter-from,
.fade-leave-to {
    opacity: 0;
}

.truncate-text {
    overflow: hidden;
    white-space: nowrap;
    text-overflow: ellipsis;
    height: 10rem;
}

.pagination-container {
    display: flex;
    column-gap: 10px;
}

.paginate-buttons {
    height: 40px;
    width: 40px;
    border-radius: 20px;
    cursor: pointer;
    background-color: rgb(242, 242, 242);
    border: 1px solid rgb(217, 217, 217);
    color: black;
}

.paginate-buttons:hover {
    background-color: #d8d8d8;
}

.active-page {
    background-color: #3498db;
    border: 1px solid #3498db;
    color: white;
}

.active-page:hover {
    background-color: #2988c8;
}
</style>
