import { defineStore } from "pinia";
import { ref } from "vue";
import { route } from "ziggy-js";
import axios from "axios";
import { useToast } from "vue-toast-notification";

const toast = useToast();

export const useVotingFeedbackStore = defineStore("votingFeedback", () => {
    const feedbacks = ref({});

    const storeFeedback = async (userId, roomId, formData) => {
        if (feedbacks.value[roomId] === undefined) {
            feedbacks.value[roomId] = {};
        }

        try {
            const response = await axios.post(
                route("api.room.user.feedback.store", {
                    user: userId,
                    room: roomId,
                }),
                formData,
            );

            if (response.status === 200) {
                feedbacks.value[roomId][userId] = response.data.data;

                toast.success(response.data.message);
            }
        } catch (error) {
            toast.error(error.response.data.message);
        }
    };

    return { feedbacks, storeFeedback };
});
