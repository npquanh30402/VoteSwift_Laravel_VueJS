<template>
    <div class="row">
        <div class="col">
            <h1 class="display-6 text-center fw-bold">Dashboard: {{ room.room_name }}</h1>
        </div>
    </div>
    <div class="my-3">
        <div class="row justify-content-center">
            <div class="col-md-2">
                <BallotSidebar :room="room" @switch-tab="handleSwitchTab"></BallotSidebar>
            </div>
            <div class="col-md-9">
                <transition name="fade" mode="out-in">
                    <component :is="tabs[currentTab]" :room="room" :questions="room_questions"
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

const prop = defineProps(['room', 'room_settings', 'room_questions', 'room_attachments', 'nestedResults', 'voteCountsInTimeInterval'])

const currentTab = ref('RoomDetails')

const tabs = {
    RoomDetails,
    DescriptionPage,
    QuestionsPage,
    AttachmentPage,
    VotingResult
}

const handleSwitchTab = (tabName) => {
    currentTab.value = tabName;
};

</script>
