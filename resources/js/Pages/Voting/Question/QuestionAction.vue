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
import {useQuestionStore} from "@/Stores/questions.js";

const props = defineProps(['room', 'question'])
const emit = defineEmits(['view-question'])

const questionStore = useQuestionStore()

const deleteQuestion = async () => {
    await questionStore.deleteQuestion(props.room.id, props.question.id)
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
