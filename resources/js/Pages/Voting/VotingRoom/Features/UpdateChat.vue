<template>
    <div class="row gx-3">
        <div class="d-flex flex-column gap-3">
            <div class="hstack gap-3 align-items-center justify-content-between">
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" role="switch" id="chatSwitch"
                           @change="toggleChat" v-model="isChatEnable">
                    <label class="form-check-label" for="passwordSwitch">Enable Chat</label>
                </div>
                <div :class="[isChatEnable ? '' : 'un-interactive']">
                    <button type="button" class="btn btn-primary opacity-100 position-relative" disabled>
                        Realtime
                        <span
                            class="position-absolute top-0 start-100 translate-middle p-2 bg-danger border border-light rounded-circle"
                            :class="{' animate__animated animate__flash animate__infinite animate__slow': isChatEnable}"></span>
                    </button>
                </div>
            </div>
            <div class="row g-3" :class="[isChatEnable ? '' : 'un-interactive']">
                <div class="col-md-3">
                    <div
                        class="card border border-3 border-success-subtle p-3 shadow-sm form-group d-flex flex-column">
                        <div class="hstack gap-3 align-items-center justify-content-between">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" role="switch"
                                       id="chatHistorySwitch"
                                       @change="toggleChatHistory" v-model="isChatHistory">
                                <label class="form-check-label" for="passwordSwitch">Save Chat History</label>
                            </div>
                            <VTooltip>
                                <i
                                    class="bi bi-exclamation-circle"></i>

                                <template #popper>
                                    The messages will be encrypted by default
                                </template>
                            </VTooltip>
                        </div>
                        <div class="hstack gap-3 align-items-center">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" role="switch"
                                       id="chatUploadSwitch"
                                       @change="toggleChatUpload" v-model="isChatUpload">
                                <label class="form-check-label" for="passwordSwitch">Allow Voters To
                                    Upload</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-9">
                    <VotingMessage :messages="messages[room.id] ? messages[room.id] : {}"/>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import {usePage} from "@inertiajs/vue3";
import {computed, onMounted, ref, watch} from "vue";
import VotingChat from "@/Pages/Voting/Vote/VotingChat.vue";
import VotingMessage from "@/Pages/Voting/Vote/Chat/VotingMessage.vue";
import {useVotingChatStore} from "@/Stores/voting-chat.js";
import {useVotingSettingStore} from "@/Stores/voting-settings.js";
import {useToast} from "vue-toast-notification";

const props = defineProps(['room'])

const $toast = useToast();
const roomSettings = computed(() => votingSettingStore.settings[props.room.id])
const votingChatStore = useVotingChatStore()
const votingSettingStore = useVotingSettingStore()

const authUser = computed(() => usePage().props.authUser.user);
const messages = computed(() => votingChatStore.messages);

const isChatEnable = ref(false)
const isChatHistory = ref(false)
const isChatUpload = ref(false)

watch(() => roomSettings.value, () => {
    isChatEnable.value = roomSettings.value?.chat_enabled === 1
    isChatHistory.value = roomSettings.value?.chat_messages_saved === 1
    isChatUpload.value = roomSettings.value?.allow_voters_upload === 1
})

const updateSetting = (key, value) => {
    const formData = new FormData();
    formData.append(key, value);

    votingSettingStore.updateSettings(props.room.id, formData)

    $toast.success('Updated successfully')
}

const toggleChat = () => {
    updateSetting('chat_enabled', isChatEnable.value)
}

const toggleChatHistory = () => {
    updateSetting('chat_messages_saved', isChatHistory.value)

    if (isChatHistory.value) {
        votingChatStore.fetchMessages(props.room.id);
    } else {
        votingChatStore.clearMessages(props.room.id)
    }
}

const toggleChatUpload = () => {
    updateSetting('allow_voters_upload', isChatUpload.value)
}

const handleReceivedMessage = (e) => {
    if (!messages.value[props.room.id]) {
        messages.value[props.room.id] = [];
    }
    messages.value[props.room.id].push({user: e.user, message: e.message, plainMessage: e.plain_message});
};

const setupEchoListeners = () => {
    if (authUser.value) {
        Echo.private(`voting.chat.${props.room.id}`).listen("VotingChat", handleReceivedMessage);
    }
};

onMounted(async () => {
    setupEchoListeners()

    await votingSettingStore.fetchSettings(props.room.id)
    if (roomSettings?.chat_messages_saved === 1) {
        await votingChatStore.fetchMessages(props.room.id)
    }

    if (roomSettings.value) {
        isChatEnable.value = roomSettings.value?.chat_enabled === 1
        isChatHistory.value = roomSettings.value?.chat_messages_saved === 1
        isChatUpload.value = roomSettings.value?.allow_voters_upload === 1
    }
})
</script>
