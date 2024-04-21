<template>
    <div class="row gx-3">
        <div class="d-flex flex-column gap-3">
            <div
                class="hstack gap-3 align-items-center justify-content-between"
            >
                <div
                    :class="{ 'un-interactive': isPublish }"
                    class="form-check form-switch"
                >
                    <input
                        id="chatSwitch"
                        v-model="isChatEnable"
                        class="form-check-input"
                        role="switch"
                        type="checkbox"
                        @change="toggleChat"
                    />
                    <label class="form-check-label" for="passwordSwitch"
                        >Enable Chat</label
                    >
                </div>
                <div :class="[isChatEnable ? '' : 'un-interactive']">
                    <button
                        class="btn btn-primary opacity-100 position-relative"
                        disabled
                        type="button"
                    >
                        Realtime
                        <span
                            :class="{
                                ' animate__animated animate__flash animate__infinite animate__slow':
                                    isChatEnable,
                            }"
                            class="position-absolute top-0 start-100 translate-middle p-2 bg-danger border border-light rounded-circle"
                        ></span>
                    </button>
                </div>
            </div>
            <div
                :class="[isChatEnable ? '' : 'un-interactive']"
                class="row g-3"
            >
                <div class="col-md-3">
                    <div
                        class="card border border-3 border-success-subtle p-3 shadow-sm form-group d-flex flex-column"
                    >
                        <div
                            class="hstack gap-3 align-items-center justify-content-between"
                        >
                            <div
                                :class="{ 'un-interactive': isPublish }"
                                class="form-check form-switch"
                            >
                                <input
                                    id="chatHistorySwitch"
                                    v-model="isChatHistory"
                                    class="form-check-input"
                                    role="switch"
                                    type="checkbox"
                                    @change="toggleChatHistory"
                                />
                                <label
                                    class="form-check-label"
                                    for="passwordSwitch"
                                    >Save Chat History</label
                                >
                            </div>
                            <VTooltip>
                                <i class="bi bi-exclamation-circle"></i>

                                <template #popper>
                                    The messages will be encrypted by default
                                </template>
                            </VTooltip>
                        </div>
                        <div class="hstack gap-3 align-items-center">
                            <div
                                :class="{ 'un-interactive': isPublish }"
                                class="form-check form-switch"
                            >
                                <input
                                    id="chatUploadSwitch"
                                    v-model="isChatUpload"
                                    class="form-check-input"
                                    role="switch"
                                    type="checkbox"
                                    @change="toggleChatUpload"
                                />
                                <label
                                    class="form-check-label"
                                    for="passwordSwitch"
                                    >Allow Voters To Upload</label
                                >
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-9">
                    <BaseNoContent v-if="!isChatEnable" />
                    <VotingMessage
                        v-else
                        :messages="messages[room.id] ? messages[room.id] : {}"
                    />
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { usePage } from "@inertiajs/vue3";
import { computed, onMounted, onUnmounted, ref, watch } from "vue";
import { useVotingChatStore } from "@/Stores/voting-chat.js";
import { useVotingSettingStore } from "@/Stores/voting-settings.js";
import VotingMessage from "@/Pages/Voting/Vote/Chat/VotingMessage.vue";
import BaseNoContent from "@/Components/BaseNoContent.vue";

const props = defineProps(["room"]);
const isPublish = computed(() => props.room.is_published === 1);
const votingSettingStore = useVotingSettingStore();
const votingChatStore = useVotingChatStore();

const authUser = computed(() => usePage().props.authUser.user);
const roomSettings = computed(() => votingSettingStore.settings[props.room.id]);
const messages = computed(() => votingChatStore.messages);

const isChatEnable = ref(false);
const isChatHistory = ref(false);
const isChatUpload = ref(false);

const channelBroadcast = {
    channelName: "voting.process." + props.room.id,
    eventName: "VotingProcess",
};
let echoListenerInitialized = false;

const toggleChat = () => {
    updateSetting("chat_enabled", isChatEnable.value);

    if (isChatEnable.value === false) {
        leaveChannel();
    } else {
        setupEchoListeners();
    }
};

const toggleChatHistory = () => {
    updateSetting("chat_messages_saved", isChatHistory.value);

    if (isChatHistory.value) {
        votingChatStore.fetchMessages(props.room.id);
    } else {
        votingChatStore.clearMessages(props.room.id);
    }
};

const toggleChatUpload = () => {
    updateSetting("allow_voters_upload", isChatUpload.value);
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
    }
};

const setupEchoListeners = () => {
    if (authUser.value.id === props.room.user_id) {
        Echo.private(channelBroadcast.channelName).listen(
            channelBroadcast.eventName,
            handleReceivedMessage,
        );
        echoListenerInitialized = true;
    }
};

const leaveChannel = () => {
    Echo.leave(channelBroadcast.channelName);
    echoListenerInitialized = false;
};

const updateSetting = (key, value) => {
    const formData = new FormData();
    formData.append(key, value);

    votingSettingStore.updateSettings(props.room.id, formData);
};

watch(
    () => roomSettings,
    () => {
        isChatEnable.value = roomSettings.value?.chat_enabled === 1;
        isChatHistory.value = roomSettings.value?.chat_messages_saved === 1;
        isChatUpload.value = roomSettings.value?.allow_voters_upload === 1;
    },
);

onMounted(() => {
    votingSettingStore.fetchSettings(props.room.id).then(() => {
        if (roomSettings.value) {
            isChatEnable.value = roomSettings.value?.chat_enabled === 1;
            isChatHistory.value = roomSettings.value?.chat_messages_saved === 1;
            isChatUpload.value = roomSettings.value?.allow_voters_upload === 1;
        }

        if (isChatEnable.value) {
            setupEchoListeners();
        }

        if (isChatHistory.value) {
            votingChatStore.fetchMessages(props.room.id);
        }
    });
});

onUnmounted(() => {
    if (echoListenerInitialized) {
        leaveChannel();
    }
});
</script>
