<template>
    <div class="d-flex flex-column gap-3">
        <div class="hstack gap-3 align-items-center">
            <div class="form-check form-switch">
                <input class="form-check-input" type="checkbox" role="switch" id="musicPlayerSwitch"
                       @change="toggleMusicPlayer" :checked="isMusicPlayerEnable">
                <label class="form-check-label" for="musicPlayerSwitch">Enable Music Player</label>
            </div>
            <div>
                <i class="bi bi-exclamation-octagon text-warning fw-bold me-2"></i>
                <span
                    class="fw-bold">Warning: Enable music player can potentially distract you from voting.</span>
            </div>
        </div>
        <div class="row g-3" :class="[isMusicPlayerEnable ? '' : 'un-interactive']">
            <div class="col-md-4">
                <form @submit.prevent="uploadMusic"
                      class="card border border-3 border-success-subtle p-3 shadow-sm form-group d-flex flex-column">
                    <label class="form-label" for="uploadMusic">Upload Music:</label>
                    <input type="file" class="form-control" id="uploadMusic" name="uploadMusic"
                           @change="handleFileChange">
                    <p class="m-0 small text-danger"></p>
                    <div class="ms-auto mt-3">
                        <button type="submit" class="btn btn-primary">Upload</button>
                    </div>
                </form>
            </div>
            <div class="col-md-8">
                <div
                    class="card border border-3 border-success-subtle p-3 shadow-sm form-group d-flex flex-column"
                    style="height: 50vh">
                    <div class="d-flex justify-content-between">
                        <span>Music playlist:</span>
                        <i class="bi bi-arrow-clockwise icon" @click="refresh"></i>
                    </div>
                    <div class="card-body">
                        <div
                            class="list-group vstack justify-content-between align-items-center overflow-auto">
                            <div v-for="file in music" :key="file.path"
                                 class="list-group-item list-group-item-action">
                                <div class="overflow-hidden">
                                    <span class="text-truncate">{{ file.title }}</span>
                                </div>
                                <div
                                    class="float-end hstack gap-3 justify-content-center align-items-center">
                                    <div
                                        class="hstack gap-3 justify-content-center align-items-center">
                                        <a :href="file.url"><i
                                            class="bi bi-download icon text-dark"></i></a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import {router, useForm, usePage} from "@inertiajs/vue3";
import {route} from "ziggy-js";
import {ref} from "vue";

const authUser = usePage().props.authUser;
const authUserSettings = usePage().props.authUserSettings;
const props = defineProps(['music'])

console.log(props.music)

const isMusicPlayerEnable = ref(authUserSettings?.music_player_enabled === 1);

const toggleMusicPlayer = () => {
    isMusicPlayerEnable.value = !isMusicPlayerEnable.value
    router.post(route('user.music.settings'), {
        isMusicPlayerEnable: isMusicPlayerEnable.value
    })
}

const form = useForm({
    music: null
})

function handleFileChange(event) {
    form.music = event.target.files[0];
}

function refresh() {
    router.reload();
}

function uploadMusic() {
    router.post(route('user.music.settings.upload'), {
        ...form,
        music: form.music,
    })
}
</script>

<style scoped>
.un-interactive {
    pointer-events: none;
    opacity: 0.5;
}
</style>
