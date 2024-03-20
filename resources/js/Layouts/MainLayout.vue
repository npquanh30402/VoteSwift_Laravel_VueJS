<template>
    <TheHeader :user="user"></TheHeader>
    <div class="alert alert-success w-50 mx-auto mt-3" v-if="flashSuccess">
        {{ flashSuccess }}
    </div>
    <slot></slot>
    <TheFooter></TheFooter>
</template>

<script setup>
import TheFooter from "./TheFooter.vue";
import TheHeader from "./TheHeader.vue";
import {usePage} from "@inertiajs/vue3";
import {computed} from "vue";

defineProps(['user']);

const page = usePage();

const flashSuccess = computed(() => page.props.flash.success);

</script>
