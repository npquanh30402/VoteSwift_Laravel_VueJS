<template>
    <div class="row row-cols-3">
        <div class="card shadow shadow-sm" v-for="(result, index) in nestedResults" :key="index">
            <div>
                <BarChart :labels="result.candidates" :datasets="result.vote_counts"></BarChart>
            </div>
        </div>
    </div>
</template>

<script setup>
import BarChart from "@/Components/BarChart.vue";
import BaseCard from "@/Components/BaseCard.vue";

const props = defineProps(['nestedResults'])

console.log(props.nestedResults)

const labels = ['January', 'February', 'March']
const datasets = [40, 20, 12]
</script>
