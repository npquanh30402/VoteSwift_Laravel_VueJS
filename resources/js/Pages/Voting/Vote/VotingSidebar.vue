<template>
    <div>
        <button class="btn btn-info vertical-button" type="button" @click="openSidebar(bsOffcanvas)">Sidebar
        </button>
        <BaseOffcanvas id="sidebar" title="Sidebar">
            <div class="list-group">
                <div class="accordion" id="SidebarAccordion">
                    <button class="list-group-item list-group-item-action"
                            @click="openModal(modals.RoomDescriptionModal)">
                        Room Description
                    </button>
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                    data-bs-target="#AttachmentCollapse" aria-expanded="false"
                                    aria-controls="AttachmentCollapse">
                                <span>Attachments</span>
                            </button>
                        </h2>

                        <div id="AttachmentCollapse" class="accordion-collapse collapse"
                             data-bs-parent="#SidebarAccordion">
                            <div class="accordion-body">
                                <div
                                    class="list-group vstack justify-content-between align-items-center">
                                    <div v-for="file in roomAttachments" :key="file.path"
                                         class="list-group-item list-group-item-action">
                                        <span>{{ file.file_name }}</span>
                                        <div class="float-end hstack gap-3 justify-content-center align-items-center">
                                            <div
                                                class="hstack gap-3 justify-content-center align-items-center">
                                                <i v-if="imageFiles.includes(file)"
                                                   class="bi bi-eye icon text-success"
                                                   @click="showImage(file.file_path)"></i>
                                                <a :href="file.file_path"><i
                                                    class="bi bi-download icon text-dark"></i></a>
                                            </div>
                                        </div>
                                    </div>
                                    <teleport to="body">
                                        <LightBoxHelper :currentImageDisplay="currentImageDisplay"/>
                                    </teleport>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </BaseOffcanvas>
    </div>
</template>
<script setup>
import BaseOffcanvas from "@/Components/BaseOffcanvas.vue";
import {computed, onMounted, reactive, ref} from "vue";
import * as bootstrap from "bootstrap";
import LightBoxHelper from "@/Components/Helpers/LightBoxHelper.vue";

const props = defineProps(['room', 'roomSettings', 'roomAttachments'])

const onlineUsers = computed(() => props.onlineUsers)
const isUserOnline = computed(() => props.isUserOnline)
const currentImageDisplay = ref(null)
const bsOffcanvas = ref(null);
const modals = reactive({
    RoomDescriptionModal: 'RoomDescriptionModal'
})

onMounted(() => {
    bsOffcanvas.value = new bootstrap.Offcanvas('#sidebar')
    modals.RoomDescriptionModal = new bootstrap.Modal(document.getElementById(modals.RoomDescriptionModal));
})

function openModal(modal) {
    modal.show()
}

function openSidebar(modal) {
    modal.show();
}

const showImage = (filePath) => {
    currentImageDisplay.value = {
        target: {
            src: filePath
        }
    };
}

const imageFiles = ref([]);
const otherFiles = ref([]);

const imageFileTypes = new Set(['jpg', 'jpeg', 'png', 'gif']);

for (let i = 0; i < props.roomAttachments.length; i++) {
    const file = props.roomAttachments[i];
    const fileType = file.file_name.substring(file.file_name.lastIndexOf('.') + 1).toLowerCase();

    if (imageFileTypes.has(fileType)) {
        imageFiles.value.push(file);
    } else {
        otherFiles.value.push(file);
    }
}
</script>

<style scoped>
.vertical-button {
    position: fixed;
    top: 50%;
    left: 0;
    transform: rotate(-90deg);
    transform-origin: top left;
    opacity: 0.8;
}

.icon {
    cursor: pointer;
    font-size: 1.2rem;
}
</style>
