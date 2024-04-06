<script setup>
import {
    Chart as ChartJS,
    Title,
    Tooltip,
    Legend,
    BarElement,
    CategoryScale,
    LinearScale,
    Colors
} from 'chart.js'
import {Bar} from 'vue-chartjs'
import {computed} from "vue";

ChartJS.register(CategoryScale, LinearScale, BarElement, Title, Tooltip, Legend, Colors)

const props = defineProps(['labels', 'datasets', 'options'])

const chartData = computed(() => ({
    labels: props.labels,
    datasets: [{
        data: props.datasets
    }]
}));

const options = computed(() => props.options)
</script>

<template>
    <Bar :data="chartData" :options="options"/>
</template>
