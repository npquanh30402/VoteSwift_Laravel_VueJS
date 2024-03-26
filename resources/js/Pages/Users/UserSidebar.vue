<template>
    <div class="list-group shadow-sm small mb-3">
        <div class="list-group-item text-bg-dark">Voting Actions</div>
        <Link :href="route('room.create')" class="list-group-item list-group-item-action" :class="createRoomTab">Create
            Room
        </Link>
    </div>
    <div class="list-group shadow-sm small mb-3">
        <div class="list-group-item text-bg-dark">User Actions</div>
        <Link :href="route('user.friends')" class="list-group-item list-group-item-action">Friend
            List
        </Link>
        <Link :href="route('user.settings')" class="list-group-item list-group-item-action"
              :class="settingsTab">
            Settings
        </Link>
        <Link :href="route('user.settings')" class="list-group-item list-group-item-action"
              :class="settingsTab">
            Settings
        </Link>
        <Link :href="route('user.music.settings')" class="list-group-item list-group-item-action">Music Player
        </Link>
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

</script>
