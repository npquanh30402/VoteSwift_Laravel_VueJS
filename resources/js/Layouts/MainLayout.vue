<template>
    <TheHeader :user="user"></TheHeader>
    <slot></slot>
    <TheFooter></TheFooter>
</template>

<script setup>
import TheFooter from "./TheFooter.vue";
import TheHeader from "./TheHeader.vue";

defineProps(['user']);
</script>
