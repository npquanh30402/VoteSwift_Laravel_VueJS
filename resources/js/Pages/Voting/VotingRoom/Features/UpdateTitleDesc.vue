<template>
    <form v-if="!isLoading" @submit.prevent="submit">
        <div class="row gx-3">
            <div class="col-md-12">
                <div class="mb-3">
                    <label class="mb-2" for="room_name">Room Name:</label>
                    <input
                        id="room_name"
                        v-model="form.room_name"
                        class="form-control form-control-sm"
                        name="room_name"
                        required
                        type="text"
                    />
                    <p
                        v-if="errorMessages.room_name"
                        class="m-0 small text-danger"
                    >
                        {{ errorMessages.room_name }}
                    </p>
                </div>
            </div>

            <div class="col-md-12">
                <div class="mb-3">
                    <label class="mb-2" for="room_description"
                        >Room Description:</label
                    >
                    <MdEditor
                        v-model="form.room_description"
                        language="en-US"
                        @onUploadImg="onUploadImg"
                    />
                </div>
            </div>

            <div class="col-md-12">
                <div class="d-grid">
                    <button
                        :class="{ disabled: errorMessages.room_name }"
                        class="btn btn-sm btn-success p-3"
                        type="submit"
                    >
                        Update
                    </button>
                </div>
            </div>
        </div>
    </form>
    <BaseLoading v-else />
</template>

<script setup>
import { MdEditor } from "md-editor-v3";
import { useForm } from "@inertiajs/vue3";
import { route } from "ziggy-js";
import { useVotingRoomStore } from "@/Stores/voting-room.js";
import { computed, onMounted, reactive, ref, watch } from "vue";
import { useHelper } from "@/Services/helper.js";
import BaseLoading from "@/Components/BaseLoading.vue";

const isLoading = ref(true);

const props = defineProps(["room"]);
const helper = useHelper();
const votingRoomStore = useVotingRoomStore();
const room = computed(() =>
    votingRoomStore.rooms.find((room) => room.id === props.room.id),
);

const form = useForm({
    room_name: room.value?.room_name,
    room_description: room.value?.room_description,
});

watch(
    () => room.value,
    () => {
        form.room_name = room.value?.room_name;
        form.room_description = room.value?.room_description;
    },
);

const errorMessages = reactive({
    room_name: "",
});

watch(
    () => form.room_name,
    (newValue) => {
        const titleLength = newValue.length;
        if (titleLength < 10) {
            errorMessages.room_name =
                "Room title must be at least 10 characters.";
        } else if (titleLength > 100) {
            errorMessages.room_name =
                "Room title cannot exceed 100 characters.";
        } else {
            errorMessages.room_name = "";
        }
    },
);

onMounted(async () => {
    await votingRoomStore.fetchRooms();

    isLoading.value = false;
});

const onUploadImg = async (files, callback) => {
    const res = await Promise.all(
        files.map((file) => {
            return new Promise((rev, rej) => {
                const form = new FormData();
                form.append("image", file);

                axios
                    .post(route("api.image.upload"), form, {
                        headers: {
                            "Content-Type": "multipart/form-data",
                        },
                    })
                    .then((res) => rev(res))
                    .catch((error) => rej(error));
            });
        }),
    );
    callback(res.map((item) => item.data.image));
};

const submit = async () => {
    const formData = new FormData();
    formData.append("room_name", helper.sanitizeAndTrim(form.room_name));
    formData.append(
        "room_description",
        helper.sanitizeAndTrim(form.room_description),
    );

    await votingRoomStore.updateRoom(props.room.id, formData);
};
</script>
