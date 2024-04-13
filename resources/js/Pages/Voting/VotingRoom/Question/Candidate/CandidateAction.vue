<template>
    <VMenu :triggers="['click']" placement="left">
        <div class="dropdown-action"><i class="bi bi-three-dots"></i></div>

        <template #popper>
            <div class="list-group">
                <button
                    class="list-group-item text-success"
                    @click="openModal(candidate)"
                >
                    Details
                </button>
                <button
                    class="text-danger list-group-item"
                    @click="deleteCandidate"
                >
                    Delete
                </button>
            </div>
        </template>
    </VMenu>
</template>

<script setup>
import { useCandidateStore } from "@/Stores/candidates.js";
import { useToast } from "vue-toast-notification";

const props = defineProps(["candidate"]);
const toast = useToast();
const emit = defineEmits(["view-candidate"]);

const CandidateStore = useCandidateStore();
const deleteCandidate = () => {
    CandidateStore.deleteCandidate(props.candidate.id);

    toast.success("Candidate deleted");
};

function openModal(candidate = null) {
    emit("view-candidate", candidate);
}
</script>

<style scoped>
.dropdown-action {
    cursor: pointer;
}
</style>
