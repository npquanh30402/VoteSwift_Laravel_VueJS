<template>
    <div class="dropdown">
        <div class="dropdown-action" data-bs-toggle="dropdown" aria-expanded="false">
            <i class="bi bi-three-dots"></i>
        </div>
        <ul class="dropdown-menu">
            <li>
                <button class="dropdown-item text-success" @click="openModal(question)">
                    Details
                </button>
            </li>
            <li>
                <button class="dropdown-item text-danger" @click="deleteQuestion">Delete</button>
            </li>
        </ul>
    </div>
</template>

<script setup>
import {router} from "@inertiajs/vue3";
import {route} from "ziggy-js";

const props = defineProps(['question'])
const emit = defineEmits(['view-question'])

const deleteQuestion = () => {
    router.delete(route('question.delete', props.question.id));
}

function openModal(question = null) {
    emit('view-question', question)
}
</script>

<style scoped>
.dropdown-action {
    cursor: pointer;
}
</style>
