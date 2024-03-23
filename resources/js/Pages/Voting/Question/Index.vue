<template>
    <div class="my-3">
        <div class="row justify-content-center mb-3">
            <div class="col-md-3">
                <a @click="back" class="btn btn-secondary small">Back</a>
            </div>
            <div class="col-md-7 gap-3 align-items-center"></div>
        </div>
        <div class="row justify-content-center">
            <div class="col-md-2">
                <BallotSidebar :room="room"></BallotSidebar>
            </div>
            <div class="col-md-9">
                <div class="card shadow-sm border-0 mb-3 overflow-auto">
                    <div class="card-header text-bg-dark text-center">Add Question
                    </div>
                    <AddQuestion id="addQuestionModal" :room="room"></AddQuestion>
                    <div class="card-body">
                        <button class="btn btn-primary" @click="openModal(modals.addQuestionModal)">Add</button>
                        <div class="mt-3 d-flex flex-column gap-3">
                            <div v-for="question in questions" :key="question.id" class="card">
                                <div class="card-header fw-bold d-flex align-items-center gap-2">
                                    {{ question.question_title }}
                                </div>
                                <div class="card-body d-flex flex-column">
                                    <p class="card-text text-truncate">{{ question.question_description }}</p>
                                    <div class="d-flex justify-content-between">
                                        <a href="#" class="btn btn-primary">Add Candidates</a>
                                        <button href="#" class="btn btn-secondary"
                                                @click="openModal(modals.questionDetailsModal, question)">Details
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <QuestionDetails :question="modalQuestion"
                     id="questionDetailsModal"></QuestionDetails>
</template>

<script setup>
import {router, usePage} from "@inertiajs/vue3";
import BallotSidebar from "@/Pages/Voting/BallotSidebar.vue";
import AddQuestion from "@/Pages/Voting/Question/AddQuestion.vue";
import {onMounted, reactive, ref} from "vue";
import * as bootstrap from 'bootstrap'
import QuestionDetails from "@/Pages/Voting/Question/QuestionDetails.vue";

const modals = reactive({
    addQuestionModal: 'addQuestionModal',
    questionDetailsModal: 'questionDetailsModal'
})

let modalQuestion = ref(null);

onMounted(() => {
    modals.addQuestionModal = new bootstrap.Modal(document.getElementById(modals.addQuestionModal));
    modals.questionDetailsModal = new bootstrap.Modal(document.getElementById(modals.questionDetailsModal));
})

const props = defineProps(['room', 'questions'])

function openModal(modal, question = null) {
    modalQuestion.value = question
    modal.show()
}

let urlPrev = usePage().props.urlPrev;
const back = () => {
    if (urlPrev !== 'empty') {
        router.visit(urlPrev)
    }
}
</script>
