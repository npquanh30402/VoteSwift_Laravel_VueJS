<template>
    <div v-if="roomSettings && invitedUsers && roomAttachments">
        <VotingSidebar :room="room" :roomSettings="roomSettings"
                       :roomAttachments="roomAttachments"/>

        <BaseModal id="RoomDescriptionModal" title="Room Description" class="modal-dialog-scrollable"
                   data-bs-backdrop="true">
            <MdPreview :editorId="'room_' + room.id" :modelValue="room.room_description"/>
        </BaseModal>

        <VotingOnlineUser :room="room" :invitedUsers="invitedUsers" :owner="owner"
                          :onlineUsers="onlineUsers" :isUserOnline="isUserOnline" style="z-index: 999"
                          v-if="isChatEnable"/>

        <VotingChat :room="room" :roomSettings="roomSettings" style="z-index: 999" v-if="isChatEnable"/>
        <h3>Time remaining: </h3>
        <VotingClock :date="room.end_time"/>

        <transition name="fade" mode="out-in">
            <component :is="tabs[currentTab]" :room="room" :roomSettings="roomSettings" :questions="questions"
                       :isReadyToStart="isReadyToStart"
                       @switch-tab="currentTab = $event" @start-voting="startVoting"
                       v-if="roomSettings.invitation_only"></component>

            <component :is="tabs[currentTab]" :room="room" :roomSettings="roomSettings" :questions="questions"
                       @switch-tab="currentTab = $event" v-else></component>
        </transition>
    </div>
</template>

<script setup>
import {computed, onMounted, onUnmounted, ref, watch} from "vue";
import Welcome from "@/Pages/Voting/Vote/Welcome.vue";
import StartVoting from "@/Pages/Voting/Vote/StartVoting.vue";
import VotingClock from "@/Components/VotingClock.vue";
import BaseModal from "@/Components/BaseModal.vue";
import {MdPreview} from "md-editor-v3";
import VotingChat from "@/Pages/Voting/Vote/VotingChat.vue";
import VotingSidebar from "@/Pages/Voting/Vote/VotingSidebar.vue";
import {usePage} from "@inertiajs/vue3";
import {useVoteStore} from "@/Stores/vote.js";
import {useToast} from "vue-toast-notification";
import {useVotingSettingStore} from "@/Stores/voting-settings.js";
import {useInvitationStore} from "@/Stores/invitations.js";
import VotingOnlineUser from "@/Pages/Voting/Vote/VotingOnlineUser.vue";
import {useAttachmentStore} from "@/Stores/attachments.js";
import MusicPlayer from "@/Components/MusicPlayer.vue";

const authUser = computed(() => usePage().props.authUser.user);
const props = defineProps(['questions', 'room', 'owner']);
const $toast = useToast();

const votingSettingStore = useVotingSettingStore()
const invitationStore = useInvitationStore()
const attachmentStore = useAttachmentStore()
const voteStore = useVoteStore();

const roomSettings = computed(() => votingSettingStore.settings[props.room.id])
const invitedUsers = computed(() => invitationStore.invitations[props.room.id])
const roomAttachments = computed(() => attachmentStore.attachments[props.room.id]);
// const currentTab = ref(props.room.vote_started === 1 ? 'StartVoting' : 'Welcome');
const currentTab = ref('Welcome');

const isChatEnable = ref(false);
const isReadyToStart = ref(false);
const onlineUsers = ref([]);

watch(roomSettings, () => {
    isChatEnable.value = roomSettings.value?.chat_enabled === 1
    isReadyToStart.value = roomSettings.value?.wait_for_voters === 0
})

const tabs = {
    Welcome,
    StartVoting,
};

const isUserOnline = (user) => onlineUsers.value.some(onlineUser => onlineUser.id === user.id);

const channelName = 'voting.' + props.room.id
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
        isReadyToStart.value = onlineUsers.value.length === invitedUsers.value.length + 1;
    };

    // Functions to handle presence channel events
    const handleHere = (users) => {
        onlineUsers.value = users;
        updateOnlineUsersAndReadiness();
    };

    const handleJoining = (user) => {
        onlineUsers.value.push(user);
        updateOnlineUsersAndReadiness();
    };

    const handleLeaving = (user) => {
        onlineUsers.value = onlineUsers.value.filter((u) => u.id !== user.id);
        updateOnlineUsersAndReadiness();
    };

    // Join presence channel and listen for events
    if (roomSettings.value?.wait_for_voters === 1) {
        Echo.join('voting.' + props.room.id)
            .here(handleHere)
            .joining(handleJoining)
            .leaving(handleLeaving);

        Echo.private('voting.' + props.room.id).listen('VotingProcess', (e) => {
            if (e.room.vote_started) {
                currentTab.value = tabs.StartVoting;

                if (authUser.value.id !== e.room.user_id) {
                    $toast.success('Voting started by ' + e.user.username);
                }
            }
        });

        echoListenerInitialized = true;
    }
};

const leaveChannel = () => {
    Echo.leave(channelName)
    echoListenerInitialized = false
}

const joinRoom = () => {
    const formData = new FormData();
    formData.append('user_id', authUser.value.id);
    formData.append('join_time', new Date(Date.now()));

    voteStore.storeJoinTime(props.room.id, authUser.value.id, formData).then((response) => {
        if (response.status === 200) {
            $toast.success('You have joined successfully');
        }
    })
}

onMounted(() => {
    joinRoom()

    votingSettingStore.fetchSettings(props.room.id).then(() => {
        setupPresenceChannel()
    })

    invitationStore.fetchInvitations(props.room.id).then(() => {
        invitedUsers.value.unshift(props.owner)
    })

    attachmentStore.fetchAttachments(props.room.id)
})

onUnmounted(() => {
    if (echoListenerInitialized) {
        leaveChannel()
    }
})
</script>
