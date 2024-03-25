<template>
    <div class="list-group shadow-sm small mb-3">
        <div class="list-group-item text-bg-dark">Voting Actions</div>
        <Link :href="route('room.create')" class="list-group-item list-group-item-action">Create
            Room
        </Link>
        <Link :href="route('room.description', room.id)" class="list-group-item list-group-item-action">Room Description
        </Link>
        <Link :href="route('question.main', room.id)" class="list-group-item list-group-item-action">Add Question
        </Link>
        <Link :href="route('room.update.form', room.id)" class="list-group-item list-group-item-action">Change Settings
        </Link>
        <Link class="list-group-item list-group-item-action">Type
        </Link>
        <Link :href="route('room.attachment', room.id)" class="list-group-item list-group-item-action">Attachments
        </Link>
        <Link class="list-group-item list-group-item-action">Chat Feature
        </Link>
        <Link class="list-group-item list-group-item-action">Published
        </Link>
        <Link :href="route('vote.result', room.id)" class="list-group-item list-group-item-action">Results
        </Link>
    </div>
</template>

<script setup>
import {Link} from "@inertiajs/vue3";
import {route} from "ziggy-js"

defineProps(['room']);
</script>
