<template>
    <div v-if="!isLoading">
        <button class="btn btn-primary" @click="submit">Publish Room</button>
    </div>
    <BaseLoading v-else />
</template>

<script setup>
import { useVotingRoomStore } from "@/Stores/voting-room.js";
import { computed, onMounted, ref } from "vue";
import BaseLoading from "@/Components/BaseLoading.vue";

const isLoading = ref(true);

const props = defineProps(["room"]);
const roomStore = useVotingRoomStore();

const room = computed(() => {
    for (const room of roomStore.rooms) {
        if (room.id === props.room.id) {
            return room;
        }
    }
});

const submit = async () => {
    const formData = new FormData();
    formData.append("is_published", true);

    await roomStore.updateRoom(props.room.id, formData);
};

onMounted(async () => {
    await roomStore.fetchRooms();

    isLoading.value = false;
});
</script>
