import { defineStore } from "pinia";
import { ref } from "vue";
import { route } from "ziggy-js";
import { useToast } from "vue-toast-notification";
import axios from "axios";

const toast = useToast();

export const useQuestionStore = defineStore("question", () => {
    const questions = ref([]);

    const importQuestions = async (roomId, formData) => {
        try {
            const response = await axios.post(
                route("api.rooms.questions.csv", roomId),
                formData,
            );

            if (response.status === 200) {
                questions.value[roomId] = [
                    ...questions.value[roomId],
                    ...response.data.data,
                ];
                toast.success(response.data.message);
            }
        } catch (error) {
            toast.error(error.response.data.message);
        }
    };

    const fetchQuestions = async (roomId) => {
        if (!!questions.value[roomId]) {
            return;
        }

        try {
            const response = await axios.get(
                route("api.rooms.questions.index", roomId),
            );

            if (response.status === 200) {
                questions.value[roomId] = response.data.data;
            }
        } catch (error) {
            toast.error(error.response.data.message);
        }
    };

    const storeQuestion = async (roomId, formData) => {
        try {
            const response = await axios.post(
                route("api.rooms.question.store", roomId),
                formData,
            );

            if (response.status === 201) {
                questions.value[roomId].push(response.data.data);
                toast.success(response.data.message);
            }
        } catch (error) {
            toast.error(error.response.data.message);
        }
    };

    const updateQuestion = async (roomId, questionId, formData) => {
        formData.append("_method", "PUT");

        try {
            const response = await axios.post(
                route("api.rooms.question.update", questionId),
                formData,
            );

            if (response.status === 200) {
                const questionsInRoom = questions.value[roomId];
                for (let i = 0; i < questionsInRoom.length; i++) {
                    if (questionsInRoom[i].id === questionId) {
                        questionsInRoom[i] = response.data.data;
                        break;
                    }
                }
                toast.success(response.data.message);
            }
        } catch (error) {
            toast.error(error.response.data.message);
        }
    };

    const deleteQuestion = async (roomId, questionId) => {
        try {
            const response = await axios.delete(
                route("api.rooms.question.delete", questionId),
            );

            if (response.status === 200) {
                const questionsInRoom = questions.value[roomId];
                for (let i = 0; i < questionsInRoom.length; i++) {
                    if (questionsInRoom[i].id === questionId) {
                        questionsInRoom.splice(i, 1);
                        break;
                    }
                }
                toast.success(response.data.message);
            }
        } catch (error) {
            toast.error(error.response.data.message);
        }
    };

    const transformQuestions = (questions, candidates) => {
        if (!questions || !candidates) {
            return [];
        }
        const candidatesArray = Object.values(candidates).flatMap(
            (candidateGroup) =>
                candidateGroup.map((candidateObject) => ({
                    ...candidateObject,
                })),
        );

        const questionMap = new Map();
        candidatesArray.forEach((candidate) => {
            const questionId = candidate.question_id;
            if (!questionMap.has(questionId)) {
                questionMap.set(questionId, [candidate]);
            } else {
                questionMap.get(questionId).push(candidate);
            }
        });

        return questions.map((question) => ({
            ...question,
            candidates: questionMap.get(question.id) || [],
        }));
    };

    return {
        questions,
        fetchQuestions,
        storeQuestion,
        updateQuestion,
        deleteQuestion,
        transformQuestions,
        importQuestions,
    };
});
