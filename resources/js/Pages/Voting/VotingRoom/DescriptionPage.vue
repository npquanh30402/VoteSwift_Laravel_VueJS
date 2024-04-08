<template>
    <div class="card shadow-sm border-0 mb-3 overflow-auto">
        <div class="card-header text-bg-dark text-center">Room Description</div>
        {{ room?.room_name }}
        <div class="card-body">
            <MdPreview :editorId="'room_' + room?.id" :modelValue="room?.room_description"/>
        </div>
    </div>
</template>

<script setup>
import {MdPreview} from "md-editor-v3";
import {useVotingRoomStore} from "@/Stores/voting-room.js";
import {computed, onMounted} from "vue";

const props = defineProps(['room'])
const votingRoomStore = useVotingRoomStore()

const room = computed(() => votingRoomStore.rooms.find(room => room.id === props.room.id))

onMounted(async () => {
    await votingRoomStore.fetchRooms()
})

</script>
