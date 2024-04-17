import { defineStore } from "pinia";
import { ref } from "vue";
import axios from "axios";
import { route } from "ziggy-js";
import { useToast } from "vue-toast-notification";

const toast = useToast();

export const useUserChatStore = defineStore("userChat", () => {
    const messages = ref({});
    const unreadMessages = ref({});
    const totalUnreadMessages = ref(0);

    const channelBroadcast = {
        channelName: "chat.",
        eventName: "UserMessageEvent",
    };

    const handleReceivedMessage = (e) => {
        storeBroadcastMessage(e.data.message.sender_id, e.data);
    };

    const setupEchoPrivateListener = (userId) => {
        Echo.private(channelBroadcast.channelName + userId).listen(
            channelBroadcast.eventName,
            handleReceivedMessage,
        );
    };

    const markRead = async (receiverId, senderId) => {
        if (unreadMessages.value[senderId]) {
            const response = await axios.post(
                route("api.user.chat.mark-read", {
                    receiver: receiverId,
                    sender: senderId,
                }),
            );

            if (response.status === 200) {
                totalUnreadMessages.value -= Object.values(
                    unreadMessages.value[senderId],
                ).length;
                unreadMessages.value[senderId] = [];
            }
        }
    };

    const fetchUnreadMessages = async (userId) => {
        try {
            const response = await axios.get(
                route("api.user.chat.unread", userId),
            );
            if (response.status === 200) {
                response.data.data.forEach((message) => {
                    const senderId = message.message.sender_id;
                    if (!unreadMessages.value[senderId]) {
                        unreadMessages.value[senderId] = [];
                    }
                    unreadMessages.value[senderId].push(message);
                });

                totalUnreadMessages.value = Object.values(
                    unreadMessages.value,
                ).reduce((total, messages) => total + messages.length, 0);
            }
        } catch (error) {
            console.error(
                "Error fetching unread messages for user ID " + userId + ":",
                error,
            );
        }
    };

    const fetchMessages = async (senderId, receiverId) => {
        try {
            if (!!messages.value[receiverId]) {
                return;
            }

            if (!messages.value[receiverId]) {
                messages.value[receiverId] = [];
            }

            const response = await axios.get(
                route("api.user.chat.index", {
                    sender: senderId,
                    receiver: receiverId,
                }),
            );

            if (response.status === 200) {
                messages.value[receiverId] = response.data.data;
            }
        } catch (error) {
            console.error(
                "Error fetching messages for recipient ID " + recipientId + ":",
                error,
            );
        }
    };

    const storeMessage = async (senderId, receiverId, formData) => {
        try {
            if (!messages.value[receiverId]) {
                messages.value[receiverId] = [];
            }

            const response = await axios.post(
                route("api.user.chat.store", {
                    sender: senderId,
                    receiver: receiverId,
                }),
                formData,
                {
                    headers: {
                        "Content-Type": "multipart/form-data",
                    },
                },
            );

            if (response.status === 201) {
                messages.value[receiverId].push(response.data.data);
            }
        } catch (error) {
            console.error(
                "Error storing message for recipient ID " + receiverId + ":",
                error,
            );
        }
    };

    const storeBroadcastMessage = async (senderId, message) => {
        if (!messages.value[senderId]) {
            messages.value[senderId] = [];
        }

        messages.value[senderId].push(message);
    };

    return {
        messages,
        unreadMessages,
        totalUnreadMessages,
        fetchMessages,
        fetchUnreadMessages,
        markRead,
        storeMessage,
        storeBroadcastMessage,
        setupEchoPrivateListener,
    };
});
