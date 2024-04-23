<template>
    <div v-if="!isLoading" class="text-center">
        <div v-if="!room.is_published">
            <p class="display-5">Confirm Room Publication</p>
            <p class="text-secondary">
                Once published, you won't be able to change the room settings.
            </p>
            <p class="text-danger">
                This action is irreversible. Are you sure you want to proceed?
            </p>
            <button class="btn btn-primary" @click="submit">
                Publish Room
            </button>
        </div>
        <div v-else>
            <p class="display-5">Room Published</p>
        </div>
    </div>
</template>

<script setup>
import { useVotingRoomStore } from "@/Stores/voting-room.js";
import { computed, onMounted, ref } from "vue";

const isLoading = ref(true);

const props = defineProps(["room"]);
const roomStore = useVotingRoomStore();

// const room = computed(() => {
//     for (const room of roomStore.rooms) {
//         if (room.id === props.room.id) {
//             return room;
//         }
//     }
// });
const room = computed(() => props.room);

const submit = async () => {
    await roomStore.publishRoom(props.room.id);
};

onMounted(async () => {
    // await roomStore.fetchRooms();

    isLoading.value = false;
});
</script>
