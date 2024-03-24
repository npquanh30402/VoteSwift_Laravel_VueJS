<template>
    <BaseModal title="Create a Candidate">
        <form @submit.prevent="submit" class="row">
            <div class="col-md-8">
                <div class="mb-3">
                    <label for="candidate_title" class="form-label">Candidate Title</label>
                    <input type="text" class="form-control" v-model="form.candidate_title"
                           placeholder="Enter Question Title">
                </div>
                <div class="mb-3">
                    <label for="candidate_description" class="form-label">Candidate Description</label>
                    <textarea v-model="form.candidate_description"
                              class="form-control"
                              style="height: 10rem"></textarea>
                </div>
                <div class="text-end">
                    <button type="submit" class="btn btn-primary" data-bs-dismiss="modal">Add</button>
                </div>
            </div>
            <!--            <div class="col-md-4 vstack">-->
            <!--                <div class="form-group mb-4">-->
            <!--                    <label class="form-label" for="candidate_image">Image:</label>-->
            <!--                    <input type="file" class="form-control" id="candidate_image" name="candidate_image"-->
            <!--                           @change="handleFileChange">-->
            <!--                    <p class="m-0 small text-danger"></p>-->
            <!--                </div>-->
            <!--                <div class="form-group mb-4 text-center">-->
            <!--                    <img :src="imgSrc"-->
            <!--                         class="img-fluid"-->
            <!--                         style="cursor: pointer"-->
            <!--                         alt="Image" @click="showSingle"/>-->
            <!--                    <teleport to="body">-->
            <!--                        <vue-easy-lightbox-->
            <!--                            :visible="visibleRef"-->
            <!--                            :imgs="imgsRef"-->
            <!--                            :index="indexRef"-->
            <!--                            @hide="onHide"-->
            <!--                        ></vue-easy-lightbox>-->
            <!--                    </teleport>-->
            <!--                </div>-->
            <!--            </div>-->
        </form>
    </BaseModal>
</template>

<script setup>
import BaseModal from "@/Components/BaseModal.vue";
import {router, useForm} from "@inertiajs/vue3";
import {route} from "ziggy-js";
import {ref} from "vue";
import VueEasyLightbox from "vue-easy-lightbox";

const props = defineProps(['question'])

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

const form = useForm({
    candidate_title: '',
    candidate_description: '',
    // candidate_image: null,
});

function handleFileChange(event) {
    const file = event.target.files[0];

    if (!file) {
        return;
    }

    form.candidate_image = file;
    imgSrc.value = URL.createObjectURL(file);
}


const submit = () => {
    router.post(route('candidate.store', props.question.id), {
        ...form,
    });
}
</script>
