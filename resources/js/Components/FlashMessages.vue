<template>
    <div class="d-none">
        <div v-if="flashSuccess">
            {{ toastSuccess() }}
        </div>
        <div v-if="flashError">
            {{ toastError() }}
        </div>
    </div>
</template>

<script setup>
import { computed } from "vue";
import { usePage } from "@inertiajs/vue3";
import { useToast } from "vue-toast-notification";

const toast = useToast();

const flashSuccess = computed(() => usePage().props.flash.success);
const flashError = computed(() => usePage().props.flash.error);

const toastSuccess = () => toast.success(flashSuccess.value);
const toastError = () => toast.error(flashError.value);
</script>
