import {defineStore} from 'pinia'
import {ref} from "vue";
import {route} from "ziggy-js";
import axios from "axios";

export const useInvitationStore = defineStore('invitation', () => {
    const invitations = ref({})

    const fetchInvitations = async (roomId) => {
        const response = await axios.get(route('api.room.invitation.index', roomId))
        invitations.value[roomId] = response.data
    }

    const storeInvitations = async (roomId, data) => {
        await axios.post(route('api.room.invitation.store', roomId), data)
    }

    return {invitations, fetchInvitations, storeInvitations}
})
