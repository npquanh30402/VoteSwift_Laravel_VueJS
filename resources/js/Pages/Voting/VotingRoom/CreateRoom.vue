<template>
    <form @submit.prevent="submit" class="my-5">
        <div class="container card shadow p-5">
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

                <div class="col-md-8">
                    <div class="d-grid">
                        <button type="submit" class="btn btn-sm btn-success p-3">Create Room</button>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="d-grid">
                        <button type="reset" class="btn btn-sm btn-secondary p-3" aria-label="Clear">Clear
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</template>

<script setup>
import '@vuepic/vue-datepicker/dist/main.css';
import {MdEditor} from 'md-editor-v3';
import 'md-editor-v3/lib/style.css';
import {router, useForm} from "@inertiajs/vue3";
import {route} from "ziggy-js";
import {useToast} from "vue-toast-notification";
import {useVotingRoomStore} from "@/Stores/voting-room.js";

const roomStore = useVotingRoomStore()
const $toast = useToast();
const form = useForm({
    room_name: '',
    room_description: '',
});

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

const submit = async () => {
    const formData = new FormData();
    formData.append('room_name', form.room_name);
    formData.append('room_description', form.room_description);

    try {
        const response = await roomStore.storeRoom(formData)

        if (response) {
            router.get(route('dashboard.user'))
        }

        $toast.open({
            message: "<strong>Room created successfully</strong><br>Click here to go to the room dashboard",
            type: 'success',
            duration: 5000,
            onClick: () => {
                router.get(route('room.dashboard', response.data.id))
            }
        })
    } catch (error) {
        console.log(error)
        $toast.error('Error occurred while creating the room');
    }
}
</script>
