<template>
    <VMenu :distance="-50" :triggers="['click', 'focus']">
        <div class="dropdown-action"><i class="bi bi-three-dots"></i></div>

        <template #popper>
            <div class="list-group">
                <button
                    class="list-group-item text-success"
                    @click="openModal(question)"
                >
                    Details
                </button>
                <button
                    class="text-danger list-group-item"
                    @click="deleteQuestion"
                >
                    Delete
                </button>
            </div>
        </template>
    </VMenu>
</template>

<script setup>
import { useQuestionStore } from "@/Stores/questions.js";

const props = defineProps(["room", "question"]);
const emit = defineEmits(["view-question"]);
const questionStore = useQuestionStore();

const deleteQuestion = () => {
    questionStore.deleteQuestion(props.room.id, props.question.id);
};

function openModal(question = null) {
    emit("view-question", question);
}
</script>

<style scoped>
.dropdown-action {
    cursor: pointer;
}
</style>
