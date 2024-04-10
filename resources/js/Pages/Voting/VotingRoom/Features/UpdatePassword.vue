<template>
    <form @submit.prevent="submit">
        <div class="row gx-3">
            <div class="d-flex flex-column gap-3">
                <div class="hstack gap-3 align-items-center">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" role="switch" id="passwordSwitch"
                               @change="togglePassword" v-model="isPasswordEnable">
                        <label class="form-check-label" for="passwordSwitch">Enable Password</label>
                    </div>
                </div>
                <div class="row g-3" :class="[isPasswordEnable ? '' : 'un-interactive']">
                    <div class="col-md-12">
                        <form @submit.prevent="submit"
                              class="card border border-3 border-success-subtle p-3 shadow-sm form-group d-flex flex-column">
                            <label class="form-label" for="require_password">Password:</label>
                            <input type="password" id="require_password" name="require_password"
                                   class="form-control form-control-sm"
                                   v-model="form.require_password">
                            <div class="ms-auto mt-3">
                                <button type="submit" class="btn btn-primary">Save</button>
                            </div>
                        </form>
                    </div>
                    <div v-if="roomSettings?.password_qrcode" class="hstack justify-content-end">
                        <img :src="roomSettings.password_qrcode" alt="" class="img-fluid rounded me-2"
                             @click="showImage" style="cursor: pointer">
                        <div class="mt-auto">
                            <a :href="roomSettings.password_qrcode" class="btn btn-primary"
                               :download="downloadFileName('room_1_qrcode', roomSettings.password_qrcode)"><i
                                class="bi bi-download"></i></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <teleport to="body">
            <LightBoxHelper :currentImageDisplay="currentImageDisplay"/>
        </teleport>
    </form>
</template>

<script setup>
import {useForm} from "@inertiajs/vue3";
import {computed, onActivated, onMounted, ref, watch} from "vue";
import {useToast} from "vue-toast-notification";
import {useVotingSettingStore} from "@/Stores/voting-settings.js";
import LightBoxHelper from "@/Components/Helpers/LightBoxHelper.vue";

const props = defineProps(['room'])

const votingSettingStore = useVotingSettingStore()
const $toast = useToast();
const currentImageDisplay = ref(null)

const roomSettings = computed(() => votingSettingStore.settings[props.room.id])
const isPasswordEnable = ref(false);
const form = useForm({
    require_password: roomSettings.value?.password,
});

const downloadFileName = computed(() => (baseFileName, url) => {
    const fileExtension = url.split('.').pop();
    return `${baseFileName}.${fileExtension}`;
})

const updatePasswordSettings = () => {
    isPasswordEnable.value = roomSettings.value.password !== null;
    form.require_password = roomSettings.value.password;
};

watch(roomSettings, updatePasswordSettings);
onActivated(updatePasswordSettings);

onMounted(() => {
    votingSettingStore.fetchSettings(props.room.id)
    isPasswordEnable.value = roomSettings.value?.password !== null
})

const updateSetting = (key, value) => {
    const formData = new FormData();
    formData.append(key, value);

    votingSettingStore.updateSettings(props.room.id, formData).then(() => $toast.success('Updated successfully'))
        .catch(() => $toast.error('Failed to update'))
}

const togglePassword = () => {
    if (isPasswordEnable.value === false) {
        updateSetting('require_password', null)
    }
}

const submit = () => {
    updateSetting('require_password', form.require_password)
}

const showImage = (e) => {
    currentImageDisplay.value = e;
}
</script>
