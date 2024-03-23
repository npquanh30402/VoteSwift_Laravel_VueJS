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
                <div class="col-md-12">
                    <div class="mb-3">
                        <div class="row justify-content-center align-items-center text-center">
                            <div class="col-md-8">
                                <input type="text" id="search" v-model="search" placeholder="Search User"
                                       class="form-control form-control-lg">
                            </div>
                            <div class="col-md-3">
                                <select v-model="search_user" id="invitation"
                                        class="form-select form-select-lg form-label">
                                    <option disabled>Pick user to send invitation</option>
                                    <option v-if="search !== ''" v-for="user in filteredList" :value="user.id"
                                            :key="user.id">
                                        {{ user.id }} - {{ user.username }}
                                    </option>
                                </select>
                            </div>
                            <div class="col-md-1">
                                <span @click="addToInvite"><i class="bi bi-plus-circle text-success"
                                                              style="font-size: 2em;"></i></span>
                            </div>
                        </div>
                    </div>
                </div>
                <div v-if="user_invitation_list.length > 0" class="col-md-12">
                    <div class="mb-3 row justify-content-center align-content-center text-center">
                        <div style="height: 5rem">
                            <ul>
                                <li v-for="user in user_invitation_list" :key="user.id">{{ user.username }}
                                    <button type="button" class="text-danger" @click="removeToInvite(user.id)"><i
                                        class="bi bi-dash-circle"></i></button>
                                </li>
                            </ul>
                        </div>
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
                        <label for="files" class="form-label">Files <span
                            class="text-muted">(Can select multiple files)</span></label>
                        <input class="form-control" type="file" id="files" @change="handleFileChange" multiple>
                        <div>
                            <ul id="file-list" class="list-group mt-3">
                                <li class="list-group-item border-success" v-for="(file, index) in form.files"
                                    :key="Math.random() + file.name" @click="removeFile(index)">
                                    {{ file.name }}
                                </li>
                            </ul>
                        </div>
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
                            <option value="after_voting">After Voting</option>
                            <option value="restricted" selected>Restricted</option>
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
                                   name="allow_voting" v-model="form.allow_voting" checked>
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
import VueDatePicker from '@vuepic/vue-datepicker';
import '@vuepic/vue-datepicker/dist/main.css';
import {MdEditor} from 'md-editor-v3';
import 'md-editor-v3/lib/style.css';
import {router, useForm} from "@inertiajs/vue3";
import {route} from "ziggy-js";
import {computed, ref} from "vue";

const props = defineProps(['timezones_with_offset', 'user_list'])

const disable_password = ref(true);

const search = ref('');
const search_user = ref('');
const userListArray = Object.entries(props.user_list).map(([id, username]) => ({id, username}));

const filteredList = computed(() => {
    return userListArray.filter(item => {
        return item.username.toLowerCase().includes(search.value.toLowerCase());
    });
})

let user_invitation_list = ref([]);

function addToInvite() {
    const userIdToAdd = search_user.value;

    const userExistsInInvitationList = user_invitation_list.value.some(user => user.id === userIdToAdd);

    if (!userExistsInInvitationList) {
        const userToAdd = userListArray.find(user => user.id === userIdToAdd);
        if (userToAdd) {
            user_invitation_list.value.push(userToAdd);
        }
    }
}

function removeToInvite(userId) {
    const userIndexToRemove = userListArray.findIndex(user => user.id === userId);

    if (userIndexToRemove !== -1) {
        user_invitation_list.value.splice(userIndexToRemove, 1);
    }
}

const form = useForm({
    room_name: '',
    start_time: null,
    end_time: null,
    timezone: '',
    room_description: '',
    allow_multiple_votes: false,
    public_visibility: false,
    results_visibility: 'after_voting',
    require_password: '',
    allow_voting: true,
    allow_skipping: false,
    allow_anonymous_voting: false,
    files: []
});

function handleFileChange(event) {
    form.files = Array.from(event.target.files);
}

function removeFile(index) {
    console.log(index, form.files)
    form.files.splice(index, 1);
}

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

    console.log('Upload responses:', res); // Log the responses for debugging

    const urls = res.map((item) => item.data.image); // Access the 'image' property from the response data
    console.log('Image URLs:', urls); // Log the extracted URLs
    callback(res.map((item) => item.data.image));
}

const submit = () => {
    router.post(route('room.store'), {
        ...form,
        require_password: disable_password.value ? '' : form.require_password,
        user_invitation_list: user_invitation_list.value
    });
}
</script>

<style>
#file-list li:hover {
    cursor: pointer;
    background: red;
    color: white;
}
</style>
