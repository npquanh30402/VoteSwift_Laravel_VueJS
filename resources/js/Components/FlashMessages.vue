<template>
    <div>
        <div class="alert alert-success w-50 mx-auto mt-3" v-if="flashSuccess">
            {{ flashSuccess }}
        </div>
        <div class="alert alert-danger w-50 mx-auto mt-3" v-else-if="flashError">
            {{ flashError }}
        </div>
    </div>
</template>

<script setup>
import {computed} from "vue";
import {usePage} from "@inertiajs/vue3";

const flashSuccess = computed(() => usePage().props.flash.success);
const flashError = computed(() => usePage().props.flash.error);
</script>
