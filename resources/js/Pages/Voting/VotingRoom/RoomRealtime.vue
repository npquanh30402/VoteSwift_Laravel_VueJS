<template>
    <div class="row gx-3">
        <div class="d-flex flex-column gap-3">
            <div class="hstack gap-3 align-items-center justify-content-between">
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" role="switch" id="chatSwitch"
                           @change="toggleRealTimeVoting" v-model="isRealTimeVoting">
                    <label class="form-check-label" for="realTimeVotingSwitch">Enable Real-time Voting</label>
                </div>
                <div :class="[isRealTimeVoting ? '' : 'un-interactive']">
                    <button type="button" class="btn btn-primary opacity-100 position-relative" disabled>
                        Realtime
                        <span
                            class="position-absolute top-0 start-100 translate-middle p-2 bg-danger border border-light rounded-circle"
                            :class="{' animate__animated animate__flash animate__infinite animate__slow': isRealTimeVoting}"></span>
                    </button>
                </div>
            </div>
            <div :class="[isRealTimeVoting ? '' : 'un-interactive']">
                <BaseNoContent/>
            </div>
        </div>
    </div>
</template>

<script setup>
import {usePage} from "@inertiajs/vue3";
import {computed, onMounted, ref, watch} from "vue";
import VotingChat from "@/Pages/Voting/Vote/VotingChat.vue";
import {useVotingSettingStore} from "@/Stores/voting-settings.js";
import {useToast} from "vue-toast-notification";
import BaseNoContent from "@/Components/BaseNoContent.vue";

const props = defineProps(['room'])

const $toast = useToast();
const votingSettingStore = useVotingSettingStore()
const roomSettings = computed(() => votingSettingStore.settings[props.room.id])

const authUser = computed(() => usePage().props.authUser.user);
const messages = computed(() => votingChatStore.messages);

const isRealTimeVoting = ref(false)
const isChatHistory = ref(false)
const isChatUpload = ref(false)

watch(() => roomSettings.value, () => {
    isRealTimeVoting.value = roomSettings.value?.chat_enabled === 1
    isChatHistory.value = roomSettings.value?.chat_messages_saved === 1
    isChatUpload.value = roomSettings.value?.allow_voters_upload === 1
})

const updateSetting = (key, value) => {
    const formData = new FormData();
    formData.append(key, value);

    votingSettingStore.updateSettings(props.room.id, formData)

    $toast.success('Updated successfully')
}

const toggleRealTimeVoting = () => {
    updateSetting('chat_enabled', isRealTimeVoting.value)
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
    // setupEchoListeners()

    await votingSettingStore.fetchSettings(props.room.id)
    if (roomSettings?.chat_messages_saved === 1) {
        await votingChatStore.fetchMessages(props.room.id)
    }

    if (roomSettings.value) {
        isRealTimeVoting.value = roomSettings.value?.chat_enabled === 1
        isChatHistory.value = roomSettings.value?.chat_messages_saved === 1
        isChatUpload.value = roomSettings.value?.allow_voters_upload === 1
    }
})
</script>
