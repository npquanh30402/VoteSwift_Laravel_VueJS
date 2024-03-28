<template>
    <div class="row">
        <div class="col">
            <h1 class="display-6 text-center fw-bold">Room Dashboard
                <i class="bi bi-arrow-clockwise icon" @click="router.reload()" style="cursor: pointer"></i>
            </h1>
        </div>
    </div>
    <div class="my-3">
        <div class="row justify-content-center">
            <div class="col-md-2">
                <BallotSidebar :room="room" @switch-tab="handleSwitchTab"></BallotSidebar>
            </div>
            <div class="col-md-9">
                <transition name="fade" mode="out-in">
                    <component :is="tabs[currentTab]" :room="room" :room_settings="room_settings"
                               :questions="room_questions"
                               :attachments="room_attachments" :nestedResults="nestedResults"
                               :voteCountsInTimeInterval="voteCountsInTimeInterval"></component>
                </transition>
            </div>
        </div>
    </div>
</template>

<script setup>
import {ref} from "vue";
import DescriptionPage from "@/Pages/Voting/VotingRoom/DescriptionPage.vue";
import QuestionsPage from "@/Pages/Voting/Question/Index.vue";
import AttachmentPage from "@/Pages/Voting/VotingRoom/AttachmentPage.vue";
import RoomDetails from "@/Pages/Voting/VotingRoom/RoomDetails.vue";
import VotingResult from "@/Pages/Voting/Vote/VotingResult.vue";
import BallotSidebar from "@/Pages/Voting/VotingRoom/BallotSidebar.vue";
import UpdateTitleDesc from "@/Pages/Voting/VotingRoom/Features/UpdateTitleDesc.vue";
import UpdateTime from "@/Pages/Voting/VotingRoom/Features/UpdateTime.vue";
import UpdatePassword from "@/Pages/Voting/VotingRoom/Features/UpdatePassword.vue";
import {router} from "@inertiajs/vue3";

const props = defineProps(['room', 'room_settings', 'room_questions', 'room_attachments', 'nestedResults', 'voteCountsInTimeInterval'])

const currentTab = ref('RoomDetails')

const tabs = {
    RoomDetails,
    DescriptionPage,
    QuestionsPage,
    UpdateTitleDesc,
    UpdateTime,
    AttachmentPage,
    VotingResult,
    UpdatePassword,
}

const handleSwitchTab = (tabName) => {
    currentTab.value = tabName;
};

</script>
