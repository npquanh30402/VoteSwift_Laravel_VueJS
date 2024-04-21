<template>
    <div class="text-center">
        <p class="display-5">Are you sure you want to duplicate this room?</p>
        <button class="btn btn-warning" type="button" @click="handleDuplicate">
            DUPLICATE
        </button>
    </div>
</template>

<script setup>
import { useVotingRoomStore } from "@/Stores/voting-room.js";

const props = defineProps(["room"]);
const roomStore = useVotingRoomStore();

const handleDuplicate = async () => {
    await roomStore.duplicateRoom(props.room.id);
};
</script>
