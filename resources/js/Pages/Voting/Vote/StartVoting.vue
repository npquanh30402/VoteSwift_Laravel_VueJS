<template>
    <form @submit.prevent="submitVotes" class="vstack gap-5 align-items-center">
        <BaseCard class="w-75 shadow shadow-sm" v-for="(question, index) in questions" :key="question.id">
            <template #title>
                <h3 class="fw-semibold">Question {{ index + 1 }}: {{ question.question_title }}</h3>
            </template>
            <template #description>
                <p class="text-truncate" style="width: 90%">{{ question.question_description }}</p>
            </template>
            <div v-for="(candidate, candidateIndex) in question.candidates" :key="candidate.id">
                <div class="form-check ms-4">
                    <input @click="onClick(question.id, candidate.id)" class="form-check-input fs-3" type="radio"
                           :name="question.id"
                           :id="candidate.id + candidateIndex">
                    <label class="form-check-label fs-4 text-truncate" :for="candidate.id + candidateIndex">
                        {{ candidate.candidate_title }}
                    </label>
                    <p class="text-truncate text-muted fs-6 w-75">{{ candidate.candidate_description }}</p>
                </div>
            </div>
        </BaseCard>

        <button type="submit" class="btn btn-primary">Submit</button>
    </form>
</template>

<script setup>
import BaseCard from "@/Components/BaseCard.vue";
import {onMounted, ref} from "vue";
import {router} from "@inertiajs/vue3";
import {route} from "ziggy-js";

const props = defineProps(['questions', 'room'])

const selectedOptions = ref({});

const onClick = (questionId, candidateId) => {
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
