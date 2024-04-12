<template>
    <div class="row row-cols-2 d-flex align-items-center">
        <div
            v-for="friend_request in authUserFriends.friendRequests"
            :key="friend_request.id"
            class="col"
        >
            <div class="card" style="border-radius: 15px">
                <div class="card-body p-4">
                    <div class="d-flex text-black">
                        <div class="flex-shrink-0">
                            <img
                                :src="friend_request.avatar"
                                alt="avatar"
                                class="img-fluid"
                                style="width: 100px; border-radius: 10px"
                            />
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h5 class="mb-1">
                                {{ friend_request.username }}
                            </h5>
                            <p class="mb-2 pb-1" style="color: #2b2a2a">
                                {{ friend_request.first_name }}
                                {{ friend_request.last_name }}
                            </p>
                            <div class="d-flex flex-column gap-1 pt-1">
                                <div
                                    class="d-flex justify-content-between gap-1"
                                >
                                    <Link
                                        :href="
                                            route(
                                                'user.profile',
                                                friend_request.id,
                                            )
                                        "
                                        class="btn btn-primary flex-grow-1"
                                    >
                                        Profile
                                    </Link>
                                    <button
                                        class="btn btn-danger flex-grow-1"
                                        @click="
                                            rejectFriendRequest(
                                                friend_request.id,
                                            )
                                        "
                                    >
                                        Reject
                                    </button>
                                </div>
                                <div class="d-grid">
                                    <button
                                        class="btn btn-success"
                                        @click="
                                            acceptFriendRequest(
                                                friend_request.id,
                                            )
                                        "
                                    >
                                        Accept
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { route } from "ziggy-js";
import { Link } from "@inertiajs/vue3";
import { useFriendStore } from "@/Stores/friends.js";
import { useToast } from "vue-toast-notification";

defineProps(["authUserFriends"]);
const toast = useToast();
const friendStore = useFriendStore();

const acceptFriendRequest = (id) => {
    friendStore.acceptFriendRequest(id);
    toast.success("Friend request accepted");
};

const rejectFriendRequest = (id) => {
    friendStore.rejectFriendRequest(id);
    toast.success("Friend request rejected");
};
</script>
