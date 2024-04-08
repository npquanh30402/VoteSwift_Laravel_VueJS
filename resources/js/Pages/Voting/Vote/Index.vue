<template>
    <VotingSidebar :room="room" :roomSettings="roomSettings" :invitedUsers="invitedUsers"
                   :roomAttachments="roomAttachments" :onlineUsers="onlineUsers" :isUserOnline="isUserOnline"/>

    <BaseModal id="RoomDescriptionModal" title="Room Description" class="modal-dialog-scrollable"
               data-bs-backdrop="true">
        <MdPreview :editorId="'room_' + room.id" :modelValue="room.room_description"/>
    </BaseModal>

    <VotingChat :room="room" :roomSettings="roomSettings" style="z-index: 999" v-if="isChatEnable"/>
    <!--    <h3>Time remaining: </h3>-->
    <!--    <VotingClock :date="room.end_time"/>-->

    <transition name="fade" mode="out-in">
        <component :is="tabs[currentTab]" :room="room" :roomSettings="roomSettings" :questions="questions"
                   :isReadyToStart="isReadyToStart"
                   @switch-tab="currentTab = $event" @start-voting="startVoting"
                   v-if="roomSettings.invitation_only"></component>

        <component :is="tabs[currentTab]" :room="room" :roomSettings="roomSettings" :questions="questions"
                   @switch-tab="currentTab = $event" v-else></component>
    </transition>
</template>

<script setup>
import {ref} from "vue";
import Welcome from "@/Pages/Voting/Vote/Welcome.vue";
import StartVoting from "@/Pages/Voting/Vote/StartVoting.vue";
import {route} from "ziggy-js";
import VotingClock from "@/Components/VotingClock.vue";
import BaseModal from "@/Components/BaseModal.vue";
import {MdPreview} from "md-editor-v3";
import VotingChat from "@/Pages/Voting/Vote/VotingChat.vue";
import VotingSidebar from "@/Pages/Voting/Vote/VotingSidebar.vue";

const props = defineProps(['questions', 'room', 'roomSettings', 'invitedUsers', 'roomAttachments'])
// const currentTab = ref(props.room.vote_started === 1 ? 'StartVoting' : 'Welcome');
const currentTab = ref('StartVoting');
const isChatEnable = ref(props.roomSettings?.chat_enabled === 1)

const tabs = {
    Welcome,
    StartVoting,
}

const onlineUsers = ref([]);
const isReadyToStart = ref(props.roomSettings.wait_for_voters === 0);
const invitedUsers = ref(props.invitedUsers);

const isUserOnline = (user) => onlineUsers.value.some(onlineUser => onlineUser.id === user.id);

function startVoting() {
    axios.get(route('api.room.vote.start', props.room.id))
        .then(function (response) {
            // currentTab.value = 'StartVoting';
            console.log(response.data)
        })
}

if (props.roomSettings.wait_for_voters === 1) {
    const handleHere = (users) => {
        onlineUsers.value = users;
        isReadyToStart.value = onlineUsers.value.length >= invitedUsers.value.length;
    };

    const handleJoining = (user) => {
        onlineUsers.value.push(user);
        isReadyToStart.value = onlineUsers.value.length >= invitedUsers.value.length;
    };

    const handleLeaving = (user) => {
        onlineUsers.value = onlineUsers.value.filter((u) => u.id !== user.id);
        isReadyToStart.value = onlineUsers.value.length >= invitedUsers.value.length;
    };

    Echo.join('voting.' + props.room.id)
        .here(handleHere)
        .joining(handleJoining)
        .leaving(handleLeaving);

    Echo.private('voting.' + props.room.id).listen('VotingProcess', (e) => {
        if (e.room.vote_started) {
            currentTab.value = 'StartVoting';
        }
    })
}
</script>
