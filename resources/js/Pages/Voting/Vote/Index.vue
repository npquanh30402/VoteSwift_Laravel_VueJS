<template>
    <button class="btn btn-info vertical-button" type="button" @click="openSidebar(bsOffcanvas)">Sidebar
    </button>
    <BaseOffcanvas id="sidebar" title="Sidebar">
        <div class="list-group">
            <div class="accordion" id="SidebarAccordion">
                <button class="list-group-item list-group-item-action" @click="openModal(modals.RoomDescriptionModal)">
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

                    <div id="AttachmentCollapse" class="accordion-collapse collapse" data-bs-parent="#SidebarAccordion">
                        <div class="accordion-body">
                            <div
                                class="list-group vstack justify-content-between align-items-center">
                                <div v-for="file in roomAttachments" :key="file.path"
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
                    </div>
                </div>
                <div class="accordion-item" v-if="roomSettings.invitation_only">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                data-bs-target="#VoterCollapse" aria-expanded="false" aria-controls="VoterCollapse">
                            <span class="me-3">Voters</span>
                            <span style="font-size: 0.8rem">
                                <i class="bi bi-circle-fill text-success animate__animated animate__flash animate__infinite animate__slow"
                                   style="font-size: 0.6rem"></i>
                                {{ onlineUsers.length }} Online / {{ invitedUsers.length }} Invited
                            </span>
                        </button>
                    </h2>

                    <div id="VoterCollapse" class="accordion-collapse collapse" data-bs-parent="#SidebarAccordion">
                        <div class="accordion-body">
                            <div v-for="user in invitedUsers" :key="user.id"
                                 :class="{ 'opacity-100': isUserOnline(user), 'opacity-50': !isUserOnline(user) }"
                                 class="mb-4">
                                <a :href="route('user.profile', user.id)" target="_blank"
                                   class="d-flex text-decoration-none align-items-center text-dark">
                                    <img class="rounded-circle me-2 img-fluid" :src="user.avatar" alt="" width="48">
                                    <p><strong>{{ user.username }}</strong></p>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </BaseOffcanvas>
    <BaseModal id="RoomDescriptionModal" title="Room Description" class="modal-dialog-scrollable"
               data-bs-backdrop="true">
        <MdPreview :editorId="'room_' + room.id" :modelValue="room.room_description"/>
    </BaseModal>

    <VotingChat :room="room" style="z-index: 999"/>
    <h3>Time remaining: </h3>
    <VotingClock :date="room.end_time"/>

    <transition name="fade" mode="out-in">
        <component :is="tabs[currentTab]" :room="room" :roomSettings="roomSettings" :questions="questions"
                   :isReadyToStart="isReadyToStart"
                   @switch-tab="currentTab = $event" @start-voting="startVoting"
                   v-if="roomSettings.invitation_only"></component>

        <component :is="tabs[currentTab]" :room="room" :roomSettings="roomSettings" :questions="questions"
                   @switch-tab="currentTab = $event" v-else></component>
    </transition>
</template>

<script setup>
import BaseOffcanvas from "@/Components/BaseOffcanvas.vue";
import {onMounted, reactive, ref} from "vue";
import * as bootstrap from 'bootstrap'
import Welcome from "@/Pages/Voting/Vote/Welcome.vue";
import StartVoting from "@/Pages/Voting/Vote/StartVoting.vue";
import {route} from "ziggy-js";
import VotingClock from "@/Components/VotingClock.vue";
import VueEasyLightbox from "vue-easy-lightbox";
import BaseModal from "@/Components/BaseModal.vue";
import {MdPreview} from "md-editor-v3";
import VotingChat from "@/Pages/Voting/Vote/VotingChat.vue";

const props = defineProps(['questions', 'room', 'roomSettings', 'invitedUsers', 'roomAttachments'])
// const currentTab = ref(props.room.vote_started === 1 ? 'StartVoting' : 'Welcome');
const currentTab = ref('Welcome');

const tabs = {
    Welcome,
    StartVoting,
}
const modals = reactive({
    RoomDescriptionModal: 'RoomDescriptionModal'
})

onMounted(() => {
    modals.RoomDescriptionModal = new bootstrap.Modal(document.getElementById(modals.RoomDescriptionModal));
})

function openModal(modal) {
    modal.show()
}

const onlineUsers = ref([]);
const isReadyToStart = ref(props.roomSettings.wait_for_voters === 0);
const invitedUsers = ref(props.invitedUsers);

const isUserOnline = (user) => onlineUsers.value.some(onlineUser => onlineUser.id === user.id);

function startVoting() {
    axios.get(route('api.room.vote.start', props.room.id))
        .then(function (response) {
            // currentTab.value = 'StartVoting';
            console.log(response.data)
        })
}

if (props.roomSettings.wait_for_voters === 1) {

    const handleHere = (users) => {
        onlineUsers.value = users;
        isReadyToStart.value = onlineUsers.value.length >= invitedUsers.value.length;
    };

    const handleJoining = (user) => {
        onlineUsers.value.push(user);
        isReadyToStart.value = onlineUsers.value.length >= invitedUsers.value.length;
    };

    const handleLeaving = (user) => {
        onlineUsers.value = onlineUsers.value.filter((u) => u.id !== user.id);
        isReadyToStart.value = onlineUsers.value.length >= invitedUsers.value.length;
    };

    Echo.join('voting.' + props.room.id)
        .here(handleHere)
        .joining(handleJoining)
        .leaving(handleLeaving);

    Echo.private('voting.' + props.room.id).listen('VotingProcess', (e) => {
        if (e.room.vote_started) {
            currentTab.value = 'StartVoting';
        }
    })
}

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
const otherFiles = ref([]);

const getFileType = (fileName) => {
    return fileName.substring(fileName.lastIndexOf('.') + 1).toLowerCase();
};
const isImageFile = (fileName) => {
    const fileType = getFileType(fileName);
    return ['jpg', 'jpeg', 'png', 'gif'].includes(fileType);
};

for (let i = 0; i < props.roomAttachments.length; i++) {
    const file = props.roomAttachments[i];
    const fileType = getFileType(file.file_name);

    if (isImageFile(fileType)) {
        imageFiles.value.push(file);
    } else {
        otherFiles.value.push(file);
    }
}

const bsOffcanvas = ref(null);

onMounted(() => {
    bsOffcanvas.value = new bootstrap.Offcanvas('#sidebar')
})

function openSidebar(modal) {
    modal.show();
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
