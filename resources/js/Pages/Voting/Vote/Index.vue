<template>
    <button class="btn btn-info vertical-button" type="button" @click="openSidebar(bsOffcanvas)">Sidebar
    </button>
    <BaseOffcanvas id="sidebar" title="Sidebar">
        <div class="list-group">
            <a href="#" class="list-group-item list-group-item-action">Room Description</a>
            <a href="#" class="list-group-item list-group-item-action">Attachments</a>
            <a href="#" class="list-group-item list-group-item-action">Voter</a>
        </div>
    </BaseOffcanvas>

    <ul>
        <li v-for="user in onlineUsers">{{ user.username }}</li>
    </ul>

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
</template>

<script setup>
import BaseOffcanvas from "@/Components/BaseOffcanvas.vue";
import {onMounted, ref} from "vue";
import * as bootstrap from 'bootstrap'
import Welcome from "@/Pages/Voting/Vote/Welcome.vue";
import StartVoting from "@/Pages/Voting/Vote/StartVoting.vue";
import {route} from "ziggy-js";
import VotingClock from "@/Components/VotingClock.vue";

const props = defineProps(['questions', 'room', 'roomSettings', 'invitedUsers'])
// const currentTab = ref(props.room.vote_started === 1 ? 'StartVoting' : 'Welcome');
const currentTab = ref('Welcome');

const tabs = {
    Welcome,
    StartVoting,
}

const onlineUsers = ref([]);
const isReadyToStart = ref(props.roomSettings.wait_for_voters === 0);
const invitedUsers = ref(props.invitedUsers);

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

const bsOffcanvas = ref(null);

onMounted(() => {
    bsOffcanvas.value = new bootstrap.Offcanvas('#sidebar')
})

function openSidebar(modal) {
    modal.show();
}
</script>

<style scoped>
.vertical-button {
    position: fixed;
    top: 50%;
    left: 0;
    transform: rotate(-90deg);
    transform-origin: top left;
    opacity: 0.8;
}
</style>
