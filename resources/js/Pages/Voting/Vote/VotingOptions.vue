<template>
    <form @submit.prevent="submitVotes">
        <div class="vstack gap-5 align-items-center">
            <BaseCard
                v-for="(question, index) in questions"
                :key="question.id"
                class="w-75 shadow shadow-sm"
            >
                <template #description>
                    <div class="row justify-content-between align-items-center">
                        <div class="col-md-10 hstack">
                            <img
                                v-if="question.question_image"
                                :src="question.question_image"
                                alt=""
                                class="img-fluid me-3 img-style"
                                @click="showImage"
                            />
                            <div class="w-100">
                                <h3
                                    class="fw-semibold fs-4 text-uppercase text-truncate"
                                >
                                    Question {{ index + 1 }}:
                                    {{ question.question_title }}
                                </h3>
                                <p
                                    class="text-truncate fs-5 text-muted"
                                    style="width: 50vw"
                                >
                                    {{
                                        helper.removeSpecialCharacters(
                                            question.question_description,
                                        )
                                    }}
                                </p>
                            </div>
                        </div>
                        <div
                            class="col-md-2 hstack justify-content-end align-items-center"
                        >
                            <QuestionInfo :question="question" />
                        </div>
                    </div>
                </template>
                <div class="row mx-2">
                    <div
                        :class="{
                            'col-md-6': voterRealtimeResultEnabled,
                            'col-md-12': !voterRealtimeResultEnabled,
                        }"
                    >
                        <p
                            v-if="question.allow_multiple_votes"
                            class="text-muted"
                        >
                            You can choose
                            <span class="fw-bold text-uppercase">multiple</span>
                            options
                        </p>
                        <p v-else class="text-muted">
                            You can only choose
                            <span class="fw-bold text-uppercase">one</span>
                            option
                        </p>
                        <div
                            v-for="(
                                candidate, candidateIndex
                            ) in question.candidates"
                            :key="candidate.id"
                            class="mb-5"
                        >
                            <div class="form-check ms-4 mt-2">
                                <div
                                    class="d-flex justify-content-between gap-3 align-items-center"
                                >
                                    <div class="w-100">
                                        <div>
                                            <input
                                                v-if="
                                                    question.allow_multiple_votes
                                                "
                                                :id="
                                                    candidate.id +
                                                    candidateIndex
                                                "
                                                :name="question.id"
                                                class="form-check-input fs-3"
                                                type="checkbox"
                                                @click="
                                                    onClickCheck(
                                                        question.id,
                                                        candidate.id,
                                                    )
                                                "
                                            />
                                            <input
                                                v-else
                                                :id="
                                                    candidate.id +
                                                    candidateIndex
                                                "
                                                :name="question.id"
                                                class="form-check-input fs-3"
                                                type="radio"
                                                @click="
                                                    onClickRadio(
                                                        question.id,
                                                        candidate.id,
                                                    )
                                                "
                                            />
                                        </div>
                                        <div>
                                            <label
                                                :for="
                                                    candidate.id +
                                                    candidateIndex
                                                "
                                                class="form-check-label fs-4 text-truncate"
                                                style="width: 30rem"
                                            >
                                                {{ candidate.candidate_title }}
                                            </label>
                                            <p
                                                class="text-truncate text-muted"
                                                style="width: 30rem"
                                            >
                                                {{
                                                    helper.removeSpecialCharacters(
                                                        candidate.candidate_description,
                                                    )
                                                }}
                                            </p>
                                        </div>
                                    </div>
                                    <div
                                        class="d-flex justify-content-between align-items-center me-4"
                                    >
                                        <img
                                            v-if="candidate.candidate_image"
                                            :src="candidate.candidate_image"
                                            alt=""
                                            class="img-fluid me-3 img-style"
                                            @click="showImage"
                                        />
                                        <CandidateInfo :candidate="candidate" />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div
                        v-if="voterRealtimeResultEnabled"
                        class="card col-md-6"
                    >
                        <div class="card-body">
                            <BarChart
                                :datasets="voteCounts[question.id]?.voteCounts"
                                :labels="
                                    trimText(
                                        voteCounts[question.id]
                                            ?.candidateLabels,
                                        10,
                                    )
                                "
                                :options="options"
                            />
                        </div>
                    </div>
                </div>
            </BaseCard>
            <teleport to="body">
                <LightBoxHelper :currentImageDisplay="currentImageDisplay" />
            </teleport>
        </div>

        <div class="text-center my-5">
            <button
                class="btn-lg btn btn-primary"
                type="submit"
                @click="submitVotes"
            >
                Submit
            </button>
        </div>
    </form>
