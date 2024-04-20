<template>
    <div>
        <button @click="test">Test</button>
        <Sidebar :tabs="tabData" @switch-tab="handleSwitchTab" />
        <div class="mt-3">
            <transition mode="out-in" name="fade">
                <KeepAlive>
                    <component
                        :is="tabData[currentTab].component"
                        :nestedResults="nestedResults"
                        :room="room"
                        :voteCountsInTimeInterval="voteCountsInTimeInterval"
                    ></component>
                </KeepAlive>
            </transition>
        </div>
    </div>
</template>
<script setup>
import { computed, onMounted, ref } from "vue";
import Sidebar from "@/Pages/Voting/VotingRoom/Components/Sidebar.vue";
import VotingDetails from "@/Pages/Voting/VotingRoom/VotingResult/VotingDetails.vue";
import VotingCharts from "@/Pages/Voting/VotingRoom/VotingResult/VotingCharts.vue";
import { useQuestionStore } from "@/Stores/questions.js";
import { useCandidateStore } from "@/Stores/candidates.js";
import { useVoteStore } from "@/Stores/vote.js";

const props = defineProps([
    "room",
    "nestedResults",
    "voteCountsInTimeInterval",
]);

const room = computed(() => props.room);

const questionStore = useQuestionStore();
const candidateStore = useCandidateStore();
const voteStore = useVoteStore();

const questions = computed(() => questionStore.questions[room.value.id]);
const candidates = computed(() => candidateStore.candidates[room.value.id]);
const votes = computed(() => voteStore.votes[room.value.id]);
const voteCounts = computed(() => {
    const voteCounts = {};
    votes.value.forEach((vote) => {
        const candidateId = vote.candidate_id;
        if (!voteCounts[candidateId]) {
            voteCounts[candidateId] = 0;
        }
        voteCounts[candidateId]++;
    });

    return voteCounts;
});

onMounted(async () => {
    await questionStore.fetchQuestions(room.value.id);
    await candidateStore.fetchCandidates(room.value.id);
    await voteStore.fetchVotes(room.value.id);
});

const test = () => {
    console.log(votes.value);

    let nestedResults = [];

    for (let i = 0; i < questions.value.length; i++) {
        let question = questions.value[i];
        let candidatesForQuestion = candidates.value[question.id];
        let voteCountsArray = [];

        candidatesForQuestion.forEach((candidate) => {
            let candidateId = candidate.id;

            if (voteCounts.value.hasOwnProperty(candidateId)) {
                voteCountsArray.push(voteCounts[candidateId]);
            } else {
                voteCountsArray.push(0);
            }
        });

        let questionArray = {
            questionTitle: question.question_title,
            candidates: candidatesForQuestion.map(
                (candidate) => candidate.candidate_title,
            ),
            voteCounts: voteCountsArray,
        };

        nestedResults.push(questionArray);
    }

    console.log(nestedResults);
};

const tabData = {
    VotingDetails: {
        component: VotingDetails,
        name: "Details",
        componentName: "VotingDetails",
    },
    VotingCharts: {
        component: VotingCharts,
        name: "Charts",
        componentName: "VotingCharts",
    },
};

const currentTab = ref(tabData.VotingDetails.componentName);

const handleSwitchTab = (tabName) => {
    currentTab.value = tabName;
};
</script>
