<template>
    <div class="card shadow-sm border-0 mb-3 overflow-auto">
        <div class="card-header text-bg-dark text-center">Add Question/Candidate
        </div>
        <div class="card-body">
            <button class="btn btn-primary" @click="openModal(modals.addQuestionModal)">Add</button>
            <AddQuestion id="addQuestionModal" :room="room"></AddQuestion>
            <div class="mt-3 d-flex flex-column gap-3">
                <div v-for="(question, index) in paginatedQuestions" :key="question.id" class="card">
                    <div class="card-header fw-bold d-flex flex-column gap-2">
                        <div class="d-flex justify-content-between">
                            <div class="vstack w-50">
                                <p class="fs-5">#{{ index + 1 }}: {{ question.question_title }}</p>
                                <p class="text-muted text-truncate w-75">{{ question.question_description }}</p>
                            </div>
                            <div class="d-flex gap-5">
                                <img class="img-fluid" :src="question.question_image" width="128"
                                     v-if="question.question_image" style="cursor: pointer"
                                     alt="Imagquestion_descriptione" @click="showSingle">
                                <QuestionAction :question="question" @view-question="handleViewQuestion"/>
                            </div>
                        </div>
                        <QuestionSidebar @switch-tab="tabName => handleSwitchTab(tabName, index)"/>
                    </div>
                    <div class="card-body d-flex flex-column">
                        <transition name="fade" mode="out-in">
                            <component :is="tabs[currentTabs[index]]" :question="question"
                                       :candidates="roomCandidates[question.id]"></component>
                        </transition>
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
        <teleport to="body">
            <vue-easy-lightbox
                :visible="visibleRef"
                :imgs="imgsRef"
                :index="indexRef"
                @hide="onHide"
            ></vue-easy-lightbox>
        </teleport>
        <ViewQuestion :id="'viewQuestionModal' + room.id" :question="modalQuestion"/>
    </div>
</template>

<script setup>
import {computed, onMounted, reactive, ref} from "vue";
import {VueAwesomePaginate} from "vue-awesome-paginate";
import QuestionSidebar from "@/Pages/Voting/Question/QuestionSidebar.vue";
import CandidateList from "@/Pages/Voting/Question/Candidate/CandidateList.vue";
import QuestionRule from "@/Pages/Voting/Question/QuestionRule.vue";
import {useCandidateStore} from "@/Stores/candidates.js";
import AddQuestion from "@/Pages/Voting/Question/AddQuestion.vue";
import * as bootstrap from "bootstrap";
import QuestionAction from "@/Pages/Voting/Question/QuestionAction.vue";
import ViewQuestion from "@/Pages/Voting/Question/ViewQuestion.vue";
import VueEasyLightbox from "vue-easy-lightbox";

const props = defineProps(['room', 'questions'])

const modals = reactive({
    addQuestionModal: 'addQuestionModal',
    viewQuestionModal: 'viewQuestionModal' + props.room.id,
})

onMounted(() => {
    modals.addQuestionModal = new bootstrap.Modal(document.getElementById(modals.addQuestionModal));
    modals.viewQuestionModal = new bootstrap.Modal(document.getElementById(modals.viewQuestionModal));
})

function openModal(modal, question = null) {
    modal.show()
}

let modalQuestion = ref(null);
const handleViewQuestion = (question) => {
    modalQuestion.value = question;
    modals.viewQuestionModal.show();
}

const currentTabs = ref(props.questions.map(() => 'CandidateList'))

const tabs = {
    CandidateList,
    QuestionRule
}

const handleSwitchTab = (tabName, index) => {
    currentTabs.value[index] = tabName;
};

const onClickHandler = (page) => {
    currentPage.value = page
};

const currentPage = ref(1);
const paginatedQuestions = computed(() => {
    const startIndex = (currentPage.value - 1) * 5;
    const endIndex = startIndex + 5;
    return props.questions.slice(startIndex, endIndex);
});

const CandidateStore = useCandidateStore()
const roomCandidates = computed(() => CandidateStore.candidates)

onMounted(() => {
    CandidateStore.fetchCandidates(props.room.id)
})

const visibleRef = ref(false)
const indexRef = ref(0)
const imgsRef = ref([])

const onShow = () => {
    visibleRef.value = true
}

const showSingle = (e) => {
    imgsRef.value = e.target.src
    onShow()
}

const onHide = () => {
    visibleRef.value = false
}
</script>
