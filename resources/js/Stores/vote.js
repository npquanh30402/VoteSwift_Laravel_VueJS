import { defineStore } from "pinia";
import { route } from "ziggy-js";

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
        setupChannel,
        startVoting,
        storeJoinTime,
        storeLeaveTime,
        setupEchoJoinListener,
        setupEchoPrivateListener,
        setupEchoListeners,
        leaveEchoListeners,
    };
});
