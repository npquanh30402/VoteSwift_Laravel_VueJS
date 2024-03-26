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
        <Link :href="route('room.update.form', room.id)" class="list-group-item list-group-item-action">Change Settings
        </Link>
        <!--        <Link class="list-group-item list-group-item-action">Type-->
        <!--        </Link>-->
        <button class="list-group-item list-group-item-action"
                @click="switchTab('AttachmentPage')">Attachments
        </button>
        <!--        <Link class="list-group-item list-group-item-action">Chat Feature-->
        <!--        </Link>-->
        <!--        <Link class="list-group-item list-group-item-action">Published-->
        <!--        </Link>-->
        <button class="list-group-item list-group-item-action"
                @click="switchTab('VotingResult')">Results
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
