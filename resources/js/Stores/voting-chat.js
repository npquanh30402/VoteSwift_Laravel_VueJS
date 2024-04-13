import { defineStore } from "pinia";
import { ref } from "vue";
import axios from "axios";
import { route } from "ziggy-js";

export const useVotingChatStore = defineStore("votingChat", () => {
    const messages = ref({});
    const unreadCounts = ref({});

    const clearMessages = (roomId) => {
        messages.value[roomId] = [];
        unreadCounts.value[roomId] = 0;
    };
    const markRead = (roomId) => {
        unreadCounts.value[roomId] = 0;
    };

    const fetchMessages = async (roomId) => {
        try {
            const response = await axios.get(
                route("api.vote.chat.index", roomId),
            );
            messages.value[roomId] = response.data.messages;

            unreadCounts.value[roomId] = 0;
        } catch (error) {
            console.error(
                "Error fetching messages for recipient ID " + roomId + ":",
                error,
            );
        }
    };

    const storeMessage = async (roomId, formData) => {
        try {
            await window.axios.post(
                route("api.vote.chat.store", roomId),
                formData,
                {
                    headers: {
                        "Content-Type": "multipart/form-data",
                    },
                },
            );
        } catch (error) {
            console.error(
                "Error storing message for recipient ID " + roomId + ":",
                error,
            );
        }
    };

    return {
        messages,
        unreadCounts,
        fetchMessages,
        storeMessage,
        markRead,
        clearMessages,
    };
});
