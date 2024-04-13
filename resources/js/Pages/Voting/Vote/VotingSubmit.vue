<template>
    <div class="vstack justify-content-center align-items-center">
        <transition mode="out-in" name="fade">
            <div
                v-if="!feedbackSubmitted"
                class="container vstack justify-content-center align-items-center"
            >
                <h2 class="display-2">Thank you for voting!</h2>
                <div style="min-width: 100%">
                    <h3 class="display-6">Leave a Feedback:</h3>
                    <div
                        class="form-floating"
                        style="min-height: 100%; min-width: 80%"
                    >
                        <textarea
                            v-model="feedback"
                            class="form-control"
                            placeholder="Leave a feedback"
                            style="height: 15rem"
                        ></textarea>
                        <label for="floatingTextarea">Feedback</label>
                    </div>
                    <button
                        class="btn btn-primary float-end my-3"
                        @click="submitFeedback"
                    >
                        Submit
                    </button>
                </div>
            </div>
            <div v-else>
                <h2 class="display-2">Thank you for your feedback!</h2>
            </div>
        </transition>
    </div>
</template>
<script setup>
import { computed, ref } from "vue";
import { useHelper } from "@/Services/helper.js";
import { useToast } from "vue-toast-notification";
import { useVotingFeedbackStore } from "@/Stores/voting-feedback.js";
import { usePage } from "@inertiajs/vue3";

const authUser = computed(() => usePage().props.authUser.user);
const props = defineProps(["room"]);
const toast = useToast();
const helper = useHelper();
const feedbackStore = useVotingFeedbackStore();
const feedback = ref(null);
const feedbackSubmitted = ref(false);

const submitFeedback = async () => {
    const formData = new FormData();
    if (feedback.value) {
        formData.append("feedback", helper.sanitizeAndTrim(feedback.value));
    }

    try {
        feedbackStore
            .storeFeedback(authUser.value.id, props.room.id, formData)
            .then((response) => {
                if (response.status === 200) {
                    toast.success("Feedback submitted successfully");
                    feedbackSubmitted.value = true;
                }
            });
    } catch (error) {
        toast.error("Error while submitting feedback");
    }
};
</script>
