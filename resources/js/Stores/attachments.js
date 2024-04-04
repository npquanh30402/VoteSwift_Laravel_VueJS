import {defineStore} from 'pinia'
import {ref} from "vue";
import {route} from "ziggy-js";

export const useAttachmentStore = defineStore('attachment', () => {
    const attachments = ref([])

    const fetchAttachment = async (roomId) => {
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

    const updateCandidate = async (candidateId, candidateData) => {
        await axios.post(route('api.candidate.update', candidateId), candidateData)
            .then(function (response) {
                if (response.status === 200) {
                    for (const questionId in candidates.value) {
                        const candidatesForQuestion = candidates.value[questionId];

                        const index = candidatesForQuestion.findIndex(candidate => candidate.id === candidateId);

                        if (index !== -1) {
                            candidatesForQuestion[index] = response.data;
                        }
                    }
                }
            })
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

    return {candidates, fetchCandidates, storeCandidate, updateCandidate, deleteCandidate}
})
