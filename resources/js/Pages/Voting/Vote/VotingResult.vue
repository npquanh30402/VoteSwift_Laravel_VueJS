<template>
    <div class="card shadow shadow-sm container-fluid" v-for="(result, index) in rec_nestedResults"
         :key="index">
        <div class="row justify-content-center align-items-center">
            <div class="col-md-8">
                <h3 class="fw-semibold">Question {{ index + 1 }}: {{ result.question_title }}</h3>
                <div v-for="(candidate, candidateIndex) in result.candidates">
                    <div class="form-check ms-4">
                        <input class="form-check-input fs-3"
                               type="radio"
                               :checked="isWinner(result.vote_counts, candidateIndex)" disabled>
                        <label class="form-check-label fs-4 text-truncate w-75"
                               :class="isWinner(result.vote_counts, candidateIndex) ? 'text-success fw-bold' : 'text-muted'">
                            {{ candidate }}
                        </label>
                        <p class="text-truncate text-muted fs-6 w-75">{{ candidate.candidate_description }}</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <BarChart :labels="trimCandidates(result.candidates, 10)" :title="result.question_title"
                          :datasets="result.vote_counts"></BarChart>
            </div>
        </div>
    </div>
</template>

<script setup>
import BarChart from "@/Components/BarChart.vue";
import {ref} from "vue";

const props = defineProps(['nestedResults'])

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
