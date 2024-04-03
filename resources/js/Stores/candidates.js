import {defineStore} from 'pinia'
import {ref} from "vue";
import {route} from "ziggy-js";

export const useCandidateStore = defineStore('candidate', () => {
    const candidates = ref([])

    const fetchCandidates = async (roomId) => {
        const response = await axios.get(route('api.room.candidate.index', roomId))
        candidates.value = response.data
    }

    const storeCandidate = async (questionId, candidateData) => {
        await axios.post(route('api.question.candidate.store', {question: questionId}), candidateData)
            .then(function (response) {
                if (response.status === 201) {
                    candidates.value[questionId].push(response.data);
                }
            });
    }

    const deleteCandidate = async (candidateId) => {
        await axios.delete(route('api.candidate.destroy', candidateId))

        for (const questionId in candidates.value) {
            const candidatesForQuestion = candidates.value[questionId];

            const index = candidatesForQuestion.findIndex(candidate => candidate.id === candidateId);

            if (index !== -1) {
                candidatesForQuestion.splice(index, 1);
            }
        }
    }

    // const createQuestion = async (roomId, data) => {
    //     const response = await axios.post(route('api.room.questions.store', roomId), data)
    //     candidates.value = candidates.value.push(response.data)
    // }
    //
    // const updateQuestion = async (questionId, data) => {
    //     const response = await axios.put(route('api.room.questions.update', questionId), data)
    //
    //     const index = candidates.value.findIndex(question => question.id === questionIds)
    //     if (index !== -1) {
    //         candidates.value[index] = response.data
    //     }
    // }

    return {candidates, fetchCandidates, storeCandidate, deleteCandidate}
})
