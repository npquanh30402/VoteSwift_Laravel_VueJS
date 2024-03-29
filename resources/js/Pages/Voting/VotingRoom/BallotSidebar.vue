<template>
    <div class="list-group shadow-sm small mb-3">
        <div class="list-group-item text-bg-dark">Actions</div>
        <button class="list-group-item list-group-item-action"
                @click="switchTab('RoomDetails')">Room Details
        </button>
        <button class="list-group-item list-group-item-action" @click="switchTab('DescriptionPage')">Room Description
        </button>
        <button class="list-group-item list-group-item-action"
                @click="switchTab('QuestionsPage')">Questions/Candidates
        </button>

        <button class="list-group-item list-group-item-action"
                @click="switchTab('UpdateTitleDesc')">Change Title/Desc
        </button>

        <button class="list-group-item list-group-item-action"
                @click="switchTab('UpdateTime')">Change Date/Time
        </button>

        <button class="list-group-item list-group-item-action"
                @click="switchTab('VoterPage')">Voters
        </button>

        <button class="list-group-item list-group-item-action"
                @click="switchTab('AttachmentPage')">Attachments
        </button>

        <button class="list-group-item list-group-item-action"
                @click="switchTab('UpdatePassword')">Password
        </button>

        <button class="list-group-item list-group-item-action"
                @click="switchTab('')">Chat Feature
        </button>

        <button class="list-group-item list-group-item-action"
                @click="switchTab('')">Extra Settings
        </button>

        <button class="list-group-item list-group-item-action"
                @click="switchTab('VotingResult')">Results
        </button>

        <button class="list-group-item list-group-item-action"
                @click="switchTab('')">Published
        </button>
    </div>
</template>

<script setup>
import {Link} from "@inertiajs/vue3";
import {route} from "ziggy-js"

defineProps(['room']);

const emit = defineEmits(['switch-tab'])

const switchTab = (tabName) => {
    emit('switch-tab', tabName)
}
</script>
