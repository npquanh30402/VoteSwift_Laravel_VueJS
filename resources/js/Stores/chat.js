import {defineStore} from 'pinia';
import {ref} from "vue";
import axios from 'axios';
import {route} from "ziggy-js";

export const useChatStore = defineStore('chat', () => {
    const messages = ref({});

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

            if (!messages.value[recipientId]) {
                messages.value[recipientId] = [];
            }

            messages.value[recipientId].push(formData);
        } catch (error) {
            console.error('Error storing message for recipient ID ' + recipientId + ':', error);
        }
    };
    
    return {messages, fetchMessages, storeMessage};
});
