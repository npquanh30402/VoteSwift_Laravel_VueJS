import { defineStore } from "pinia";
import { ref } from "vue";
import { route } from "ziggy-js";

export const useInvitationMailStore = defineStore("invitationMail", () => {
    const invitationMails = ref([]);

    const fetchInvitationMail = async (roomId) => {
        if (!!invitationMails.value[roomId]) {
            return;
        }

        await axios
            .get(route("api.room.invitation.mail.index", roomId))
            .then(function (response) {
                if (response.status === 200) {
                    invitationMails.value[roomId] = response.data.data;
                }
            });
    };

    const storeInvitationMail = async (roomId, formData) => {
        try {
            if (
                !invitationMails.value[roomId] ||
                invitationMails.value[roomId].length === 0
            ) {
                invitationMails.value[roomId] = [];
            }

            const response = await axios.post(
                route("api.room.invitation.mail.store", roomId),
                formData,
            );

            if (response.status === 200) {
                invitationMails.value[roomId] = response.data.data;
            }
            return response;
        } catch (error) {
            throw error;
        }
    };

    const deleteInvitationMail = async (roomId, invitationMailId) => {
        const response = await axios.delete(
            route("api.room.invitation.mail.destroy", invitationMailId),
        );

        if (response.status === 200) {
            invitationMails.value[roomId] = null;
        }
        
        return response;
    };

    return {
        invitationMails,
        fetchInvitationMail,
        storeInvitationMail,
        deleteInvitationMail,
    };
});
