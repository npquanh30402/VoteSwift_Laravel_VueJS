<template>
    <div class="row row-cols-3">
        <div class="card shadow shadow-sm" v-for="(result, index) in rec_nestedResults" :key="index">
            <div>
                <BarChart :labels="trimCandidates(result.candidates, 10)" :datasets="result.vote_counts"></BarChart>
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
</script>
