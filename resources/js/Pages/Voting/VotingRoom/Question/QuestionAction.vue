<template>
    <VDropdown :autoHide="false" :distance="-50" :triggers="['click', 'focus']">
        <div class="dropdown-action"><i class="bi bi-three-dots"></i></div>

        <template #popper>
            <div class="list-group">
                <ViewQuestion :question="question" :room="room" />
                <button
                    class="text-danger list-group-item"
                    @click="deleteQuestion"
                >
                    Delete
                </button>
                <button
                    v-close-popper
                    class="bg-dark text-white list-group-item"
                >
                    Close
                </button>
            </div>
        </template>
    </VDropdown>
</template>

<script setup>
import { useQuestionStore } from "@/Stores/questions.js";
import ViewQuestion from "@/Pages/Voting/VotingRoom/Question/ViewQuestion.vue";

const props = defineProps(["room", "question"]);
const questionStore = useQuestionStore();

const deleteQuestion = () => {
    questionStore.deleteQuestion(props.room.id, props.question.id);
};
</script>

<style scoped>
.dropdown-action {
    cursor: pointer;
}
</style>
