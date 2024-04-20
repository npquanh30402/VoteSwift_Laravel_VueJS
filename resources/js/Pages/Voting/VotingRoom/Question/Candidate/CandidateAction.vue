<template>
    <VDropdown :autoHide="false" :triggers="['click', 'focus']">
        <div class="dropdown-action"><i class="bi bi-three-dots"></i></div>

        <template #popper>
            <div class="list-group">
                <ViewCandidate :candidate="candidate" :room="room" />
                <button
                    class="text-danger list-group-item"
                    @click="deleteCandidate"
                >
                    Delete
                </button>
                <button
                    v-close-popper
                    class="bg-dark text-white list-group-item"
                >
                    Close
                </button>
            </div>
        </template>
    </VDropdown>
</template>

<script setup>
import { useCandidateStore } from "@/Stores/candidates.js";
import ViewCandidate from "@/Pages/Voting/VotingRoom/Question/Candidate/ViewCandidate.vue";

const props = defineProps(["room", "candidate"]);

const CandidateStore = useCandidateStore();

const deleteCandidate = () => {
    CandidateStore.deleteCandidate(props.room.id, props.candidate.id);
};
</script>

<style scoped>
.dropdown-action {
    cursor: pointer;
}
</style>
