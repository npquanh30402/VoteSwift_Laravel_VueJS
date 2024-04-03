<template>
    <div class="dropdown">
        <div class="dropdown-action" data-bs-toggle="dropdown" aria-expanded="false">
            <i class="bi bi-three-dots"></i>
        </div>
        <ul class="dropdown-menu">
            <li>
                <button class="dropdown-item text-success">Details</button>
            </li>
            <li>
                <button class="dropdown-item text-danger" @click="deleteCandidate">Delete</button>
            </li>
        </ul>
    </div>
</template>

<script setup>

import {useCandidateStore} from "@/Stores/candidates.js";

const props = defineProps(['candidate'])

const CandidateStore = useCandidateStore()

const deleteCandidate = () => {
    CandidateStore.deleteCandidate(props.candidate.id)
}

</script>

<style scoped>
.dropdown-action {
    cursor: pointer;
}
</style>
