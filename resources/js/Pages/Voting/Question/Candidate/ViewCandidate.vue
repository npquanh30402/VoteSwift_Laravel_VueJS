<template>
    <BaseModal title="Candidate">
        <form @submit.prevent="submit" class="row">
            <div class="col-md-8">
                <div class="mb-3">
                    <label for="candidate_title" class="form-label">Candidate Title</label>
                    <input type="text" class="form-control" v-model="form.candidate_title"
                           placeholder="Enter Candidate Title">
                </div>
                <div class="mb-3">
                    <label for="candidate_description" class="form-label">Candidate Description</label>
                    <MdEditor v-model="form.candidate_description" @onUploadImg="onUploadImg"
                              language="en-US"></MdEditor>
                </div>
                <div class="text-end">
                    <button type="submit" class="btn btn-warning" data-bs-dismiss="modal">Update</button>
                </div>
            </div>
            <div class="col-md-4 vstack">
                <div class="form-group mb-4">
                    <label class="form-label" for="candidate_image">Image:</label>
                    <input type="file" class="form-control" id="candidate_image" name="candidate_image"
                           @change="handleFileChange">
                    <p class="m-0 small text-danger"></p>
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
import {ref, watch} from "vue";
import VueEasyLightbox from "vue-easy-lightbox";
import {MdEditor} from "md-editor-v3";
import {useCandidateStore} from "@/Stores/candidates.js";

const props = defineProps(['candidate'])

const CandidateStore = useCandidateStore()

const form = useForm({
    candidate_title: props.candidate?.candidate_title,
    candidate_description: props.candidate?.candidate_description,
    candidate_image: props.candidate?.candidate_image,
});
const imgSrc = ref(null);

watch(() => props.candidate, (newQuestion) => {
    form.candidate_title = newQuestion?.candidate_title;
    form.candidate_description = newQuestion?.candidate_description;
    form.candidate_image = newQuestion?.candidate_image;

    imgSrc.value = newQuestion?.candidate_image
}, {
    deep: true,
    immediate: true,
});

const submit = async () => {
    const formData = new FormData();
    formData.append('candidate_title', form.candidate_title);
    formData.append('candidate_description', form.candidate_description);
    formData.append('candidate_image', form.candidate_image);

    formData.append('_method', 'PUT');

    // const candidateData = {
    //     candidate_title: form.candidate_title,
    //     candidate_description: form.candidate_description,
    //     candidate_image: form.candidate_image,
    // };

    await CandidateStore.updateCandidate(props.candidate.id, formData);
}

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
