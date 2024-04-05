import {defineStore} from 'pinia';
import {ref} from "vue";
import axios from 'axios';
import {route} from "ziggy-js";

export const useChatStore = defineStore('chat', () => {
    const messages = ref({});
    const unreadMessagesBySender = ref(null);
    const unreadCountsBySender = ref(null);

    const markReadAll = async (recipientId) => {
        try {
            await axios.post(route('api.user.chat.read.all', recipientId));

            unreadCountsBySender.value[recipientId] = 0;
        } catch (error) {
            console.error('Error fetching unread messages:', error);
        }
    }

    const fetchUnreadAll = async () => {
        try {
            const response = await axios.get(route('api.user.chat.unread.all'));
            unreadMessagesBySender.value = response.data;

            const counts = {};

            for (const senderId in unreadMessagesBySender.value) {
                if (unreadMessagesBySender.value.hasOwnProperty(senderId)) {
                    // Count the number of unread messages for the current sender
                    counts[senderId] = unreadMessagesBySender.value[senderId].length;
                }
            }

            unreadCountsBySender.value = counts;
        } catch (error) {
            console.error('Error fetching unread messages:', error);
        }
    }

    const fetchMessages = async (recipientId) => {
        // if (!messages.value[recipientId]) { }
        try {
            const response = await axios.get(route('api.user.chat.index', recipientId));
            messages.value[recipientId] = response.data.decryptedMessages;
        } catch (error) {
            console.error('Error fetching messages for recipient ID ' + recipientId + ':', error);
        }
    };

    const storeMessage = async (recipientId, formData) => {
        try {
            const response = await window.axios.post(route('api.user.chat.store', recipientId), formData, {
                headers: {
                    'Content-Type': 'multipart/form-data'
                }
            });

            // unreadCountsBySender.value[recipientId] += 1;

            // if (!messages.value[recipientId]) {
            //     messages.value[recipientId] = [];
            // }
            //
            // messages.value[recipientId].push(formData);
        } catch (error) {
            console.error('Error storing message for recipient ID ' + recipientId + ':', error);
        }
    };

    return {messages, fetchMessages, storeMessage, fetchUnreadAll, markReadAll, unreadCountsBySender};
});
