import {defineStore} from 'pinia'
import {ref} from "vue";
import {route} from "ziggy-js";
import axios from "axios";

export const useVotingSettingStore = defineStore('votingSetting', () => {
    const settings = ref({})

    const fetchSettings = async (roomId) => {
        const response = await axios.get(route('api.room.setting.invitation.index', roomId))
        settings.value[roomId] = response.data
    }

    const updateSettings = async (roomId, formData) => {
        formData.append('_method', 'PUT');

        await axios.post(route('api.room.setting.invitation.update', roomId), formData)
    }

    return {settings, fetchSettings, updateSettings}
})
