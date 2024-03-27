<template>
    <div class="clock">
        {{ time }}
    </div>
</template>

<script setup>

import {onMounted, onUnmounted, ref} from "vue";

const time = ref(new Date().toLocaleTimeString());

const updateTime = () => {
    time.value = new Date().toLocaleTimeString();
};

let intervalId;

onMounted(() => {
    intervalId = setInterval(updateTime, 1000);
});

onUnmounted(() => {
    clearInterval(intervalId);
});
</script>

<style scoped>
.clock {
    font-size: 2em;
    text-align: center;
    color: white;
}
</style>
