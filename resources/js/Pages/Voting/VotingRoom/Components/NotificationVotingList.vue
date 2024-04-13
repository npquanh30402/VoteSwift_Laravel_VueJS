<template>
    <div class="overflow-auto" style="height: 70vh">
        <TransitionGroup name="list">
            <div v-for="userChoice in userChoices" :key="userChoice.id">
                <div
                    :class="{
                        'alert-success':
                            userChoice.broadcast_type === 'voting_choices',
                        'alert-secondary text-bg-dark':
                            userChoice.broadcast_type === 'voting_join',
                        'alert-danger text-dark':
                            userChoice.broadcast_type === 'voting_leave',
                    }"
                    class="alert vstack"
                >
                    <div class="hstack">
                        <VMenu>
                            <img
                                :src="userChoice.user.avatar"
                                alt=""
                                class="img-fluid rounded-circle border-black border"
                                style="height: 3rem"
                            />

                            <template #popper>
                                <UserProfileMini :user="userChoice.user" />
                            </template>
                        </VMenu>
                        <div class="ms-3">
                            <strong>{{ userChoice.user.username }}</strong>
                            <template
                                v-if="
                                    userChoice.broadcast_type ===
                                    'voting_choices'
                                "
                            >
                                : Has Voted for Candidate
                                <div class="ms-3">
                                    <i class="bi bi-arrow-right me-1"></i>
                                    <span class="fw-bold">Question</span>:
                                    {{ userChoice.question.question_title }}
                                </div>
                                <div class="ms-5">
                                    <i class="bi bi-dot"></i>
                                    <span class="fw-bold">Candidate</span>:
                                    {{ userChoice.candidate.candidate_title }}
                                </div>
                            </template>
                            <template
                                v-else-if="
                                    userChoice.broadcast_type === 'voting_join'
                                "
                            >
                                : Has Joined the Room
                            </template>
                            <template v-else> : Has Left the Room</template>
                        </div>
                    </div>
                    <div
                        :class="{
                            'text-muted':
                                userChoice.broadcast_type === 'voting_choices',
                            'text-white':
                                userChoice.broadcast_type === 'voting_join',
                            'text-dark':
                                userChoice.broadcast_type === 'voting_leave',
                        }"
                        class="ms-auto"
                    >
                        <small>{{
                            formattedDate(userChoice.created_at)
                        }}</small>
                    </div>
                </div>
            </div>
        </TransitionGroup>
    </div>
</template>

<script setup>
import { useHelper } from "@/Services/helper.js";
import { computed } from "vue";
import UserProfileMini from "@/Pages/Voting/VotingRoom/Components/UserProfileMini.vue";

const props = defineProps(["userChoices"]);
const helper = useHelper();

const userChoices = computed(() => props.userChoices);
const formattedDate = computed(() => {
    return (date) => {
        return helper.formattedDate(date);
    };
});
</script>
