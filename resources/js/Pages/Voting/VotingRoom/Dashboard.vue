<template>
    <div class="row">
        <div class="col">
            <h1 class="display-6 text-center fw-bold">
                Room Dashboard
                <i
                    class="bi bi-arrow-clockwise icon"
                    style="cursor: pointer"
                    @click="router.reload()"
                ></i>
            </h1>
        </div>
    </div>
    <div class="my-3">
        <div class="row justify-content-center">
            <div class="col-md-2">
                <BallotSidebar
                    :tabData="tabData"
                    @switch-tab="handleSwitchTab"
                ></BallotSidebar>
            </div>
            <div class="col-md-9">
                <div class="card shadow-sm border-0 mb-3 overflow-auto">
                    <div class="card-header text-bg-dark text-center">
                        {{ tabData[currentTab].name }}
                    </div>
                    <div class="card-body">
                        <transition mode="out-in" name="fade">
                            <KeepAlive>
                                <component
                                    :is="tabData[currentTab].component"
                                    :nestedResults="nestedResults"
                                    :room="room"
                                    :voteCountsInTimeInterval="
                                        voteCountsInTimeInterval
                                    "
                                ></component>
                            </KeepAlive>
                        </transition>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref } from "vue";
import DescriptionPage from "@/Pages/Voting/VotingRoom/DescriptionPage.vue";
import QuestionsPage from "@/Pages/Voting/VotingRoom/Question/Index.vue";
import AttachmentPage from "@/Pages/Voting/VotingRoom/AttachmentPage.vue";
import VotingResult from "@/Pages/Voting/Vote/VotingResult.vue";
import BallotSidebar from "@/Pages/Voting/VotingRoom/BallotSidebar.vue";
import UpdateTitleDesc from "@/Pages/Voting/VotingRoom/Features/UpdateTitleDesc.vue";
import UpdateTime from "@/Pages/Voting/VotingRoom/Features/UpdateTime.vue";
import UpdatePassword from "@/Pages/Voting/VotingRoom/Features/UpdatePassword.vue";
import { router } from "@inertiajs/vue3";
import RoomPublish from "@/Pages/Voting/VotingRoom/RoomPublish.vue";
import DeleteRoom from "@/Pages/Voting/VotingRoom/DeleteRoom.vue";
import UpdateChat from "@/Pages/Voting/VotingRoom/Features/UpdateChat.vue";
import RoomRealtime from "@/Pages/Voting/VotingRoom/RoomRealtime.vue";
import RoomOverview from "@/Pages/Voting/VotingRoom/RoomOverview.vue";
import InvitationPage from "@/Pages/Voting/VotingRoom//Invitation/Index.vue";

const props = defineProps([
    "room",
    "nestedResults",
    "voteCountsInTimeInterval",
]);

const tabData = {
    RoomOverview: {
        component: RoomOverview,
        name: "Overview",
        componentName: "RoomOverview",
    },
    DescriptionPage: {
        component: DescriptionPage,
        name: "Description",
        componentName: "DescriptionPage",
    },
    UpdateTitleDesc: {
        component: UpdateTitleDesc,
        name: "Update Title & Description",
        componentName: "UpdateTitleDesc",
    },
    QuestionsPage: {
        component: QuestionsPage,
        name: "Questions & Candidates",
        componentName: "QuestionsPage",
    },
    UpdateTime: {
        component: UpdateTime,
        name: "Update Time",
        componentName: "UpdateTime",
    },
    AttachmentPage: {
        component: AttachmentPage,
        name: "Attachment",
        componentName: "AttachmentPage",
    },
    InvitationPage: {
        component: InvitationPage,
        name: "Invitations",
        componentName: "InvitationPage",
    },
    UpdatePassword: {
        component: UpdatePassword,
        name: "Update Password",
        componentName: "UpdatePassword",
    },
    UpdateChat: {
        component: UpdateChat,
        name: "Update Chat",
        componentName: "UpdateChat",
    },
    RoomRealtime: {
        component: RoomRealtime,
        name: "Realtime Voting",
        componentName: "RoomRealtime",
    },
    VotingResult: {
        component: VotingResult,
        name: "Voting Result",
        componentName: "VotingResult",
    },
    RoomPublish: {
        component: RoomPublish,
        name: "Publish",
        componentName: "RoomPublish",
    },
    DeleteRoom: {
        component: DeleteRoom,
        name: "Delete Room",
        componentName: "DeleteRoom",
    },
};

const currentTab = ref(tabData.RoomOverview.componentName);

const handleSwitchTab = (tabName) => {
    currentTab.value = tabName;
};
</script>
