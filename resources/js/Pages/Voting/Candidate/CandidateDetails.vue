<template>
    <BaseModal title="Candidate Details">
        <form @submit.prevent="submit" class="row">
            <div class="col-md-8">
                <div class="mb-3">
                    <label for="candidate_title" class="form-label">Candidate Title</label>
                    <div style="position: relative;">
                        <input type="text" class="form-control" v-model="form.candidate_title"
                               placeholder="Enter Candidate Title" :disabled="!editMode.title">
                        <i @click="toggleEditMode('title')" class="bi bi-pencil"></i>
                    </div>
                </div>
                <div class="mb-3">
                    <div style="position: relative;">
                        <label for="candidate_description" class="form-label">Candidate Description</label>
                        <textarea v-model="form.candidate_description"
                                  class="form-control"
                                  style="height: 10rem" :disabled="!editMode.description"></textarea>
                        <i @click="toggleEditMode('description')" class="bi bi-pencil" style="top: 25%"></i>
                    </div>
                </div>
                <div class="text-end" v-if="editMode.title || editMode.description || editMode.image">
                    <button type="submit" class="btn btn-warning" data-bs-dismiss="modal">Update</button>
                </div>
            </div>
            <!--            <div class="col-md-4">-->
            <!--                <div class="form-group mb-4">-->
            <!--                    <label class="form-label" for="candidate_image">Image:</label>-->
            <!--                    <div style="position: relative;">-->
            <!--                        <input type="file" class="form-control" id="candidate_image" name="candidate_image"-->
            <!--                               @change="handleFileChange" :disabled="!editMode.image">-->
            <!--                        <i @click="toggleEditMode('image')" class="bi bi-pencil"></i>-->
            <!--                    </div>-->
            <!--                    <p class="m-0 small text-danger"></p>-->
            <!--                </div>-->
            <!--                <div class="form-group mb-4 text-center">-->
            <!--                    <img :src="data.candidate_image"-->
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
import {reactive, ref, watch} from "vue";
import VueEasyLightbox from "vue-easy-lightbox";
import {router, useForm} from "@inertiajs/vue3";
import {route} from "ziggy-js";

const props = defineProps(['candidate'])

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

const data = reactive({
    candidate_title: props.question?.candidate_title,
    candidate_description: props.question?.candidate_description,
    // candidate_image: props.question?.candidate_image,
});

watch(() => props.question, (newQuestion) => {
    data.candidate_title = newQuestion?.candidate_title;
    data.candidate_description = newQuestion?.candidate_description;
    // data.candidate_image = newQuestion?.candidate_image;
}, {
    deep: true,
    immediate: true,
});

// function handleFileChange(event) {
//     const file = event.target.files[0];
//
//     if (!file) {
//         return;
//     }
//
//     form.candidate_image = file;
//     data.candidate_image = URL.createObjectURL(file);
// }

const editMode = ref({
    title: false,
    description: false,
    // image: false
});

const toggleEditMode = (field) => {
    editMode.value[field] = !editMode.value[field];
};

const form = useForm({
    candidate_title: data.candidate_title,
    candidate_description: data.candidate_description,
    // candidate_image: data.candidate_image,
});

const submit = () => {
    router.post(route('candidate.update', props.candidate.id), {
        _method: 'put',
        ...form,
    });

    editMode.value = {
        title: false,
        description: false,
        // image: false
    }
}

</script>

<style scoped>
.bi-pencil {
    position: absolute;
    right: 10px;
    top: 50%;
    transform: translateY(-50%);
    cursor: pointer;
    font-size: 1.5rem;
}
</style>
