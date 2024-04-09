<template>
    <div class="text-center">
        <p class="display-5">Are you sure you want to delete this room?</p>
        <p class="text-muted">This cannot be undone!</p>
        <button @click="handleDelete" type="button" class="btn btn-danger">DELETE
        </button>
    </div>
</template>

<script setup>
import {useVotingRoomStore} from "@/Stores/voting-room.js";
import {useToast} from "vue-toast-notification";
import {router} from "@inertiajs/vue3";
import {route} from "ziggy-js";

const props = defineProps(['room'])
const roomStore = useVotingRoomStore()
const $toast = useToast();

const handleDelete = async () => {
    try {
        const response = await roomStore.deleteRoom(props.room.id)

        if (response) {
            $toast.success('Room deleted successfully')
            router.get(route('dashboard.user'))
        }
    } catch (error) {
        console.log(error)
        $toast.error('Error occurred while deleting the room');
    }
}

</script>
