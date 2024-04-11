<template>
    <div class="row row-cols-2 d-flex align-items-center h-100">
        <div
            class="col"
            v-for="friend in authUserFriends.friends"
            :key="friend.id"
        >
            <div class="card" style="border-radius: 15px">
                <div class="card-body p-4">
                    <div class="d-flex text-black">
                        <div class="flex-shrink-0">
                            <img
                                :src="friend.avatar"
                                alt="Generic placeholder image"
                                class="img-fluid"
                                style="width: 100px; border-radius: 10px"
                            />
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h5 class="mb-1">{{ friend.username }}</h5>
                            <p class="mb-2 pb-1" style="color: #2b2a2a">
                                {{ friend.first_name }}
                                {{ friend.last_name }}
                            </p>
                            <div class="d-flex flex-column gap-1 pt-1">
                                <div
                                    class="d-flex justify-content-between gap-1"
                                >
                                    <Link
                                        :href="route('chat.main', friend.id)"
                                        class="btn btn-outline-primary flex-grow-1"
                                    >
                                        Chat
                                    </Link>
                                    <Link
                                        :href="route('user.profile', friend.id)"
                                        class="btn btn-primary flex-grow-1"
                                    >
                                        Profile
                                    </Link>
                                </div>
                                <div class="d-grid">
                                    <button
                                        @click="unfriend(friend.id)"
                                        class="btn btn-danger"
                                    >
                                        Unfriend
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
import { useToast } from "vue-toast-notification";
import { useFriendStore } from "@/Stores/friends.js";

defineProps(["authUserFriends"]);
const $toast = useToast();
const friendStore = useFriendStore();

const unfriend = (id) => {
    friendStore.unfriend(id);
    $toast.success("Unfriended successfully");
};
</script>
