<template>
    <div v-if="roomSettings && invitedUsers && roomAttachments">
        <VotingSidebar
            :room="room"
            :roomAttachments="roomAttachments"
            :roomSettings="roomSettings"
        />

        <BaseModal
            id="RoomDescriptionModal"
            class="modal-dialog-scrollable"
            data-bs-backdrop="true"
            title="Room Description"
        >
            <MdPreview
                :editorId="'room_' + room.id"
                :modelValue="room.room_description"
            />
        </BaseModal>

        <div v-if="isRealtimeEnabled || isChatEnable">
            <VotingOnlineUser
                :invitedUsers="invitedUsers"
                :isUserOnline="isUserOnline"
                :onlineUsers="onlineUsers"
                :owner="owner"
                :room="room"
                :roomSettings="roomSettings"
                style="z-index: 999"
            />
            <VotingChat
                v-if="isChatEnable"
                :channelBroadcast="channelBroadcast"
                :room="room"
                :roomSettings="roomSettings"
                style="z-index: 999"
            />
        </div>

        <div>
            <h3>Time remaining:</h3>
            <VotingClock :date="room.end_time" />
        </div>

        <transition mode="out-in" name="fade">
            <KeepAlive>
                <component
                    :is="tabs[currentTab]"
                    v-if="roomSettings.invitation_only"
                    :channelBroadcast="channelBroadcast"
                    :isReadyToStart="isReadyToStart"
                    :questions="questions"
                    :room="room"
                    :roomSettings="roomSettings"
                    @switch-tab="currentTab = $event"
                    @start-voting="startVoting"
                ></component>
                <component
                    :is="tabs[currentTab]"
                    v-else
                    :channelBroadcast="channelBroadcast"
                    :questions="questions"
                    :room="room"
                    :roomSettings="roomSettings"
                    @switch-tab="currentTab = $event"
                ></component>
            </KeepAlive>
        </transition>
    </div>
</template>

<script setup>
import { computed, onMounted, onUnmounted, ref, watch } from "vue";
import Welcome from "@/Pages/Voting/Vote/Welcome.vue";
import StartVoting from "@/Pages/Voting/Vote/StartVoting.vue";
import VotingClock from "@/Components/VotingClock.vue";
import BaseModal from "@/Components/BaseModal.vue";
import { MdPreview } from "md-editor-v3";
import VotingChat from "@/Pages/Voting/Vote/VotingChat.vue";
import VotingSidebar from "@/Pages/Voting/Vote/VotingSidebar.vue";
import { usePage } from "@inertiajs/vue3";
import { useVoteStore } from "@/Stores/vote.js";
import { useToast } from "vue-toast-notification";
import { useVotingSettingStore } from "@/Stores/voting-settings.js";
import { useInvitationStore } from "@/Stores/invitations.js";
import VotingOnlineUser from "@/Pages/Voting/Vote/VotingOnlineUser.vue";
import { useAttachmentStore } from "@/Stores/attachments.js";

const authUser = computed(() => usePage().props.authUser.user);
const props = defineProps(["questions", "room", "owner"]);
const $toast = useToast();

const votingSettingStore = useVotingSettingStore();
const invitationStore = useInvitationStore();
const attachmentStore = useAttachmentStore();
const voteStore = useVoteStore();

const roomSettings = computed(() => votingSettingStore.settings[props.room.id]);
const invitedUsers = computed(() => invitationStore.invitations[props.room.id]);
const roomAttachments = computed(
    () => attachmentStore.attachments[props.room.id],
);

const isRealtimeEnabled = computed(
    () => roomSettings.value?.realtime_enabled === 1,
);
const isWaitForVoters = computed(
    () => roomSettings.value?.wait_for_voters === 1,
);

const isReadyToRender = computed(() => {
    return roomSettings && invitedUsers && roomAttachments && isRealtimeEnabled;
});

const tabs = {
    Welcome,
    StartVoting,
};

// const currentTab = ref(props.room.vote_started === 1 ? 'StartVoting' : 'Welcome');
const currentTab = ref("StartVoting");

