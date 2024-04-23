import { defineStore } from "pinia";
import { ref } from "vue";
import { route } from "ziggy-js";
import { useToast } from "vue-toast-notification";
import axios from "axios";

const toast = useToast();

export const useCandidateStore = defineStore("candidate", () => {
    const candidates = ref([]);

    const importCandidates = async (roomId, questionId, formData) => {
        try {
            const response = await axios.post(
                route("api.questions.candidates.csv", questionId),
                formData,
            );

            if (response.status === 200) {
                if (!candidates.value[roomId][questionId]) {
                    candidates.value[roomId][questionId] = [];
                }

                candidates.value[roomId][questionId] = [
                    ...candidates.value[roomId][questionId],
                    ...response.data.data,
                ];

                toast.success(response.data.message);
            }
        } catch (error) {
            toast.error(error.response.data.message);
        }
    };

    const fetchCandidates = async (roomId) => {
        if (!!candidates.value[roomId]) {
            return;
        }

        try {
            const response = await axios.get(
                route("api.rooms.candidates.index", roomId),
            );

            if (response.status === 200) {
                candidates.value[roomId] = response.data.data;
            }
        } catch (error) {
            toast.error(error.response.data.message);
        }
    };

    const storeCandidate = async (roomId, questionId, formData) => {
        try {
            const response = await axios.post(
                route("api.questions.candidate.store", questionId),
                formData,
            );

            if (!candidates.value[roomId][questionId]) {
                candidates.value[roomId][questionId] = [];
            }

            if (response.status === 201) {
                candidates.value[roomId][questionId].push(response.data.data);
                toast.success(response.data.message);
            }
        } catch (error) {
            toast.error(error.response.data.message);
        }
    };

    const updateCandidate = async (roomId, candidateId, formData) => {
        formData.append("_method", "PUT");

        try {
            const response = await axios.post(
                route("api.candidate.update", candidateId),
                formData,
            );

            if (response.status === 200) {
                const roomCandidates = candidates.value[roomId];
                for (const questionId in roomCandidates) {
                    const candidatesForQuestion = roomCandidates[questionId];

                    const index = candidatesForQuestion.findIndex(
                        (candidate) => candidate.id === candidateId,
                    );

                    if (index !== -1) {
                        candidatesForQuestion[index] = response.data.data;
                    }
                }
                toast.success(response.data.message);
            }
        } catch (error) {
            toast.error(error.response.data.message);
        }
    };

    const deleteCandidate = async (roomId, candidateId) => {
        try {
            const response = await axios.delete(
                route("api.candidate.delete", candidateId),
            );

            if (response.status === 200) {
                const roomCandidates = candidates.value[roomId];
                for (const questionId in roomCandidates) {
                    const candidatesForQuestion = roomCandidates[questionId];

                    const index = candidatesForQuestion.findIndex(
                        (candidate) => candidate.id === candidateId,
                    );

                    if (index !== -1) {
                        candidatesForQuestion.splice(index, 1);
                    }
                }
                toast.success(response.data.message);
            }
        } catch (error) {
            toast.error(error.response.data.message);
        }
    };

    return {
        candidates,
        fetchCandidates,
        storeCandidate,
        updateCandidate,
        deleteCandidate,
        importCandidates,
    };
});
