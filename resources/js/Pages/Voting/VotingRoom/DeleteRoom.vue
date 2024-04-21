<template>
    <div class="text-center">
        <p class="display-5">Are you sure you want to delete this room?</p>
        <p class="text-muted">This cannot be undone!</p>
        <button class="btn btn-danger" type="button" @click="handleDelete">
            DELETE
        </button>
    </div>
</template>

<script setup>
import { useVotingRoomStore } from "@/Stores/voting-room.js";

const props = defineProps(["room"]);
const roomStore = useVotingRoomStore();

const handleDelete = async () => {
    await roomStore.deleteRoom(props.room.id);
};
</script>
