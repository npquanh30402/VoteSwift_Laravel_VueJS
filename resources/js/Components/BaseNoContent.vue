<template>
    <div class="position-relative">
        <div
            class="border border-2 border-success rounded p-3 d-flex justify-content-center align-items-center text-center position-relative"
            style="min-height: 60vh; filter: blur(5px); top: 0; left: 0; right: 0; bottom: 0;">
            <slot></slot>
        </div>
        <div class="position-absolute top-50 start-50 translate-middle" style="z-index: 1;">
            <div class="w-100 h-100 fs-1">
                <i class="bi bi-cloud-snow"></i>
                No content
            </div>
        </div>
    </div>
</template>
