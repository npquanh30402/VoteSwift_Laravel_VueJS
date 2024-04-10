<template>
    <div class="row gx-3">
        <div class="d-flex flex-column gap-3">
            <div class="hstack gap-3 align-items-center justify-content-between">
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" role="switch" id="chatSwitch"
                           @change="toggleRealTimeVoting" v-model="isRealTimeVotingEnable">
                    <label class="form-check-label" for="realTimeVotingSwitch">Enable Real-time Voting</label>
                </div>
                <div :class="[isRealTimeVotingEnable ? '' : 'un-interactive']">
                    <button type="button" class="btn btn-primary opacity-100 position-relative" disabled>
                        Realtime
                        <span
                            class="position-absolute top-0 start-100 translate-middle p-2 bg-danger border border-light rounded-circle"
                            :class="{' animate__animated animate__flash animate__infinite animate__slow': isRealTimeVotingEnable}"></span>
                    </button>
                </div>
            </div>
            <div :class="[isRealTimeVotingEnable ? '' : 'un-interactive']">
                <BaseNoContent/>
            </div>
        </div>
    </div>
</template>

<script setup>
import {usePage} from "@inertiajs/vue3";
import {computed, onMounted, ref, watch} from "vue";
import {useVotingSettingStore} from "@/Stores/voting-settings.js";
import {useToast} from "vue-toast-notification";
import BaseNoContent from "@/Components/BaseNoContent.vue";

const props = defineProps(['room'])
const $toast = useToast();

const votingSettingStore = useVotingSettingStore()

const authUser = computed(() => usePage().props.authUser.user)
const roomSettings = computed(() => votingSettingStore.settings[props.room.id])

const isRealTimeVotingEnable = ref(false)

watch(roomSettings, () => {
    isRealTimeVotingEnable.value = roomSettings.value?.realtime_enabled === 1
})

const updateSetting = (key, value) => {
    const formData = new FormData();
    formData.append(key, value);

    votingSettingStore.updateSettings(props.room.id, formData).then(() => $toast.success('Updated successfully'))
        .catch(() => $toast.error('Failed to update'))
}

const toggleRealTimeVoting = () => {
    updateSetting('realtime_enabled', isRealTimeVotingEnable.value)
}

onMounted(() => {
    votingSettingStore.fetchSettings(props.room.id).then(() => {
        if (roomSettings) {
            isRealTimeVotingEnable.value = roomSettings.value?.realtime_enabled === 1
        }
    })
})
</script>
