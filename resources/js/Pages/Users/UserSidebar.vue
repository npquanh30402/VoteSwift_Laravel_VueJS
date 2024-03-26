<template>
    <div class="list-group shadow-sm small mb-3">
        <div class="list-group-item text-bg-dark">Voting Actions</div>
        <button class="list-group-item list-group-item-action"
                @click="switchTab('RoomList')">Room List
        </button>
        <Link :href="route('room.create')" class="list-group-item list-group-item-action" :class="createRoomTab">Create
            Room
        </Link>
    </div>
    <div class="list-group shadow-sm small mb-3">
        <div class="list-group-item text-bg-dark">User Actions</div>
        <button class="list-group-item list-group-item-action" @click="switchTab('FriendList')">Friend
            List
        </button>
        <button class="list-group-item list-group-item-action" @click="switchTab('MusicPlayerSettings')">Music Player
        </button>
        <button class="list-group-item list-group-item-action"
                @click="switchTab('UserSettings')">
            Settings
        </button>
    </div>
</template>

<script setup>
import {Link} from "@inertiajs/vue3";
import {route} from "ziggy-js";
import {computed} from "vue";

const props = defineProps(['selectedTab'])

const settingsTab = computed(() => {
    return props.selectedTab === 'settings' ? 'active' : '';
});

const createRoomTab = computed(() => {
    return props.selectedTab === 'createRoom' ? 'active' : '';
});

const emit = defineEmits(['switch-tab'])

const switchTab = (tabName) => {
    emit('switch-tab', tabName)
}

</script>
