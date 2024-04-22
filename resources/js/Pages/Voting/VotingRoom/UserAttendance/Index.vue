<template>
    <div v-if="!isLoading">
        <Sidebar :tabs="tabData" @switch-tab="handleSwitchTab" />
        <div class="mt-3">
            <transition mode="out-in" name="fade">
                <KeepAlive>
                    <component
                        :is="tabData[currentTab].component"
                        :room="room"
                    ></component>
                </KeepAlive>
            </transition>
        </div>
    </div>
    <BaseLoading v-else />
</template>
<script setup>
import { onMounted, ref } from "vue";
import Sidebar from "@/Pages/Voting/VotingRoom/Components/Sidebar.vue";
import BaseLoading from "@/Components/BaseLoading.vue";
import AttendanceDetails from "@/Pages/Voting/VotingRoom/UserAttendance/AttendanceDetails.vue";
import AttendanceCharts from "@/Pages/Voting/VotingRoom/UserAttendance/AttendanceCharts.vue";

const isLoading = ref(true);

const props = defineProps(["room"]);

onMounted(async () => {
    isLoading.value = false;
});

const tabData = {
    AttendanceDetails: {
        component: AttendanceDetails,
        name: "Details",
        componentName: "AttendanceDetails",
    },
    AttendanceCharts: {
        component: AttendanceCharts,
        name: "Charts",
        componentName: "AttendanceCharts",
    },
};

const currentTab = ref(tabData.AttendanceDetails.componentName);

const handleSwitchTab = (tabName) => {
    currentTab.value = tabName;
};
</script>
