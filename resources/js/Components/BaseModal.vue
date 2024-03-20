<template>
    <div class="modal fade" :id="modalId" tabindex="-1" data-bs-backdrop="static" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ title }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <slot></slot>
                </div>
                <div class="modal-footer">
                    <slot name="footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </slot>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
defineProps([
    'modalId',
    'title',
])
</script>

<style scoped>
.modal {
    backdrop-filter: blur(5px);
}
</style>
