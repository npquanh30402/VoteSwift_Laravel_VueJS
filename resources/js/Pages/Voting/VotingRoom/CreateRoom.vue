<template>
    <form class="my-5" @submit.prevent="submit">
        <div class="container card shadow p-5">
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

                <div class="col-md-8">
                    <div class="d-grid">
                        <button
                            class="btn btn-sm btn-success p-3"
                            type="submit"
                        >
                            Create Room
                        </button>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="d-grid">
                        <button
                            aria-label="Clear"
                            class="btn btn-sm btn-secondary p-3"
                            type="reset"
                        >
                            Clear
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</template>

<script setup>
import "@vuepic/vue-datepicker/dist/main.css";
import { MdEditor } from "md-editor-v3";
import "md-editor-v3/lib/style.css";
import { useForm } from "@inertiajs/vue3";
import { useVotingRoomStore } from "@/Stores/voting-room.js";
import { useEtcStore } from "@/Stores/etc.js";

const roomStore = useVotingRoomStore();
const etcStore = useEtcStore();
const form = useForm({
    room_name: "",
    room_description: "",
});

const onUploadImg = etcStore.onUploadImg;

const submit = async () => {
    const formData = new FormData();
    formData.append("room_name", form.room_name);
    formData.append("room_description", form.room_description);

    await roomStore.storeRoom(formData);
};
</script>
