<template>
    <div>
        <div class="mb-3 d-flex gap-3">
            <div>
                <button type="button" class="btn btn-primary position-relative" disabled>
                    Realtime
                    <span
                        class="position-absolute top-0 start-100 translate-middle p-2 bg-danger border border-light rounded-circle animate__animated animate__flash animate__infinite animate__slow"></span>
                </button>
            </div>
            <div>
                <button class="btn btn-secondary" @click="openModal(modals.moreChartsModal)">More Charts
                </button>
            </div>
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
                        <BarChart :labels="trimCandidates(result.candidates, 10)"
                                  :datasets="result.vote_counts"
                                  :options="options"></BarChart>
                    </div>
                </div>
            </div>
        </div>
        <BaseModal :id="modals.moreChartsModal" title="More Charts">
            <!--        <VueDatePicker v-model="date" :enable-time-picker="false" range multi-calendars/>-->
            <LineChart :labels="rec_voteCountsInTimeInterval[0]"
                       :datasets="rec_voteCountsInTimeInterval[1]"/>
        </BaseModal>
    </div>
</template>

<script setup>
import BarChart from "@/Components/BarChart.vue";
import {onMounted, reactive, ref} from "vue";
import '@vuepic/vue-datepicker/dist/main.css';
import LineChart from "@/Components/LineChart.vue";
import * as bootstrap from "bootstrap";
import BaseModal from "@/Components/BaseModal.vue";

const props = defineProps(['nestedResults', 'room', 'voteCountsInTimeInterval']);

const date = ref();

onMounted(() => {
    const startDate = new Date(props.room.start_time);
    const endDate = new Date(props.room.end_time);
    date.value = [startDate, endDate];
})

const rec_nestedResults = ref(props.nestedResults)
const rec_voteCountsInTimeInterval = ref(props.voteCountsInTimeInterval)

const modals = reactive({
    moreChartsModal: 'moreChartsModal'
})

onMounted(() => {
    modals.moreChartsModal = new bootstrap.Modal(document.getElementById(modals.moreChartsModal));
})

function openModal(modal) {
    modal.show()
}

Echo.private('result-update').listen('ResultUpdate', (e) => {
    rec_nestedResults.value = e.nestedResults;
    rec_voteCountsInTimeInterval.value = e.voteCountsInTimeInterval;
})

function trimCandidates(candidates, length) {
    return candidates.map(candidate => candidate.length > length ? candidate.substring(0, length) : candidate);
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
}
</script>
