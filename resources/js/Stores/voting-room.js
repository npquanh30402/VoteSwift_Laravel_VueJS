import { defineStore } from "pinia";
import { ref } from "vue";
import { route } from "ziggy-js";
import { useToast } from "vue-toast-notification";
import { router } from "@inertiajs/vue3";

const toast = useToast();

export const useVotingRoomStore = defineStore("room", () => {
    const rooms = ref([]);

    const duplicateRoom = async (roomId) => {
        try {
            const response = await axios.get(
                route(`api.room.duplicate`, roomId),
            );

            if (response.status === 200) {
                const fetchedRoom = response.data.data;

                rooms.value.push(fetchedRoom);

                let htmlContent = `<strong>${response.data.message}</strong><br>Click here to go to the room dashboard`;

                toast.open({
                    message: htmlContent,
                    type: "success",
                    duration: 5000,
                    onClick: () => {
                        router.get(
                            route("room.dashboard", response.data.data.id),
                        );
                    },
                });
            }
        } catch (error) {
            toast.error(error.response.data.message);
        }
    };

    const fetchARoom = async (roomId) => {
        const roomExists = rooms.value.some((room) => room.id === roomId);

        if (roomExists) return;

        const response = await axios.get(route(`api.room.show`, roomId));

        if (response.status === 200) {
            const fetchedRoom = response.data.data;

            rooms.value.push(fetchedRoom);
        }
    };

    const fetchRooms = async () => {
        if (rooms.value?.length > 0) {
            return;
        }

        const response = await axios.get(route("api.room.index"));

        if (response.status === 200) {
            rooms.value = response.data.data;
        }
    };

    const storeRoom = async (formData) => {
        try {
            const response = await axios.post(
                route("api.room.store"),
                formData,
            );

            if (response.status === 201) {
                rooms.value.push(response.data.data);

                router.get(route("dashboard.user"));

                let htmlContent = `<strong>${response.data.message}</strong><br>Click here to go to the room dashboard`;

                toast.open({
                    message: htmlContent,
                    type: "success",
                    duration: 5000,
                    onClick: () => {
                        router.get(
                            route("room.dashboard", response.data.data.id),
                        );
                    },
                });
            }
        } catch (error) {
            toast.error(error.response.data.message);
        }
    };

    const updateRoom = async (roomId, formData) => {
        try {
            formData.append("_method", "PUT");

            const response = await axios.post(
                route("api.room.update", roomId),
                formData,
            );

            if (response.status === 200) {
                const index = rooms.value.findIndex(
                    (room) => room.id === roomId,
                );

                if (index !== -1) {
                    rooms.value[index] = response.data.data;

                    toast.success(response.data.message);
                }
            }
        } catch (error) {
            toast.error(error.response.data.message);
        }
    };

    const deleteRoom = async (roomId) => {
        try {
            const response = await axios.delete(
                route("api.room.destroy", roomId),
            );

            if (response.status === 200) {
                rooms.value = rooms.value.filter((room) => room.id !== roomId);

                toast.success(response.data.message);

                router.get(route("dashboard.user"));
            }
        } catch (error) {
            toast.error(error.response.data.message);
        }
    };

    return {
        rooms,
        fetchRooms,
        fetchARoom,
        storeRoom,
        updateRoom,
        duplicateRoom,
        deleteRoom,
    };
});
