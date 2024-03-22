<template>
    <TheHeader></TheHeader>
    <div class="alert alert-success w-50 mx-auto mt-3" v-if="flashSuccess">
        {{ flashSuccess }}
    </div>
    <div class="alert alert-danger w-50 mx-auto mt-3" v-else-if="flashError">
        {{ flashError }}
    </div>
    <transition name="fade" mode="out-in">
        <div :key="$page.url">
            <slot></slot>
        </div>
    </transition>
    <TheFooter></TheFooter>
</template>

<script setup>
import TheFooter from "./TheFooter.vue";
import TheHeader from "./TheHeader.vue";
import {usePage} from "@inertiajs/vue3";
import {computed} from "vue";

const flashSuccess = computed(() => usePage().props.flash.success);
const flashError = computed(() => usePage().props.flash.error);
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
</style>
