<template>
    <form @submit.prevent="submit">
        <div class="row gx-3">
            <div class="col-md-12">
                <div class="mb-3">
                    <label for="room_name" class="mb-2">Room Name:</label>
                    <input type="text" id="room_name" name="room_name"
                           class="form-control form-control-sm"
                           v-model="form.room_name" required>
                </div>
            </div>

            <div class="col-md-12">
                <div class="mb-3">
                    <label for="room_description" class="mb-2">Room Description:</label>
                    <MdEditor v-model="form.room_description" @onUploadImg="onUploadImg"
                              language="en-US"/>
                </div>
            </div>

            <div class="col-md-12">
                <div class="d-grid">
                    <button type="submit" class="btn btn-sm btn-success p-3">Update</button>
                </div>
            </div>
        </div>
    </form>
</template>

<script setup>
import {MdEditor} from "md-editor-v3";
import {useForm} from "@inertiajs/vue3";
import {route} from "ziggy-js";
import {useToast} from "vue-toast-notification";
import {useVotingRoomStore} from "@/Stores/voting-room.js";
import {computed, onMounted, watch} from "vue";

const props = defineProps(['room'])
const $toast = useToast();
const votingRoomStore = useVotingRoomStore()
const room = computed(() => votingRoomStore.rooms.find(room => room.id === props.room.id))

const form = useForm({
    room_name: room.value?.room_name,
    room_description: room.value?.room_description,
});

watch(() => room.value, () => {
    form.room_name = room.value?.room_name
    form.room_description = room.value?.room_description
})

onMounted(async () => {
    await votingRoomStore.fetchRooms()
})

const onUploadImg = async (files, callback) => {
    const res = await Promise.all(
        files.map((file) => {
            return new Promise((rev, rej) => {
                const form = new FormData();
                form.append('image', file);

                axios
                    .post(route('api.image.upload'), form, {
                        headers: {
                            'Content-Type': 'multipart/form-data'
                        }
                    })
                    .then((res) => rev(res))
                    .catch((error) => rej(error));
            });
        })
    );
    callback(res.map((item) => item.data.image));
}

const submit = () => {
    try {
        const formData = new FormData();
        formData.append('room_name', form.room_name);
        formData.append('room_description', form.room_description);

        votingRoomStore.updateRoom(props.room.id, formData)

        $toast.success('Room updated successfully');
    } catch (e) {
        $toast.error('Failed to update room');
    }
}
</script>
