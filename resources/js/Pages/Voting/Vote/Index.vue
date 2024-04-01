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

    <transition name="fade" mode="out-in">
        <component :is="tabs[currentTab]" :room="room" :questions="questions"
                   @switch-tab="currentTab = $event"></component>
    </transition>
</template>

<script setup>
import BaseOffcanvas from "@/Components/BaseOffcanvas.vue";
import {computed, onMounted, ref} from "vue";
import * as bootstrap from 'bootstrap'
import Welcome from "@/Pages/Voting/Vote/Welcome.vue";
import StartVoting from "@/Pages/Voting/Vote/StartVoting.vue";
import {usePage} from "@inertiajs/vue3";

const currentTab = ref('Welcome')

const tabs = {
    Welcome,
    StartVoting,
}

const props = defineProps(['questions', 'room'])

const bsOffcanvas = ref(null);

onMounted(() => {
    bsOffcanvas.value = new bootstrap.Offcanvas('#sidebar')
})

function openSidebar(modal) {
    modal.show();
}

const onlineUsers = ref([]);

// Define the handleHere function
const handleHere = (users) => {
    onlineUsers.value = users;
};

const handleJoining = (user) => {
    onlineUsers.value.push(user);
};

const handleLeaving = (user) => {
    onlineUsers.value = onlineUsers.value.filter((u) => u.id !== user.id);
};

Echo.join('voting.' + props.room.id)
    .here(handleHere)
    .joining(handleJoining)
    .leaving(handleLeaving);
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
