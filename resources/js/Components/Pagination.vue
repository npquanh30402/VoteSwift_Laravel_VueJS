<template>
    <ul class="pagination">
        <li
            v-for="(link, index) in links"
            :key="index"
            class="page-item"
            :class="{ active: link.active }"
        >
            <button
                class="page-link"
                @click="loadPage(link.url, index)"
                v-html="link.label"
            ></button>
        </li>
    </ul>
</template>

<script setup>
const emit = defineEmits(["loadPage"]);

const loadPage = (url, page) => {
    try {
        emit("loadPage", url, page);
    } catch (error) {
        console.error("Error loading page:", error);
    }
};

defineProps(["links"]);
</script>
