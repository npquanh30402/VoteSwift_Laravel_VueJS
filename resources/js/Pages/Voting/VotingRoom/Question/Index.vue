<template>
    <div>
        <div>
            <button
                class="btn btn-primary"
                @click="openModal(modals.addQuestionModal)"
            >
                Add
            </button>
            <AddQuestion
                id="addQuestionModal"
                :room="room"
                @handleTab="populateTabs"
            ></AddQuestion>
            <div class="mt-3 d-flex flex-column gap-3">
                <div
                    v-for="(question, index) in paginatedQuestions"
                    :key="question.id"
                    class="card"
                >
                    <div class="card-header fw-bold d-flex flex-column gap-2">
                        <div class="d-flex justify-content-between">
                            <div class="vstack w-50">
                                <p class="fs-5 text-truncate pe-5">
                                    #{{ index + 1 }}:
                                    {{ question.question_title }}
                                </p>
                                <p class="text-muted text-truncate pe-5">
                                    {{ question.question_description }}
                                </p>
                            </div>
                            <div class="d-flex gap-5">
                                <img
                                    v-if="question.question_image"
                                    :src="question.question_image"
                                    alt=""
                                    class="img-fluid"
                                    style="cursor: pointer"
                                    width="128"
                                    @click="showImage"
                                />
                                <QuestionAction
                                    :question="question"
                                    :room="room"
                                    @view-question="handleViewQuestion"
                                />
                            </div>
                        </div>
                        <QuestionSidebar
                            @switch-tab="
                                (tabName) => handleSwitchTab(tabName, index)
                            "
                        />
                    </div>
                    <div class="card-body d-flex flex-column">
                        <transition mode="out-in" name="fade">
                            <component
                                :is="tabs[currentTabs[index]]"
                                :candidates="roomCandidates[question.id]"
                                :question="question"
                            ></component>
                        </transition>
                    </div>
                </div>
            </div>
        </div>
        <div class="mt-3 d-flex justify-content-end">
            <vue-awesome-paginate
                v-model="currentPage"
                :items-per-page="5"
                :max-pages-shown="5"
                :on-click="onClickHandler"
                :total-items="questions?.length || 0"
            />
        </div>
        <div class="others">
            <teleport to="body">
                <LightBoxHelper :currentImageDisplay="currentImageDisplay" />
            </teleport>
            <ViewQuestion
                :id="'viewQuestionModal' + room.id"
                :question="modalQuestion"
                :room="room"
            />
        </div>
    </div>
</template>

<script setup>
import { computed, onMounted, reactive, ref, watch } from "vue";
import { VueAwesomePaginate } from "vue-awesome-paginate";
import QuestionSidebar from "@/Pages/Voting/VotingRoom/Question/QuestionSidebar.vue";
import CandidateList from "@/Pages/Voting/VotingRoom/Question/Candidate/CandidateList.vue";
import QuestionRule from "@/Pages/Voting/VotingRoom/Question/QuestionRule.vue";
import { useCandidateStore } from "@/Stores/candidates.js";
import AddQuestion from "@/Pages/Voting/VotingRoom/Question/AddQuestion.vue";
import * as bootstrap from "bootstrap";
import QuestionAction from "@/Pages/Voting/VotingRoom/Question/QuestionAction.vue";
import ViewQuestion from "@/Pages/Voting/VotingRoom/Question/ViewQuestion.vue";
import { useQuestionStore } from "@/Stores/questions.js";
import LightBoxHelper from "@/Components/Helpers/LightBoxHelper.vue";

const props = defineProps(["room"]);
const questionStore = useQuestionStore();
const CandidateStore = useCandidateStore();
const questions = computed(() => questionStore.questions[props.room.id]);
const roomCandidates = computed(() => CandidateStore.candidates);
const currentImageDisplay = ref(null);
const currentTabs = ref(["CandidateList"]);
const currentPage = ref(1);
const paginatedQuestions = computed(() => {
    const startIndex = (currentPage.value - 1) * 5;
    const endIndex = startIndex + 5;
    return questions.value?.slice(startIndex, endIndex);
});

watch(
    () => questions.value,
    () => {
        populateTabs();
    },
);

const tabs = {
    CandidateList,
    QuestionRule,
};

const modalQuestion = ref(null);
const modals = reactive({
    addQuestionModal: "addQuestionModal",
    viewQuestionModal: "viewQuestionModal" + props.room.id,
});

const populateTabs = () => {
    if (questions.value) {
        currentTabs.value = questions.value.map(() => "CandidateList");
    }
};

onMounted(() => {
    questionStore.fetchQuestions(props.room.id);
    CandidateStore.fetchCandidates(props.room.id);

    populateTabs();

    modals.addQuestionModal = new bootstrap.Modal(
        document.getElementById(modals.addQuestionModal),
    );
    modals.viewQuestionModal = new bootstrap.Modal(
        document.getElementById(modals.viewQuestionModal),
    );
});

function openModal(modal, question = null) {
    modal.show();
}

const handleViewQuestion = (question) => {
    modalQuestion.value = question;
    modals.viewQuestionModal.show();
};

const handleSwitchTab = (tabName, index) => {
    currentTabs.value[index] = tabName;
};

const onClickHandler = (page) => {
    currentPage.value = page;
};

const showImage = (e) => {
    currentImageDisplay.value = e;
};
</script>