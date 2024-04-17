<template>
    <div class="card border border-2 border-success rounded p-3">
        <div
            ref="chatContainer"
            class="card-body overflow-auto"
            style="position: relative; height: 400px"
        >
            <template
                v-for="message in receiverMessages"
                :key="generateUniqueKey"
            >
                <div
                    v-if="!isYou(message.user.id)"
                    class="hstack justify-content-start mb-4 pt-1"
                >
                    <div class="d-flex flex-column gap-2">
                        <a
                            :href="route('user.profile', message.user.id)"
                            target="_blank"
                        >
                            <img
                                :alt="'avatar_' + message.user.id"
                                :src="message.user.avatar"
                                class="img-fluid rounded-circle"
                                style="width: 45px; height: 100%"
                            />
                        </a>
                        <span>{{ message.user.username }}</span>
                    </div>
                    <div>
                        <div v-if="message.message.file" class="ms-3">
                            <img
                                v-if="isImage(message.message.file)"
                                :src="message.message.file"
                                alt="file"
                                class="img-fluid"
                                style="width: 10rem"
                                @click="showImage"
                            />
                            <div v-else class="vstack">
                                <a
                                    :href="message.message.file"
                                    class="btn btn-danger"
                                    download
                                    >Download File</a
                                >
                                <span class="text-muted">{{
                                    truncateFileName(
                                        extractFileName(message.message.file),
                                        maxLength,
                                    )
                                }}</span>
                            </div>
                        </div>
                        <p
                            v-else
                            class="small p-2 ms-3 mb-1 rounded-3"
                            style="background-color: #f5f6f7"
                        >
                            {{ message.message.content }}
                        </p>
                        <p class="small ms-3 mb-3 rounded-3 text-muted">
                            {{ formatHour(message.message.created_at) }}
                        </p>
                    </div>
                </div>
                <div v-else class="hstack justify-content-end mb-4 pt-1">
                    <div>
                        <div v-if="message.message.file" class="me-3">
                            <img
                                v-if="isImage(message.message.file)"
                                :src="message.message.file"
                                alt="file"
                                class="img-fluid"
                                style="width: 10rem"
                                @click="showImage"
                            />
                            <div v-else class="vstack">
                                <a
                                    :href="message.message.file"
                                    class="btn btn-danger"
                                    download
                                    >Download File</a
                                >
                                <span class="text-muted">{{
                                    truncateFileName(
                                        extractFileName(message.message.file),
                                        maxLength,
                                    )
                                }}</span>
                            </div>
                        </div>
                        <p
                            v-else
                            class="small p-2 me-3 mb-1 text-white rounded-3 bg-primary"
                        >
                            {{ message.message.content }}
                        </p>
                        <p
                            class="small me-3 mb-3 rounded-3 text-muted d-flex justify-content-end"
                        >
                            {{ formatHour(message.message.created_at) }}
                        </p>
                    </div>
                    <div class="d-flex flex-column gap-2">
                        <a
                            :href="route('user.profile', message.user.id)"
                            target="_blank"
                        >
                            <img
                                :alt="'avatar_' + message.user.id"
                                :src="message.user.avatar"
                                class="img-fluid rounded-circle"
                                style="width: 45px; height: 100%"
                            />
                        </a>
                        <span>{{ message.user.username }}</span>
                    </div>
                </div>
            </template>
        </div>
        <teleport to="body">
            <LightBoxHelper :currentImageDisplay="currentImageDisplay" />
        </teleport>
    </div>
</template>

<script setup>
import { computed, onActivated, onMounted, onUpdated, ref } from "vue";
import { usePage } from "@inertiajs/vue3";
import { route } from "ziggy-js";
import LightBoxHelper from "@/Components/Helpers/LightBoxHelper.vue";
import { useHelper } from "@/Services/helper.js";
import { useUserChatStore } from "@/Stores/user-chat.js";

const props = defineProps(["currentReceiver"]);
const authUser = computed(() => usePage().props.authUser.user);
const helper = useHelper();

const { messages, fetchMessages } = useUserChatStore();

const receiverMessages = computed(() => messages[props.currentReceiver?.id]);
const chatContainer = ref(null);
const currentImageDisplay = ref(null);
const maxLength = 20;

const showImage = (e) => (currentImageDisplay.value = e);
const isYou = (msgUserId) => msgUserId === authUser.value.id;

const formatHour = helper.formatHour;
const isImage = helper.isImage;
const extractFileName = helper.extractFileName;
const truncateFileName = helper.truncateFileName;
const generateUniqueKey = helper.generateUniqueKey;

const scrollToBottom = () =>
    (chatContainer.value.scrollTop = chatContainer.value.scrollHeight);

onUpdated(() => {
    scrollToBottom();
});
onMounted(() => {
    fetchMessages(authUser.value.id, props.currentReceiver?.id);
    scrollToBottom();
});
onActivated(() => {
    scrollToBottom();
});
</script>

<style scoped>
img {
    cursor: pointer;
}
</style>
