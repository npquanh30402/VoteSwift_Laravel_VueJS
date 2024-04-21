<template>
    <div v-if="!isLoading">
        <MdPreview
            :editorId="'room_' + room?.id"
            :modelValue="room?.room_description"
        />
    </div>
    <BaseLoading v-else />
</template>

<script setup>
import { MdPreview } from "md-editor-v3";
import { useVotingRoomStore } from "@/Stores/voting-room.js";
import { computed, onMounted, ref } from "vue";
import BaseLoading from "@/Components/BaseLoading.vue";

const isLoading = ref(true);
const props = defineProps(["room"]);
const votingRoomStore = useVotingRoomStore();

const room = computed(() =>
    votingRoomStore.rooms.find((room) => room.id === props.room.id),
);

onMounted(async () => {
    await votingRoomStore.fetchRooms();

    isLoading.value = false;
});
</script>
