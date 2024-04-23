<template>
    <div>
        <p v-if="question.allow_multiple_votes" class="text-muted">
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
            v-for="candidate in candidates"
            :key="candidate.id"
            :class="{ 'un-interactive': question.isSkipped }"
            class="mb-5"
        >
            <div class="form-check ms-4 mt-2">
                <div
                    class="d-flex justify-content-between gap-3 align-items-center"
                >
                    <div class="w-100">
                        <CandidateInfo :candidate="candidate" />

                        <input
                            :id="inputId(candidate.id)"
                            :checked="!question.isSkipped ? false : null"
                            :name="inputName(question.id)"
                            :required="
                                !question.isSkipped &&
                                inputType(question.allow_multiple_votes) !==
                                    'checkbox'
                            "
                            :type="inputType(question.allow_multiple_votes)"
                            class="form-check-input fs-3"
                            @click="onClick(question.id, candidate.id)"
                        />
                        <label
                            :for="inputId(candidate.id)"
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
                                removeSpecialCharacters(
                                    candidate.candidate_description,
                                )
                            }}
                        </p>
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
                    </div>
                </div>
            </div>
        </div>
        <LightBoxHelper :currentImageDisplay="currentImageDisplay" />
    </div>
</template>

<script setup>
import { useHelper } from "@/Services/helper.js";
import CandidateInfo from "@/Pages/Voting/Vote/CandidateInfo.vue";
import { ref } from "vue";
import LightBoxHelper from "@/Components/Helpers/LightBoxHelper.vue";

const props = defineProps(["question", "candidates"]);
const emit = defineEmits(["click-check", "click-radio"]);
const { removeSpecialCharacters } = useHelper();
const currentImageDisplay = ref(null);

const inputId = (candidateId) => `input_${candidateId}`;
const inputName = (questionId) => `input_${questionId}`;
const inputType = (allowMultiple) => (allowMultiple ? "checkbox" : "radio");

const onClick = (questionId, candidateId) => {
    const eventName = props.question.allow_multiple_votes
        ? "click-check"
        : "click-radio";
    emit(eventName, questionId, candidateId);
};

const showImage = (e) => {
    currentImageDisplay.value = e;
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
