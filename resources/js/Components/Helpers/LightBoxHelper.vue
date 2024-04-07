<template>
    <vue-easy-lightbox
        :visible="visible"
        :imgs="imgs"
        :index="index"
        @hide="onHide"
    ></vue-easy-lightbox>
</template>

<script setup>
import {computed, ref, watch} from 'vue';
import VueEasyLightbox from "vue-easy-lightbox";

const props = defineProps(['currentImageDisplay'])

const visible = ref(false);
const index = ref(0);
const imgs = ref([]);
const currentImageDisplay = computed(() => props.currentImageDisplay);

const onShow = () => {
    visible.value = true;
};

const showSingle = () => {
    imgs.value = [currentImageDisplay.value.target.src];
    onShow();
};

watch(() => currentImageDisplay.value, () => {
    showSingle()
})
const onHide = () => {
    visible.value = false;
};
</script>
