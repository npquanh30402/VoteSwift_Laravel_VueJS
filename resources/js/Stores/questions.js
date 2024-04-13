import { defineStore } from "pinia";
import { ref } from "vue";
import { route } from "ziggy-js";

export const useQuestionStore = defineStore("question", () => {
    const questions = ref([]);

    const fetchQuestions = async (roomId) => {
        if (!!questions.value[roomId]) {
            return;
        }

        await axios
            .get(route("api.room.question.index", roomId))
            .then(function (response) {
                if (response.status === 200) {
                    questions.value[roomId] = response.data;
                }
            });
    };

    const storeQuestion = async (roomId, formData) => {
        await axios
            .post(route("api.room.question.store", roomId), formData)
            .then(function (response) {
                if (response.status === 201) {
                    questions.value[roomId].push(response.data);
                }
            });
    };

    const updateQuestion = async (roomId, questionId, formData) => {
        formData.append("_method", "PUT");

        await axios
            .post(route("api.room.question.update", questionId), formData)
            .then(function (response) {
                if (response.status === 200) {
                    const questionsInRoom = questions.value[roomId];
                    for (let i = 0; i < questionsInRoom.length; i++) {
                        if (questionsInRoom[i].id === questionId) {
                            questionsInRoom[i] = response.data;
                            break;
                        }
                    }
                }
            });
    };

    const deleteQuestion = async (roomId, questionId) => {
        await axios
            .delete(route("api.room.question.destroy", questionId))
            .then(function (response) {
                if (response.status === 204) {
                    const questionsInRoom = questions.value[roomId];
                    for (let i = 0; i < questionsInRoom.length; i++) {
                        if (questionsInRoom[i].id === questionId) {
                            questionsInRoom.splice(i, 1);
                            break;
                        }
                    }
                }
            });
    };

    const transformQuestions = (questions, candidates) => {
        if (!questions || !candidates) {
            return [];
        }
        const candidatesArray = Object.values(candidates).flatMap(
            (candidateGroup) =>
                candidateGroup.map((candidateProxy) => ({ ...candidateProxy })),
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
    };
});
