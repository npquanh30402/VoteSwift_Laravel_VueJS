<template>
    <div>
        <button class="btn btn-info vertical-button" type="button" @click="openSidebar(bsOffcanvas)">Sidebar
        </button>
        <BaseOffcanvas id="sidebar" title="Sidebar">
            <div class="list-group">
                <div class="accordion" id="SidebarAccordion">
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                    data-bs-target="#OverviewCollapse" aria-expanded="false"
                                    aria-controls="OverviewCollapse">
                                <span>Overview</span>
                            </button>
                        </h2>

                        <div id="OverviewCollapse" class="accordion-collapse collapse"
                             data-bs-parent="#SidebarAccordion">
                            <div class="accordion-body">Nothing currently</div>
                        </div>
                    </div>
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
                                    class="list-group vstack justify-content-between align-items-center overflow-auto"
                                    style="height: 50vh">
                                    <div v-for="file in roomAttachments" :key="file.path"
                                         class="list-group-item list-group-item-action">
                                        <VTooltip>
                                            <span>{{
                                                    helper.truncateFileName(helper.extractFileName(file.file_name), 25)
                                                }}</span>

                                            <template #popper>
                                                {{ file.file_name }}
                                            </template>
                                        </VTooltip>
                                        <div class="float-end hstack gap-3 justify-content-center align-items-center">
                                            <div
                                                class="hstack gap-3 justify-content-center align-items-center">
                                                <i v-if="imageFiles.includes(file)"
                                                   class="bi bi-eye icon text-success"
                                                   @click="showImage(file.file_path)"></i>
                                                <i v-if="musicFiles.includes(file) && currentFile !== file"
                                                   class="bi bi-play-fill icon text-success"
                                                   @click="handlePlaySpecify(file)"></i>
                                                <i v-if="currentFile === file"
                                                   class="bi bi-pause-fill icon text-success"
                                                   @click="handlePause"></i>
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
            <div v-if="musicFiles.length">
                <MusicPlayer
                    :music="musicFiles"
                    :currentFile="currentFile"/>
            </div>
        </BaseOffcanvas>
    </div>
</template>
<script setup>
import BaseOffcanvas from "@/Components/BaseOffcanvas.vue";
import {computed, onMounted, reactive, ref} from "vue";
import * as bootstrap from "bootstrap";
import LightBoxHelper from "@/Components/Helpers/LightBoxHelper.vue";
import MusicPlayer from "@/Components/MusicPlayer.vue";
import {useHelper} from "@/Services/helper.js";

const props = defineProps(['room', 'roomSettings', 'roomAttachments'])
const helper = useHelper()
const onlineUsers = computed(() => props.onlineUsers)
const isUserOnline = computed(() => props.isUserOnline)
const currentImageDisplay = ref(null)
const bsOffcanvas = ref(null);
const modals = reactive({
    RoomDescriptionModal: 'RoomDescriptionModal'
})

const currentFile = ref(null)

onMounted(() => {
    bsOffcanvas.value = new bootstrap.Offcanvas('#sidebar')
    modals.RoomDescriptionModal = new bootstrap.Modal(document.getElementById(modals.RoomDescriptionModal));

    filterFiles(props.roomAttachments);
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

const handlePlaySpecify = (file) => {
    currentFile.value = file;
}
const handlePause = () => {
    currentFile.value = null;
}

const imageFiles = ref([]);
const musicFiles = ref([]);
const otherFiles = ref([]);

const imageFileTypes = new Set(['jpg', 'jpeg', 'png', 'gif']);
const musicFileTypes = new Set(['mp3', 'wav', 'flac', 'ogg']);

function filterFiles(roomAttachments) {
    for (let i = 0; i < roomAttachments.length; i++) {
        const file = roomAttachments[i];
        const fileType = file.file_name.substring(file.file_name.lastIndexOf('.') + 1).toLowerCase();

        if (imageFileTypes.has(fileType)) {
            imageFiles.value.push(file);
        } else if (musicFileTypes.has(fileType)) {
            musicFiles.value.push(file);
        } else {
            otherFiles.value.push(file);
        }
    }

    for (let i = 0; i < musicFiles.value.length; i++) {
        const file = musicFiles.value[i];

        musicFiles.value[i].title = file.file_name;
        musicFiles.value[i].url = file.file_path;
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
