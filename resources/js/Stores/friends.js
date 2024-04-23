import { defineStore } from "pinia";
import { ref } from "vue";
import { route } from "ziggy-js";
import { useToast } from "vue-toast-notification";

const toast = useToast();

export const useFriendStore = defineStore("friend", () => {
    const friends = ref({});

    const channelBroadcast = {
        channelName: "friend-request.",
        eventName: "FriendRequestEvent",
    };

    const handleReceivedMessage = (e) => {
        storeBroadcastMessage(e);
    };

    const setupEchoPrivateListener = (userId) => {
        try {
            Echo.private(channelBroadcast.channelName + userId).listen(
                channelBroadcast.eventName,
                handleReceivedMessage,
            );
        } catch (error) {
            console.log(error);
        }
    };

    const fetchFriends = async () => {
        if (Object.keys(friends.value).length > 0) {
            return;
        }

        try {
            const response = await axios.get(route("api.friends.index"));

            if (response.status === 200) {
                friends.value = response.data.data;
            }
        } catch (error) {
            toast.error(error.response.data.message);
        }
    };

    const sendFriendRequest = async (senderId, receiverId) => {
        const existingRequest = friends.value.requestSent.find(
            (request) => request.id === receiverId,
        );

        if (existingRequest) {
            toast.error("You have already sent a friend request to this user.");
            return;
        }

        try {
            const response = await axios.post(
                route("api.friend.send-request", {
                    sender: senderId,
                    receiver: receiverId,
                }),
            );

            if (response.status === 200) {
                friends.value.requestSent.push(response.data.data);
                toast.success(response.data.message);
            }
        } catch (error) {
            toast.error(error.response.data.message);
        }
    };

    const acceptFriendRequest = async (senderId, receiverId) => {
        try {
            const response = await axios.post(
                route("api.friend.accept-request", {
                    sender: senderId,
                    receiver: receiverId,
                }),
            );

            if (response.status === 200) {
                const requestData = response.data.data;
                friends.value.friends.push(requestData);

                const removedIndex = friends.value.friendRequests.findIndex(
                    (request) => request.id === receiverId,
                );
                if (removedIndex !== -1) {
                    friends.value.friendRequests.splice(removedIndex, 1);
                }
                toast.success(response.data.message);
            }
        } catch (error) {
            toast.error(error.response.data.message);
        }
    };

    const rejectFriendRequest = async (senderId, receiverId) => {
        try {
            const response = await axios.post(
                route("api.friend.reject-request", {
                    sender: senderId,
                    receiver: receiverId,
                }),
            );

            if (response.status === 200) {
                const removedIndex = friends.value.friendRequests.findIndex(
                    (request) => request.id === receiverId,
                );
                if (removedIndex !== -1) {
                    friends.value.friendRequests.splice(removedIndex, 1);
                }
                toast.success(response.data.message);
            }
        } catch (error) {
            toast.error(error.response.data.message);
        }
    };

    const unfriend = async (senderId, receiverId) => {
        try {
            const response = await axios.post(
                route("api.friend.unfriend", {
                    sender: senderId,
                    receiver: receiverId,
                }),
            );

            if (response.status === 200) {
                for (let i = 0; i < friends.value.friends.length; i++) {
                    if (friends.value.friends[i].id === receiverId) {
                        friends.value.friends.splice(i, 1);
                    }
                }
                toast.success(response.data.message);
            }
        } catch (error) {
            toast.error(error.response.data.message);
        }
    };

    const abortFriendRequest = async (senderId, receiverId) => {
        try {
            const response = await axios.post(
                route("api.friend.abort-request", {
                    sender: senderId,
                    receiver: receiverId,
                }),
            );

            if (response.status === 200) {
                const removedIndex = friends.value.requestSent.findIndex(
                    (request) => request.id === receiverId,
                );

                if (removedIndex !== -1) {
                    friends.value.requestSent.splice(removedIndex, 1);
                }
                toast.success(response.data.message);
            }
        } catch (error) {
            toast.error(error.response.data.message);
        }
    };

    const storeBroadcastMessage = (message) => {
        try {
            const sender = message.sender;
            const broadcastType = message.broadcastType;

            if (broadcastType === "friend_request_sent") {
                friends.value.friendRequests.push(sender);
            }

            if (broadcastType === "friend_request_accepted") {
                friends.value.friends.push(sender);

                const removedIndex = friends.value.requestSent.findIndex(
                    (request) => request.id === sender.id,
                );

                if (removedIndex !== -1) {
                    friends.value.requestSent.splice(removedIndex, 1);
                }
            }

            if (broadcastType === "friend_request_rejected") {
                const removedIndex = friends.value.requestSent.findIndex(
                    (request) => request.id === sender.id,
                );
                if (removedIndex !== -1) {
                    friends.value.requestSent.splice(removedIndex, 1);
                }
            }

            if (broadcastType === "friend_request_aborted") {
                const removedIndex = friends.value.friendRequests.findIndex(
                    (request) => request.id === sender.id,
                );
                if (removedIndex !== -1) {
                    friends.value.friendRequests.splice(removedIndex, 1);
                }
            }

            if (broadcastType === "unfriend") {
                const removedIndex = friends.value.friends.findIndex(
                    (request) => request.id === sender.id,
                );
                if (removedIndex !== -1) {
                    friends.value.friends.splice(removedIndex, 1);
                }
            }
        } catch (error) {
            console.log(error);
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
        storeBroadcastMessage,
        setupEchoPrivateListener,
    };
});
