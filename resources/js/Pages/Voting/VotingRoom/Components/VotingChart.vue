<template>
    <div class="overflow-auto vstack gap-3" style="height: 70vh">
        <div v-for="(question, index) in questions" :key="question.id">
            <h5 class="mb-3">
                # Question {{ index + 1 }}: {{ question.question_title }}
            </h5>
            <BarChart
                :datasets="voteCounts[question.id]?.voteCounts"
                :labels="voteCounts[question.id]?.candidateLabels"
                :options="options"
            />
        </div>
    </div>
</template>
<script setup>
import BarChart from "@/Components/BarChart.vue";
import { computed } from "vue";

const props = defineProps(["questions", "voteCounts"]);
const voteCounts = computed(() => props.voteCounts);

const options = {
    responsive: true,
    backgroundColor: [
        "rgba(255, 99, 132, 0.2)",
        "rgba(255, 159, 64, 0.2)",
        "rgba(255, 205, 86, 0.2)",
        "rgba(75, 192, 192, 0.2)",
        "rgba(54, 162, 235, 0.2)",
        "rgba(153, 102, 255, 0.2)",
        "rgba(201, 203, 207, 0.2)",
    ],
    borderColor: [
        "rgb(255, 99, 132)",
        "rgb(255, 159, 64)",
        "rgb(255, 205, 86)",
        "rgb(75, 192, 192)",
        "rgb(54, 162, 235)",
        "rgb(153, 102, 255)",
        "rgb(201, 203, 207)",
    ],
    borderWidth: 1,
    indexAxis: "y",
    maintainAspectRatio: true,
};
</script>
