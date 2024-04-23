import { defineStore } from "pinia";
import { ref } from "vue";
import { route } from "ziggy-js";
import { useToast } from "vue-toast-notification";

const toast = useToast();

export const useUserStore = defineStore("user", () => {
    const users = ref([]);

    const searchUsers = async (keyword) => {
        try {
            const userExists = users.value.some(
                (user) =>
                    user.username.includes(keyword) ||
                    user.email.includes(keyword),
            );

            if (userExists) {
                return;
            }

            const response = await axios.get(route("api.users.search"), {
                params: {
                    query: keyword,
                },
            });

            if (response.status === 200) {
                users.value = response.data.data;
            }
        } catch (error) {
            toast.error(error.response.data.message);
        }
    };

    return {
        users,
        searchUsers,
    };
});
