<template>
    <div>
        <div v-if="isReady">
            <div class="row">
                <div class="col-md-9">
                    <div class="row mb-3">
                        <div class="col-md-6 d-flex">
                            <OverviewCardDetails>
                                <template #header>Room ID</template>
                                {{ room.id }}
                            </OverviewCardDetails>
                        </div>
                        <div class="col-md-6 d-flex">
                            <OverviewCardDetails>
                                <template #header>Room Name</template>
                                {{ room.room_name }}
                            </OverviewCardDetails>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6 d-flex">
                            <OverviewCardDetails>
                                <template #header>Start Date</template>
                                {{ startTime }}
                            </OverviewCardDetails>
                        </div>
                        <div class="col-md-6 d-flex">
                            <OverviewCardDetails>
                                <template #header>End Date</template>
                                {{ endTime }}
                            </OverviewCardDetails>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12 d-flex">
                            <OverviewCardDetails>
                                <template #header>Room URL</template>
                                <div
                                    class="hstack gap-5 justify-content-between align-items-center"
                                >
                                    <code
                                        ref="roomLink"
                                        class="text-decoration-none text-dark"
                                        >{{ route("vote.main", room.id) }}
                                    </code>
                                    <button
                                        class="btn btn-primary"
                                        @click="customCopy(source)"
                                    >
                                        <i class="bi bi-clipboard-check"></i>
                                    </button>
                                </div>
                            </OverviewCardDetails>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <OverviewCard
                        :count="invitationCount"
                        class="text-bg-warning mb-3"
                        title="Voters"
                    >
                        <i class="bi bi-people"></i>
                    </OverviewCard>
                    <OverviewCard
                        :count="questionCount"
                        class="text-bg-success mb-3"
                        title="Questions"
                    >
                        <i class="bi bi-question-circle"></i>
                    </OverviewCard>
                    <OverviewCard
                        :count="candidateCount"
                        class="text-bg-secondary mb-3"
                        title="Candidates"
                    >
                        <i class="bi bi-list-ul"></i>
                    </OverviewCard>
                </div>
            </div>
        </div>
        <BaseLoading v-else />
    </div>
</template>
<script setup>
import "md-editor-v3/lib/preview.css";
import { useInvitationStore } from "@/Stores/invitations.js";
import { computed, onMounted, ref } from "vue";
import { useQuestionStore } from "@/Stores/questions.js";
import { useCandidateStore } from "@/Stores/candidates.js";
import BaseLoading from "@/Components/BaseLoading.vue";
import { useVotingRoomStore } from "@/Stores/voting-room.js";
import { route } from "ziggy-js";
import { useClipboard } from "@vueuse/core";
import { useToast } from "vue-toast-notification";
import OverviewCard from "@/Pages/Voting/VotingRoom/Overview/OverviewCard.vue";
import OverviewCardDetails from "@/Pages/Voting/VotingRoom/Overview/OverviewCardDetails.vue";
import { fromZonedTime } from "date-fns-tz";
import { format } from "date-fns";
import moment from "moment-timezone";
import { useHelper } from "@/Services/helper.js";

const props = defineProps(["room"]);

const toast = useToast();
const isReady = ref(false);
const helper = useHelper();
const roomStore = useVotingRoomStore();
const questionStore = useQuestionStore();
const candidateStore = useCandidateStore();
const invitationStore = useInvitationStore();

const room = computed(() => {
    for (const room of roomStore.rooms) {
        if (room.id === props.room.id) {
            return room;
        }
    }
});

const invitationCount = computed(
    () => Object.keys(invitationStore.invitations[props.room.id]).length,
);
const questionCount = computed(
    () => Object.keys(questionStore.questions[props.room.id]).length,
);
const candidateCount = computed(() => {
    return Object.values(candidateStore.candidates[props.room.id] || {}).reduce(
        (acc, candidates) => acc + candidates.length,
        0,
    );
});

const source = ref(route("vote.main", props.room.id));
const { text, copy, copied, isSupported } = useClipboard({ source });

const customCopy = () => {
    copy();
    toast.success("Copied to clipboard!");
};

const startTime = computed(() =>
    helper.formatDate(
        helper.convertToLocal(room.value.start_time, helper.getUserTimeZone()),
    ),
);

const endTime = computed(() =>
    helper.formatDate(
        helper.convertToLocal(room.value.end_time, helper.getUserTimeZone()),
    ),
);

onMounted(async () => {
    await roomStore.fetchRooms();
    await questionStore.fetchQuestions(props.room.id);
    await candidateStore.fetchCandidates(props.room.id);
    await invitationStore.fetchInvitations(props.room.id);

    isReady.value = true;
});
</script>
