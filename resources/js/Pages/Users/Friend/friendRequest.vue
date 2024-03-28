<template>
    <div class="card shadow-sm border-0 mb-3">
        <div class="card-header text-bg-dark text-center">Friend Requests</div>
        <div class="card-body">
            <div class="row row-cols-2 d-flex align-items-center">
                <div class="col" v-for="friend_request in authUserFriends['friendRequests']" :key="friend_request.id">
                    <div class="card" style="border-radius: 15px;">
                        <div class="card-body p-4">
                            <div class="d-flex text-black">
                                <div class="flex-shrink-0">
                                    <img
                                        :src="friend_request.avatar" class="img-fluid"
                                        style="width: 100px; border-radius: 10px;" alt="avatar">
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h5 class="mb-1">{{ friend_request.username }}</h5>
                                    <p class="mb-2 pb-1"
                                       style="color: #2b2a2a;">{{ friend_request.first_name }}
                                        {{ friend_request.last_name }}</p>
                                    <div class="d-flex flex-column gap-1 pt-1">
                                        <div class="d-flex justify-content-between gap-1">
                                            <Link :href="route('user.profile', friend_request.id)"
                                                  class="btn btn-primary flex-grow-1">
                                                Profile
                                            </Link>
                                            <Link :href="route('user.reject-friend-request', friend_request.id)"
                                                  method="POST" as="button" class="btn btn-danger flex-grow-1">
                                                Reject
                                            </Link>
                                        </div>
                                        <div class="d-grid">
                                            <Link :href="route('user.accept-friend-request', friend_request.id)"
                                                  method="POST" as="button" class="btn btn-success">Accept
                                            </Link>
                                        </div>
                                    </div>
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
import {route} from "ziggy-js";
import {Link} from "@inertiajs/vue3";

defineProps(['authUserFriends'])
</script>
