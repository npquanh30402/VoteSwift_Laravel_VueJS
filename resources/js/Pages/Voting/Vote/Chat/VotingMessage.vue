<template>
    <div class="card border border-2 border-success rounded p-3">
        <div class="card-body overflow-auto" ref="chatContainer" data-mdb-perfect-scrollbar="true"
             style="position: relative; height: 400px">
            <div v-for="(message, index) in messages" :key="'realtime_' + index">
                <div class="hstack justify-content-start mb-4 pt-1" v-if="!isYou(message.user.id)">
                    <div class="d-flex flex-column gap-2">
                        <a :href="route('user.profile', message.user.id)" target="_blank">
                            <img :src="message.user.avatar" :alt="'avatar_' + message.user.id"
                                 class="img-fluid rounded-circle" style="width: 45px; height: 100%;">
                        </a>
                        <span>{{ message.user.username }}</span>
                    </div>
                    <div>
                        <div v-if="message.message.file" class="ms-3">
                            <img class="img-fluid" style="width: 10rem" :src="message.message.file"
                                 alt="file" v-if="isImage(message.message.file)" @click="showImage">
                            <div class="vstack" v-else>
                                <a :href="message.message.file" class="btn btn-danger" download>Download File</a>
                                <span class="text-muted">{{
                                        truncateFileName(extractFileName(message.message.file), maxLength)
                                    }}</span>
                            </div>
                        </div>
                        <p class="small p-2 ms-3 mb-1 rounded-3" style="background-color: #f5f6f7;" v-else>
                            {{ message.plainMessage }}</p>
                        <p class="small ms-3 mb-3 rounded-3 text-muted">
                            {{ formatHour(message.message.created_at) }}</p>
                    </div>
                </div>
                <div class="hstack justify-content-end mb-4 pt-1" v-else>
                    <div>
                        <div v-if="message.message.file" class="me-3">
                            <img class="img-fluid" style="width: 10rem" :src="message.message.file"
                                 alt="file" v-if="isImage(message.message.file)" @click="showImage">
                            <div class="vstack" v-else>
                                <a :href="message.message.file" class="btn btn-danger" download>Download File</a>
                                <span class="text-muted">{{
                                        truncateFileName(extractFileName(message.message.file), maxLength)
                                    }}</span>
                            </div>
                        </div>
                        <p class="small p-2 me-3 mb-1 text-white rounded-3 bg-primary" v-else>{{
                                message.plainMessage
                            }}</p>
                        <p class="small me-3 mb-3 rounded-3 text-muted d-flex justify-content-end">
                            {{ formatHour(message.message.created_at) }}</p>
                    </div>
                    <div class="d-flex flex-column gap-2">
                        <a :href="route('user.profile', message.user.id)" target="_blank">
                            <img :src="message.user.avatar" :alt="'avatar_' + message.user.id"
                                 class="img-fluid rounded-circle" style="width: 45px; height: 100%;">
                        </a>
                        <span>{{ message.user.username }}</span>
                    </div>
                </div>
            </div>
        </div>
        <teleport to="body">
            <LightBoxHelper :currentImageDisplay="currentImageDisplay"/>
        </teleport>
    </div>
</template>

<script setup>
import {computed, onActivated, onMounted, onUpdated, ref} from 'vue';
import {usePage} from "@inertiajs/vue3";
import {route} from "ziggy-js";
import LightBoxHelper from "@/Components/Helpers/LightBoxHelper.vue";

const props = defineProps(['messages'])
const authUser = computed(() => usePage().props.authUser.user);

const messages = computed(() => props.messages)
const chatContainer = ref(null);
const currentImageDisplay = ref(null)

const showImage = (e) => {
    currentImageDisplay.value = e;
}

const isYou = (msgUserId) => {
    return msgUserId === authUser.value.id
}

function formatHour(dt) {
    const fdt = new Date(dt);
    return fdt.toLocaleString('en-US', {hour: 'numeric', minute: 'numeric', hour12: true});
}

function isImage(file) {
    const extension = file.split('.').pop().toLowerCase();

    const imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'bmp'];

    return imageExtensions.includes(extension);
}

function extractFileName(filePath) {
    const parts = filePath.split('/');

    return parts[parts.length - 1];
}

const maxLength = 20;

function truncateFileName(fileName, maxLength) {
    if (fileName.length <= maxLength) {
        return fileName;
    } else {
        return '...' + fileName.substr(fileName.length - maxLength);
    }
}

const scrollToBottom = () => chatContainer.value.scrollTop = chatContainer.value.scrollHeight

onUpdated(scrollToBottom)
onMounted(scrollToBottom)
onActivated(scrollToBottom)
</script>

<style scoped>
img {
    cursor: pointer;
}
</style>
