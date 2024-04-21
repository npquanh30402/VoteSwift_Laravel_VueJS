<template>
    <div class="card flex-fill shadow-sm">
        <div class="card-header">
            <slot name="header"></slot>
        </div>
        <div class="card-body vstack justify-content-between">
            <slot></slot>
        </div>
    </div>
</template>

<script setup></script>
