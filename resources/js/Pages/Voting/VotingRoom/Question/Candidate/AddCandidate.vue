<template>
    <div>
        <button
            class="btn btn-secondary mb-3"
            @click="candidateDialogVisible = true"
        >
            Add Candidate
        </button>
        <Dialog
            v-model:visible="candidateDialogVisible"
            :style="{ width: '80vw' }"
            header="Add Candidate"
            maximizable
            modal
        >
            <form class="row" @submit.prevent="submit">
                <div class="col-md-8">
                    <div class="mb-3">
                        <label class="form-label" for="candidate_title"
                            >Candidate Title</label
                        >
                        <input
                            v-model="form.candidate_title"
                            class="form-control"
                            placeholder="Enter Candidate Title"
                            type="text"
                        />
                        <p
                            v-if="errorMessages.candidate_title"
                            class="m-0 small text-danger"
                        >
                            {{ errorMessages.candidate_title }}
                        </p>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="candidate_description"
                            >Candidate Description</label
                        >
                        <MdEditor
                            v-model="form.candidate_description"
                            language="en-US"
                            @onUploadImg="onUploadImg"
                        ></MdEditor>
                    </div>
                    <div class="text-end">
                        <button
                            :class="{ disabled: errorMessages.candidate_title }"
                            class="btn btn-primary"
                            type="submit"
                            @click="candidateDialogVisible = false"
                        >
                            Add
                        </button>
                    </div>
                </div>
                <div class="col-md-4 vstack">
                    <div class="form-group mb-4">
                        <label class="form-label" for="candidate_image"
                            >Image:</label
                        >
                        <input
                            id="candidate_image"
                            class="form-control"
                            name="candidate_image"
                            type="file"
                            @change="handleFileChange"
                        />
                        <!--                    <p-->
                        <!--                        v-if="errorMessages.candidate_image"-->
                        <!--                        class="m-0 small text-danger"-->
                        <!--                    >-->
                        <!--                        {{ errorMessages.candidate_image }}-->
                        <!--                    </p>-->
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
import { reactive, ref, watch } from "vue";
import { MdEditor } from "md-editor-v3";
import { useCandidateStore } from "@/Stores/candidates.js";
import LightBoxHelper from "@/Components/Helpers/LightBoxHelper.vue";
import { useHelper } from "@/Services/helper.js";
import Dialog from "primevue/dialog";
import { useEtcStore } from "@/Stores/etc.js";

const props = defineProps(["room", "question"]);
const helper = useHelper();
const CandidateStore = useCandidateStore();

const candidateDialogVisible = ref(false);

const form = useForm({
    candidate_title: "",
    candidate_description: "",
    candidate_image: null,
});

const errorMessages = reactive({
    candidate_title: "",
    candidate_description: "",
    candidate_image: "",
});
const currentImageDisplay = ref(null);
const imgSrc = ref(null);

function updateErrorMessage(fieldName, value) {
    switch (fieldName) {
        case "candidate_title":
            const titleLength = value.length;
            if (titleLength < 10) {
                errorMessages.candidate_title =
                    "Candidate title must be at least 10 characters.";
            } else if (titleLength > 100) {
                errorMessages.candidate_title =
                    "Candidate title cannot exceed 100 characters.";
            } else {
                errorMessages.candidate_title = "";
            }
            break;
        case "candidate_image":
            errorMessages.candidate_image = value;
            break;
    }
}

watch(
    () => form.candidate_title,
    (newValue) => {
        updateErrorMessage("candidate_title", newValue);
    },
);

watch(
    () => form.candidate_image,
    (newValue) => {
        updateErrorMessage("candidate_image", newValue);
    },
);

const submit = () => {
    const formData = new FormData();
    formData.append(
        "candidate_title",
        helper.sanitizeAndTrim(form.candidate_title),
    );

    if (form.candidate_description) {
        formData.append(
            "candidate_description",
            helper.sanitizeAndTrim(form.candidate_description),
        );
    }

    if (form.candidate_image) {
        formData.append("candidate_image", form.candidate_image);
    }

    CandidateStore.storeCandidate(props.room.id, props.question.id, formData);

    form.candidate_title = "";
    form.candidate_description = "";
    form.candidate_image = null;

    currentImageDisplay.value = null;
    imgSrc.value = null;
};

const showImage = (e) => {
    currentImageDisplay.value = e;
};

function handleFileChange(event) {
    const file = event.target.files[0];

    if (!file) {
        return;
    }

    if (!file.type.startsWith("image/")) {
        errorMessages.candidate_image = "Please select an image file.";
        return;
    }

    errorMessages.candidate_image = "";

    form.candidate_image = file;
    imgSrc.value = URL.createObjectURL(file);
}

const etcStore = useEtcStore();
const onUploadImg = etcStore.onUploadImg;
</script>
