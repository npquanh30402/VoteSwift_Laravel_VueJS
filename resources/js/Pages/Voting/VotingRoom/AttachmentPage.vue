<template>
    <div class="row">
        <div class="col">
            <h1 class="display-6 text-center fw-bold">Dashboard: {{ room.room_name }}</h1>
        </div>
    </div>
    <div class="my-3">
        <div class="row justify-content-center">
            <div class="col-md-2">
                <BallotSidebar :room="room"></BallotSidebar>
            </div>
            <div class="col-md-9">
                <div class="row">
                    <div class="col-sm-8 mb-3 mb-sm-0">
                        <div class="card">
                            <div class="card-header text-bg-dark">
                                Upload Attachments
                            </div>
                            <div class="card-body">
                                <Dashboard
                                    :uppy="uppy"
                                    :plugins="['Webcam', 'ImageEditor']"
                                />
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="card">
                            <div class="card-header text-bg-dark d-flex justify-content-between">
                                <span>Uploaded Attachments</span>
                                <i class="bi bi-arrow-clockwise icon" @click="refresh"></i>
                            </div>
                            <div class="card-body">
                                <div
                                    class="list-group vstack justify-content-between align-items-center">
                                    <div v-for="file in attachments" :key="file.path"
                                         class="list-group-item list-group-item-action">
                                        <span>{{ file.file_name }}</span>
                                        <div class="float-end hstack gap-3 justify-content-center align-items-center">
                                            <div
                                                class="hstack gap-3 justify-content-center align-items-center">
                                                <i v-if="isImageFile(file.file_name)"
                                                   class="bi bi-eye icon text-success"
                                                   @click="showSingle(file.file_path)"></i>
                                                <a :href="file.file_path"><i
                                                    class="bi bi-download icon text-dark"></i></a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <teleport to="body">
        <vue-easy-lightbox
            :visible="visibleRef"
            :imgs="imgsRef"
            :index="indexRef"
            @hide="onHide"
        ></vue-easy-lightbox>
    </teleport>

</template>

<script setup>
import BallotSidebar from "@/Pages/Voting/BallotSidebar.vue";

import {Dashboard} from "@uppy/vue";

import "@uppy/core/dist/style.min.css";
import "@uppy/dashboard/dist/style.min.css";
import "@uppy/webcam/dist/style.min.css";
import "@uppy/image-editor/dist/style.min.css";

import Uppy from "@uppy/core";
import Webcam from "@uppy/webcam";
import XHRUpload from "@uppy/xhr-upload";
import ImageEditor from "@uppy/image-editor";
import "@uppy/file-input";
import {onBeforeUnmount, ref} from "vue";
import {route} from "ziggy-js";
import VueEasyLightbox from "vue-easy-lightbox";
import {router} from "@inertiajs/vue3";

const props = defineProps(['room', 'attachments'])

const visibleRef = ref(false)
const indexRef = ref(0)
const imgsRef = ref([])

const onShow = () => {
    visibleRef.value = true
}

const showSingle = (filePath) => {
    imgsRef.value = filePath
    console.log(filePath)
    onShow()
}

const onHide = () => {
    visibleRef.value = false
}

const imageFiles = ref([]);
const documentFiles = ref([]);
const otherFiles = ref([]);

const getFileType = (fileName) => {
    return fileName.substring(fileName.lastIndexOf('.') + 1).toLowerCase();
};
const isImageFile = (fileName) => {
    const fileType = getFileType(fileName);
    return ['jpg', 'jpeg', 'png', 'gif'].includes(fileType);
};

const isDocumentFile = (fileType) => {
    return ['docx', 'pptx', 'xlsx', 'odt', 'odp', 'ods', 'pdf'].includes(fileType);
};

for (let i = 0; i < props.attachments.length; i++) {
    const file = props.attachments[i];
    const fileType = getFileType(file.file_name);

    if (isImageFile(fileType)) {
        imageFiles.value.push(file);
    } else {
        otherFiles.value.push(file);
    }
}


const uppy = new Uppy();

uppy.use(XHRUpload, {
    endpoint: route('api.room.attachment.store', props.room.id),
    fieldName: "file",
});
uppy.use(Webcam);

uppy.use(ImageEditor, {
    quality: 0.8,
});

onBeforeUnmount(() => {
    uppy.close();
});

function refresh() {
    router.reload();
}
</script>

<style scoped>
.icon {
    cursor: pointer;
    font-size: 1.2rem;
}
</style>
