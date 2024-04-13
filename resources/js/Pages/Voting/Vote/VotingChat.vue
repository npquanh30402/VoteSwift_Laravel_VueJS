<template>
    <div>
        <div v-if="!showChatForm" class="popup-chat">
            <button
                class="btn btn-primary fs-5 position-relative"
                @click="toggleChatForm"
            >
                <i class="bi bi-chat-dots"></i>
                <span
                    v-if="
                        unreadMessagesCount && unreadMessagesCount[room.id] > 0
                    "
                    class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger"
                >
                    {{ unreadMessagesCount[room.id] }}
                </span>
            </button>
        </div>
        <div class="popup-chat" style="z-index: 999">
            <transition name="fade">
                <div
                    v-if="showChatForm"
                    ref="el"
                    :style="style"
                    class="card shadow"
                    style="position: fixed; z-index: 1"
                >
                    <div class="card-header d-flex justify-content-between">
                        <span>Chat</span>
                        <span style="cursor: pointer" @click="toggleChatForm"
                            ><i class="bi bi-x-lg"></i
                        ></span>
                    </div>
                    <div class="card-body">
                        <div class="mb-3" style="min-height: 50vh">
                            <VotingMessage
                                :messages="
                                    messages[room.id] ? messages[room.id] : {}
                                "
                            />
                        </div>
                        <div>
                            <form
                                class="card-footer text-muted d-flex justify-content-start align-items-center p-3"
                                @submit.prevent="sendMessage(newMessage)"
                            >
                                <img
                                    :src="authUser.avatar"
                                    alt="avatar 3"
                                    style="width: 40px; height: 100%"
                                />
                                <input
                                    id="exampleFormControlInput1"
                                    v-model="newMessage"
                                    class="form-control form-control-lg"
                                    placeholder="Type message"
                                    type="text"
                                />
                                <div v-if="isChatUpload">
                                    <label
                                        for="fileInput"
                                        style="cursor: pointer"
                                    >
                                        <i class="bi bi-paperclip"></i>
                                    </label>
                                    <input
                                        id="fileInput"
                                        hidden
                                        type="file"
                                        @change="handleFileUpload"
                                    />
                                </div>
                                <button
                                    class="ms-3 btn btn-primary"
                                    href="#"
                                    type="submit"
                                >
                                    <i class="bi bi-send"></i>
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
import { computed, onMounted, ref } from "vue";
import { usePage } from "@inertiajs/vue3";
import VotingMessage from "@/Pages/Voting/Vote/Chat/VotingMessage.vue";
import { useVotingChatStore } from "@/Stores/voting-chat.js";
import { useHelper } from "@/Services/helper.js";
import { useDraggable } from "@vueuse/core";

const props = defineProps(["room", "roomSettings", "channelBroadcast"]);
const helper = useHelper();

const el = ref();
const { x, y, style } = useDraggable(el, {
    initialValue: { x: 1000, y: 150 },
});

const channelBroadcast = props.channelBroadcast;
const votingChatStore = useVotingChatStore();
const showChatForm = ref(false);
const authUser = computed(() => usePage().props.authUser.user);
const messages = computed(() => votingChatStore.messages);
const newMessage = ref(null);
const unreadMessagesCount = computed(() => votingChatStore.unreadCounts);

const isChatUpload = ref(props.roomSettings?.allow_voters_upload === 1);

function toggleChatForm() {
    showChatForm.value = !showChatForm.value;
    votingChatStore.markRead(props.room.id);
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
        formData.append("message", helper.sanitizeAndTrim(msg));
    }

    if (file) {
        formData.append("file", file);
    }

    if (msg || file) {
        votingChatStore.storeMessage(props.room.id, formData);
    }

    newMessage.value = null;
};

const handleReceivedMessage = (e) => {
    if (e.broadcast_type === "voting_chat") {
        if (!messages.value[props.room.id]) {
            messages.value[props.room.id] = [];
        }
        messages.value[props.room.id].push({
            user: e.user,
            message: e.message,
            plainMessage: e.plain_message,
        });

        if (showChatForm.value === false) {
            votingChatStore.unreadCounts[props.room.id] =
                votingChatStore.unreadCounts[props.room.id] + 1;
        }
    }
};

const setupEchoListeners = () => {
    if (authUser.value) {
        Echo.private(channelBroadcast.channelName).listen(
            channelBroadcast.eventName,
            handleReceivedMessage,
        );
    }
};

onMounted(async () => {
    setupEchoListeners();

    if (props.roomSettings?.chat_messages_saved === 1) {
        await votingChatStore.fetchMessages(props.room.id);
    } else {
        votingChatStore.clearMessages(props.room.id);
    }
});
</script>

<style scoped>
.popup-chat {
    position: fixed;
    bottom: 5vh;
    right: 3vw;
}
</style>
