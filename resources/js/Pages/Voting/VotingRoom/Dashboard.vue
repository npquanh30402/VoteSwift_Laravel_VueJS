<template>
    <div v-if="!isLoading">
        <div class="row">
            <div class="col">
                <h1 class="display-6 text-center fw-bold">Room Dashboard</h1>
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
                            <i :class="tabData[currentTab].icon" class="bi"></i>
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
    </div>
    <BaseLoading v-else />
</template>

<script setup>
import { computed, onMounted, ref } from "vue";
import DescriptionPage from "@/Pages/Voting/VotingRoom/DescriptionPage.vue";
import QuestionsPage from "@/Pages/Voting/VotingRoom/Question/Index.vue";
import AttachmentPage from "@/Pages/Voting/VotingRoom/AttachmentPage.vue";
import VotingResult from "@/Pages/Voting/VotingRoom/VotingResult/Index.vue";
import BallotSidebar from "@/Pages/Voting/VotingRoom/BallotSidebar.vue";
import UpdateTitleDesc from "@/Pages/Voting/VotingRoom/Features/UpdateTitleDesc.vue";
import UpdateTime from "@/Pages/Voting/VotingRoom/Features/UpdateTime.vue";
import UpdatePassword from "@/Pages/Voting/VotingRoom/Features/UpdatePassword.vue";
import RoomPublish from "@/Pages/Voting/VotingRoom/RoomPublish.vue";
import DeleteRoom from "@/Pages/Voting/VotingRoom/DeleteRoom.vue";
import UpdateChat from "@/Pages/Voting/VotingRoom/Features/UpdateChat.vue";
import RoomRealtime from "@/Pages/Voting/VotingRoom/RoomRealtime.vue";
import RoomOverview from "@/Pages/Voting/VotingRoom/RoomOverview.vue";
import InvitationPage from "@/Pages/Voting/VotingRoom//Invitation/Index.vue";
import RoomExtra from "@/Pages/Voting/VotingRoom/Features/RoomExtra.vue";
import { useVotingRoomStore } from "@/Stores/voting-room.js";
import BaseLoading from "@/Components/BaseLoading.vue";

const isLoading = ref(true);
const props = defineProps([
    "room",
    "nestedResults",
    "voteCountsInTimeInterval",
]);

const roomStore = useVotingRoomStore();

const room = computed(() =>
    roomStore.rooms.find((room) => room.id === props.room.id),
);

const tabData = {
    RoomOverview: {
        component: RoomOverview,
        name: "Overview",
        icon: "bi-house-door-fill",
        componentName: "RoomOverview",
    },
    DescriptionPage: {
        component: DescriptionPage,
        name: "Description",
        icon: "bi-newspaper",
        componentName: "DescriptionPage",
    },
    UpdateTitleDesc: {
        component: UpdateTitleDesc,
        name: "Update Title & Description",
        icon: "bi-pencil-fill",
        componentName: "UpdateTitleDesc",
    },
    QuestionsPage: {
        component: QuestionsPage,
        name: "Questions & Candidates",
        icon: "bi-patch-question-fill",
        componentName: "QuestionsPage",
    },
    UpdateTime: {
        component: UpdateTime,
        name: "Update Time",
        icon: "bi-alarm-fill",
        componentName: "UpdateTime",
    },
    AttachmentPage: {
        component: AttachmentPage,
        name: "Attachment",
        icon: "bi-file-earmark-fill",
        componentName: "AttachmentPage",
    },
    InvitationPage: {
        component: InvitationPage,
        name: "Invitations",
        icon: "bi-envelope-at-fill",
        componentName: "InvitationPage",
    },
    UpdatePassword: {
        component: UpdatePassword,
        name: "Password",
        icon: "bi-shield-lock-fill",
        componentName: "UpdatePassword",
    },
    UpdateChat: {
        component: UpdateChat,
        name: "Chat",
        icon: "bi-chat-dots-fill",
        componentName: "UpdateChat",
    },
    RoomRealtime: {
        component: RoomRealtime,
        name: "Realtime Voting",
        icon: "bi-hand-thumbs-up-fill",
        componentName: "RoomRealtime",
    },
    RoomExtra: {
        component: RoomExtra,
        name: "Extra",
        icon: "bi-star-fill",
        componentName: "RoomExtra",
    },
    VotingResult: {
        component: VotingResult,
        name: "Result",
        icon: "bi-clipboard-data-fill",
        componentName: "VotingResult",
    },
    RoomPublish: {
        component: RoomPublish,
        name: "Publish",
        icon: "bi-megaphone-fill",
        componentName: "RoomPublish",
    },
    DeleteRoom: {
        component: DeleteRoom,
        name: "Delete Room",
        icon: "bi-trash-fill",
        componentName: "DeleteRoom",
    },
};

const currentTab = ref(tabData.RoomOverview.componentName);

const handleSwitchTab = (tabName) => {
    currentTab.value = tabName;
};

onMounted(async () => {
    await roomStore.fetchRooms();
    // await roomStore.fetchARoom(props.room.id);

    isLoading.value = false;
});
</script>
