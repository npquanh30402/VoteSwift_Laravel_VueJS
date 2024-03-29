<template>
    <div class="list-group shadow-sm small mb-3">
        <div class="list-group-item text-bg-dark">Actions</div>
        <button class="list-group-item list-group-item-action" :class="{'active': currentTab === 'RoomDetails'}"
                @click="switchTab('RoomDetails')">Room Details
        </button>
        <button class="list-group-item list-group-item-action" :class="{'active': currentTab === 'DescriptionPage'}"
                @click="switchTab('DescriptionPage')">Room Description
        </button>
        <button class="list-group-item list-group-item-action" :class="{'active': currentTab === 'QuestionsPage'}"
                @click="switchTab('QuestionsPage')">Questions/Candidates
        </button>

        <button class="list-group-item list-group-item-action" :class="{'active': currentTab === 'UpdateTitleDesc'}"
                @click="switchTab('UpdateTitleDesc')">Change Title/Desc
        </button>

        <button class="list-group-item list-group-item-action" :class="{'active': currentTab === 'UpdateTime'}"
                @click="switchTab('UpdateTime')">Change Date/Time
        </button>

        <button class="list-group-item list-group-item-action" :class="{'active': currentTab === 'VoterPage'}"
                @click="switchTab('VoterPage')">Voters
        </button>

        <button class="list-group-item list-group-item-action" :class="{'active': currentTab === 'AttachmentPage'}"
                @click="switchTab('AttachmentPage')">Attachments
        </button>

        <button class="list-group-item list-group-item-action" :class="{'active': currentTab === 'UpdatePassword'}"
                @click="switchTab('UpdatePassword')">Password
        </button>

        <button class="list-group-item list-group-item-action"
                @click="switchTab('')">Chat Feature
        </button>

        <button class="list-group-item list-group-item-action"
                @click="switchTab('')">Extra Settings
        </button>

        <button class="list-group-item list-group-item-action" :class="{'active': currentTab === 'VotingResult'}"
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
import {ref} from "vue";

defineProps(['room']);

const emit = defineEmits(['switch-tab'])
let currentTab = ref('RoomDetails');

const switchTab = (tabName) => {
    currentTab.value = tabName;
    emit('switch-tab', tabName)
}
</script>
