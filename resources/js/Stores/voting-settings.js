import {defineStore} from 'pinia'
import {ref} from "vue";
import {route} from "ziggy-js";
import axios from "axios";

export const useVotingSettingStore = defineStore('votingSetting', () => {
    const settings = ref({})

    const fetchSettings = async (roomId) => {
        if (!!settings.value[roomId]) {
            return;
        }

        const response = await axios.get(route('api.room.setting.index', roomId))
        settings.value[roomId] = response.data
    }

    const updateSettings = async (roomId, formData) => {
        formData.append('_method', 'PUT');

        const response = await axios.post(route('api.room.setting.update', roomId), formData)

        if (response.status === 200) {
            settings.value[roomId] = response.data
        }
    }

    return {settings, fetchSettings, updateSettings}
})
