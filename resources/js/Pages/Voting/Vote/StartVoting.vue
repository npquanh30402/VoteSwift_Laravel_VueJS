<template>
    <form @submit.prevent="submitVotes" class="vstack gap-5 align-items-center">
        <BaseCard class="w-75 shadow shadow-sm" v-for="(question, index) in questions" :key="question.id">
            <template #title>
                <h3 class="fw-semibold">Question {{ index + 1 }}: {{ question.question_title }}</h3>
            </template>
            <template #description>
                <p class="text-truncate" style="width: 90%">{{ question.question_description }}</p>
            </template>
            <div class="row mx-2">
                <div class="col-md-6">
                    <p class="text-muted" v-if="question.allow_multiple_votes">You can choose <span
                        class="fw-bold text-uppercase">multiple</span> options</p>
                    <p class="text-muted" v-else>You can only choose <span class="fw-bold text-uppercase">one</span>
                        option
                    </p>
                    <div v-for="(candidate, candidateIndex) in question.candidates" :key="candidate.id">
                        <div class="form-check ms-4 mt-2">
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
                            <label class="form-check-label fs-4 text-truncate w-75"
                                   :for="candidate.id + candidateIndex">
                                {{ candidate.candidate_title }}
                            </label>
                            <p class="text-truncate text-muted fs-6 w-75">{{ candidate.candidate_description }}</p>
                        </div>
                    </div>
                </div>
                <div class="card col-md-6">
                    <div class="card-body">
                        <BarChart :options="options"
                                  :labels="trimText(combinedCounts[question.id]?.candidateLabels, 10)"
                                  :datasets="combinedCounts[question.id]?.voteCounts"/>
                    </div>
                </div>
            </div>
        </BaseCard>

        <div class="text-center my-5">
            <button type="submit" class="btn-lg btn btn-primary">Submit</button>
        </div>
    </form>
</template>

<script setup>
import BaseCard from "@/Components/BaseCard.vue";
import {computed, onMounted, ref} from "vue";
import {router, usePage} from "@inertiajs/vue3";
import {route} from "ziggy-js";
import BarChart from "@/Components/BarChart.vue";
import {useVotingResultStore} from "@/Stores/voting-results.js";
import axios from "axios";

const props = defineProps(['questions', 'room'])
const votingResultStore = useVotingResultStore()
const votingResult = computed(() => votingResultStore.results[props.room.id])
const authUser = computed(() => usePage().props.authUser.user);

const combinedCounts = ref({});

const updateVoteCount = (questionId, candidateId, change) => {
    if (combinedCounts.value.hasOwnProperty(questionId)) {
        const index = combinedCounts.value[questionId].candidateIds.indexOf(candidateId);
        if (index !== -1) {
            const newVoteCounts = [...combinedCounts.value[questionId].voteCounts];
            newVoteCounts[index] += change;

            combinedCounts.value = {
                ...combinedCounts.value,
                [questionId]: {
                    ...combinedCounts.value[questionId],
                    voteCounts: newVoteCounts
                }
            };
        }
    }
};

const updateCombinedCounts = (result) => {
    const counts = {};

    if (result) {
        for (const questionId in result) {
            const questionData = result[questionId];
            counts[questionId] = {
                candidateIds: questionData.map(candidate => candidate.id),
                voteCounts: questionData.map(candidate => candidate.vote_count),
                candidateLabels: questionData.map(candidate => candidate.candidate_title)
            };
        }
    }

    combinedCounts.value = counts;
};

onMounted(async () => {
    await votingResultStore.fetchResults(props.room.id)
    updateCombinedCounts(votingResultStore.results[props.room.id])
})

const selectedOptions = ref({});

const onClickCheck = async (questionId, candidateId) => {
    const formData = new FormData();
    formData.append('questionId', questionId);
    formData.append('candidateId', candidateId);

    await axios.post(route('api.room.vote.broadcast.choice', props.room.id), formData);

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
const currentChoice = ref({});

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
    await axios.post(route('api.room.vote.broadcast.choice', props.room.id), formData);
};

const handleReceivedChoice = (e) => {
    const questionId = e.question_id;
    const candidateId = parseInt(e.candidate_id);

    if (e.question_type.allow_multiple_votes === 1) {
        if (!selectedOptions.value.hasOwnProperty(questionId)) {
            selectedOptions.value[questionId] = [];
        }

        const isSelected = selectedOptions.value[questionId].includes(candidateId);
        if (isSelected === false) {
            updateVoteCount(questionId, candidateId, 1);
        } else {
            selectedOptions.value[questionId].forEach((oldCandidateId) => {
                if (oldCandidateId === candidateId) {
                    updateVoteCount(questionId, oldCandidateId, -1);
                }
            });
        }
    } else {
        const oldCandidateId = currentChoice.value[questionId];
        if (oldCandidateId) {
            updateVoteCount(questionId, oldCandidateId, -1);
        }

        currentChoice.value[questionId] = candidateId;

        updateVoteCount(questionId, candidateId, 1);
    }
};

const setupEchoListeners = () => {
    if (authUser.value) {
        Echo.private(`voting.choice.${props.room.id}`).listen("VotingChoice", handleReceivedChoice);
    }
};

onMounted(() => {
    setupEchoListeners()
    window.scrollTo({top: 0, left: 0, behavior: 'smooth'});
})

function submitVotes() {
    router.post(route('vote.store', props.room.id), {
        selectedOptions: selectedOptions.value
    })
}

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
