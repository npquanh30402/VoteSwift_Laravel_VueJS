<template>
    <div class="clock">
        {{ displayTime.days }} days, {{ displayTime.hours }} hours,
        {{ displayTime.minutes }} minutes, {{ displayTime.seconds }} seconds
    </div>
</template>

<script setup>
import { onMounted, onUnmounted, ref, computed } from "vue";
import { intervalToDuration } from "date-fns";
import { useHelper } from "@/Services/helper.js";

const props = defineProps(["date"]);
const helper = useHelper();

const date = computed(() => {
    const localDate = helper.convertToLocal(
        props.date,
        helper.getUserTimeZone(),
    );
    return new Date(localDate);
});

const displayTime = ref(calculateDisplayTime(date.value));

function calculateDisplayTime(endDate) {
    const currentTime = new Date();
    const duration = intervalToDuration({ start: currentTime, end: endDate });

    return {
        days: duration.days || 0,
        hours: duration.hours || 0,
        minutes: duration.minutes || 0,
        seconds: duration.seconds || 0,
    };
}

let intervalId;

onMounted(() => {
    intervalId = setInterval(() => {
        displayTime.value = calculateDisplayTime(date.value);
    }, 1000);
});

onUnmounted(() => {
    clearInterval(intervalId);
});

function updateDisplayTime() {
    displayTime.value = calculateDisplayTime(date.value);
}
</script>
