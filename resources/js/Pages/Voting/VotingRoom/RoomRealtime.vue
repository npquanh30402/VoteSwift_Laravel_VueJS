<template>
    <div class="row gx-3">
        <div class="d-flex flex-column gap-3">
            <div
                class="hstack gap-3 align-items-center justify-content-between"
            >
                <div
                    :class="{ 'un-interactive': isPublish }"
                    class="hstack gap-3 justify-content-center align-items-center"
                >
                    <div class="form-check form-switch">
                        <input
                            id="realTimeVotingSwitch"
                            v-model="isRealTimeVotingEnable"
                            class="form-check-input"
                            role="switch"
                            type="checkbox"
                            @change="toggleRealTimeVoting"
                        />
                        <label
                            class="form-check-label"
                            for="realTimeVotingSwitch"
                            >Enable Real-time Voting</label
                        >
                    </div>
                    <div
                        :class="[
                            isRealTimeVotingEnable ? '' : 'un-interactive',
                        ]"
                        class="form-check form-switch"
                    >
                        <input
                            id="voterRealtimeResultSwitch"
                            v-model="voterRealtimeResultEnabled"
                            class="form-check-input"
                            role="switch"
                            type="checkbox"
                            @change="toggleVoterRealtimeResult"
                        />
                        <label
                            class="form-check-label"
                            for="voterRealtimeResultSwitch"
                            >Allow Voters to See Real-time Results</label
                        >
                    </div>
                </div>
                <div :class="[isRealTimeVotingEnable ? '' : 'un-interactive']">
                    <button
                        class="btn btn-primary opacity-100 position-relative"
                        disabled
                        type="button"
                    >
                        Realtime
                        <span
                            :class="{
                                ' animate__animated animate__flash animate__infinite animate__slow':
                                    isRealTimeVotingEnable,
                            }"
                            class="position-absolute top-0 start-100 translate-middle p-2 bg-danger border border-light rounded-circle"
                        ></span>
                    </button>
                </div>
            </div>
            <div :class="[isRealTimeVotingEnable ? '' : 'un-interactive']">
                <BaseNoContent v-if="!isRealTimeVotingEnable" />
                <RealtimeSidebar v-else @switch-tab="handleSwitchTab" />
                <div class="mt-3">
                    <transition mode="out-in" name="fade">
                        <KeepAlive>
                            <component
                                :is="tabs[currentTab]"
                                :channelBroadcast="channelBroadcast"
                                :room="room"
                                :roomSettings="roomSettings"
                                :userChoices="userChoices"
                            ></component>
                        </KeepAlive>
                    </transition>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { usePage } from "@inertiajs/vue3";
import { computed, onMounted, onUnmounted, ref, watch } from "vue";
import { useVotingSettingStore } from "@/Stores/voting-settings.js";
import BaseNoContent from "@/Components/BaseNoContent.vue";
import { useHelper } from "@/Services/helper.js";
import NotificationVotingList from "@/Pages/Voting/VotingRoom/Components/NotificationVotingList.vue";
import RealtimeChart from "@/Pages/Voting/VotingRoom/Components/RealtimeChart.vue";
import RealtimeSidebar from "@/Pages/Voting/VotingRoom/Components/RealtimeSidebar.vue";

const props = defineProps(["room"]);
const helper = useHelper();
const votingSettingStore = useVotingSettingStore();
const isPublish = computed(() => props.room.is_published === 1);
const tabs = {
    NotificationVotingList,
    RealtimeChart,
};
const currentTab = ref("NotificationVotingList");

const authUser = computed(() => usePage().props.authUser.user);
const roomSettings = computed(() => votingSettingStore.settings[props.room.id]);

const isRealTimeVotingEnable = ref(false);
const voterRealtimeResultEnabled = ref(false);

const channelBroadcast = {
    channelName: "voting.process." + props.room.id,
    eventName: "VotingProcess",
};

const userChoices = ref([]);

const handleReceivedMessage = (e) => {
    if (
        (e.broadcast_type === "voting_choices") |
        (e.broadcast_type === "voting_join") |
        (e.broadcast_type === "voting_leave")
    ) {
        e.id = helper.generateUniqueKey();
        userChoices.value.unshift(e);
    }
};

const setupEchoListeners = () => {
    if (authUser.value.id === props.room.user_id) {
        Echo.private(channelBroadcast.channelName).listen(
            channelBroadcast.eventName,
            handleReceivedMessage,
        );
    }
};

watch(roomSettings, () => {
    isRealTimeVotingEnable.value = roomSettings.value?.realtime_enabled === 1;
    voterRealtimeResultEnabled.value =
        roomSettings.value?.voters_can_see_realtime_results === 1;
});

const updateSetting = (key, value) => {
    const formData = new FormData();
    formData.append(key, value);

    votingSettingStore.updateSettings(props.room.id, formData);
};

const toggleRealTimeVoting = () => {
    updateSetting("realtime_enabled", isRealTimeVotingEnable.value);

    if (isRealTimeVotingEnable.value === false) {
        Echo.leave(channelBroadcast.channelName);

        if (voterRealtimeResultEnabled.value === true) {
            updateSetting("voters_can_see_realtime_results", false);
        }
    } else setupEchoListeners();
};

const toggleVoterRealtimeResult = () => {
    updateSetting(
        "voters_can_see_realtime_results",
        voterRealtimeResultEnabled.value,
    );
};

const handleSwitchTab = (tabName) => {
    currentTab.value = tabName;
};

onMounted(() => {
    votingSettingStore.fetchSettings(props.room.id).then(() => {
        if (roomSettings) {
            isRealTimeVotingEnable.value =
                roomSettings.value?.realtime_enabled === 1;

            if (isRealTimeVotingEnable.value) setupEchoListeners();
        }
    });
});

onUnmounted(() => Echo.leave(channelBroadcast.channelName));
</script>

<style>
.list-move,
.list-enter-active,
.list-leave-active {
    transition: all 0.5s ease;
}

.list-enter-from,
.list-leave-to {
    opacity: 0;
    transform: translateX(30px);
}

.list-leave-active {
    position: absolute;
}
</style>
