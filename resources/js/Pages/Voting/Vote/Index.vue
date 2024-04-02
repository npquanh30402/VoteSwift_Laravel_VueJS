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

    <button @click="startVoting" class="btn btn-primary">Start</button>

    <ul>
        <li v-for="user in onlineUsers">{{ user.username }}</li>
    </ul>

    <h3>Time remaining: </h3>
    <VotingClock :date="room.end_time"/>

    <transition name="fade" mode="out-in">
        <component :is="tabs[currentTab]" :room="room" :questions="questions" :isReadyToStart="isReadyToStart"
                   @switch-tab="currentTab = $event"></component>
    </transition>
</template>

<script setup>
import {Link} from "@inertiajs/vue3";
import BaseOffcanvas from "@/Components/BaseOffcanvas.vue";
import {onMounted, ref} from "vue";
import * as bootstrap from 'bootstrap'
import Welcome from "@/Pages/Voting/Vote/Welcome.vue";
import StartVoting from "@/Pages/Voting/Vote/StartVoting.vue";
import {route} from "ziggy-js";
import VotingClock from "@/Components/VotingClock.vue";

const props = defineProps(['questions', 'room'])
// const currentTab = ref(props.room.vote_started === 1 ? 'StartVoting' : 'Welcome');
const currentTab = ref('Welcome');

const tabs = {
    Welcome,
    StartVoting,
}

function startVoting() {
    axios.get(route('api.room.vote.start', props.room.id))
        .then(function (response) {
            console.log(response.data);
        })
}


const bsOffcanvas = ref(null);

onMounted(() => {
    bsOffcanvas.value = new bootstrap.Offcanvas('#sidebar')
})

function openSidebar(modal) {
    modal.show();
}

const onlineUsers = ref([]);
const isReadyToStart = ref(false);

// Define the handleHere function
const handleHere = (users) => {
    onlineUsers.value = users;
    isReadyToStart.value = onlineUsers.value.length >= 2;
};

const handleJoining = (user) => {
    onlineUsers.value.push(user);
    isReadyToStart.value = onlineUsers.value.length >= 2;
};

const handleLeaving = (user) => {
    onlineUsers.value = onlineUsers.value.filter((u) => u.id !== user.id);
};

Echo.join('voting.' + props.room.id)
    .here(handleHere)
    .joining(handleJoining)
    .leaving(handleLeaving);

Echo.private('voting.' + props.room.id).listen('VotingProcess', (e) => {
    if (e.room.vote_started) {
        currentTab.value = 'StartVoting';
    }
    console.log(e.room.vote_started)
})
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
