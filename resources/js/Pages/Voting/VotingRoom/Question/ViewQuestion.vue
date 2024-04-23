<template>
    <div>
        <button
            class="list-group-item text-success"
            @click="viewQuestionDialogVisible = true"
        >
            Details
        </button>
        <Dialog
            v-model:visible="viewQuestionDialogVisible"
            :style="{ width: '80vw' }"
            header="Question Details"
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
                            :class="{ 'un-interactive': isPublish }"
                            class="form-control"
                            placeholder="Enter Question Title"
                            type="text"
                        />
                        <p
                            v-if="errorMessages.question_title"
                            class="m-0 small text-danger"
                        >
                            {{ errorMessages.question_title }}
                        </p>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="question_description"
                            >Question Description</label
                        >
                        <MdEditor
                            v-model="form.question_description"
                            :class="{ 'un-interactive': isPublish }"
                            language="en-US"
                            @onUploadImg="onUploadImg"
                        ></MdEditor>
                    </div>
                    <div class="text-end">
                        <button
                            v-if="!isPublish"
                            class="btn btn-warning"
                            type="submit"
                            @click="viewQuestionDialogVisible = false"
                        >
                            Update
                        </button>
                    </div>
                </div>
                <div class="col-md-4 vstack">
                    <label class="form-label">Options:</label>
                    <div class="form-check mb-4">
                        <input
                            id="allow_multiple_votes"
                            v-model="form.allow_multiple_votes"
                            :class="{ 'un-interactive': isPublish }"
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
                            :class="{ 'un-interactive': isPublish }"
                            class="form-check-input"
                            type="checkbox"
                        />
                        <label class="form-check-label" for="allow_skipping"
                            >Allow Skipping</label
                        >
                        <p class="m-0 small text-danger"></p>
                    </div>
                    <div v-if="!isPublish" class="form-group mb-4">
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
                        <button
                            v-if="imgSrc"
                            class="btn btn-secondary mt-3 float-end"
                            type="button"
                            @click="clearImg"
                        >
                            Clear
                        </button>
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
import { computed, onMounted, reactive, ref, watch } from "vue";
import { MdEditor } from "md-editor-v3";
import LightBoxHelper from "@/Components/Helpers/LightBoxHelper.vue";
import { useQuestionStore } from "@/Stores/questions.js";
import { useHelper } from "@/Services/helper.js";
import Dialog from "primevue/dialog";
import { useEtcStore } from "@/Stores/etc.js";

const props = defineProps(["room", "question"]);
const helper = useHelper();
const questionStore = useQuestionStore();
const currentImageDisplay = ref(null);
const imgSrc = ref(null);
const isPublish = computed(() => props.room.is_published === 1);
const viewQuestionDialogVisible = ref(false);

const form = useForm({
    question_title: props.question?.question_title,
    question_description: props.question?.question_description,
    question_image: props.question?.question_image,
    allow_multiple_votes: props.question?.allow_multiple_votes,
    allow_skipping: props.question?.allow_skipping,
});

const errorMessages = reactive({
    question_title: "",
});

function updateErrorMessage(fieldName, value) {
    switch (fieldName) {
        case "question_title":
            const titleLength = value.length;
            if (titleLength < 10) {
                errorMessages.question_title =
                    "Question title must be at least 10 characters.";
            } else if (titleLength > 100) {
                errorMessages.question_title =
                    "Question title cannot exceed 100 characters.";
            } else {
                errorMessages.question_title = "";
            }
            break;
    }
}

watch(
    () => form.question_title,
    (newValue) => {
        updateErrorMessage("question_title", newValue);
    },
);

onMounted(() => {
    imgSrc.value = props.question?.question_image;
});

const submit = async () => {
    const formData = new FormData();

    formData.append(
        "question_title",
        helper.sanitizeAndTrim(form.question_title),
    );
    formData.append(
        "question_description",
        helper.sanitizeAndTrim(form.question_description),
    );

    formData.append("question_image", form.question_image);

    if (form.allow_multiple_votes) {
        formData.append("allow_multiple_votes", form.allow_multiple_votes);
    }

    if (form.allow_skipping) {
        formData.append("allow_skipping", form.allow_skipping);
    }

    await questionStore.updateQuestion(
        props.room.id,
        props.question.id,
        formData,
    );
};

const showImage = (e) => {
    currentImageDisplay.value = e;
};

function handleFileChange(event) {
    const file = event.target.files[0];

    if (!file) {
        return;
    }

    form.question_image = file;
    imgSrc.value = URL.createObjectURL(file);
}

const clearImg = () => {
    form.question_image = null;
    imgSrc.value = null;
    console.log(form.question_image);
};

const etcStore = useEtcStore();
const onUploadImg = etcStore.onUploadImg;
</script>
