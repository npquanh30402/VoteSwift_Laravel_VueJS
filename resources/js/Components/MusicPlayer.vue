<template>
    <div class="vstack justify-content-center">
        <p>{{ title }}</p>
        <audio-player
            ref="audioPlayerRef"
            :audio-list="audioList.map((elm) => elm.url)"
            :before-play="handleBeforePlay"
        />
    </div>
</template>

<script setup>
import { computed, nextTick, ref, watch } from "vue";

const props = defineProps(["music", "currentFile"]);
const audioPlayerRef = ref(null);
const title = ref("");
const audioList = computed(() => props.music);
const currentFile = computed(() => props.currentFile || null);
const handleBeforePlay = (next) => {
    title.value = audioList.value[audioPlayerRef.value.currentPlayIndex].title;
    next();
};

const handlePlaySpecify = () => {
    const index = audioList.value.findIndex(
        (item) => item.url === currentFile.value.url,
    );
    if (index !== -1) {
        audioPlayerRef.value.currentPlayIndex = index;
        nextTick(() => {
            audioPlayerRef.value.play();
            title.value = audioList.value[index].title;
        });
    }
};

watch(
    () => props.currentFile,
    () => {
        if (props.currentFile === null) {
            audioPlayerRef.value.pause();
        } else {
            handlePlaySpecify();
        }
    },
);
</script>
