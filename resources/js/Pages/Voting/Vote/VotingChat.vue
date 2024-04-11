<template>
    <div>
        <div class="popup-chat" v-if="!showChatForm">
            <button class="btn btn-primary fs-5 position-relative" @click="toggleChatForm">
                <i class="bi bi-chat-dots"></i>
                <span
                    class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger"
                    v-if="unreadMessagesCount && unreadMessagesCount[room.id] > 0">
                {{
                        unreadMessagesCount[room.id]
                    }}
            </span>
            </button>
        </div>
        <div class="popup-chat" style="z-index: 999">
            <transition name="fade">
                <div v-if="showChatForm" class="card shadow">
                    <div class="card-header d-flex justify-content-between">
                        <span>Chat</span>
                        <span style="cursor: pointer" @click="toggleChatForm"><i class="bi bi-x-lg"></i></span>
                    </div>
                    <div class="card-body">
                        <div class="mb-3" style="min-height: 50vh">
                            <VotingMessage :messages="messages[room.id] ? messages[room.id] : {}"/>
                        </div>
                        <div>
                            <form @submit.prevent="sendMessage(newMessage)"
                                  class="card-footer text-muted d-flex justify-content-start align-items-center p-3">
                                <img :src="authUser.avatar"
                                     alt="avatar 3" style="width: 40px; height: 100%;">
                                <input type="text" class="form-control form-control-lg" id="exampleFormControlInput1"
                                       placeholder="Type message" v-model="newMessage">
                                <div v-if="isChatUpload">
                                    <label for="fileInput" style="cursor:pointer;">
                                        <i class="bi bi-paperclip"></i>
                                    </label>
                                    <input id="fileInput" type="file" @change="handleFileUpload" hidden/>
                                </div>
                                <button type="submit" class="ms-3 btn btn-primary" href="#"><i class="bi bi-send"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </transition>
        </div>
    </div>
</template>

<script setup>
import {computed, onMounted, onUnmounted, ref} from "vue";
import {usePage} from "@inertiajs/vue3";
import VotingMessage from "@/Pages/Voting/Vote/Chat/VotingMessage.vue";
import {useVotingChatStore} from "@/Stores/voting-chat.js";
import {useHelper} from "@/Services/helper.js";

const props = defineProps(['room', 'roomSettings', 'channelBroadcast'])
const helper = useHelper()

const channelBroadcast = props.channelBroadcast
const votingChatStore = useVotingChatStore()
const showChatForm = ref(false)
const authUser = computed(() => usePage().props.authUser.user);
const messages = computed(() => votingChatStore.messages);
const newMessage = ref(null)
const unreadMessagesCount = computed(() => votingChatStore.unreadCounts)

const isChatUpload = ref(props.roomSettings?.allow_voters_upload === 1)

function toggleChatForm() {
    showChatForm.value = !showChatForm.value;
    votingChatStore.markRead(props.room.id)
}

const handleFileUpload = (event) => {
    if (isChatUpload) {
        const uploadedFile = event.target.files[0];

        sendMessage(null, uploadedFile);
    }
};

const sendMessage = async (msg, file = null) => {
    const formData = new FormData();

    if (msg) {
        formData.append('message', helper.sanitizeAndTrim(msg));
    }

    if (file) {
        formData.append('file', file);
    }

    if (msg || file) {
        votingChatStore.storeMessage(props.room.id, formData);
    }

    newMessage.value = null;
};

const handleReceivedMessage = (e) => {
    if (e.broadcast_type === 'voting_chat') {
        if (!messages.value[props.room.id]) {
            messages.value[props.room.id] = [];
        }
        messages.value[props.room.id].push({user: e.user, message: e.message, plainMessage: e.plain_message});

        if (showChatForm.value === false) {
            votingChatStore.unreadCounts[props.room.id] = votingChatStore.unreadCounts[props.room.id] + 1;
        }
    }
};

const setupEchoListeners = () => {
    if (authUser.value) {
        Echo.private(channelBroadcast.channelName).listen(channelBroadcast.eventName, handleReceivedMessage);
    }
};

onMounted(async () => {
    setupEchoListeners()

    if (props.roomSettings?.chat_messages_saved === 1) {
        await votingChatStore.fetchMessages(props.room.id);
    } else {
        votingChatStore.clearMessages(props.room.id);
    }
})
</script>

<style scoped>
.popup-chat {
    position: fixed;
    bottom: 5vh;
    right: 3vw;
}
</style>
