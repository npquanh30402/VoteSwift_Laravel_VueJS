<template>
    <div class="card flex-fill shadow-sm">
        <div class="card-header">
            <slot name="header"></slot>
        </div>
        <div class="card-body d-flex justify-content-center align-items-center">
            <div class="fs-5">
                <slot></slot>
            </div>
        </div>
    </div>
</template>

<script setup></script>
