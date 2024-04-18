<template>
    <div class="card shadow p-2">
        <div class="card-body position-relative">
            <span
                class="position-absolute top-0 end-0 text-uppercase text-white bg-danger rounded-pill px-3 py-1"
                >{{ title }}</span
            >
            <span class="fs-1">
                <slot></slot>
                {{ count }}</span
            >
        </div>
    </div>
</template>

<script setup>
defineProps(["title", "count"]);
</script>
