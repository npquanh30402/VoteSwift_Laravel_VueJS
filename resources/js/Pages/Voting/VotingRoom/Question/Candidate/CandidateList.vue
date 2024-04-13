<template>
    <div>
        <button
            class="btn btn-secondary mb-3"
            @click="openModal(modals.addCandidateModal)"
        >
            Add Candidate
        </button>
        <AddCandidate
            :id="'addCandidateModal' + question.id"
            :question="question"
        />
        <div class="list-group">
            <div
                v-for="candidate in candidates"
                :key="candidate.id"
                class="list-group-item d-flex justify-content-between justify-content-center align-items-center"
            >
                <div class="d-flex align-items-center gap-3">
                    <img
                        v-if="candidate.candidate_image"
                        :src="candidate.candidate_image"
                        alt="Image"
                        class="img-fluid"
                        style="cursor: pointer"
                        width="128"
                        @click="showImage"
                    />
                    <span class="text-truncate" style="width: 50rem"
                        ><strong>{{ candidate.candidate_title }}</strong></span
                    >
                </div>
                <CandidateAction
                    :candidate="candidate"
                    @view-candidate="handleViewCandidate"
                />
            </div>
        </div>
        <teleport to="body">
            <LightBoxHelper :currentImageDisplay="currentImageDisplay" />
        </teleport>
        <ViewCandidate
            :id="'viewCandidateModal' + question.id"
            :candidate="modalCandidate"
        />
    </div>
</template>

<script setup>
import CandidateAction from "@/Pages/Voting/VotingRoom/Question/Candidate/CandidateAction.vue";
import { onMounted, reactive, ref } from "vue";
import * as bootstrap from "bootstrap";
import AddCandidate from "@/Pages/Voting/VotingRoom/Question/Candidate/AddCandidate.vue";
import ViewCandidate from "@/Pages/Voting/VotingRoom/Question/Candidate/ViewCandidate.vue";
import LightBoxHelper from "@/Components/Helpers/LightBoxHelper.vue";

const props = defineProps(["question", "candidates"]);

const modalCandidate = ref(null);
const currentImageDisplay = ref(null);
const modals = reactive({
    addCandidateModal: "addCandidateModal" + props.question.id,
    viewCandidateModal: "viewCandidateModal" + props.question.id,
});

onMounted(() => {
    modals.addCandidateModal = new bootstrap.Modal(
        document.getElementById(modals.addCandidateModal),
    );
    modals.viewCandidateModal = new bootstrap.Modal(
        document.getElementById(modals.viewCandidateModal),
    );
});

function openModal(modal, candidate = null) {
    modalCandidate.value = candidate;
    modal.show();
}

const handleViewCandidate = (candidate) => {
    modalCandidate.value = candidate;
    modals.viewCandidateModal.show();
};

const showImage = (e) => {
    currentImageDisplay.value = e;
};
</script>
