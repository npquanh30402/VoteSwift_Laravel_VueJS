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
                <div class="card shadow-sm border-0 mb-3 overflow-auto">
                    <div class="card-header text-bg-dark text-center">{{ tabData[currentTab].name }}</div>
                    <div class="card-body">
                        <transition name="fade" mode="out-in">
                            <component :is="tabData[currentTab].component" :room="room" :nestedResults="nestedResults"
                                       :voteCountsInTimeInterval="voteCountsInTimeInterval"></component>
                        </transition>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import {ref} from "vue";
import DescriptionPage from "@/Pages/Voting/VotingRoom/DescriptionPage.vue";
import QuestionsPage from "@/Pages/Voting/Question/Index.vue";
import AttachmentPage from "@/Pages/Voting/VotingRoom/AttachmentPage.vue";
import VotingResult from "@/Pages/Voting/Vote/VotingResult.vue";
import BallotSidebar from "@/Pages/Voting/VotingRoom/BallotSidebar.vue";
import UpdateTitleDesc from "@/Pages/Voting/VotingRoom/Features/UpdateTitleDesc.vue";
import UpdateTime from "@/Pages/Voting/VotingRoom/Features/UpdateTime.vue";
import UpdatePassword from "@/Pages/Voting/VotingRoom/Features/UpdatePassword.vue";
import {router} from "@inertiajs/vue3";
import RoomPublish from "@/Pages/Voting/VotingRoom/RoomPublish.vue";
import DeleteRoom from "@/Pages/Voting/VotingRoom/DeleteRoom.vue";
import UpdateChat from "@/Pages/Voting/VotingRoom/Features/UpdateChat.vue";
import RoomRealtime from "@/Pages/Voting/VotingRoom/RoomRealtime.vue";
import RoomOverview from "@/Pages/Voting/VotingRoom/RoomOverview.vue";
import InvitationPage from "@/Pages/Voting/VotingRoom/InvitationPage.vue";

const props = defineProps(['room', 'nestedResults', 'voteCountsInTimeInterval'])

const currentTab = ref('RoomOverview');

const tabData = {
    RoomOverview: {component: RoomOverview, name: 'Overview'},
    DescriptionPage: {component: DescriptionPage, name: 'Description'},
    QuestionsPage: {component: QuestionsPage, name: 'Questions & Candidates'},
    UpdateTitleDesc: {component: UpdateTitleDesc, name: 'Update Title & Description'},
    UpdateTime: {component: UpdateTime, name: 'Update Time'},
    AttachmentPage: {component: AttachmentPage, name: 'Attachment'},
    VotingResult: {component: VotingResult, name: 'Voting Result'},
    UpdatePassword: {component: UpdatePassword, name: 'Update Password'},
    UpdateChat: {component: UpdateChat, name: 'Update Chat'},
    RoomRealtime: {component: RoomRealtime, name: 'Realtime'},
    InvitationPage: {component: InvitationPage, name: 'Invitations'},
    RoomPublish: {component: RoomPublish, name: 'Publish'},
    DeleteRoom: {component: DeleteRoom, name: 'Delete Room'}
};

const handleSwitchTab = (tabName) => {
    currentTab.value = tabName;
};
</script>
