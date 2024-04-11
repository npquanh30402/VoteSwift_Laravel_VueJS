import { defineStore } from "pinia";
import { ref } from "vue";
import { route } from "ziggy-js";

export const useFriendStore = defineStore("friend", () => {
    const friends = ref(null);

    const fetchFriends = async () => {
        // if (!!friends.value) {
        //     return;
        // }

        const response = await axios.get(route("api.user.friend.index"));

        if (response.status === 200) {
            friends.value = response.data;
        }
    };

    const sendFriendRequest = async (userId) => {
        const response = await axios.post(
            route("api.user.send-friend-request", userId),
        );

        if (response.status === 200) {
            await fetchFriends();
        }
    };

    const acceptFriendRequest = async (userId) => {
        const response = await axios.post(
            route("api.user.accept-friend-request", userId),
        );

        if (response.status === 200) {
            await fetchFriends();
        }
    };

    const rejectFriendRequest = async (userId) => {
        const response = await axios.post(
            route("api.user.reject-friend-request", userId),
        );

        if (response.status === 200) {
            await fetchFriends();
        }
    };

    const unfriend = async (userId) => {
        const response = await axios.post(route("api.user.unfriend", userId));

        if (response.status === 200) {
            await fetchFriends();
        }
    };

    const abortFriendRequest = async (userId) => {
        const response = await axios.post(
            route("api.user.abort-request-sent", userId),
        );

        if (response.status === 200) {
            await fetchFriends();
        }
    };

    return {
        friends,
        fetchFriends,
        sendFriendRequest,
        acceptFriendRequest,
        rejectFriendRequest,
        abortFriendRequest,
        unfriend,
    };
});
