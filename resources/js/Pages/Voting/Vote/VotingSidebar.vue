<template>
    <div>
        <button
            class="btn btn-info vertical-button"
            type="button"
            @click="sidebarVisible = true"
        >
            Sidebar
        </button>
        <Sidebar
            v-model:visible="sidebarVisible"
            header="Sidebar"
            style="width: 30rem"
        >
            <Accordion :activeIndex="0">
                <AccordionTab header="Overview">
                    <p>Nothing</p>
                    <div class="d-grid">
                        <button
                            class="btn btn-outline-primary"
                            @click="RoomDescriptionDialogVisible = true"
                        >
                            Room Description
                        </button>
                        <Dialog
                            v-model:visible="RoomDescriptionDialogVisible"
                            :breakpoints="{ '1199px': '75vw', '575px': '90vw' }"
                            :style="{ width: '50vw' }"
                            header="Room Description"
                            maximizable
                            modal
                        >
                            <MdPreview
                                :editorId="'room_' + room.id"
                                :modelValue="room.room_description"
                            />
                        </Dialog>
                    </div>
                </AccordionTab>
                <AccordionTab header="Attachments">
                    <div>
                        <div
                            v-for="file in roomAttachments"
                            :key="file.path"
                            class="list-group-item list-group-item-action mb-5 mt-3"
                        >
                            <div class="hstack justify-content-between">
                                <VTooltip>
                                    <span>{{
                                        helper.truncateFileName(
                                            helper.extractFileName(
                                                file.file_name,
                                            ),
                                            25,
                                        )
                                    }}</span>

                                    <template #popper>
                                        {{ file.file_name }}
                                    </template>
                                </VTooltip>
                                <div
                                    class="hstack gap-3 justify-content-center align-items-center"
                                >
                                    <div
                                        class="hstack gap-3 justify-content-center align-items-center"
                                    >
                                        <i
                                            v-if="imageFiles.includes(file)"
                                            class="bi bi-eye icon text-success"
                                            @click="showImage(file.file_path)"
                                        ></i>
                                        <i
                                            v-if="
                                                musicFiles.includes(file) &&
                                                currentFile !== file
                                            "
                                            class="bi bi-play-fill icon text-success"
                                            @click="handlePlaySpecify(file)"
                                        ></i>
                                        <i
                                            v-if="currentFile === file"
                                            class="bi bi-pause-fill icon text-success"
                                            @click="handlePause"
                                        ></i>
                                        <a :href="file.file_path"
                                            ><i
                                                class="bi bi-download icon text-dark"
                                            ></i
                                        ></a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <teleport to="body">
                            <LightBoxHelper
                                :currentImageDisplay="currentImageDisplay"
                            />
                        </teleport>
                    </div>
                </AccordionTab>
            </Accordion>

            <div v-show="musicFiles.length && currentFile">
                <MusicPlayer
                    :currentFile="currentFile"
                    :music="musicFiles"
                    class="text-dark"
                />
            </div>
        </Sidebar>
    </div>
</template>
<script setup>
import { computed, onMounted, ref } from "vue";
import LightBoxHelper from "@/Components/Helpers/LightBoxHelper.vue";
import MusicPlayer from "@/Components/MusicPlayer.vue";
import { useHelper } from "@/Services/helper.js";
import Dialog from "primevue/dialog";
import { MdPreview } from "md-editor-v3";
import Sidebar from "primevue/sidebar";
import Accordion from "primevue/accordion";
import AccordionTab from "primevue/accordiontab";

const props = defineProps(["room", "roomSettings", "roomAttachments"]);
const helper = useHelper();
const onlineUsers = computed(() => props.onlineUsers);
const isUserOnline = computed(() => props.isUserOnline);
const currentImageDisplay = ref(null);

const currentFile = ref(null);

const RoomDescriptionDialogVisible = ref(false);
const sidebarVisible = ref(false);

onMounted(() => {
    filterFiles(props.roomAttachments);
});

const showImage = (filePath) => {
    currentImageDisplay.value = {
        target: {
            src: filePath,
        },
    };
};

const handlePlaySpecify = (file) => {
    currentFile.value = file;
};
const handlePause = () => {
    currentFile.value = null;
};

const imageFiles = ref([]);
const musicFiles = ref([]);
const otherFiles = ref([]);

const imageFileTypes = new Set(["jpg", "jpeg", "png", "gif"]);
const musicFileTypes = new Set(["mp3", "wav", "flac", "ogg"]);

function filterFiles(roomAttachments) {
    for (let i = 0; i < roomAttachments.length; i++) {
        const file = roomAttachments[i];
        const fileType = file.file_name
            .substring(file.file_name.lastIndexOf(".") + 1)
            .toLowerCase();

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
