<template>
    <div class="my-3">
        <div class="row justify-content-center mb-3">
            <div class="col-md-3">
                <a @click="back" class="btn btn-secondary small">Back</a>
            </div>
            <div class="col-md-7 gap-3 align-items-center"></div>
        </div>
        <div class="row justify-content-center">
            <div class="col-md-2">
                <!--                <BallotSidebar :room="room"></BallotSidebar>-->
            </div>
            <div class="col-md-9">
                <div class="card shadow-sm border-0 mb-3 overflow-auto">
                    <div class="card-header text-bg-dark text-center">Add Candidate
                    </div>
                    <AddCandidate id="addCandidateModal" :question="question"></AddCandidate>
                    <div class="card-body">
                        <p>Total Candidates: {{ candidates.length }}</p>
                        <button class="btn btn-primary" @click="openModal(modals.addCandidateModal)">Add</button>
                        <div class="mt-3 d-flex flex-column gap-3">
                            <div v-for="(candidate, index) in paginatedCandidates" :key="candidate.id" class="card">
                                <div class="card-header fw-bold d-flex align-items-center gap-2">
                                    #{{ index + 1 }}: {{ candidate.candidate_title }}
                                </div>
                                <div class="card-body d-flex flex-column">
                                    <p class="card-text text-truncate">{{ candidate.candidate_description }}</p>
                                    <div class="d-flex justify-content-end">
                                        <div class="hstack gap-3">
                                            <button class="btn btn-secondary"
                                                    @click="openModal(modals.candidateDetailsModal, candidate)">Details
                                            </button>
                                            <button class="btn btn-danger"
                                                    @click="openModal(modals.deleteCandidateModal, candidate)">Delete
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="d-flex justify-content-end">
                        <vue-awesome-paginate
                            :total-items="candidates.length"
                            :items-per-page="5"
                            :max-pages-shown="5"
                            v-model="currentPage"
                            :on-click="onClickHandler"
                        />
                    </div>
                </div>
            </div>
        </div>
    </div>
    <CandidateDetails :candidate="modalCandidate"
                      id="candidateDetailsModal"></CandidateDetails>
    <DeleteCandidate :candidate="modalCandidate" id="deleteCandidateModal"></DeleteCandidate>
</template>

<script setup>
import {router, usePage} from "@inertiajs/vue3";
import BallotSidebar from "@/Pages/Voting/BallotSidebar.vue";
import AddQuestion from "@/Pages/Voting/Question/AddQuestion.vue";
import {computed, onMounted, reactive, ref} from "vue";
import * as bootstrap from 'bootstrap'
import QuestionDetails from "@/Pages/Voting/Question/QuestionDetails.vue";
import {VueAwesomePaginate} from "vue-awesome-paginate";
import DeleteQuestion from "@/Pages/Voting/Question/DeleteQuestion.vue";
import {route} from "ziggy-js";
import {Link} from "@inertiajs/vue3";
import AddCandidate from "@/Pages/Voting/Candidate/AddCandidate.vue";
import DeleteCandidate from "@/Pages/Voting/Candidate/DeleteCandidate.vue";
import CandidateDetails from "@/Pages/Voting/Candidate/CandidateDetails.vue";

const modals = reactive({
    addCandidateModal: 'addCandidateModal',
    candidateDetailsModal: 'candidateDetailsModal',
    deleteCandidateModal: 'deleteCandidateModal'
})

let modalCandidate = ref(null);

onMounted(() => {
    modals.addCandidateModal = new bootstrap.Modal(document.getElementById(modals.addCandidateModal));
    modals.candidateDetailsModal = new bootstrap.Modal(document.getElementById(modals.candidateDetailsModal));
    modals.deleteCandidateModal = new bootstrap.Modal(document.getElementById(modals.deleteCandidateModal));
})

const props = defineProps(['candidates', 'question'])

function openModal(modal, candidate = null) {
    modalCandidate.value = candidate
    modal.show()
}

let urlPrev = usePage().props.urlPrev;
const back = () => {
    if (urlPrev !== 'empty') {
        router.visit(urlPrev)
    }
}

const onClickHandler = (page) => {
    currentPage.value = page
};

const currentPage = ref(1);
const paginatedCandidates = computed(() => {
    const startIndex = (currentPage.value - 1) * 5;
    const endIndex = startIndex + 5;
    return props.candidates.slice(startIndex, endIndex);
});
</script>
