import { defineStore } from "pinia";
import { ref } from "vue";
import { route } from "ziggy-js";
import { useToast } from "vue-toast-notification";

const toast = useToast();

export const useInvitationMailStore = defineStore("invitationMail", () => {
    const invitationMails = ref([]);

    const fetchInvitationMail = async (roomId) => {
        if (!!invitationMails.value[roomId]) {
            return;
        }

        try {
            const response = await axios.get(
                route("api.room.invitations.mail.index", roomId),
            );

            if (response.status === 200) {
                invitationMails.value[roomId] = response.data.data || null;
            }
        } catch (error) {
            toast.error(error.response.data.message);
        }
    };

    const storeInvitationMail = async (roomId, formData) => {
        if (
            !invitationMails.value[roomId] ||
            invitationMails.value[roomId].length === 0
        ) {
            invitationMails.value[roomId] = [];
        }

        try {
            const response = await axios.post(
                route("api.room.invitation.mail.store", roomId),
                formData,
            );

            if (response.status === 200) {
                invitationMails.value[roomId] = response.data.data;

                toast.success(response.data.message);
            }
        } catch (error) {
            toast.error(error.response.data.message);
        }
    };

    const deleteInvitationMail = async (roomId, invitationMailId) => {
        try {
            const response = await axios.delete(
                route("api.invitation.mail.delete", invitationMailId),
            );

            if (response.status === 200) {
                invitationMails.value[roomId] = null;

                toast.success(response.data.message);
            }
        } catch (error) {
            toast.error(error.response.data.message);
        }
    };

    return {
        invitationMails,
        fetchInvitationMail,
        storeInvitationMail,
        deleteInvitationMail,
    };
});
