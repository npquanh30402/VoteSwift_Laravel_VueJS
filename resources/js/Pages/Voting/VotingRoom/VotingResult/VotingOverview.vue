<template>
    <div v-if="!isLoading">
        <div class="d-flex justify-content-end mb-3">
            <button class="btn btn-success" @click="refreshData">
                <i class="bi bi-arrow-clockwise" style="cursor: pointer"></i>
            </button>
        </div>
        <div v-if="room.has_ended">
            <div v-if="hasTie()" class="container mt-4">
                <div class="text-center">
                    <h1 class="display-4">Room Ended</h1>
                    <p class="lead">However, some candidates are tied.</p>
                    <p>
                        Would you like to create a new room to resolve the tie?
                    </p>
                    <p class="text-muted">
                        All data—except for the candidates who lost—will be
                        transferred to the new room.
                    </p>

                    <button class="btn btn-primary" @click="handleTie">
                        Create New Room
                    </button>
                </div>
            </div>
            <div v-else>
                <p class="lead">No candidates are tied.</p>
                <p>Check the charts tab to view the final results.</p>
            </div>
        </div>
        <BaseNoContent v-else />
    </div>
    <BaseLoading v-else />
</template>
<script setup>
import BaseLoading from "@/Components/BaseLoading.vue";
import { computed, onMounted, ref } from "vue";
import { useVoteStore } from "@/Stores/vote.js";
import { useVotingRoomStore } from "@/Stores/voting-room.js";
import BaseNoContent from "@/Components/BaseNoContent.vue";

const isLoading = ref(true);

const props = defineProps(["room"]);
const voteStore = useVoteStore();
const roomStore = useVotingRoomStore();

const votes = computed(() => voteStore.votes[props.room.id]);

function hasTie() {
    const questionVoteMap = {};

    votes.value.forEach((vote) => {
        const { question_id, candidate_id } = vote;

        if (!questionVoteMap[question_id]) {
            questionVoteMap[question_id] = {};
        }

        if (!questionVoteMap[question_id][candidate_id]) {
            questionVoteMap[question_id][candidate_id] = 0;
        }
        questionVoteMap[question_id][candidate_id]++;
    });

    for (const question_id in questionVoteMap) {
        const candidateVotes = Object.values(questionVoteMap[question_id]);

        const maxVotes = Math.max(...candidateVotes);

        const maxVoteCandidates = candidateVotes.filter(
            (voteCount) => voteCount === maxVotes,
        );

        if (maxVoteCandidates.length > 1) {
            return true;
        }
    }

    return false;
}

const handleTie = () => {
    roomStore.handleTie(props.room.id);
};

const refreshData = async () => {
    isLoading.value = true;

    await roomStore.fetchARoom(props.room.id);

    isLoading.value = false;
};

onMounted(async () => {
    await voteStore.fetchVotes(props.room.id);

    isLoading.value = false;
});
</script>
