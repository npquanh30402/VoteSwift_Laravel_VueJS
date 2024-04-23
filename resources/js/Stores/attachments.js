import { defineStore } from "pinia";
import { ref } from "vue";
import { route } from "ziggy-js";
import axios from "axios";
import { useToast } from "vue-toast-notification";

const toast = useToast();

export const useAttachmentStore = defineStore("attachment", () => {
    const attachments = ref({});

    const fetchAttachments = async (roomId) => {
        if (!!attachments.value[roomId]) {
            return;
        }

        try {
            const response = await axios.get(
                route("api.room.attachments.index", roomId),
            );

            if (response.status === 200) {
                attachments.value[roomId] = response.data.data;
            }
        } catch (error) {
            toast.error(error.response.data.message);
        }
    };

    const storeAttachment = async (roomId, formData) => {
        try {
            const response = await axios.post(
                route("api.room.attachments.store", roomId),
                formData,
            );

            if (response.status === 200) {
                attachments.value[roomId].push(response.data.data);
            }
        } catch (error) {
            toast.error(error.response.data.message);
        }
    };

    const destroyAttachment = async (roomId, attachmentId) => {
        try {
            const response = await axios.delete(
                route("api.room.attachments.delete", attachmentId),
            );

            if (response.status === 200) {
                attachments.value[roomId] = attachments.value[roomId].filter(
                    (attachment) => attachment.id !== attachmentId,
                );
            }
        } catch (error) {
            toast.error(error.response.data.message);
        }
    };

    return {
        attachments,
        fetchAttachments,
        storeAttachment,
        destroyAttachment,
    };
});
