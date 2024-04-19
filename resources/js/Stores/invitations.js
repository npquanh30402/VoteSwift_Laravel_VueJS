import { defineStore } from "pinia";
import { ref } from "vue";
import { route } from "ziggy-js";
import axios from "axios";
import { useToast } from "vue-toast-notification";

const toast = useToast();

export const useInvitationStore = defineStore("invitation", () => {
    const invitations = ref({});

    const importInvitations = async (roomId, formData) => {
        try {
            const response = await axios.post(
                route("api.room.invitation.csv", roomId),
                formData,
            );

            if (response.status === 200) {
                invitations.value[roomId] = response.data.data;
                toast.success(response.data.message);
            } else {
                toast.error(response.data.message);
            }
        } catch (error) {
            toast.error(error.response.data.message);
        }
    };

    const fetchInvitations = async (roomId) => {
        if (!!invitations.value[roomId]) {
            return;
        }

        const response = await axios.get(
            route("api.room.invitation.index", roomId),
        );

        if (response.status === 200) {
            invitations.value[roomId] = response.data.data;
        }
    };

    const storeInvitations = async (roomId, data) => {
        try {
            const response = await axios.post(
                route("api.room.invitation.store", roomId),
                data,
                {
                    headers: {
                        "Content-Type": "multipart/form-data",
                    },
                },
            );

            if (response.status === 200) {
                invitations.value[roomId] = response.data.data;

                toast.success(response.data.message);
            }
        } catch (error) {
            console.error("Error:", error);
            throw error;
        }
    };

    return {
        invitations,
        fetchInvitations,
        storeInvitations,
        importInvitations,
    };
});
