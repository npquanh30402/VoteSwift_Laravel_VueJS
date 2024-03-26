<template>
    <div class="vstack justify-content-center">
        <p class="text-white">{{ title }}</p>
        <audio-player
            ref="audioPlayerRef"
            :audio-list="audioList.map((elm) => elm.url)"
            :before-play="handleBeforePlay"
        />
    </div>
</template>

<script setup>
import {ref} from 'vue';
import {usePage} from "@inertiajs/vue3";

const music = usePage().props.music;

const audioPlayerRef = ref(null);
const title = ref("");
const audioList = ref(music);

const handleBeforePlay = (next) => {
    title.value = audioList.value[audioPlayerRef.value.currentPlayIndex].title;
    next();
};
</script>
