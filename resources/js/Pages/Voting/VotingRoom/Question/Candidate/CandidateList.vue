<template>
    <div>
        <div class="hstack justify-content-between mb-3">
            <AddCandidate v-if="!isPublish" :question="question" :room="room" />
            <div v-if="!isPublish" class="col-md-3">
                <div class="hstack justify-content-between">
                    <label class="form-label" for="csv_file"
                        >Import Candidates:</label
                    >
                    <VTooltip :skidding="-48">
                        <i class="bi bi-info-circle"></i>

                        <template #popper>
                            <div>
                                <p>The file must be in CSV format.</p>
                                <code>
                                    candidate_title,candidate_description
                                </code>
                                <br />
                                <code>Blue,The color blue is calming</code>
                                <br />
                                <code>...</code>
                            </div>
                        </template>
                    </VTooltip>
                </div>
                <input
                    id="csv_file"
                    class="form-control form-control-sm"
                    type="file"
                    @change="importCandidates"
                />
            </div>
        </div>

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
                <CandidateAction :candidate="candidate" :room="room" />
            </div>
        </div>
        <teleport to="body">
            <LightBoxHelper :currentImageDisplay="currentImageDisplay" />
        </teleport>
    </div>
</template>

<script setup>
import CandidateAction from "@/Pages/Voting/VotingRoom/Question/Candidate/CandidateAction.vue";
import { computed, ref } from "vue";
import AddCandidate from "@/Pages/Voting/VotingRoom/Question/Candidate/AddCandidate.vue";
import LightBoxHelper from "@/Components/Helpers/LightBoxHelper.vue";
import { useCandidateStore } from "@/Stores/candidates.js";

const props = defineProps(["room", "question", "candidates"]);
const candidateStore = useCandidateStore();
const isPublish = computed(() => props.room.is_published === 1);
const currentImageDisplay = ref(null);

const showImage = (e) => {
    currentImageDisplay.value = e;
};

const importCandidates = (event) => {
    const file = event.target.files[0];

    if (!file) {
        return;
    }

    const formData = new FormData();
    formData.append("csv_file", file);

    candidateStore.importCandidates(props.room.id, props.question.id, formData);
};
</script>
