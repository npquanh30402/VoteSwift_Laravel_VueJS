<template>
    <div>
        <button class="btn btn-secondary mb-3" @click="openModal(modals.addCandidateModal)">Add Candidate</button>
        <AddCandidate :id="'addCandidateModal' + question.id" :question="question"/>
        <div class="list-group">
            <div class="list-group-item d-flex justify-content-between justify-content-center align-items-center"
                 v-for="candidate in candidates" :key="candidate.id">
                <div class="d-flex align-items-center gap-3">
                    <img class="img-fluid" :src="candidate.candidate_image" width="128"
                         v-if="candidate.candidate_image" style="cursor: pointer"
                         alt="Image" @click="showImage">
                    <span class="text-truncate" style="width: 50rem"><strong>{{
                            candidate.candidate_title
                        }}</strong></span>
                </div>
                <CandidateAction :candidate="candidate" @view-candidate="handleViewCandidate"/>
            </div>
        </div>
        <teleport to="body">
            <LightBoxHelper :currentImageDisplay="currentImageDisplay"/>
        </teleport>
        <ViewCandidate :id="'viewCandidateModal' + question.id" :candidate="modalCandidate"/>
    </div>
</template>

<script setup>
import CandidateAction from "@/Pages/Voting/Question/Candidate/CandidateAction.vue";
import {onMounted, reactive, ref} from "vue";
import * as bootstrap from "bootstrap";
import AddCandidate from "@/Pages/Voting/Question/Candidate/AddCandidate.vue";
import ViewCandidate from "@/Pages/Voting/Question/Candidate/ViewCandidate.vue";
import LightBoxHelper from "@/Components/Helpers/LightBoxHelper.vue";

const props = defineProps(['question', 'candidates'])

const modalCandidate = ref(null);
const currentImageDisplay = ref(null)
const modals = reactive({
    addCandidateModal: 'addCandidateModal' + props.question.id,
    viewCandidateModal: 'viewCandidateModal' + props.question.id,
})

onMounted(() => {
    modals.addCandidateModal = new bootstrap.Modal(document.getElementById(modals.addCandidateModal));
    modals.viewCandidateModal = new bootstrap.Modal(document.getElementById(modals.viewCandidateModal));
})

function openModal(modal, candidate = null) {
    modalCandidate.value = candidate
    modal.show()
}

const handleViewCandidate = (candidate) => {
    modalCandidate.value = candidate;
    modals.viewCandidateModal.show();
}

const showImage = (e) => {
    currentImageDisplay.value = e;
}
</script>
