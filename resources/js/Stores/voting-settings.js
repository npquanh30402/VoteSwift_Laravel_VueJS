import { defineStore } from "pinia";
import { ref } from "vue";
import { route } from "ziggy-js";
import axios from "axios";
import { useToast } from "vue-toast-notification";

const toast = useToast();

export const useVotingSettingStore = defineStore("votingSetting", () => {
    const settings = ref({});

    const fetchSettings = async (roomId) => {
        if (!!settings.value[roomId]) {
            return;
        }

        try {
            const response = await axios.get(
                route("api.room.settings.index", roomId),
            );

            if (response.status === 200) {
                settings.value[roomId] = response.data.data;
            }
        } catch (error) {
            toast.error(error.response.data.message);
        }
    };

    const updateSettings = async (roomId, formData) => {
        formData.append("_method", "PUT");

        try {
            const response = await axios.post(
                route("api.room.settings.update", roomId),
                formData,
            );

            if (response.status === 200) {
                settings.value[roomId] = response.data.data;
                toast.success(response.data.message);
            }
        } catch (error) {
            toast.error(error.response.data.message);
        }
    };

    return { settings, fetchSettings, updateSettings };
});
