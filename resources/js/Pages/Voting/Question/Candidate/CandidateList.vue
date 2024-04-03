<template>
    <div>
        <button class="btn btn-secondary mb-3" @click="openModal(modals.addCandidateModal)">Add Candidate</button>
        <AddCandidate :id="'addCandidateModal' + question.id" :question="question"></AddCandidate>
        <div class="list-group">
            <div class="list-group-item d-flex justify-content-between justify-content-center align-items-center"
                 v-for="candidate in candidates" :key="candidate.id">
                <div class="d-flex align-items-center gap-3">
                    <img class="img-fluid" :src="candidate.candidate_image" width="128"
                         v-if="candidate.candidate_image" style="cursor: pointer"
                         alt="Image" @click="showSingle">
                    <span><strong>{{ candidate.candidate_title }}</strong></span>
                </div>
                <CandidateAction :candidate="candidate"/>
            </div>
        </div>
        <teleport to="body">
            <vue-easy-lightbox
                :visible="visibleRef"
                :imgs="imgsRef"
                :index="indexRef"
                @hide="onHide"
            ></vue-easy-lightbox>
        </teleport>
    </div>
</template>

<script setup>
import CandidateAction from "@/Pages/Voting/Question/Candidate/CandidateAction.vue";
import {onMounted, reactive, ref} from "vue";
import * as bootstrap from "bootstrap";
import AddCandidate from "@/Pages/Voting/Question/Candidate/AddCandidate.vue";
import VueEasyLightbox from "vue-easy-lightbox";

const props = defineProps(['question', 'candidates'])

const modals = reactive({
    addCandidateModal: 'addCandidateModal' + props.question.id,
})

let modalCandidate = ref(null);

onMounted(() => {
    modals.addCandidateModal = new bootstrap.Modal(document.getElementById(modals.addCandidateModal));
})

function openModal(modal, candidate = null) {
    modalCandidate.value = candidate
    modal.show()
}

const imgSrc = ref(null);
const visibleRef = ref(false)
const indexRef = ref(0)
const imgsRef = ref([])

const onShow = () => {
    visibleRef.value = true
}

const showSingle = (e) => {
    imgsRef.value = e.target.src
    onShow()
}

const onHide = () => {
    visibleRef.value = false
}
</script>