</template>
<script setup>
import BaseCard from "@/Components/BaseCard.vue";
import BarChart from "@/Components/BarChart.vue";
import { computed, ref } from "vue";
import { useVotingResultStore } from "@/Stores/voting-results.js";
import QuestionInfo from "@/Pages/Voting/Vote/QuestionInfo.vue";
import LightBoxHelper from "@/Components/Helpers/LightBoxHelper.vue";
import { useHelper } from "@/Services/helper.js";
import CandidateInfo from "@/Pages/Voting/Vote/CandidateInfo.vue";
import { route } from "ziggy-js";
import { router } from "@inertiajs/vue3";

const props = defineProps(["room", "questions", "voteCounts", "roomSettings"]);
const votingResultStore = useVotingResultStore();
const helper = useHelper();
const currentImageDisplay = ref(null);
const voteCounts = computed(() => props.voteCounts);

const selectedOptions = ref({});
const voterRealtimeResultEnabled = computed(
    () => props.roomSettings?.voters_can_see_realtime_results,
);

const onClickCheck = async (questionId, candidateId) => {
    const formData = new FormData();
    formData.append("questionId", questionId);
    formData.append("candidateId", candidateId);

    await votingResultStore.broadCastChoice(props.room.id, formData);

    if (!selectedOptions.value.hasOwnProperty(questionId)) {
        selectedOptions.value[questionId] = [candidateId, -1];
    } else {
        const index = selectedOptions.value[questionId].indexOf(candidateId);
        if (index !== -1) {
            selectedOptions.value[questionId].splice(index, 1);
            if (
                selectedOptions.value[questionId].length === 1 &&
                selectedOptions.value[questionId][0] === -1
            ) {
                delete selectedOptions.value[questionId];
            }
        } else {
            selectedOptions.value[questionId].push(candidateId);
        }
    }

    await axios.post(
        route("api.room.vote.store.choices", props.room.id),
        selectedOptions.value,
    );
};

const onClickRadio = async (questionId, candidateId) => {
    const formData = new FormData();
    formData.append("questionId", questionId);
    formData.append("candidateId", candidateId);

    if (selectedOptions.value.hasOwnProperty(questionId)) {
        const oldCandidateId = selectedOptions.value[questionId][0];
        if (oldCandidateId !== candidateId) {
            selectedOptions.value[questionId] = [candidateId];
        }
    } else {
        selectedOptions.value[questionId] = [candidateId];
    }

    await votingResultStore.broadCastChoice(props.room.id, formData);
    await axios.post(
        route("api.room.vote.store.choices", props.room.id),
        selectedOptions.value,
    );
};

function trimText(textArray, length) {
    if (!Array.isArray(textArray)) {
        return [];
    }

    return textArray.map((text) => {
        if (typeof text !== "string") {
            return "";
        }

        return text.length > length ? text.substring(0, length) : text;
    });
}

const showImage = (e) => {
    currentImageDisplay.value = e;
};
const emit = defineEmits(["switch-tab"]);

function submitVotes() {
    // router.post(route("vote.store", props.room.id), {
    //     selectedOptions: selectedOptions.value,
    // });

    emit("switch-tab", "VotingSubmit");
}

const options = {
    responsive: true,
    backgroundColor: [
        "rgba(255, 99, 132, 0.2)",
        "rgba(255, 159, 64, 0.2)",
        "rgba(255, 205, 86, 0.2)",
        "rgba(75, 192, 192, 0.2)",
        "rgba(54, 162, 235, 0.2)",
        "rgba(153, 102, 255, 0.2)",
        "rgba(201, 203, 207, 0.2)",
    ],
    borderColor: [
        "rgb(255, 99, 132)",
        "rgb(255, 159, 64)",
        "rgb(255, 205, 86)",
        "rgb(75, 192, 192)",
        "rgb(54, 162, 235)",
        "rgb(153, 102, 255)",
        "rgb(201, 203, 207)",
    ],
    borderWidth: 1,
    indexAxis: "y",
    maintainAspectRatio: false,
};
</script>

<style scoped>
.img-style {
    border-radius: 4px;
    box-shadow:
        rgba(0, 0, 0, 0.024) 0px 0px 0px 1px,
        rgba(0, 0, 0, 0.05) 0px 1px 0px 0px,
        rgba(0, 0, 0, 0.03) 0px 0px 8px 0px,
        rgba(0, 0, 0, 0.1) 0px 20px 30px 0px;

    height: 8rem;
    cursor: pointer;
}
</style>
