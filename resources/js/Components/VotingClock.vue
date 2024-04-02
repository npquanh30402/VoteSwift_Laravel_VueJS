<template>
    <div class="clock">
        {{ displayTime.days }} days, {{ displayTime.hours }} hours, {{ displayTime.minutes }} minutes,
        {{ displayTime.seconds }} seconds
    </div>
</template>

<script setup>
import {onMounted, onUnmounted, ref, defineProps} from "vue";
import {intervalToDuration} from "date-fns";

const props = defineProps(['date']);
const displayTime = ref(calculateDisplayTime(new Date(props.date).toLocaleTimeString()));

function calculateDisplayTime(endDate) {
    const currentTime = new Date().toLocaleString();
    const duration = intervalToDuration({start: currentTime, end: endDate});

    return {
        days: duration.days,
        hours: duration.hours,
        minutes: duration.minutes,
        seconds: duration.seconds
    };
}

let intervalId;

onMounted(() => {
    intervalId = setInterval(updateDisplayTime, 1000);
});

onUnmounted(() => {
    clearInterval(intervalId);
});

function updateDisplayTime() {
    displayTime.value = calculateDisplayTime(new Date(props.date));
}
</script>
