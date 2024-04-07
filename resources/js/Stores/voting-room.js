import {defineStore} from 'pinia'
import {ref} from "vue";
import {route} from "ziggy-js";

export const useVotingRoomStore = defineStore('room', () => {
    const rooms = ref([])

    const fetchRooms = async () => {
        if (rooms.value?.length > 0) {
            return;
        }

        const response = await axios.get(route('api.room.index'))

        if (response.status === 200) {
            rooms.value = response.data
        }
    }

    const storeRoom = async (formData) => {
        try {
            const response = await axios.post(route('api.room.store'), formData);
            if (response.status === 201) {
                rooms.value.push(response.data);
            }
            return response;
        } catch (error) {
            console.error('Error:', error);
            throw error;
        }
    };

    const deleteRoom = async (roomId) => {
        const response = await axios.delete(route('api.room.destroy', roomId))

        if (response.status === 204) {
            rooms.value = rooms.value.filter((room) => room.id !== roomId);
        }

        return response
    };

    return {rooms, fetchRooms, storeRoom, deleteRoom}
})
