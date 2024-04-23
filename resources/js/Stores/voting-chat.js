import { defineStore } from "pinia";
import { ref } from "vue";
import axios from "axios";
import { route } from "ziggy-js";
import { useToast } from "vue-toast-notification";

const toast = useToast();

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
                route("api.rooms.chats.index", roomId),
            );

            if (response.status === 200) {
                messages.value[roomId] = response.data.data;

                unreadCounts.value[roomId] = 0;
            }
        } catch (error) {
            toast.error(error.response.data.message);
        }
    };

    const storeMessage = async (roomId, formData) => {
        try {
            const response = await window.axios.post(
                route("api.rooms.chat.store", roomId),
                formData,
                {
                    headers: {
                        "Content-Type": "multipart/form-data",
                    },
                },
            );
        } catch (error) {
            toast.error(error.response.data.message);
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
