<template>
    <ul class="pagination">
        <li v-for="(link, index) in links" :key="index" class="page-item" :class="{ 'active': link.active }">
            <Link class="page-link" :href="link.url" v-html="link.label"></Link>
        </li>
    </ul>
</template>

<script setup>
import {Link} from "@inertiajs/vue3";

defineProps(['links'])
</script>
