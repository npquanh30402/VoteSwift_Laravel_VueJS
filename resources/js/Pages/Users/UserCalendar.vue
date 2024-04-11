<template>
    <div class="card shadow-sm border-0 mb-3 overflow-auto">
        <div class="card-header text-bg-dark text-center">Calendar</div>
        <div class="card-body">
            <CalendarView
                :items="items"
                show-times
                :show-date="showDate"
                :starting-day-of-week="1"
                display-week-numbers
                :enable-date-selection="true"
                :time-format-options="{ hour: 'numeric', minute: '2-digit' }"
                class="theme-default min-vh-100">
                <template #header="{ headerProps }">
                    <calendar-view-header slot="header" :header-props="headerProps" @input="setShowDate"/>
                </template>
            </CalendarView>
            <!--            <Calendar v-model="calendar" borderless :attributes="attributes" :first-day-of-week="2" :columns="2"-->
            <!--                      expanded-->
            <!--                      show-iso-weeknumbers/>-->
        </div>
    </div>
</template>

<script setup>
import 'v-calendar/style.css';
import {computed, onMounted, ref} from "vue";

import {CalendarView, CalendarViewHeader} from "vue-simple-calendar";
import 'vue-simple-calendar/dist/style.css';
import 'vue-simple-calendar/dist/css/default.css';
import 'vue-simple-calendar/dist/css/holidays-us.css';
import {route} from "ziggy-js";
import {useVotingRoomStore} from "@/Stores/voting-room.js";

const props = defineProps(['rooms'])
const roomStore = useVotingRoomStore()
const authUserRooms = computed(() => roomStore?.rooms);
const showDate = ref(new Date())

const parseDateString = (dateString) => {
    const parts = dateString.split(', ');
    const datePart = parts[0];
    const timePart = parts[1];

    const [month, day, year] = datePart.split('/');
    const [time, period] = timePart.split(' ');

    let [hour, minute, second] = time.split(':');

    if (period === 'PM' && hour !== '12') {
        hour = String(parseInt(hour, 10) + 12);
    } else if (period === 'AM' && hour === '12') {
        hour = '00';
    }

    return new Date(year, month - 1, day, hour, minute, second);
};

const items = authUserRooms.value.map(item => ({
    id: item.id,
    startDate: parseDateString(new Date(item.start_time).toLocaleString()),
    endDate: parseDateString(new Date(item.end_time).toLocaleString()),
    title: item.room_name,
    tooltip: item.room_name,
    url: route('vote.main', item.id)
}));


function setShowDate(d) {
    showDate.value = d
}

onMounted(() => {
    roomStore.fetchRooms()
})

// console.log(authUserRooms)
//
// const officialColors = ['red', 'blue', 'green', 'yellow', 'orange', 'purple'];
//
// const getRandomColor = () => {
//     const randomIndex = Math.floor(Math.random() * officialColors.length);
//     return officialColors[randomIndex];
// };
//
// const transformedArray = authUserRooms.value.map(item => ({
//     description: item.room_name,
//     isComplete: new Date(item.end_time).toLocaleString() < new Date().toLocaleString(),
//     dates: [[new Date(item.start_time).toLocaleString(), new Date(item.end_time).toLocaleString()]],
//     color: getRandomColor()
// }));
//
// console.log(transformedArray)
//
// const calendar = ref(new Date());
//
// const todos = ref(transformedArray);
//
// const attributes = computed(() => [
//     ...todos.value.map(todo => ({
//         dates: todo.dates,
//         highlight: {
//             color: todo.color,
//             ...(todo.isComplete && {class: 'opacity-25'}),
//             start: {fillMode: 'outline'},
//             base: {fillMode: 'light'},
//             end: {fillMode: 'outline'},
//         },
//         popover: {
//             label: todo.description,
//             // visibility: 'click'
//         },
//     })),
// ]);
</script>
