<template>
    <button
        v-show="showMoveUpButton"
        class="move-up-button btn btn-secondary rounded-circle"
        @click="scrollToTop"
    >
        <i class="bi bi-arrow-up"></i>
    </button>
</template>

<script setup>
import { onMounted, onUnmounted, ref } from "vue";

const showMoveUpButton = ref(false);

onMounted(() => {
    window.addEventListener("scroll", checkScroll);
});

onUnmounted(() => {
    window.removeEventListener("scroll", checkScroll);
});

const checkScroll = () => {
    showMoveUpButton.value = window.pageYOffset > 300;
};

const scrollToTop = () => {
    window.scrollTo({
        top: 0,
        behavior: "smooth",
    });
};
</script>

<style scoped>
.move-up-button {
    position: fixed;
    bottom: 20px;
    right: 20px;
    z-index: 1000;
    cursor: pointer;
}
</style>
