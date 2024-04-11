<template>
    <VotingOptions :room="room" :questions="questions" :voteCounts="combinedCounts"/>
</template>

<script setup>
import {computed, onMounted, ref} from "vue";
import {usePage} from "@inertiajs/vue3";
import {route} from "ziggy-js";
import {useVotingResultStore} from "@/Stores/voting-results.js";
import VotingOptions from "@/Pages/Voting/Vote/VotingOptions.vue";

const props = defineProps(['questions', 'room', 'channelBroadcast'])
const votingResultStore = useVotingResultStore()
const votingResult = computed(() => votingResultStore.results[props.room.id])
const authUser = computed(() => usePage().props.authUser.user);
const userChoicesInRoom = ref({})
const combinedCounts = ref({});
const channelBroadcast = props.channelBroadcast

const updateVoteCount = (questionId, candidateId, change) => {
    if (combinedCounts.value.hasOwnProperty(questionId)) {
        const index = combinedCounts.value[questionId].candidateIds.indexOf(candidateId);
        if (index !== -1) {
            const newVoteCounts = [...combinedCounts.value[questionId].voteCounts];
            newVoteCounts[index] += change;

            combinedCounts.value = {
                ...combinedCounts.value,
                [questionId]: {
                    ...combinedCounts.value[questionId],
                    voteCounts: newVoteCounts
                }
            };
        }
    }
};

const updateCombinedCounts = (result) => {
    const counts = {};

    if (result) {
        for (const questionId in result) {
            const questionData = result[questionId];
            counts[questionId] = {
                candidateIds: questionData.map(candidate => candidate.id),
                voteCounts: questionData.map(candidate => candidate.vote_count),
                candidateLabels: questionData.map(candidate => candidate.candidate_title)
            };
        }
    }

    combinedCounts.value = counts;
};

onMounted(async () => {
    await votingResultStore.fetchResults(props.room.id)
    updateCombinedCounts(votingResultStore.results[props.room.id])
})

const handleReceivedChoice = (e) => {
    if (e.broadcast_type === 'voting_choices') {
        const questionId = e.question_id;
        const candidateId = parseInt(e.candidate_id);

        if (!userChoicesInRoom.value[e.user.id]) {
            userChoicesInRoom.value[e.user.id] = {};
        }

        if (!userChoicesInRoom.value[e.user.id][questionId]) {
            userChoicesInRoom.value[e.user.id][questionId] = [];
        }

        if (e.question_type.allow_multiple_votes === 'true') {
            handleCheckbox(e.user.id, questionId, candidateId);
        } else {
            handleRadio(e.user.id, questionId, candidateId);
        }
    }
};

const handleCheckbox = (userId, questionId, candidateId) => {
    const isSelected = userChoicesInRoom.value[userId][questionId].includes(candidateId);

    if (!isSelected) {
        userChoicesInRoom.value[userId][questionId].push(candidateId);
        updateVoteCount(questionId, candidateId, 1);
    } else {
        userChoicesInRoom.value[userId][questionId] = userChoicesInRoom.value[userId][questionId].filter((oldCandidateId) => oldCandidateId !== candidateId);
        updateVoteCount(questionId, candidateId, -1);
    }
};

const handleRadio = (userId, questionId, candidateId) => {
    const oldCandidateId = userChoicesInRoom.value[userId][questionId];
    if (oldCandidateId) {
        updateVoteCount(questionId, oldCandidateId, -1);
    }

    userChoicesInRoom.value[userId][questionId] = candidateId;

    updateVoteCount(questionId, candidateId, 1);
};

const handleDisconnect = async (user) => {
    const userId = user.id;

    if (userChoicesInRoom.value[userId]) {
        for (const questionId in userChoicesInRoom.value[userId]) {
            const candidateIds = userChoicesInRoom.value[userId][questionId];

            if (!Array.isArray(candidateIds)) {
                updateVoteCount(questionId, candidateIds, -1);
            } else {
                for (const candidateId of candidateIds) {
                    updateVoteCount(questionId, candidateId, -1);
                }
            }
        }

        delete userChoicesInRoom.value[userId];
    }

    await axios.delete(route('api.room.vote.delete.choices', {
        'room': props.room.id,
        'user': userId
    }));
};

const handleSubscribe = async () => {
    const choicesResponse = await axios.get(route('api.room.vote.get.choices', props.room.id));

    const responseData = choicesResponse.data

    for (const userId in responseData) {
        if (Object.prototype.hasOwnProperty.call(responseData, userId)) {
            const userChoices = responseData[userId];

            if (!userChoicesInRoom.value[userId]) {
                userChoicesInRoom.value[userId] = {};
            }

            for (const questionId in userChoices) {
                if (Object.prototype.hasOwnProperty.call(userChoices, questionId)) {
                    const candidateIds = userChoices[questionId];

                    if (!userChoicesInRoom.value[userId][questionId]) {
                        userChoicesInRoom.value[userId][questionId] = [];
                    }

                    if (candidateIds.length > 1) {
                        for (const candidateId of candidateIds) {
                            handleCheckbox(userId, questionId, candidateId);
                        }
                    } else {
                        handleRadio(userId, questionId, candidateIds[0]);
                    }
                }
            }
        }
    }
};

const setupEchoListeners = () => {
    if (authUser.value) {
        Echo.private(channelBroadcast.channelName).listen(channelBroadcast.eventName, handleReceivedChoice);

        Echo.join(channelBroadcast.channelName)
            .leaving(handleDisconnect)
            .subscribed(handleSubscribe)
    }
};

onMounted(() => {
    setupEchoListeners()
    window.scrollTo({top: 0, left: 0, behavior: 'smooth'});
})
</script>
