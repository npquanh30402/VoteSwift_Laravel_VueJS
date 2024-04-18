<template>
    <form @submit.prevent="submitVotes">
        <div class="vstack gap-5 align-items-center">
            <BaseCard
                v-for="(question, index) in newQuestions"
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
                        <div class="col-md-2">
                            <div
                                v-if="question.allow_skipping"
                                class="hstack justify-content-end"
                            >
                                <div class="form-check">
                                    <input
                                        :id="'skipCheckBox_' + question.id"
                                        class="form-check-input"
                                        type="checkbox"
                                        @click="onClickSkip(question.id)"
                                    />
                                    <label
                                        :for="'skipCheckBox_' + question.id"
                                        class="form-check-label"
                                    >
                                        Skip
                                    </label>
                                </div>
                            </div>
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
                        <CandidateOptions
                            :candidates="question.candidates"
                            :question="question"
                            @click-check="onClickCheck"
                            @click-radio="onClickRadio"
                        />
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
            <button class="btn-lg btn btn-primary" type="submit">Submit</button>
        </div>
    </form>
</template>
<script setup>
import BaseCard from "@/Components/BaseCard.vue";
import BarChart from "@/Components/BarChart.vue";
import { computed, onMounted, ref, watch } from "vue";
import { useVotingResultStore } from "@/Stores/voting-results.js";
import QuestionInfo from "@/Pages/Voting/Vote/QuestionInfo.vue";
import LightBoxHelper from "@/Components/Helpers/LightBoxHelper.vue";
import { useHelper } from "@/Services/helper.js";
import { route } from "ziggy-js";
import CandidateOptions from "@/Pages/Voting/Vote/StartVoting/CandidateOptions.vue";

const props = defineProps(["room", "questions", "voteCounts", "roomSettings"]);
const votingResultStore = useVotingResultStore();
const helper = useHelper();
const currentImageDisplay = ref(null);
const voteCounts = computed(() => props.voteCounts);

const selectedOptions = ref({});
const voterRealtimeResultEnabled = computed(
    () => props.roomSettings?.voters_can_see_realtime_results === 1,
);

const newQuestions = ref([]);

watch(
    () => props.questions,
    (newVal) => {
        if (newVal.length > 0) {
            newQuestions.value = newVal.map((question) => ({
                ...question,
                isSkipped: 0,
            }));
        }
    },
);

onMounted(() => {
    newQuestions.value = props.questions.map((question) => ({
        ...question,
        isSkipped: 0,
    }));
});

const onClickSkip = async (questionId) => {
    const question = newQuestions.value.find((q) => q.id === questionId);
    if (question) {
        question.isSkipped = !question.isSkipped ? 1 : 0;
    }

    if (selectedOptions.value.hasOwnProperty(questionId)) {
        delete selectedOptions.value[questionId];
    }
};

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
    const formData = new FormData();

    formData.append("selectedOptions", JSON.stringify(selectedOptions.value));
    voteStore.storeVotes(props.room.id, formData);

    // emit("switch-tab", "VotingSubmit");
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
