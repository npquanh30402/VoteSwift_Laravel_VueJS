<template>
    <div class="card">
        <div class="card-body">
            <h5 class="card-title">
                <slot name="title"></slot>
            </h5>
            <p class="card-text">
                <slot name="description"></slot>
            </p>
        </div>
        <div class="card-body">
            <slot></slot>
        </div>
    </div>
</template>

<script setup>
// const props = defineProps(['title', 'description'])
</script>
