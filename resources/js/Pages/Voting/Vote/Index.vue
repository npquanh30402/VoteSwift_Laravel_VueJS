<template>
    <div v-if="isReady">
        <div v-if="room.has_ended === 0">
            <VotingSidebar
                :room="room"
                :roomAttachments="roomAttachments"
                :roomSettings="roomSettings"
            />

            <div v-if="isRealtimeEnabled || isWaitForVoters">
                <VotingOnlineUser
                    :invitedUsers="invitedUsers"
                    :isUserOnline="isUserOnline"
                    :onlineUsers="onlineUsers"
                    :owner="owner"
                    :room="room"
                    :roomSettings="roomSettings"
                    style="z-index: 999"
                />
            </div>

            <VotingChat
                v-if="isChatEnable"
                :channelBroadcast="channelBroadcast"
                :room="room"
                :roomSettings="roomSettings"
                style="z-index: 999"
            />

            <VotingNote :room="room" :roomSettings="roomSettings" />

            <div class="text-center mb-4">
                <h3>Time remaining:</h3>
                <VotingClock :date="room.end_time" />
            </div>

            <transition mode="out-in" name="fade">
                <component
                    :is="tabs[currentTab]"
                    v-if="roomSettings.invitation_only"
                    :channelBroadcast="channelBroadcast"
                    :isReadyToStart="isReadyToStart"
                    :room="room"
                    :roomSettings="roomSettings"
                    @switch-tab="currentTab = $event"
                    @start-voting="startVoting"
                ></component>
                <component
                    :is="tabs[currentTab]"
                    v-else
                    :channelBroadcast="channelBroadcast"
                    :room="room"
                    :roomSettings="roomSettings"
                    @switch-tab="currentTab = $event"
                ></component>
            </transition>
        </div>
        <div v-else>
            <h1 class="text-center">The voting has ended</h1>
        </div>
    </div>
    <BaseLoading v-else />
</template>

<script setup>
import { computed, onMounted, onUnmounted, ref, watch } from "vue";
import Welcome from "@/Pages/Voting/Vote/Welcome.vue";
import StartVoting from "@/Pages/Voting/Vote/StartVoting.vue";
import VotingChat from "@/Pages/Voting/Vote/VotingChat.vue";
import VotingSidebar from "@/Pages/Voting/Vote/VotingSidebar.vue";
import { usePage } from "@inertiajs/vue3";
import { useVoteStore } from "@/Stores/vote.js";
import { useToast } from "vue-toast-notification";
import { useVotingSettingStore } from "@/Stores/voting-settings.js";
import { useInvitationStore } from "@/Stores/invitations.js";
import VotingOnlineUser from "@/Pages/Voting/Vote/VotingOnlineUser.vue";
import { useAttachmentStore } from "@/Stores/attachments.js";
import VotingSubmit from "@/Pages/Voting/Vote/VotingSubmit.vue";
import VotingNote from "@/Pages/Voting/Vote/Note/VotingNote.vue";
import VotingClock from "@/Pages/Voting/Vote/VotingClock.vue";
import BaseLoading from "@/Components/BaseLoading.vue";
import { useVotingAttendanceStore } from "@/Stores/voting-attendance.js";

const props = defineProps(["room", "owner"]);
const toast = useToast();

const isReady = ref(false);

const votingSettingStore = useVotingSettingStore();
const invitationStore = useInvitationStore();
const attachmentStore = useAttachmentStore();
const voteStore = useVoteStore();
const attendanceStore = useVotingAttendanceStore();

const authUser = computed(() => usePage().props.authUser.user);
const roomSettings = computed(() => votingSettingStore.settings[props.room.id]);
const invitedUsers = computed(
    () => invitationStore.invitations[props.room.id] || [],
);
const roomAttachments = computed(
    () => attachmentStore.attachments[props.room.id],
);
const isRealtimeEnabled = computed(
    () => roomSettings.value?.realtime_enabled === 1,
);
const isWaitForVoters = computed(
    () => roomSettings.value?.wait_for_voters === 1,
);
const isChatEnable = computed(() => roomSettings.value?.chat_enabled === 1);
const isReadyToStart = ref(false);
const onlineUsers = ref([]);

