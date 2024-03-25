<script setup>
import {
    Chart as ChartJS,
    Title,
    Tooltip,
    Legend,
    BarElement,
    CategoryScale,
    LinearScale,
    Colors,
    PointElement,
    LineElement
} from 'chart.js'
import {Line} from 'vue-chartjs'
import {computed} from "vue";

ChartJS.register(CategoryScale, LinearScale, BarElement, Title, Tooltip, Legend, Colors, PointElement, LineElement)

const props = defineProps(['labels', 'datasets', 'title'])

const chartData = computed(() => ({
    labels: props.labels,
    datasets: [{label: props.title, data: props.datasets}]
}));

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
    borderWidth: 1
}
</script>

<template>
    <div>
        <Line :data="chartData" :options="options"/>
    </div>
</template>
