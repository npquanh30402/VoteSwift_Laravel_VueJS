<template>
    <div class="list-group shadow-sm small mb-3">
        <div class="list-group-item text-bg-dark">Voting Actions</div>
        <Link :href="route('room.create')" class="list-group-item list-group-item-action">Create Room
        </Link>
    </div>
    <div class="list-group shadow-sm small mb-3">
        <div class="list-group-item text-bg-dark">User Actions</div>
        <Link :href="route('user.friends')" class="list-group-item list-group-item-action">Friend
            List
        </Link>
        <Link :href="route('information.user')" class="list-group-item list-group-item-action">
            Settings
        </Link>
        <Link :href="route('user.history')" class="list-group-item list-group-item-action">Voting
            History
        </Link>
    </div>
</template>

<script setup>
import {Link} from "@inertiajs/vue3";
import {route} from "ziggy-js";
</script>