const tabs = {
    Welcome,
    StartVoting,
    VotingSubmit,
};

// const currentTab = ref(props.room.vote_started === 1 ? 'StartVoting' : 'Welcome');

const currentTab = ref("Welcome");

function updateCurrentTab() {
    if (roomSettings.value.wait_for_voters === 1) {
        currentTab.value =
            props.room.vote_started === 1 ? "StartVoting" : "Welcome";
    } else {
        currentTab.value = "Welcome";
    }
}

watch(roomSettings, () => {
    isReadyToStart.value = roomSettings.value?.wait_for_voters === 0;

    updateCurrentTab();
});

const isUserOnline = (user) =>
    onlineUsers.value.some((onlineUser) => onlineUser.id === user.id);

const channelBroadcast = {
    channelName: "voting.process." + props.room.id,
    eventName: "VotingProcess",
};

function startVoting() {
    voteStore.startVoting(props.room.id).then((response) => {
        if (response.status === 200) {
            toast.success(response.data.message);
        } else {
            toast.error(response.data.message);
        }
    });
}

const setupRealTime = () => {
    const updateOnlineUsersAndReadiness = () => {
        // invited users + room owner
        isReadyToStart.value =
            onlineUsers.value.length === invitedUsers.value.length + 1;
    };

    const handleHere = (users) => {
        onlineUsers.value = users;
        updateOnlineUsersAndReadiness();
    };

    const handleJoining = (user) => {
        onlineUsers.value.push(user);
        updateOnlineUsersAndReadiness();

        toast.info(user.username + " has join the room");
    };

    const handleLeaving = (user) => {
        onlineUsers.value = onlineUsers.value.filter((u) => u.id !== user.id);
        updateOnlineUsersAndReadiness();

        toast.info(user.username + " has left the room");
    };

    function handleVotingStartBroadcast(e) {
        if (e.broadcast_type === "voting_start") {
            if (e.room.vote_started) {
                currentTab.value = "StartVoting";

                if (authUser.value.id !== e.room.user_id) {
                    toast.success("Voting started by " + e.user.username);
                }
            }
        }
    }

    if (isRealtimeEnabled.value | isChatEnable.value | isWaitForVoters.value) {
        voteStore.setupEchoJoinListener(
            handleHere,
            handleJoining,
            handleLeaving,
        );

        if (roomSettings.value?.wait_for_voters === 1)
            voteStore.setupEchoPrivateListener(handleVotingStartBroadcast);
    }
};

let joinTimeId = null;
const joinRoom = async () => {
    const formData = new FormData();
    formData.append("user_id", authUser.value.id);
    formData.append("join_time", new Date(Date.now()));

    const response = await attendanceStore.storeJoinTime(
        props.room.id,
        authUser.value.id,
        formData,
    );

    joinTimeId = response.id;
};

const leaveRoom = async () => {
    const formData = new FormData();
    formData.append("joinTimeId", joinTimeId);
    formData.append("leave_time", new Date(Date.now()));

    await attendanceStore.storeLeaveTime(
        props.room.id,
        authUser.value.id,
        formData,
    );

    window.removeEventListener("beforeunload", leaveRoom);
};

onMounted(async () => {
    voteStore.setupChannel(props.room.id);
    window.addEventListener("beforeunload", leaveRoom);

    joinRoom();

    await votingSettingStore
        .fetchSettings(props.room.id)
        .then(() => setupRealTime());

    await invitationStore.fetchInvitations(props.room.id);

    await attachmentStore.fetchAttachments(props.room.id);

    setupRealTime();

    isReady.value = true;
});

onUnmounted(() => {
    voteStore.leaveEchoListeners();
    leaveRoom();
});
</script>
