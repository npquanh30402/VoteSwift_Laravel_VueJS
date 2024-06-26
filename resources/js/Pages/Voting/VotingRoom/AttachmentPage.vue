<template>
    <div class="row">
        <div v-if="!isPublish" class="col-sm-8 mb-3 mb-sm-0">
            <div class="card">
                <div class="card-header text-bg-dark">Upload Attachments</div>
                <div class="card-body">
                    <Dashboard
                        :plugins="['Webcam', 'ImageEditor']"
                        :uppy="uppy"
                    />
                </div>
            </div>
        </div>
        <div :class="{ 'col-sm-4': !isPublish }">
            <div class="card">
                <div
                    class="card-header text-bg-dark d-flex justify-content-between"
                >
                    <span>Uploaded Attachments</span>
                </div>
                <div v-if="attachments" class="card-body">
                    <div
                        class="list-group vstack justify-content-between align-items-center"
                    >
                        <div
                            v-for="file in attachments"
                            :key="file.path"
                            class="list-group-item list-group-item-action"
                        >
                            <span>{{ file.file_name }}</span>
                            <div
                                class="float-end hstack gap-3 justify-content-center align-items-center"
                            >
                                <div
                                    class="hstack gap-3 justify-content-center align-items-center"
                                >
                                    <i
                                        v-if="isImageFile(file.file_name)"
                                        class="bi bi-eye icon text-success"
                                        @click="showImage(file.file_path)"
                                    ></i>
                                    <a :href="file.file_path"
                                        ><i
                                            class="bi bi-download icon text-dark"
                                        ></i
                                    ></a>
                                    <i
                                        class="bi bi-trash icon text-danger"
                                        @click="handleDelete(file)"
                                    ></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <teleport to="body">
            <LightBoxHelper :currentImageDisplay="currentImageDisplay" />
        </teleport>
    </div>
</template>

<script setup>
import { Dashboard } from "@uppy/vue";

import "@uppy/core/dist/style.min.css";
import "@uppy/dashboard/dist/style.min.css";
import "@uppy/webcam/dist/style.min.css";
import "@uppy/image-editor/dist/style.min.css";

import Uppy from "@uppy/core";
import Webcam from "@uppy/webcam";
import ImageEditor from "@uppy/image-editor";
import { computed, onBeforeUnmount, onMounted, ref } from "vue";
import { useAttachmentStore } from "@/Stores/attachments.js";
import LightBoxHelper from "@/Components/Helpers/LightBoxHelper.vue";
import { useToast } from "vue-toast-notification";

const props = defineProps(["room"]);
const $toast = useToast();
const isPublish = computed(() => props.room.is_published === 1);
const attachmentStore = useAttachmentStore();

const attachments = computed(() => attachmentStore.attachments[props.room.id]);
const currentImageDisplay = ref(null);

const imageFiles = ref([]);
const otherFiles = ref([]);

onMounted(async () => {
    await attachmentStore.fetchAttachments(props.room.id);
});

const getFileType = (fileName) => {
    return fileName?.substring(fileName.lastIndexOf(".") + 1).toLowerCase();
};
const isImageFile = (fileName) => {
    const fileType = getFileType(fileName);
    return ["jpg", "jpeg", "png", "gif"].includes(fileType);
};

for (let i = 0; i < attachments.value?.length; i++) {
    const file = attachments?.value[i];
    const fileType = getFileType(file.file_name);

    if (isImageFile(fileType)) {
        imageFiles.value.push(file);
    } else {
        otherFiles.value.push(file);
    }
}

const uppy = new Uppy({
    restrictions: {
        maxFileSize: 10000000,
    },
})
    .use(Webcam)
    .use(ImageEditor, {
        quality: 0.8,
    });

const fileQueue = [];

uppy.on("upload", () => {
    while (fileQueue.length > 0) {
        const formData = new FormData();
        const file = fileQueue.shift();
        formData.append("file", file.data);
        attachmentStore.storeAttachment(props.room.id, formData);
    }
});

uppy.on("file-added", (file) => {
    fileQueue.push(file);
});

uppy.on("complete", (result) => {
    if (result.successful) {
        $toast.success("Attachments uploaded successfully");
    } else {
        $toast.error("Failed to upload attachments");
    }
});

onBeforeUnmount(() => {
    uppy.close();
});

const handleDelete = (file) => {
    try {
        attachmentStore.destroyAttachment(props.room.id, file.id);

        $toast.success("Attachment deleted successfully");
    } catch (error) {
        $toast.error("Failed to delete attachment");
    }
};

const showImage = (filePath) => {
    currentImageDisplay.value = {
        target: {
            src: filePath,
        },
    };
};
</script>

<style scoped>
.icon {
    cursor: pointer;
    font-size: 1.2rem;
}
</style>
