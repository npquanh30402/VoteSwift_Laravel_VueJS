<template>
    <div>
        <div class="list-group">
            <div class="list-group-item d-flex justify-content-between justify-content-center"
                 v-for="candidate in candidates" :key="candidate.id">
                <p>{{ candidate.candidate_title }}</p>
                <CandidateAction :candidate="candidate"/>
            </div>
        </div>
    </div>
</template>

<script setup>

import CandidateAction from "@/Pages/Voting/Question/Candidate/CandidateAction.vue";

const props = defineProps(['question', 'candidates'])

</script>
