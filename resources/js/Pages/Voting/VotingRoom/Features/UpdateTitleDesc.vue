<template>
    <div class="card shadow-sm border-0 mb-3 overflow-auto">
        <div class="card-header text-bg-dark text-center">Change Title/Description</div>
        <form @submit.prevent="submit" class="card-body">
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
    </div>
</template>

<script setup>
import {MdEditor} from "md-editor-v3";
import {router, useForm} from "@inertiajs/vue3";
import {route} from "ziggy-js";

const props = defineProps(['room'])

const form = useForm({
    room_name: props.room?.room_name,
    room_description: props.room?.room_description,
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

const submit = () => {
    router.post(route('room.update', props.room.id), {
        _method: 'put',
        ...form
    })
}
</script>
