<template>
    <BaseModal title="Create a Candidate">
        <form @submit.prevent="submit" class="row">
            <div class="col-md-8">
                <div class="mb-3">
                    <label for="candidate_title" class="form-label">Candidate Title</label>
                    <input type="text" class="form-control" v-model="form.candidate_title"
                           placeholder="Enter Candidate Title">
                    <p class="m-0 small text-danger" v-if="errorMessages.candidate_title">
                        {{ errorMessages.candidate_title }}</p>
                </div>
                <div class="mb-3">
                    <label for="candidate_description" class="form-label">Candidate Description</label>
                    <MdEditor v-model="form.candidate_description" @onUploadImg="onUploadImg"
                              language="en-US"></MdEditor>
                </div>
                <div class="text-end">
                    <button type="submit" class="btn btn-primary" :class="{'disabled': errorMessages.candidate_title}"
                            data-bs-dismiss="modal">Add
                    </button>
                </div>
            </div>
            <div class="col-md-4 vstack">
                <div class="form-group mb-4">
                    <label class="form-label" for="candidate_image">Image:</label>
                    <input type="file" class="form-control" id="candidate_image" name="candidate_image"
                           @change="handleFileChange">
                    <p class="m-0 small text-danger" v-if="errorMessages.candidate_image">
                        {{ errorMessages.candidate_image }}</p>
                </div>
                <div class="form-group mb-4 text-center">
                    <img :src="imgSrc"
                         class="img-fluid"
                         style="cursor: pointer"
                         alt="Image" @click="showSingle"/>
                    <teleport to="body">
                        <vue-easy-lightbox
                            :visible="visibleRef"
                            :imgs="imgsRef"
                            :index="indexRef"
                            @hide="onHide"
                        ></vue-easy-lightbox>
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
import {computed, reactive, ref, watch} from "vue";
import VueEasyLightbox from "vue-easy-lightbox";
import {MdEditor} from "md-editor-v3";
import {useCandidateStore} from "@/Stores/candidates.js";

const props = defineProps(['question'])

const CandidateStore = useCandidateStore()

const form = useForm({
    candidate_title: '',
    candidate_description: '',
    candidate_image: null,
});

const errorMessages = reactive({
    candidate_title: '',
    candidate_description: '',
    candidate_image: '',
});

function updateErrorMessage(fieldName, value) {
    switch (fieldName) {
        case 'candidate_title':
            const titleLength = value.length;
            if (titleLength < 10) {
                errorMessages.candidate_title = 'Candidate title must be at least 10 characters.';
            } else if (titleLength > 100) {
                errorMessages.candidate_title = 'Candidate title cannot exceed 100 characters.';
            } else {
                errorMessages.candidate_title = '';
            }
            break;
        case 'candidate_image':
            errorMessages.candidate_image = value;
            break;
    }
}

watch(() => form.candidate_title, (newValue) => {
    updateErrorMessage('candidate_title', newValue);
});

watch(() => form.candidate_image, (newValue) => {
    updateErrorMessage('candidate_image', newValue);
});

const submit = async () => {
    const formData = new FormData();
    formData.append('candidate_title', form.candidate_title);

    if (form.candidate_description) {
        formData.append('candidate_description', form.candidate_description);
    }

    if (form.candidate_image) {
        formData.append('candidate_image', form.candidate_image);
    }

    await CandidateStore.storeCandidate(props.question.id, formData);
}

const imgSrc = ref(null);
const visibleRef = ref(false)
const indexRef = ref(0)
const imgsRef = ref([])

const onShow = () => {
    visibleRef.value = true
}

const showSingle = (e) => {
    imgsRef.value = e.target.src
    onShow()
}

const onHide = () => {
    visibleRef.value = false
}

function handleFileChange(event) {
    const file = event.target.files[0];

    if (!file) {
        return;
    }

    if (!file.type.startsWith('image/')) {
        errorMessages.candidate_image = 'Please select an image file.'
        return;
    }

    errorMessages.candidate_image = '';

    form.candidate_image = file;
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
