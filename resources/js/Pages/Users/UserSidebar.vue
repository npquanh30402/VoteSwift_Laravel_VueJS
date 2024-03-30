<template>
    <div class="list-group shadow-sm small mb-3">
        <div class="list-group-item text-bg-dark">Voting Actions</div>
        <button
            :class="{ 'list-group-item': true, 'list-group-item-action': true, 'active': currentTab === 'RoomList' }"
            @click="switchTab('RoomList')"
        >
            Room List
        </button>
        <Link :href="route('room.create')" class="list-group-item list-group-item-action">Create Room</Link>
    </div>
    <div class="list-group shadow-sm small mb-3">
        <div class="list-group-item text-bg-dark">User Actions</div>
        <button
            :class="{ 'list-group-item': true, 'list-group-item-action': true, 'active': currentTab === 'UserCalendar' }"
            @click="switchTab('UserCalendar')"
        >
            Calendar
        </button>
        <button
            :class="{ 'list-group-item': true, 'list-group-item-action': true, 'active': currentTab === 'Friend' }"
            @click="switchTab('Friend')"
        >
            Friend List
        </button>
        <button
            :class="{ 'list-group-item': true, 'list-group-item-action': true, 'active': currentTab === 'MusicPlayerSettings' }"
            @click="switchTab('MusicPlayerSettings')"
        >
            Music Player
        </button>
        <button
            :class="{ 'list-group-item': true, 'list-group-item-action': true, 'active': currentTab === 'UserSettings' }"
            @click="switchTab('UserSettings')"
        >
            Settings
        </button>
    </div>
</template>

<script setup>
import {Link} from "@inertiajs/vue3";
import {route} from "ziggy-js";
import {ref} from "vue";

const emit = defineEmits(['switch-tab']);
let currentTab = ref('RoomList');

const switchTab = (tabName) => {
    currentTab.value = tabName;
    emit('switch-tab', tabName);
};
</script>
