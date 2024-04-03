<template>
    <div class="card shadow-sm border-0 mb-3 overflow-auto">
        <div class="card-header text-bg-dark text-center">Add Question
        </div>
        <div class="card-body">
            <div class="mt-3 d-flex flex-column gap-3">
                <div v-for="(question, index) in paginatedQuestions" :key="question.id" class="card">
                    <div class="card-header fw-bold d-flex flex-column gap-2">
                        <p>#{{ index + 1 }}: {{ question.question_title }}</p>
                        <QuestionSidebar @switch-tab="tabName => handleSwitchTab(tabName, index)"/>
                    </div>
                    <div class="card-body d-flex flex-column">
                        <component :is="tabs[currentTabs[index]]" :question="question"
                                   :candidates="roomCandidates[question.id]"></component>
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
    </div>
</template>

<script setup>
import {computed, onMounted, ref} from "vue";
import {VueAwesomePaginate} from "vue-awesome-paginate";
import QuestionSidebar from "@/Pages/Voting/Question/QuestionSidebar.vue";
import CandidateList from "@/Pages/Voting/Question/Candidate/CandidateList.vue";
import QuestionRule from "@/Pages/Voting/Question/QuestionRule.vue";
import {useCandidateStore} from "@/Stores/candidates.js";

const props = defineProps(['room', 'questions'])
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
</script>
