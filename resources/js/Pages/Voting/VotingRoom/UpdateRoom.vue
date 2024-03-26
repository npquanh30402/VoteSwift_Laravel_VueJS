<template>
    <form @submit.prevent="submit">
        <div class="container card shadow p-5">
            <div class="row gx-3">
                <div class="col-md-12">
                    <div class="mb-3 form-floating">
                        <input type="text" id="room_name" name="room_name"
                               class="form-control form-control-sm"
                               placeholder="Room Name"
                               v-model="form.room_name"
                               required>
                        <label for="room_name">Room Name</label>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3 row justify-content-center align-content-center text-center">
                        <div class="col-md-3 ">
                            <label for="start_time" class="align-middle">Start Time</label>
                        </div>
                        <div class="col-md-9">
                            <VueDatePicker v-model="form.start_time" id="start_time" name="start_time"></VueDatePicker>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3 row justify-content-center align-content-center text-center">
                        <div class="col-md-3 ">
                            <label for="start_time" class="align-middle">End Time</label>
                        </div>
                        <div class="col-md-9">
                            <VueDatePicker v-model="form.end_time" id="end_time" name="end_time"></VueDatePicker>
                        </div>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="mb-3 form-floating">
                        <select class="form-select" id="timezone"
                                name="timezone"
                                aria-label="Timezone" placeholder="Timezone" v-model="form.timezone">
                            <option v-for="(key, timezone) in timezones_with_offset" :value="timezone" :key="timezone">
                                {{ timezone }}:
                                GMT{{ key }}
                            </option>
                        </select>
                        <label for="Timezone">Timezone</label>
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
                    <div class="mb-3">
                        <div class="form-check">
                            <input type="checkbox" id="allow_multiple_votes" class="form-check-input"
                                   name="allow_multiple_votes" v-model="form.allow_multiple_votes">
                            <label for="allow_multiple_votes">Allow Multiple Votes</label>
                        </div>
                    </div>
                </div>

                <div class="col-md-12">
                    <div class="mb-3">
                        <div class="form-check">
                            <label for="public_visibility">Public Visibility</label>
                            <input type="checkbox" id="public_visibility" class="form-check-input"
                                   name="public_visibility" v-model="form.public_visibility">
                        </div>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="mb-3 form-floating">
                        <select class="form-select" id="results_visibility"
                                name="results_visibility"
                                aria-label="Results Visibility" placeholder="Results Visibility"
                                v-model="form.results_visibility">
                            <option disabled value="">Please select one</option>
                            <option value="after_voting">After Voting</option>
                            <option value="restricted">Restricted</option>
                        </select>
                        <label for="results_visibility">Results Visibility</label>
                    </div>
                </div>
                <div class="col-md-12 row align-items-center justify-content-center">
                    <div class="mb-3 col-md-9">
                        <div class="form-floating">
                            <input type="password" id="require_password" name="require_password"
                                   class="form-control form-control-sm" placeholder="Password"
                                   v-model="form.require_password" :disabled="disable_password">
                            <label for="require_password">Password</label>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3 form-check">
                        <label for="disable_password">Disable Password</label>
                        <input type="checkbox" id="disable_password" class="form-check-input"
                               name="disable_password" v-model="disable_password">
                    </div>
                </div>

                <div class="col-md-12">
                    <div class="mb-3">
                        <div class="form-check">
                            <label for="allow_voting">Allow Voting</label>
                            <input type="checkbox" id="allow_voting" class="form-check-input"
                                   name="allow_voting" v-model="form.allow_voting">
                        </div>
                    </div>
                </div>

                <div class="col-md-12">
                    <div class="mb-3">
                        <div class="form-check">
                            <label for="allow_skipping">Allow Skipping</label>
                            <input type="checkbox" id="allow_skipping" class="form-check-input"
                                   name="allow_skipping" v-model="form.allow_skipping">
                        </div>
                    </div>
                </div>

                <div class="col-md-12">
                    <div class="mb-3">
                        <div class="form-check">
                            <label for="allow_anonymous_voting">Allow Anonymous Voting</label>
                            <input type="checkbox" id="allow_anonymous_voting" class="form-check-input"
                                   name="allow_anonymous_voting" v-model="form.allow_anonymous_voting">
                        </div>
                    </div>
                </div>

                <div class="col-md-12">
                    <div class="d-grid">
                        <button type="submit" class="btn btn-sm btn-success p-3">Update Room</button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</template>

<script setup>
import VueDatePicker from '@vuepic/vue-datepicker';
import '@vuepic/vue-datepicker/dist/main.css';
import {MdEditor} from 'md-editor-v3';
import 'md-editor-v3/lib/style.css';
import {router, useForm} from "@inertiajs/vue3";
import {route} from "ziggy-js";
import {ref} from "vue";

const props = defineProps(['timezones_with_offset', 'room', 'room_settings'])

const disable_password = ref(!(props.room_settings.password !== null && props.room_settings.password !== ''));

const form = useForm({
    room_name: props.room?.room_name,
    start_time: props.room?.start_time,
    end_time: props.room?.end_time,
    timezone: props.room?.timezone,
    room_description: props.room?.room_description,
    allow_multiple_votes: props.room_settings?.allow_multiple_votes === 1,
    public_visibility: props.room_settings?.public_visibility === 1,
    results_visibility: props.room_settings?.results_visibility,
    require_password: props.room_settings?.password,
    allow_voting: props.room_settings?.allow_voting === 1,
    allow_skipping: props.room_settings?.allow_skipping === 1,
    allow_anonymous_voting: props.room_settings?.allow_anonymous_voting === 1
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
        ...form,
        disable_password: disable_password.value
    })
}
</script>
