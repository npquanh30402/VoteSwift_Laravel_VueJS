<template>
    <MdPreview :editorId="'room_' + room?.id" :modelValue="room?.room_description"/>
</template>

<script setup>
import {MdPreview} from "md-editor-v3";
import {useVotingRoomStore} from "@/Stores/voting-room.js";
import {computed, onMounted} from "vue";

const props = defineProps(['room'])
const votingRoomStore = useVotingRoomStore()

const room = computed(() => votingRoomStore.rooms.find(room => room.id === props.room.id))

onMounted(() => {
    votingRoomStore.fetchRooms()
})
</script>
