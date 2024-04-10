import {defineStore} from 'pinia'
import {ref} from "vue";
import {route} from "ziggy-js";

export const useVoteStore = defineStore('vote', () => {

    const startVoting = async (roomId) => {
        return await axios.get(route('api.room.vote.start', roomId))
    }

    const storeJoinTime = async (roomId, userId, formData) => {
        try {
            return await axios.post(route('api.room.vote.store.join.time', {
                'room': roomId,
                'user': userId
            }), formData);
        } catch (error) {
            console.error('Error:', error);
            throw error;
        }
    }

    return {startVoting, storeJoinTime}
})
