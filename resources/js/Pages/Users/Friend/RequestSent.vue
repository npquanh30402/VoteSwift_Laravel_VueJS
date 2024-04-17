<template>
    <div class="row row-cols-2 d-flex align-items-center">
        <div
            v-for="sent in authUserFriends.requestSent"
            :key="sent.id"
            class="col"
        >
            <div class="card" style="border-radius: 15px">
                <div class="card-body p-4">
                    <div class="d-flex text-black">
                        <div class="flex-shrink-0">
                            <img
                                :src="sent.avatar"
                                alt="avatar"
                                class="img-fluid"
                                style="width: 100px; border-radius: 10px"
                            />
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h5 class="mb-1">{{ sent.username }}</h5>
                            <p class="mb-2 pb-1" style="color: #2b2a2a">
                                {{ sent.first_name }}
                                {{ sent.last_name }}
                            </p>
                            <div class="d-flex flex-column gap-1 pt-1">
                                <div
                                    class="d-flex justify-content-between gap-1"
                                >
                                    <Link
                                        :href="route('user.profile', sent.id)"
                                        class="btn btn-primary flex-grow-1"
                                    >
                                        Profile
                                    </Link>
                                    <button
                                        class="btn btn-danger flex-grow-1"
                                        @click="abortFriendRequest(sent.id)"
                                    >
                                        Abort
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
import { Link, usePage } from "@inertiajs/vue3";
import { useFriendStore } from "@/Stores/friends.js";
import { computed } from "vue";

defineProps(["authUserFriends"]);
const authUser = computed(() => usePage().props.authUser.user);
const friendStore = useFriendStore();

const abortFriendRequest = (id) => {
    friendStore.abortFriendRequest(authUser.value.id, id);
};
</script>
