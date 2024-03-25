<template>
    <div class="row mb-5">
        <div class="col">
            <h1 class="display-6 text-center fw-bold">Result: {{ room.room_name }}</h1>
        </div>
    </div>
    <div class="my-3">
        <div class="row justify-content-center">
            <div class="col-md-2">
                <BallotSidebar :room="room"></BallotSidebar>
            </div>
            <div class="col-md-9">
                <div class="mb-3">
                    <button type="button" class="btn btn-primary position-relative" disabled>
                        Realtime
                        <span
                            class="position-absolute top-0 start-100 translate-middle p-2 bg-danger border border-light rounded-circle animate__animated animate__flash animate__infinite animate__slow"></span>
                    </button>
                </div>
                <div class="vstack gap-4">
                    <div class="card shadow shadow-sm container-fluid p-3" v-for="(result, index) in rec_nestedResults"
                         :key="index">
                        <div class="row justify-content-center align-items-center">
                            <div class="col-md-7">
                                <h3 class="fw-semibold">Question {{ index + 1 }}: {{ result.question_title }}</h3>
                                <div v-for="(candidate, candidateIndex) in result.candidates">
                                    <div class="form-check ms-4 mb-2">
                                        <input class="form-check-input fs-5"
                                               type="radio"
                                               :checked="isWinner(result.vote_counts, candidateIndex)" disabled>
                                        <label class="form-check-label fs-5 text-truncate w-75"
                                               :class="isWinner(result.vote_counts, candidateIndex) ? 'text-success fw-bold' : 'text-muted'">
                                            {{ candidate }}
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-5">
                                <BarChart :labels="trimCandidates(result.candidates, 10)" :title="result.question_title"
                                          :datasets="result.vote_counts"></BarChart>
                                <LineChart :labels="trimCandidates(result.candidates, 10)"
                                           :title="result.question_title"
                                           :datasets="result.vote_counts"></LineChart>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import BarChart from "@/Components/BarChart.vue";
import {ref} from "vue";
import {Link} from "@inertiajs/vue3";
import BallotSidebar from "@/Pages/Voting/BallotSidebar.vue";
import LineChart from "@/Components/LineChart.vue";

const props = defineProps(['nestedResults', 'room']);

const rec_nestedResults = ref(props.nestedResults)

console.log(props.nestedResults)

Echo.private('result-update').listen('ResultUpdate', (e) => {
    rec_nestedResults.value = e.nestedResults;
    console.log(rec_nestedResults.value);
})

function trimCandidates(candidates, length) {
    return candidates.map(candidate => candidate.length > length ? candidate.substring(0, length) : candidate);
}

function findMaxIndex(arr) {
    let maxIndex = 0;
    for (let i = 0; i < arr.length; i++) {
        if (arr[i] > arr[maxIndex]) {
            maxIndex = i;
        }
    }
    return maxIndex;
}

function findMaxIndices(arr) {
    let maxIndices = [];
    let maxVal = arr.reduce((max, val) => Math.max(max, val), Number.MIN_VALUE);

    for (let i = 0; i < arr.length; i++) {
        if (arr[i] === maxVal) {
            maxIndices.push(i);
        }
    }

    return maxIndices;
}

function isWinner(voteCounts, candidateIndex) {
    const maxIndices = findMaxIndices(voteCounts);
    return maxIndices.includes(candidateIndex);
}

</script>
