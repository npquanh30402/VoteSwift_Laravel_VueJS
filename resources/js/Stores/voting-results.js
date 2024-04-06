import {defineStore} from 'pinia';
import {ref} from "vue";
import axios from 'axios';
import {route} from "ziggy-js";

export const useVotingResultStore = defineStore('votingResult', () => {
    const results = ref({});

    const fetchResults = async (roomId) => {
        try {
            const response = await axios.get(route('api.room.vote.results', roomId));
            results.value[roomId] = response.data.vote_results;
        } catch (error) {
            console.error('Error fetching results for room ID ' + roomId + ':', error);
        }
    };

    // const storeMessage = async (roomId, formData) => {
    //     try {
    //         await window.axios.post(route('api.vote.chat.store', roomId), formData, {
    //             headers: {
    //                 'Content-Type': 'multipart/form-data'
    //             }
    //         });
    //     } catch (error) {
    //         console.error('Error storing message for recipient ID ' + roomId + ':', error);
    //     }
    // };

    return {results, fetchResults};
});
