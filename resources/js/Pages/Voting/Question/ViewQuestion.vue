<template>
    <BaseModal title="Question Details">
        <form @submit.prevent="submit" class="row">
            <div class="col-md-8">
                <div class="mb-3">
                    <label for="question_title" class="form-label">Question Title</label>
                    <input type="text" class="form-control" v-model="form.question_title"
                           placeholder="Enter Question Title">
                </div>
                <div class="mb-3">
                    <label for="question_description" class="form-label">Question Description</label>
                    <MdEditor v-model="form.question_description" @onUploadImg="onUploadImg"
                              language="en-US"></MdEditor>
                </div>
                <div class="text-end">
                    <button type="submit" class="btn btn-warning" data-bs-dismiss="modal">Update</button>
                </div>
            </div>
            <div class="col-md-4 vstack">
                <label class="form-label">Options:</label>
                <div class="form-check mb-4">
                    <input type="checkbox" class="form-check-input" id="allow_multiple_votes"
                           v-model="form.allow_multiple_votes">
                    <label class="form-check-label" for="allow_multiple_votes">Allow Multiple Votes</label>
                    <p class="m-0 small text-danger"></p>
                </div>
                <div class="form-check mb-4">
                    <input type="checkbox" class="form-check-input" id="allow_skipping" v-model="form.allow_skipping">
                    <label class="form-check-label" for="allow_skipping">Allow Skipping</label>
                    <p class="m-0 small text-danger"></p>
                </div>
                <div class="form-group mb-4">
                    <label class="form-label" for="question_image">Image:</label>
                    <input type="file" class="form-control" id="question_image" name="question_image"
                           @change="handleFileChange">
                    <p class="m-0 small text-danger"></p>
                </div>
                <div class="form-group mb-4 text-center">
                    <img :src="imgSrc"
                         class="img-fluid"
                         style="cursor: pointer"
                         alt="Image" @click="showImage"/>
                    <teleport to="body">
                        <LightBoxHelper :currentImageDisplay="currentImageDisplay"/>
                    </teleport>
                </div>
            </div>
        </form>
    </BaseModal>
</template>

<script setup>
import BaseModal from "@/Components/BaseModal.vue";
import {useForm} from "@inertiajs/vue3";
import {route} from "ziggy-js";
import {ref, watch} from "vue";
import {MdEditor} from "md-editor-v3";
import LightBoxHelper from "@/Components/Helpers/LightBoxHelper.vue";
import {useQuestionStore} from "@/Stores/questions.js";

const props = defineProps(['room', 'question'])

const questionStore = useQuestionStore()
const currentImageDisplay = ref(null)
const imgSrc = ref(null);

const form = useForm({
    question_title: props.question?.question_title,
    question_description: props.question?.question_description,
    question_image: props.question?.question_image,
    allow_multiple_votes: props.question?.allow_multiple_votes,
    allow_skipping: props.question?.allow_skipping
});

watch(() => props.question, (newQuestion) => {
    form.question_title = newQuestion?.question_title;
    form.question_description = newQuestion?.question_description;
    form.question_image = newQuestion?.question_image;
    form.allow_multiple_votes = newQuestion?.allow_multiple_votes === 1;
    form.allow_skipping = newQuestion?.allow_skipping === 1;

    imgSrc.value = newQuestion?.question_image
}, {
    deep: true,
    immediate: true,
});

const submit = async () => {
    const formData = new FormData();

    formData.append('question_title', form.question_title);
    formData.append('question_description', form.question_description);

    if (form.question_image) {
        formData.append('question_image', form.question_image);
    }

    if (form.allow_multiple_votes) {
        formData.append('allow_multiple_votes', form.allow_multiple_votes);
    }

    if (form.allow_skipping) {
        formData.append('allow_skipping', form.allow_skipping);
    }

    await questionStore.updateQuestion(props.room.id, props.question.id, formData);
}

const showImage = (e) => {
    currentImageDisplay.value = e;
}

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
</script>
