<template>
    <div>
        <button class="btn btn-primary" @click="dialogVisible = true">
            Add
        </button>
        <Dialog
            v-model:visible="dialogVisible"
            :style="{ width: '80vw' }"
            header="Add Question"
            maximizable
            modal
        >
            <form class="row" @submit.prevent="submit">
                <div class="col-md-8">
                    <div class="mb-3">
                        <label class="form-label" for="question_title"
                            >Question Title</label
                        >
                        <input
                            v-model="form.question_title"
                            class="form-control"
                            placeholder="Enter Question Title"
                            type="text"
                        />
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="question_description"
                            >Question Description</label
                        >
                        <MdEditor
                            v-model="form.question_description"
                            language="en-US"
                            @onUploadImg="onUploadImg"
                        ></MdEditor>
                    </div>
                    <div class="text-end">
                        <button
                            class="btn btn-primary"
                            type="submit"
                            @click="dialogVisible = false"
                        >
                            Add
                        </button>
                    </div>
                </div>
                <div class="col-md-4 vstack">
                    <label class="form-label">Options:</label>
                    <div class="form-check mb-4">
                        <input
                            id="allow_multiple_votes"
                            v-model="form.allow_multiple_votes"
                            class="form-check-input"
                            type="checkbox"
                        />
                        <label
                            class="form-check-label"
                            for="allow_multiple_votes"
                            >Allow Multiple Votes</label
                        >
                        <p class="m-0 small text-danger"></p>
                    </div>
                    <div class="form-check mb-4">
                        <input
                            id="allow_skipping"
                            v-model="form.allow_skipping"
                            class="form-check-input"
                            type="checkbox"
                        />
                        <label class="form-check-label" for="allow_skipping"
                            >Allow Skipping</label
                        >
                        <p class="m-0 small text-danger"></p>
                    </div>
                    <div class="form-group mb-4">
                        <label class="form-label" for="question_image"
                            >Image:</label
                        >
                        <input
                            id="question_image"
                            class="form-control"
                            name="question_image"
                            type="file"
                            @change="handleFileChange"
                        />
                        <p class="m-0 small text-danger"></p>
                    </div>
                    <div class="form-group mb-4 text-center">
                        <img
                            :src="imgSrc"
                            alt="Image"
                            class="img-fluid"
                            style="cursor: pointer"
                            @click="showImage"
                        />
                        <teleport to="body">
                            <LightBoxHelper
                                :currentImageDisplay="currentImageDisplay"
                            />
                        </teleport>
                    </div>
                </div>
            </form>
        </Dialog>
    </div>
</template>

<script setup>
import { useForm } from "@inertiajs/vue3";
import { route } from "ziggy-js";
import { ref } from "vue";
import { MdEditor } from "md-editor-v3";
import { useQuestionStore } from "@/Stores/questions.js";
import LightBoxHelper from "@/Components/Helpers/LightBoxHelper.vue";
import { useHelper } from "@/Services/helper.js";

import Dialog from "primevue/dialog";

const props = defineProps(["room"]);
const helper = useHelper();

const questionStore = useQuestionStore();
const currentImageDisplay = ref(null);
const imgSrc = ref(null);

const showImage = (e) => {
    currentImageDisplay.value = e;
};

const dialogVisible = ref(false);

const form = useForm({
    question_title: "",
    question_description: "",
    question_image: null,
    allow_multiple_votes: null,
    allow_skipping: null,
});
const submit = () => {
    const formData = new FormData();
    formData.append(
        "question_title",
        helper.sanitizeAndTrim(form.question_title),
    );

    if (form.question_description) {
        formData.append(
            "question_description",
            helper.sanitizeAndTrim(form.question_description),
        );
    }

    if (form.question_image) {
        formData.append("question_image", form.question_image);
    }

    if (form.allow_multiple_votes) {
        formData.append("allow_multiple_votes", form.allow_multiple_votes);
    }

    if (form.allow_skipping) {
        formData.append("allow_skipping", form.allow_skipping);
    }

    questionStore.storeQuestion(props.room.id, formData);

    currentImageDisplay.value = null;
    imgSrc.value = null;
};

function handleFileChange(event) {
    const file = event.target.files[0];

    if (!file) {
        return;
    }

    form.question_image = file;
    imgSrc.value = URL.createObjectURL(file);
}

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
</script>
