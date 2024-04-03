<template>
    <div class="card shadow-sm border-0 mb-3 overflow-auto">
        <div class="card-header text-bg-dark text-center">Add Question
        </div>
        <!--        <AddQuestion id="addQuestionModal" :room="room"></AddQuestion>-->
        <div class="card-body">
            <!--            <p>Total Questions: {{ questions.length }}</p>-->
            <!--            <button class="btn btn-primary" @click="openModal(modals.addQuestionModal)">Add</button>-->
            <div class="mt-3 d-flex flex-column gap-3">
                <div v-for="(question, index) in paginatedQuestions" :key="question.id" class="card">
                    <div class="card-header fw-bold d-flex flex-column gap-2">
                        <p>#{{ index + 1 }}: {{ question.question_title }}</p>
                        <QuestionSidebar/>
                    </div>
                    <div class="card-body d-flex flex-column">
                        <transition name="fade" mode="out-in">
                            <component :is="tabs[currentTab]"></component>
                        </transition>
                        <!--                        <div class="truncate-text">-->
                        <!--                            <MdPreview :editorId="'question' + question.id"-->
                        <!--                                       :modelValue="question.question_description"/>-->
                        <!--                        </div>-->
                        <!--                        <div class="d-flex justify-content-between">-->
                        <!--                            <Link :href="route('candidate.main', question.id)" class="btn btn-primary">-->
                        <!--                                Candidates-->
                        <!--                            </Link>-->
                        <!--                            <div class="hstack gap-3">-->
                        <!--                                <button class="btn btn-secondary"-->
                        <!--                                        @click="openModal(modals.questionDetailsModal, question)">Details-->
                        <!--                                </button>-->
                        <!--                                <button class="btn btn-danger"-->
                        <!--                                        @click="openModal(modals.deleteQuestionModal, question)">Delete-->
                        <!--                                </button>-->
                        <!--                            </div>-->
                        <!--                        </div>-->
                    </div>
                </div>
            </div>
        </div>
        <div class="d-flex justify-content-end">
            <vue-awesome-paginate
                :total-items="questions.length"
                :items-per-page="5"
                :max-pages-shown="5"
                v-model="currentPage"
                :on-click="onClickHandler"
            />
        </div>
        <!--        <QuestionDetails :question="modalQuestion"-->
        <!--                         id="questionDetailsModal"></QuestionDetails>-->
        <!--        <DeleteQuestion :question="modalQuestion" id="deleteQuestionModal"></DeleteQuestion>-->
    </div>
</template>

<script setup>
import {router, usePage} from "@inertiajs/vue3";
import AddQuestion from "@/Pages/Voting/Question/AddQuestion.vue";
import {computed, onMounted, reactive, ref} from "vue";
import * as bootstrap from 'bootstrap'
import QuestionDetails from "@/Pages/Voting/Question/QuestionDetails.vue";
import {VueAwesomePaginate} from "vue-awesome-paginate";
import DeleteQuestion from "@/Pages/Voting/Question/DeleteQuestion.vue";
import {route} from "ziggy-js";
import {Link} from "@inertiajs/vue3";
import {MdPreview} from "md-editor-v3";
import QuestionSidebar from "@/Pages/Voting/Question/QuestionSidebar.vue";
import CandidateList from "@/Pages/Voting/Question/Candidate/CandidateList.vue";


const currentTab = ref('CandidateList')

const tabs = {
    CandidateList
}

const handleSwitchTab = (tabName) => {
    currentTab.value = tabName;
};

const modals = reactive({
    addQuestionModal: 'addQuestionModal',
    questionDetailsModal: 'questionDetailsModal',
    deleteQuestionModal: 'deleteQuestionModal'
})

let modalQuestion = ref(null);

// onMounted(() => {
//     modals.addQuestionModal = new bootstrap.Modal(document.getElementById(modals.addQuestionModal));
//     modals.questionDetailsModal = new bootstrap.Modal(document.getElementById(modals.questionDetailsModal));
//     modals.deleteQuestionModal = new bootstrap.Modal(document.getElementById(modals.deleteQuestionModal));
// })

function openModal(modal, question = null) {
    modalQuestion.value = question
    modal.show()
}

const props = defineProps(['room', 'questions'])

let urlPrev = computed(() => usePage().props.urlPrev);
const back = () => {
    if (urlPrev !== 'empty') {
        router.visit(urlPrev)
    }
}

const onClickHandler = (page) => {
    currentPage.value = page
};

const currentPage = ref(1);
const paginatedQuestions = computed(() => {
    const startIndex = (currentPage.value - 1) * 5;
    const endIndex = startIndex + 5;
    return props.questions.slice(startIndex, endIndex);
});

const roomCandidates = ref([]);
axios.get(route('api.room.candidate.index', props.room.id))
    .then((response) => {
        roomCandidates.value = response.data
    })
</script>
