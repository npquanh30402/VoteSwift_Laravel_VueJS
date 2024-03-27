<template>
    <form @submit.prevent="submitVotes" class="vstack gap-5 align-items-center">
        <BaseCard class="w-75 shadow shadow-sm" v-for="(question, index) in questions" :key="question.id">
            <template #title>
                <h3 class="fw-semibold">Question {{ index + 1 }}: {{ question.question_title }}</h3>
            </template>
            <template #description>
                <p class="text-truncate" style="width: 90%">{{ question.question_description }}</p>
            </template>
            <p class="text-muted" v-if="question.allow_multiple_votes">You can choose <span
                class="fw-bold text-uppercase">multiple</span> options</p>
            <p class="text-muted" v-else>You can only choose <span class="fw-bold text-uppercase">one</span> option
            </p>
            <div v-for="(candidate, candidateIndex) in question.candidates" :key="candidate.id">
                <div class="form-check ms-4 mt-2">
                    <input
                        v-if="question.allow_multiple_votes"
                        @click="onClickCheck(question.id, candidate.id)"
                        class="form-check-input fs-3"
                        type="checkbox"
                        :name="question.id"
                        :id="candidate.id + candidateIndex">
                    <input
                        v-else
                        @click="onClickRadio(question.id, candidate.id)"
                        class="form-check-input fs-3"
                        type="radio"
                        :name="question.id"
                        :id="candidate.id + candidateIndex">
                    <!--                    <input @click="onClick(question.id, candidate.id)" class="form-check-input fs-3" type="radio"-->
                    <!--                           :name="question.id"-->
                    <!--                           :id="candidate.id + candidateIndex">-->
                    <label class="form-check-label fs-4 text-truncate" :for="candidate.id + candidateIndex">
                        {{ candidate.candidate_title }}
                    </label>
                    <p class="text-truncate text-muted fs-6 w-75">{{ candidate.candidate_description }}</p>
                </div>
            </div>
        </BaseCard>

        <div class="text-center my-5">
            <button type="submit" class="btn-lg btn btn-primary">Submit</button>
        </div>
    </form>
</template>

<script setup>
import BaseCard from "@/Components/BaseCard.vue";
import {onMounted, ref} from "vue";
import {router} from "@inertiajs/vue3";
import {route} from "ziggy-js";

const props = defineProps(['questions', 'room'])

const selectedOptions = ref({});

const onClickCheck = (questionId, candidateId) => {
    if (!selectedOptions.value.hasOwnProperty(questionId)) {
        selectedOptions.value[questionId] = [candidateId];
    } else {
        const index = selectedOptions.value[questionId].indexOf(candidateId);
        if (index !== -1) {
            selectedOptions.value[questionId].splice(index, 1);
        } else {
            selectedOptions.value[questionId].push(candidateId);
        }
    }
};


const onClickRadio = (questionId, candidateId) => {
    if (selectedOptions.value.hasOwnProperty(questionId)) {
        const index = selectedOptions.value[questionId].indexOf(candidateId);
        if (index !== -1) {
            selectedOptions.value[questionId].splice(index, 1);
        } else {
            selectedOptions.value[questionId] = [candidateId];
        }
    } else {
        selectedOptions.value[questionId] = [candidateId];
    }

    console.log(selectedOptions.value);
};

onMounted(() => {
    window.scrollTo({top: 0, left: 0, behavior: 'smooth'});
})

function submitVotes() {
    router.post(route('vote.store', props.room.id), {
        selectedOptions: selectedOptions.value
    })
}
</script>
