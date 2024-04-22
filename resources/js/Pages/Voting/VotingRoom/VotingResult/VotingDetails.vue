<template>
    <div v-if="!isLoading">
        <div class="row row-cols-4 container">
            <OverviewCard
                :count="Object.keys(votes).length"
                class="text-bg-info mb-3"
                title="Total Votes"
            >
                <i class="bi bi-archive"></i>
            </OverviewCard>
        </div>
        <div class="card">
            <ResultTable :room="room" />
        </div>
    </div>
    <BaseLoading v-else />
</template>

<script setup>
import { computed, onMounted, ref } from "vue";
import { useVoteStore } from "@/Stores/vote.js";
import BaseLoading from "@/Components/BaseLoading.vue";
import OverviewCard from "@/Pages/Voting/VotingRoom/Overview/OverviewCard.vue";
import ResultTable from "@/Pages/Voting/VotingRoom/VotingResult/ResultTable.vue";

const isLoading = ref(true);

const props = defineProps(["room"]);
const voteStore = useVoteStore();

const votes = computed(() => {
    return Object.values(voteStore.votes[props.room.id]).reduce((acc, vote) => {
        if (acc[vote.user_id]) {
            acc[vote.user_id].push(vote);
        } else {
            acc[vote.user_id] = [vote];
        }
        return acc;
    }, {});
});

const setUpRealTime = () => {
    Echo.private("voting.result." + props.room.id).listen(
        "VotingResultEvent",
        (e) => {
            if (!voteStore.votes[props.room.id]) {
                voteStore.votes[props.room.id] = [];
            }

            voteStore.votes[props.room.id].push(e.vote);
        },
    );
};

onMounted(async () => {
    await voteStore.fetchVotes(props.room.id);

    setUpRealTime();

    isLoading.value = false;
});
</script>