const isChatEnable = computed(() => roomSettings.value?.chat_enabled === 1);
const isReadyToStart = ref(false);
const onlineUsers = ref([]);

watch(roomSettings, () => {
    isReadyToStart.value = roomSettings.value?.wait_for_voters === 0;
});

const isUserOnline = (user) =>
    onlineUsers.value.some((onlineUser) => onlineUser.id === user.id);

const channelBroadcast = {
    channelName: "voting.process." + props.room.id,
    eventName: "VotingProcess",
};

let echoListenerInitialized = false;

function startVoting() {
    voteStore.startVoting(props.room.id).then((response) => {
        if (response.status === 200) {
            $toast.success(response.data.message);
        } else {
            $toast.error(response.data.message);
        }
    });
}

const setupPresenceChannel = () => {
    // Function to handle updates to online users and readiness to start
    const updateOnlineUsersAndReadiness = () => {
        // invited users + room owner
        isReadyToStart.value =
            onlineUsers.value.length === invitedUsers.value.length + 1;
    };

    // Functions to handle presence channel events
    const handleHere = (users) => {
        onlineUsers.value = users;
        updateOnlineUsersAndReadiness();
    };

    const handleJoining = (user) => {
        onlineUsers.value.push(user);
        updateOnlineUsersAndReadiness();

        $toast.info(user.username + " has join the room");
    };

    const handleLeaving = (user) => {
        onlineUsers.value = onlineUsers.value.filter((u) => u.id !== user.id);
        updateOnlineUsersAndReadiness();

        $toast.info(user.username + " has left the room");
    };

    function handleVotingStartBroadcast(e) {
        if (e.broadcast_type === "voting_start") {
            if (e.room.vote_started) {
                currentTab.value = tabs.StartVoting;

                if (authUser.value.id !== e.room.user_id) {
                    $toast.success("Voting started by " + e.user.username);
                }
            }
        }
    }

    // Join presence channel and listen for events
    if (isRealtimeEnabled.value | isChatEnable.value) {
        Echo.join(channelBroadcast.channelName)
            .here(handleHere)
            .joining(handleJoining)
            .leaving(handleLeaving);

        if (roomSettings.value?.wait_for_voters === 1) {
            Echo.private(channelBroadcast.channelName).listen(
                channelBroadcast.eventName,
                handleVotingStartBroadcast,
            );
        }

        echoListenerInitialized = true;
    }
};

const leaveChannel = () => {
    Echo.leave(channelBroadcast.channelName);
    echoListenerInitialized = false;
};

let joinTimeId = null;
const joinRoom = () => {
    const formData = new FormData();
    formData.append("user_id", authUser.value.id);
    formData.append("join_time", new Date(Date.now()));

    voteStore
        .storeJoinTime(props.room.id, authUser.value.id, formData)
        .then((response) => {
            if (response.status === 200) {
                joinTimeId = response.data.id;
                $toast.success("You have joined successfully");
            }
        });
};

const leaveRoom = () => {
    const formData = new FormData();
    formData.append("joinTimeId", joinTimeId);
    formData.append("leave_time", new Date(Date.now()));

    voteStore
        .storeLeaveTime(props.room.id, authUser.value.id, formData)
        .then((response) => {
            if (response.status === 200) {
                $toast.success("You have left the room");
            }
        })
        .catch(() => $toast.error("Failed to leave the room"));
};

onMounted(() => {
    window.addEventListener("beforeunload", leaveRoom);

    joinRoom();

    votingSettingStore
        .fetchSettings(props.room.id)
        .then(() => setupPresenceChannel());

    if (
        invitationStore.invitations[props.room.id] === undefined ||
        invitationStore.invitations[props.room.id].length === 0
    ) {
        invitationStore.fetchInvitations(props.room.id).then(() => {
            invitedUsers.value.unshift(props.owner);
        });
    }

    attachmentStore.fetchAttachments(props.room.id);

    if (echoListenerInitialized === false) {
        setupPresenceChannel();
    }
});

onUnmounted(() => {
    if (echoListenerInitialized) {
        leaveChannel();
    }

    leaveRoom();
});
</script>
