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

        const response = await axios.get(
            route("api.user.join.time.index", roomId),
        );

        if (response.status === 200) {
            attendances.value[roomId] = response.data.data;
        }
    };

    return { attendances, fetchAttendances };
});
