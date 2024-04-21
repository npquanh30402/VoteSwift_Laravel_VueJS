<template>
    <div class="d-flex flex-column gap-3">
        <div class="hstack gap-3 align-items-center">
            <div class="form-check form-switch">
                <input
                    id="musicPlayerSwitch"
                    :checked="isMusicPlayerEnable"
                    class="form-check-input"
                    role="switch"
                    type="checkbox"
                    @change="toggleMusicPlayer"
                />
                <label class="form-check-label" for="musicPlayerSwitch"
                    >Enable Music Player</label
                >
            </div>
            <div>
                <i
                    class="bi bi-exclamation-octagon text-warning fw-bold me-2"
                ></i>
                <span class="fw-bold"
                    >Warning: Enable music player can potentially distract you
                    from voting.</span
                >
            </div>
        </div>
        <div
            :class="[isMusicPlayerEnable ? '' : 'un-interactive']"
            class="row g-3"
        >
            <div class="col-md-4">
                <form
                    class="card border border-3 border-success-subtle p-3 shadow-sm form-group d-flex flex-column"
                    @submit.prevent="uploadMusic"
                >
                    <label class="form-label" for="uploadMusic"
                        >Upload Music:</label
                    >
                    <input
                        id="uploadMusic"
                        class="form-control"
                        name="uploadMusic"
                        type="file"
                        @change="handleFileChange"
                    />
                    <p v-if="form.errors.music" class="m-0 small text-danger">
                        {{ form.errors.music }}
                    </p>
                    <div class="ms-auto mt-3">
                        <button class="btn btn-primary" type="submit">
                            Upload
                        </button>
                    </div>
                </form>
            </div>
            <div class="col-md-8">
                <div
                    class="card border border-3 border-success-subtle p-3 shadow-sm form-group d-flex flex-column"
                    style="height: 50vh"
                >
                    <span>Music playlist:</span>
                    <div class="card-body overflow-auto">
                        <div
                            class="list-group vstack justify-content-between align-items-center"
                        >
                            <div
                                v-for="file in authUser.music"
                                :key="file.path"
                                class="list-group-item list-group-item-action"
                            >
                                <div class="overflow-hidden">
                                    <span class="text-truncate">{{
                                        file.title
                                    }}</span>
                                </div>
                                <div
                                    class="float-end hstack gap-3 justify-content-center align-items-center"
                                >
                                    <div
                                        class="hstack gap-3 justify-content-center align-items-center"
                                    >
                                        <a
                                            :href="file.url"
                                            class="btn btn-secondary"
                                            ><i class="bi bi-download"></i
                                        ></a>
                                        <button
                                            class="btn btn-danger"
                                            @click="deleteMusic(file.id)"
                                        >
                                            <i class="bi bi-trash"></i>
                                        </button>
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
import { router, useForm, usePage } from "@inertiajs/vue3";
import { route } from "ziggy-js";
import { computed, ref } from "vue";
import { Link } from "@inertiajs/vue3";
import { useToast } from "vue-toast-notification";

const $toast = useToast();
const authUser = computed(() => usePage().props.authUser);

const isMusicPlayerEnable = ref(
    authUser.value.settings?.music_player_enabled === 1,
);

const toggleMusicPlayer = () => {
    isMusicPlayerEnable.value = !isMusicPlayerEnable.value;
    router.post(route("user.music.settings"), {
        isMusicPlayerEnable: isMusicPlayerEnable.value,
    });

    $toast.success("Settings updated successfully");
};

const form = useForm({
    music: null,
});

function handleFileChange(event) {
    form.music = event.target.files[0];
}

function refresh() {
    router.reload();
}

function uploadMusic() {
    form.post(route("user.music.settings.upload"), {
        ...form,
        music: form.music,
    });

    $toast.success("Music uploaded successfully");
}

const deleteMusic = (id) => {
    router.delete(route("user.music.settings.delete", id));

    $toast.success("Music deleted successfully");
};
</script>
