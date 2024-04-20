<template>
    <div>
        <button
            class="list-group-item text-success"
            @click="viewCandidateDialogVisible = true"
        >
            Details
        </button>
        <Dialog
            v-model:visible="viewCandidateDialogVisible"
            :style="{ width: '80vw' }"
            header="Candidate Details"
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
                            class="btn btn-warning"
                            type="submit"
                            @click="viewCandidateDialogVisible = false"
                        >
                            Update
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
import { onMounted, ref } from "vue";
import { MdEditor } from "md-editor-v3";
import { useCandidateStore } from "@/Stores/candidates.js";
import LightBoxHelper from "@/Components/Helpers/LightBoxHelper.vue";
import { useHelper } from "@/Services/helper.js";
import Dialog from "primevue/dialog";

const props = defineProps(["room", "candidate"]);
const helper = useHelper();
const CandidateStore = useCandidateStore();

const viewCandidateDialogVisible = ref(false);

const form = useForm({
    candidate_title: props.candidate?.candidate_title,
    candidate_description: props.candidate?.candidate_description,
    candidate_image: props.candidate?.candidate_image,
});
const currentImageDisplay = ref(null);
const imgSrc = ref(null);

const showImage = (e) => {
    currentImageDisplay.value = e;
};

onMounted(() => {
    imgSrc.value = props.candidate?.candidate_image;
});

const submit = () => {
    const formData = new FormData();
    formData.append(
        "candidate_title",
        helper.sanitizeAndTrim(form.candidate_title),
    );
    formData.append(
        "candidate_description",
        helper.sanitizeAndTrim(form.candidate_description),
    );
    formData.append("candidate_image", form.candidate_image);

    CandidateStore.updateCandidate(props.room.id, props.candidate.id, formData);
};

function handleFileChange(event) {
    const file = event.target.files[0];

    if (!file) {
        return;
    }

    form.candidate_image = file;
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
