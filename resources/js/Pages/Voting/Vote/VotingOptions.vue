<template>
    <div class="vstack gap-5 align-items-center">
        <BaseCard class="w-75 shadow shadow-sm" v-for="(question, index) in questions" :key="question.id">
            <template #description>
                <div class="row justify-content-between align-items-center">
                    <div class="col-md-10 hstack">
                        <img class="img-fluid me-3 img-style" :src="question.question_image" alt=""
                             v-if="question.question_image" @click="showImage">
                        <div class="w-100">
                            <h3 class="fw-semibold fs-4 text-uppercase text-truncate">Question {{ index + 1 }}: {{
                                    question.question_title
                                }}</h3>
                            <p class="text-truncate fs-5 text-muted" style="width: 50vw">{{
                                    helper.removeSpecialCharacters(question.question_description)
                                }}</p>
                        </div>
                    </div>
                    <div class="col-md-2 hstack justify-content-end align-items-center">
                        <QuestionInfo :question="question"/>
                    </div>
                </div>
            </template>
            <div class="row mx-2">
                <div class="col-md-6">
                    <p class="text-muted" v-if="question.allow_multiple_votes">You can choose <span
                        class="fw-bold text-uppercase">multiple</span> options</p>
                    <p class="text-muted" v-else>You can only choose <span class="fw-bold text-uppercase">one</span>
                        option
                    </p>
                    <div v-for="(candidate, candidateIndex) in question.candidates" :key="candidate.id" class="mb-5">
                        <div class="form-check ms-4 mt-2">
                            <div class="d-flex justify-content-between gap-3 align-items-center">
                                <div class="w-100">
                                    <div>
                                        <input
                                            v-if="question.allow_multiple_votes"
                                            @click="onClickCheck(question.id, candidate.id)"
                                            class="form-check-input fs-3"
                                            type="checkbox"
                                            :name="question.id"
                                            :id="candidate.id + candidateIndex">
                                        <input
                                            v-else
                                            @click="onClickRadio(question.id, candidate.id)"
                                            class="form-check-input fs-3"
                                            type="radio"
                                            :name="question.id"
                                            :id="candidate.id + candidateIndex">
                                    </div>
                                    <div>
                                        <label class="form-check-label fs-4 text-truncate" style="width: 30rem;"
                                               :for="candidate.id + candidateIndex">
                                            {{ candidate.candidate_title }}
                                        </label>
                                        <p class="text-truncate text-muted" style="width: 30rem;">{{
                                                helper.removeSpecialCharacters(candidate.candidate_description)
                                            }}
                                        </p>
                                    </div>
                                </div>
                                <div
                                    class="d-flex justify-content-between align-items-center me-4">
                                    <img class="img-fluid me-3 img-style" :src="candidate.candidate_image" alt=""
                                         v-if="candidate.candidate_image" @click="showImage">
                                    <CandidateInfo :candidate="candidate"/>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card col-md-6">
                    <div class="card-body">
                        <BarChart :options="options"
                                  :labels="trimText(voteCounts[question.id]?.candidateLabels, 10)"
                                  :datasets="voteCounts[question.id]?.voteCounts"/>
                    </div>
                </div>
            </div>
        </BaseCard>
        <teleport to="body">
            <LightBoxHelper :currentImageDisplay="currentImageDisplay"/>
        </teleport>
    </div>
</template>
<script setup>
import BaseCard from "@/Components/BaseCard.vue";
import BarChart from "@/Components/BarChart.vue";
import {computed, ref} from "vue";
import {useVotingResultStore} from "@/Stores/voting-results.js";
import QuestionInfo from "@/Pages/Voting/Vote/QuestionInfo.vue";
import LightBoxHelper from "@/Components/Helpers/LightBoxHelper.vue";
import {useHelper} from "@/Services/helper.js";
import CandidateInfo from "@/Pages/Voting/Vote/CandidateInfo.vue";

const props = defineProps(['room', 'questions', 'voteCounts'])
const votingResultStore = useVotingResultStore()
const helper = useHelper()
const currentImageDisplay = ref(null)
const voteCounts = computed(() => props.voteCounts)

const selectedOptions = ref({});

const onClickCheck = async (questionId, candidateId) => {
    const formData = new FormData();
    formData.append('questionId', questionId);
    formData.append('candidateId', candidateId);

    await votingResultStore.broadCastChoice(props.room.id, formData)

    if (!selectedOptions.value.hasOwnProperty(questionId)) {
        selectedOptions.value[questionId] = [candidateId];
    } else {
        const index = selectedOptions.value[questionId].indexOf(candidateId);
        if (index !== -1) {
            selectedOptions.value[questionId].splice(index, 1);
        } else {
            selectedOptions.value[questionId].push(candidateId);
        }
    }
};

const onClickRadio = async (questionId, candidateId) => {
    const formData = new FormData();
    formData.append('questionId', questionId);
    formData.append('candidateId', candidateId);

    if (selectedOptions.value.hasOwnProperty(questionId)) {
        const index = selectedOptions.value[questionId].indexOf(candidateId);
        if (index === -1) {
            selectedOptions.value[questionId].push(candidateId);
        }
    } else {
        selectedOptions.value[questionId] = [candidateId];
    }
    await votingResultStore.broadCastChoice(props.room.id, formData)
};

function trimText(textArray, length) {
    if (!Array.isArray(textArray)) {
        return [];
    }

    return textArray.map(text => {
        if (typeof text !== 'string') {
            return '';
        }

        return text.length > length ? text.substring(0, length) : text;
    });
}

const showImage = (e) => {
    currentImageDisplay.value = e;
}

const options = {
    responsive: true,
    backgroundColor: [
        'rgba(255, 99, 132, 0.2)',
        'rgba(255, 159, 64, 0.2)',
        'rgba(255, 205, 86, 0.2)',
        'rgba(75, 192, 192, 0.2)',
        'rgba(54, 162, 235, 0.2)',
        'rgba(153, 102, 255, 0.2)',
        'rgba(201, 203, 207, 0.2)'
    ],
    borderColor: [
        'rgb(255, 99, 132)',
        'rgb(255, 159, 64)',
        'rgb(255, 205, 86)',
        'rgb(75, 192, 192)',
        'rgb(54, 162, 235)',
        'rgb(153, 102, 255)',
        'rgb(201, 203, 207)'
    ],
    borderWidth: 1,
    indexAxis: 'y',
    maintainAspectRatio: false,
}
</script>

<style scoped>
.img-style {
    border-radius: 4px;
    box-shadow: rgba(0, 0, 0, 0.024) 0px 0px 0px 1px,
    rgba(0, 0, 0, 0.05) 0px 1px 0px 0px,
    rgba(0, 0, 0, 0.03) 0px 0px 8px 0px,
    rgba(0, 0, 0, 0.1) 0px 20px 30px 0px;

    height: 8rem;
    cursor: pointer;
}
</style>
