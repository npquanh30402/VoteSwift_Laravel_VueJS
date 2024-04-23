import { defineStore } from "pinia";
import { ref } from "vue";
import { route } from "ziggy-js";
import axios from "axios";
import { useToast } from "vue-toast-notification";

const toast = useToast();

export const useVotingAttendanceStore = defineStore("votingAttendance", () => {
    const attendances = ref({});

    const fetchAttendances = async (roomId, flag = false) => {
        if (!!attendances.value[roomId] && flag === false) {
            return;
        }

        try {
            const response = await axios.get(
                route("api.rooms.attendance.index", roomId),
            );

            if (response.status === 200) {
                attendances.value[roomId] = response.data.data;
            }
        } catch (error) {
            toast.error(error.response.data.message);
        }
    };

    const storeJoinTime = async (roomId, userId, formData) => {
        try {
            const response = await axios.post(
                route("api.rooms.attendance.join", {
                    room: roomId,
                    user: userId,
                }),
                formData,
            );

            if (response.status === 200) {
                toast.success("You have joined successfully");

                return response.data.data;
            }
        } catch (error) {
            toast.error(error.response.data.message);
        }
    };

    const storeLeaveTime = async (roomId, userId, formData) => {
        try {
            const response = await axios.post(
                route("api.rooms.attendance.leave", {
                    room: roomId,
                    user: userId,
                }),
                formData,
            );

            if (response.status === 200) {
                toast.info("You have left the room");
            }
        } catch (error) {
            toast.error(error.response.data.message);
        }
    };

    return { attendances, fetchAttendances, storeJoinTime, storeLeaveTime };
});
