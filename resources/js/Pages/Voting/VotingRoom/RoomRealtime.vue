<template>
    <div class="row gx-3">
        <div class="d-flex flex-column gap-3">
            <div
                class="hstack gap-3 align-items-center justify-content-between"
            >
                <div class="form-check form-switch">
                    <input
                        id="chatSwitch"
                        v-model="isRealTimeVotingEnable"
                        class="form-check-input"
                        role="switch"
                        type="checkbox"
                        @change="toggleRealTimeVoting"
                    />
                    <label class="form-check-label" for="realTimeVotingSwitch"
                        >Enable Real-time Voting</label
                    >
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
                <TransitionGroup name="list">
                    <div v-for="userChoice in userChoices" :key="userChoice.id">
                        <div
                            v-if="
                                userChoice.broadcast_type === 'voting_choices'
                            "
                            class="alert alert-success vstack"
                        >
                            <div class="hstack">
                                <img
                                    :src="userChoice.user.avatar"
                                    alt=""
                                    class="img-fluid rounded-circle border-black border"
                                    style="height: 3rem"
                                />
                                <div class="ms-3">
                                    <strong>{{
                                        userChoice.user.username
                                    }}</strong
                                    >: Has Voted for Candidate

                                    <div class="ms-3">
                                        <i class="bi bi-arrow-right me-1"></i>
                                        <span class="fw-bold">Question</span>:
                                        {{ userChoice.question.question_title }}
                                    </div>

                                    <div class="ms-5">
                                        <i class="bi bi-dot"></i>
                                        <span class="fw-bold">Candidate</span>:
                                        {{
                                            userChoice.candidate.candidate_title
                                        }}
                                    </div>
                                </div>
                            </div>
                            <div class="text-muted ms-auto">
                                <small>{{
                                    formattedDate(userChoice.created_at)
                                }}</small>
                            </div>
                        </div>
                        <div
                            v-if="userChoice.broadcast_type === 'voting_join'"
                            class="alert alert-secondary text-bg-dark vstack"
                        >
                            <div class="hstack">
                                <img
                                    :src="userChoice.user.avatar"
                                    alt=""
                                    class="img-fluid rounded-circle border-black border"
                                    style="height: 3rem"
                                />
                                <div class="ms-3">
                                    <strong>{{
                                        userChoice.user.username
                                    }}</strong
                                    >: Has Joined the Room
                                </div>
                            </div>
                            <div class="text-white ms-auto">
                                <small>{{
                                    formattedDate(userChoice.created_at)
                                }}</small>
                            </div>
                        </div>
                        <div
                            v-if="userChoice.broadcast_type === 'voting_leave'"
                            class="alert alert-danger text-dark vstack"
                        >
                            <div class="hstack">
                                <img
                                    :src="userChoice.user.avatar"
                                    alt=""
                                    class="img-fluid rounded-circle border-black border"
                                    style="height: 3rem"
                                />
                                <div class="ms-3">
                                    <strong>{{
                                        userChoice.user.username
                                    }}</strong
                                    >: Has Leave the Room
                                </div>
                            </div>
                            <div class="text-dark ms-auto">
                                <small>{{
                                    formattedDate(userChoice.created_at)
                                }}</small>
                            </div>
                        </div>
                    </div>
                </TransitionGroup>
            </div>
        </div>
    </div>
</template>

<script setup>
import { usePage } from "@inertiajs/vue3";
import { computed, onMounted, onUnmounted, ref, watch } from "vue";
import { useVotingSettingStore } from "@/Stores/voting-settings.js";
import { useToast } from "vue-toast-notification";
import BaseNoContent from "@/Components/BaseNoContent.vue";
import { useHelper } from "@/Services/helper.js";

const props = defineProps(["room"]);
const $toast = useToast();
const helper = useHelper();
const votingSettingStore = useVotingSettingStore();

const formattedDate = computed(() => {
    return (date) => {
        return helper.formattedDate(date);
    };
});

const authUser = computed(() => usePage().props.authUser.user);
const roomSettings = computed(() => votingSettingStore.settings[props.room.id]);

const isRealTimeVotingEnable = ref(false);

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
});

const updateSetting = (key, value) => {
    const formData = new FormData();
    formData.append(key, value);

    votingSettingStore
        .updateSettings(props.room.id, formData)
        .then(() => $toast.success("Updated successfully"))
        .catch(() => $toast.error("Failed to update"));
};

const toggleRealTimeVoting = () => {
    updateSetting("realtime_enabled", isRealTimeVotingEnable.value);
};

onMounted(() => {
    setupEchoListeners();

    votingSettingStore.fetchSettings(props.room.id).then(() => {
        if (roomSettings) {
            isRealTimeVotingEnable.value =
                roomSettings.value?.realtime_enabled === 1;
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
