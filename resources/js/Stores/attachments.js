import {defineStore} from 'pinia'
import {ref} from "vue";
import {route} from "ziggy-js";
import axios from "axios";

export const useAttachmentStore = defineStore('attachment', () => {
    const attachments = ref({})

    const fetchAttachments = async (roomId) => {
        if (!!attachments.value[roomId]) {
            return;
        }

        const response = await axios.get(route('api.room.attachment.index', roomId))
        attachments.value[roomId] = response.data
    }

    const storeAttachment = async (roomId, formData) => {
        const response = await axios.post(route('api.room.attachment.store', roomId), formData)

        if (response.status === 200) {
            attachments.value[roomId].push(response.data)
        }
    }

    const destroyAttachment = async (roomId, attachmentId) => {
        const response = await axios.delete(route('api.attachment.destroy', attachmentId))

        if (response.status === 204) {
            attachments.value[roomId] = attachments.value[roomId].filter(attachment => attachment.id !== attachmentId)
        }
    }

    return {attachments, fetchAttachments, storeAttachment, destroyAttachment}
})
