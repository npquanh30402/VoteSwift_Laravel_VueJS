<template>
    <div>
        <ul class="nav nav-pills nav-fill">
            <li class="nav-item">
                <button
                    :class="{ active: currentTab === 'NotificationVotingList' }"
                    class="nav-link"
                    @click="switchTab('NotificationVotingList')"
                >
                    Notifications
                </button>
            </li>
            <li class="nav-item">
                <button
                    :class="{ active: currentTab === 'RealtimeChart' }"
                    class="nav-link"
                    @click="switchTab('RealtimeChart')"
                >
                    Charts
                </button>
            </li>
        </ul>
    </div>
</template>

<script setup>
import { ref } from "vue";

const emit = defineEmits(["switch-tab"]);
const currentTab = ref("NotificationVotingList");

const switchTab = (tabName) => {
    currentTab.value = tabName;
    emit("switch-tab", tabName);
};
</script>
