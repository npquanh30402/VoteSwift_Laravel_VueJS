<template>
    <div class="card shadow-sm border-0 mb-3 overflow-auto">
        <div class="card-header text-bg-dark text-center">Chat</div>
        <div class="card-body">
            <div class="row gx-3">
                <div class="d-flex flex-column gap-3">
                    <div class="hstack gap-3 align-items-center justify-content-between">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" role="switch" id="chatSwitch"
                                   @change="toggleChat" :checked="isChatEnable">
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
                                               @change="toggleChatHistory" :checked="isChatHistory">
                                        <label class="form-check-label" for="passwordSwitch">Save Chat History</label>
                                    </div>
                                    <span data-bs-toggle="popover" data-bs-trigger="hover focus"
                                          data-bs-title="Notice"
                                          data-bs-content="The messages will be encrypted by default"><i
                                        class="bi bi-exclamation-circle"></i></span>
                                </div>
                                <div class="hstack gap-3 align-items-center">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" role="switch"
                                               id="chatUploadSwitch"
                                               @change="toggleChatUpload" :checked="isChatUpload">
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
        </div>
    </div>
</template>

<script setup>
import {router, usePage} from "@inertiajs/vue3";
import {route} from "ziggy-js";
import {computed, onMounted, ref} from "vue";
import VotingChat from "@/Pages/Voting/Vote/VotingChat.vue";
import VotingMessage from "@/Pages/Voting/Vote/Chat/VotingMessage.vue";
import {useVotingChatStore} from "@/Stores/voting-chat.js";

const props = defineProps(['room', 'room_settings'])

const isChatEnable = ref(props.room_settings?.chat_enabled === 1)
const isChatHistory = ref(props.room_settings?.chat_messages_saved === 1)
const isChatUpload = ref(props.room_settings?.allow_voters_upload === 1)

const votingChatStore = useVotingChatStore()
const authUser = computed(() => usePage().props.authUser.user);
const messages = computed(() => votingChatStore.messages);

const toggleChat = () => {
    isChatEnable.value = !isChatEnable.value

    router.put(route('room.settings.chat.update', props.room.id), {
        chat_enabled: isChatEnable.value
    })
}

const toggleChatHistory = () => {
    isChatHistory.value = !isChatHistory.value

    router.put(route('room.settings.chat.update', props.room.id), {
        chat_messages_saved: isChatHistory.value
    })

    if (isChatHistory.value) {
        votingChatStore.fetchMessages(props.room.id);
    } else {
        votingChatStore.clearMessages(props.room.id)
    }
}

const toggleChatUpload = () => {
    isChatUpload.value = !isChatUpload.value

    router.put(route('room.settings.chat.update', props.room.id), {
        allow_voters_upload: isChatUpload.value
    })
}
const initializePopover = () => {
    const popoverTriggerList = document.querySelectorAll('[data-bs-toggle="popover"]')
    const popoverList = [...popoverTriggerList].map(popoverTriggerEl => new bootstrap.Popover(popoverTriggerEl))
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
    initializePopover()

    setupEchoListeners()

    if (props.room_settings?.chat_messages_saved === 1) {
        await votingChatStore.fetchMessages(props.room.id);
    }
})
</script>
