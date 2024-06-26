<template>
    <div v-if="isLoading">
        <div>
            <div class="hstack justify-content-between">
                <AddQuestion v-if="!isPublish" :room="room"></AddQuestion>
                <div v-if="!isPublish" class="col-md-3">
                    <div class="hstack justify-content-between">
                        <label class="form-label" for="csv_file"
                            >Import Questions:</label
                        >
                        <VTooltip :skidding="-48">
                            <i class="bi bi-info-circle"></i>

                            <template #popper>
                                <div>
                                    <p>The file must be in CSV format.</p>
                                    <code>
                                        question_title,question_description,allow_multiple_votes,allow_skipping
                                    </code>
                                    <br />
                                    <code
                                        >Which programming language do you
                                        like?,Which language do you enjoy
                                        working with?,true,false</code
                                    >
                                    <br />
                                    <code>...</code>
                                </div>
                            </template>
                        </VTooltip>
                    </div>
                    <input
                        id="csv_file"
                        class="form-control form-control-sm"
                        type="file"
                        @change="importQuestions"
                    />
                </div>
            </div>
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
                                :room="room"
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
        </div>
    </div>
    <BaseLoading v-else />
</template>

<script setup>
import { computed, onMounted, ref, watch } from "vue";
import { VueAwesomePaginate } from "vue-awesome-paginate";
import QuestionSidebar from "@/Pages/Voting/VotingRoom/Question/QuestionSidebar.vue";
import CandidateList from "@/Pages/Voting/VotingRoom/Question/Candidate/CandidateList.vue";
import QuestionRule from "@/Pages/Voting/VotingRoom/Question/QuestionRule.vue";
import { useCandidateStore } from "@/Stores/candidates.js";
import AddQuestion from "@/Pages/Voting/VotingRoom/Question/AddQuestion.vue";
import QuestionAction from "@/Pages/Voting/VotingRoom/Question/QuestionAction.vue";
import { useQuestionStore } from "@/Stores/questions.js";
import LightBoxHelper from "@/Components/Helpers/LightBoxHelper.vue";
import BaseLoading from "@/Components/BaseLoading.vue";

const props = defineProps(["room"]);
const questionStore = useQuestionStore();
const CandidateStore = useCandidateStore();
const questions = computed(() => questionStore.questions[props.room.id]);
const roomCandidates = computed(() => CandidateStore.candidates[props.room.id]);
const currentImageDisplay = ref(null);
const currentTabs = ref(["CandidateList"]);
const currentPage = ref(1);
const paginatedQuestions = computed(() => {
    const startIndex = (currentPage.value - 1) * 5;
    const endIndex = startIndex + 5;
    return questions.value?.slice(startIndex, endIndex);
});
const isPublish = computed(() => props.room.is_published === 1);
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

const populateTabs = () => {
    if (questions.value) {
        currentTabs.value = questions.value.map(() => "CandidateList");
    }
};

const isLoading = ref(false);

onMounted(async () => {
    await questionStore.fetchQuestions(props.room.id);
    await CandidateStore.fetchCandidates(props.room.id);

    populateTabs();

    isLoading.value = true;
});

const handleSwitchTab = (tabName, index) => {
    currentTabs.value[index] = tabName;
};

const onClickHandler = (page) => {
    currentPage.value = page;
};

const showImage = (e) => {
    currentImageDisplay.value = e;
};

const importQuestions = (event) => {
    const file = event.target.files[0];

    if (!file) {
        return;
    }

    const formData = new FormData();
    formData.append("csv_file", file);

    questionStore.importQuestions(props.room.id, formData);
};
</script>
