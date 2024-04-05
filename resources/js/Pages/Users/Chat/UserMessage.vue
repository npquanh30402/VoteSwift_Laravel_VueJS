<template>
    <div class="card" id="chat2">
        <div class="card-body overflow-auto" ref="chatContainer" data-mdb-perfect-scrollbar="true"
             style="position: relative; height: 400px">
            <div v-for="(message, index) in databaseMessages" :key="'realtime_' + index">
                <div class="hstack justify-content-start mb-4 pt-1" v-if="!isYou(message.sender_id)">
                    <div class="d-flex flex-column gap-2">
                        <img :src="message.avatar" :alt="'avatar_' + message.id"
                             class="img-fluid rounded-circle" style="width: 45px; height: 100%;"
                             @click="goToProfile(message.sender_id)">
                        <span>{{ message.sender }}</span>
                    </div>
                    <div>
                        <div v-if="message.file" class="ms-3">
                            <img class="img-fluid" style="width: 10rem" :src="message.file"
                                 alt="file" v-if="isImage(message.file)" @click="showSingle">
                            <div class="vstack" v-else>
                                <a :href="message.file" class="btn btn-danger" download>Download File</a>
                                <span class="text-muted">{{
                                        truncateFileName(extractFileName(message.file), maxLength)
                                    }}</span>
                            </div>
                        </div>
                        <p class="small p-2 ms-3 mb-1 rounded-3" style="background-color: #f5f6f7;" v-else>
                            {{ message.message }}</p>
                        <p class="small ms-3 mb-3 rounded-3 text-muted">{{
                                formatHour(message.send_date)
                            }}</p>
                    </div>
                </div>
                <div class="hstack justify-content-end mb-4 pt-1" v-else>
                    <div>
                        <div v-if="message.file" class="me-3">
                            <img class="img-fluid" style="width: 10rem" :src="message.file"
                                 alt="file" v-if="isImage(message.file)" @click="showSingle">
                            <div class="vstack" v-else>
                                <a :href="message.file" class="btn btn-danger" download>Download File</a>
                                <span class="text-muted">{{
                                        truncateFileName(extractFileName(message.file), maxLength)
                                    }}</span>
                            </div>
                        </div>
                        <p class="small p-2 me-3 mb-1 text-white rounded-3 bg-primary" v-else>{{
                                message.message
                            }}</p>
                        <p class="small me-3 mb-3 rounded-3 text-muted d-flex justify-content-end">
                            {{ formatHour(message.send_date) }}</p>
                    </div>
                    <div class="d-flex flex-column gap-2">
                        <img :src="message.avatar" :alt="'avatar_' + message.id"
                             class="img-fluid rounded-circle" style="width: 45px; height: 100%;"
                             @click="goToProfile(message.sender_id)">
                        <span>{{ message.sender }}</span>
                    </div>
                </div>
            </div>
            <div v-for="(message, index) in messages" :key="'realtime_' + index">
                <div class="hstack justify-content-start mb-4 pt-1" v-if="!isYou(message.user.id)">
                    <div class="d-flex flex-column gap-2">
                        <img :src="message.user.avatar" :alt="'avatar_' + message.user.id"
                             class="img-fluid rounded-circle" style="width: 45px; height: 100%;"
                             @click="goToProfile(message.user.id)">
                        <span>{{ message.user.username }}</span>
                    </div>
                    <div>
                        <div v-if="message.messageObj.file" class="ms-3">
                            <img class="img-fluid" style="width: 10rem" :src="message.messageObj.file"
                                 alt="file" v-if="isImage(message.messageObj.file)" @click="showSingle">
                            <div class="vstack" v-else>
                                <a :href="message.messageObj.file" class="btn btn-danger" download>Download File</a>
                                <span class="text-muted">{{
                                        truncateFileName(extractFileName(message.messageObj.file), maxLength)
                                    }}</span>
                            </div>
                        </div>
                        <p class="small p-2 ms-3 mb-1 rounded-3" style="background-color: #f5f6f7;" v-else>
                            {{ message.message }}</p>
                        <p class="small ms-3 mb-3 rounded-3 text-muted">
                            {{ formatHour(message.messageObj.created_at) }}</p>
                    </div>
                </div>
                <div class="hstack justify-content-end mb-4 pt-1" v-else>
                    <div>
                        <div v-if="message.messageObj.file" class="me-3">
                            <img class="img-fluid" style="width: 10rem" :src="message.messageObj.file"
                                 alt="file" v-if="isImage(message.messageObj.file)" @click="showSingle">
                            <div class="vstack" v-else>
                                <a :href="message.messageObj.file" class="btn btn-danger" download>Download File</a>
                                <span class="text-muted">{{
                                        truncateFileName(extractFileName(message.messageObj.file), maxLength)
                                    }}</span>
                            </div>
                        </div>
                        <p class="small p-2 me-3 mb-1 text-white rounded-3 bg-primary" v-else>{{
                                message.message
                            }}</p>
                        <p class="small me-3 mb-3 rounded-3 text-muted d-flex justify-content-end">
                            {{ formatHour(message.messageObj.created_at) }}</p>
                    </div>
                    <div class="d-flex flex-column gap-2">
                        <img :src="message.user.avatar" :alt="'avatar_' + message.user.id"
                             class="img-fluid rounded-circle" style="width: 45px; height: 100%;"
                             @click="goToProfile(message.user.id)">
                        <span>{{ message.user.username }}</span>
                    </div>
                </div>
            </div>

            <!--                            <div class="divider d-flex align-items-center mb-4">-->
            <!--                                <p class="text-center mx-3 mb-0" style="color: #a2aab7;">Today</p>-->
            <!--                            </div>-->

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
</template>

<script setup>
import {computed, onMounted, onUpdated, ref} from 'vue';
import {router, usePage} from "@inertiajs/vue3";
import date from 'date-and-time';
import VueEasyLightbox from 'vue-easy-lightbox'
import {route} from "ziggy-js";
import {useChatStore} from "@/Stores/chat.js";

const props = defineProps(['messages', 'currentRecipient'])

const ChatStore = useChatStore()

const currentRecipient = ref(props?.currentRecipient)
// const databaseMessages = ref(null)
const databaseMessages = computed(() => ChatStore.messages[currentRecipient.value.id] ?? [])
const messages = computed(() => props.messages)

const fetchMessages = async () => {
    try {
        const response = await axios.get(route('api.user.chat.index', currentRecipient.value.id));
        databaseMessages.value = response.data.decryptedMessages;
    } catch (error) {
        console.error('Error fetching messages:', error);
    }
};

onMounted(async () => {
    if (currentRecipient.value) {
        ChatStore.fetchMessages(currentRecipient.value.id);
        ChatStore.markReadAll(currentRecipient.value.id)
    }
})

const authUser = computed(() => usePage().props.authUser);

const chatContainer = ref(null);

const isYou = (msgUserId) => {
    return msgUserId === authUser.value.user.id
}

function goToProfile(userId) {
    router.get(route('user.profile', userId))
}

function formatHour(dt) {
    const fdt = new Date(dt);
    return date.format(fdt, 'HH:mm');
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

const scrollToBottom = () => {
    chatContainer.value.scrollTop = chatContainer.value.scrollHeight;
}

onUpdated(() => {
    scrollToBottom();
})

onMounted(() => {
    scrollToBottom()
})
</script>

<style scoped>
img {
    cursor: pointer;
}

.divider:after,
.divider:before {
    content: "";
    flex: 1;
    height: 1px;
    background: #eee;
}
</style>
