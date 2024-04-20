import { defineStore } from "pinia";
import { route } from "ziggy-js";
import { useToast } from "vue-toast-notification";
import { ref } from "vue";
import axios from "axios";

const toast = useToast();
export const useVoteStore = defineStore("vote", () => {
    const channelBroadcast = {
        channelName: "voting.process.",
        eventName: "VotingProcess",
    };

    let isEchoJoinSetup = false;
    let isEchoPrivateSetup = false;

    const setupChannel = (roomId) => {
        channelBroadcast.channelName += roomId;
    };

    const votes = ref({});
    const voteCounts = ref({});

    const fetchVotes = async (roomId) => {
        // if (!!votes.value[roomId]) {
        //     return;
        // }

        const response = await axios.get(route("api.room.votes.get", roomId));

        if (response.status === 200) {
            votes.value[roomId] = response.data.data;

            votes.value[roomId].forEach((vote) => {
                const candidateId = vote.candidate_id;
                if (!voteCounts[roomId]) {
                    voteCounts[roomId] = {};
                }
                if (!voteCounts[roomId][candidateId]) {
                    voteCounts[roomId][candidateId] = 0;
                }
                voteCounts[roomId][candidateId]++;
            });
        }
    };

    const setupEchoJoinListener = (
        handleHere,
        handleJoining,
        handleLeaving,
    ) => {
        if (isEchoJoinSetup) {
            return;
        }

        Echo.join(channelBroadcast.channelName)
            .here(handleHere)
            .joining(handleJoining)
            .leaving(handleLeaving);

        isEchoJoinSetup = true;
    };

    const setupEchoPrivateListener = (handleVotingStartBroadcast) => {
        if (isEchoPrivateSetup) {
            return;
        }

        Echo.private(channelBroadcast.channelName).listen(
            channelBroadcast.eventName,
            handleVotingStartBroadcast,
        );

        isEchoPrivateSetup = true;
    };

    const setupEchoListeners = (
        handleHere,
        handleJoining,
        handleLeaving,
        handleVotingStartBroadcast,
    ) => {
        setupEchoJoinListener(handleHere, handleJoining, handleLeaving);
        setupEchoPrivateListener(handleVotingStartBroadcast);
    };

    const leaveEchoListeners = () => {
        Echo.leave(channelBroadcast.channelName);
        isEchoJoinSetup = false;
        isEchoPrivateSetup = false;
    };

    const startVoting = async (roomId) => {
        return await axios.get(route("api.room.vote.start", roomId));
    };

    const storeVotes = async (roomId, formData) => {
        const response = await axios.post(
            route("api.room.vote.store", roomId),
            formData,
        );

        if (response.status === 200) {
            toast.success(response.data.message);
        }
    };

    const storeJoinTime = async (roomId, userId, formData) => {
        try {
            return await axios.post(
                route("api.room.vote.store.join.time", {
                    room: roomId,
                    user: userId,
                }),
                formData,
            );
        } catch (error) {
            console.error("Error:", error);
            throw error;
        }
    };

    const storeLeaveTime = async (roomId, userId, formData) => {
        try {
            return await axios.post(
                route("api.room.vote.store.leave.time", {
                    room: roomId,
                    user: userId,
                }),
                formData,
            );
        } catch (error) {
            console.error("Error:", error);
            throw error;
        }
    };

    return {
        votes,
        voteCounts,
        fetchVotes,
        setupChannel,
        startVoting,
        storeVotes,
        storeJoinTime,
        storeLeaveTime,
        setupEchoJoinListener,
        setupEchoPrivateListener,
        setupEchoListeners,
        leaveEchoListeners,
    };
});
