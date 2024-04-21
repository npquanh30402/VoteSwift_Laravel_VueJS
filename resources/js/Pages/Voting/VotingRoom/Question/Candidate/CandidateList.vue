<template>
    <div>
        <AddCandidate v-if="!isPublish" :question="question" :room="room" />
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

const props = defineProps(["room", "question", "candidates"]);
const isPublish = computed(() => props.room.is_published === 1);
const currentImageDisplay = ref(null);

const showImage = (e) => {
    currentImageDisplay.value = e;
};
</script>
