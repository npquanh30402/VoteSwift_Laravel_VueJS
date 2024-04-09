<template>
    <div class="list-group shadow-sm small mb-3">
        <div class="list-group-item text-bg-dark">Actions</div>
        <button class="list-group-item list-group-item-action" :class="{'active': currentTab === 'RoomDetails'}"
                @click="switchTab('RoomOverview')">Room Overview
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

        <button class="list-group-item list-group-item-action" :class="{'active': currentTab === 'UpdateChat'}"
                @click="switchTab('UpdateChat')">Chat Feature
        </button>

        <button class="list-group-item list-group-item-action" :class="{'active': currentTab === 'RoomRealtime'}"
                @click="switchTab('RoomRealtime')">Realtime Feature
        </button>

        <button class="list-group-item list-group-item-action"
                @click="switchTab('')">Extra Settings
        </button>

        <button class="list-group-item list-group-item-action" :class="{'active': currentTab === 'VotingResult'}"
                @click="switchTab('VotingResult')">Results
        </button>

        <button class="list-group-item list-group-item-action" :class="{'active': currentTab === 'RoomPublish'}"
                @click="switchTab('RoomPublish')">Published
        </button>

        <button class="opacity-50 list-group-item list-group-item-action fw-bold text-bg-danger"
                :class="{'opacity-100': currentTab === 'DeleteRoom'}"
                @click="switchTab('DeleteRoom')">DELETE
        </button>
    </div>
</template>

<script setup>
import {ref} from "vue";

const emit = defineEmits(['switch-tab'])
const currentTab = ref('RoomOverview');

const switchTab = (tabName) => {
    currentTab.value = tabName;
    emit('switch-tab', tabName)
}
</script>
