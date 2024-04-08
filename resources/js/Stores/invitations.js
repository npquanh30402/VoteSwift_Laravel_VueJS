import {defineStore} from 'pinia'
import {ref} from "vue";
import {route} from "ziggy-js";
import axios from "axios";

export const useInvitationStore = defineStore('invitation', () => {
    const invitations = ref({})

    const fetchInvitations = async (roomId) => {
        if (!!invitations.value[roomId]) {
            return;
        }

        const response = await axios.get(route('api.room.invitation.index', roomId))
        invitations.value[roomId] = response.data
    }

    const storeInvitations = async (roomId, data) => {
        try {
            const response = await axios.post(route('api.room.invitation.store', roomId), data)

            if (response.status === 200) {
                invitations.value[roomId] = response.data
            }
        } catch (error) {
            console.error('Error:', error);
            throw error;
        }
    }

    return {invitations, fetchInvitations, storeInvitations}
})
