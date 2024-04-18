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

        const response = await axios.get(
            route("api.room.setting.index", roomId),
        );

        if (response.status === 200) {
            settings.value[roomId] = response.data.data;
        }
    };

    const updateSettings = async (roomId, formData) => {
        formData.append("_method", "PUT");

        const response = await axios.post(
            route("api.room.setting.update", roomId),
            formData,
        );

        if (response.status === 200) {
            settings.value[roomId] = response.data.data;
            toast.success(response.data.message);
        } else {
            throw new Error("Update failed: " + response.statusText);
        }
    };

    return { settings, fetchSettings, updateSettings };
});
