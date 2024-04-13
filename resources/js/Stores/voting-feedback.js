import { defineStore } from "pinia";
import { ref } from "vue";
import { route } from "ziggy-js";
import axios from "axios";

export const useVotingFeedbackStore = defineStore("votingFeedback", () => {
    const feedbacks = ref({});

    const storeFeedback = async (userId, roomId, formData) => {
        if (feedbacks.value[roomId] === undefined) {
            feedbacks.value[roomId] = {};
        }

        const response = await axios.post(
            route("api.room.user.feedback.store", {
                user: userId,
                room: roomId,
            }),
            formData,
        );

        if (response.status === 200) {
            feedbacks.value[roomId][userId] = response.data;
            return response;
        } else {
            throw new Error("Store feedback failed: " + response.statusText);
        }
    };

    return { feedbacks, storeFeedback };
});
