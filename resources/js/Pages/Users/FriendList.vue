<template>
    <div>
        <div class="row">
            <div class="col-md-2">
                <div class="list-group shadow-sm small">
                    <div class="list-group-item text-bg-dark">Friends Option</div>
                    <button href="#" class="list-group-item list-group-item-action active">Friend List</button>
                    <a href="#" class="list-group-item list-group-item-action d-flex justify-content-between"
                       @click="friendRequestModal">Friend Requests
                        <span v-if="authUserFriends['friendRequests'].length > 0"
                              class="badge text-bg-danger ms-auto">
                                                    {{ authUserFriends['friendRequests'].length }}
                                                </span>
                    </a>
                    <a href="#" class="list-group-item list-group-item-action d-flex justify-content-between"
                       @click="requestSendModal">Request Sent
                        <span class="badge text-bg-danger ms-auto" v-if="authUserFriends['requestSent'].length > 0">
                                                    {{ authUserFriends['requestSent'].length }}
                                                </span>
                    </a>
                </div>
            </div>
            <div class="col-md-10">
                <div class="card shadow-sm border-0 mb-3 overflow-auto">
                    <div class="card-header text-bg-dark text-center">Friend List</div>
                    <div class="card-body">
                        <div class="row row-cols-2 d-flex align-items-center h-100">
                            <div class="col" v-for="friend in authUserFriends['friends']" :key="friend.id">
                                <div class="card" style="border-radius: 15px;">
                                    <div class="card-body p-4">
                                        <div class="d-flex text-black">
                                            <div class="flex-shrink-0">
                                                <img
                                                    :src="friend.avatar"
                                                    alt="Generic placeholder image" class="img-fluid"
                                                    style="width: 100px; border-radius: 10px;">
                                            </div>
                                            <div class="flex-grow-1 ms-3">
                                                <h5 class="mb-1">{{ friend.username }}</h5>
                                                <p class="mb-2 pb-1"
                                                   style="color: #2b2a2a;">{{ friend.first_name }}
                                                    {{ friend.last_name }}</p>
                                                <div class="d-flex pt-1">
                                                    <Link :href="route('chat.main', friend.id)"
                                                          class="btn btn-outline-primary me-1 flex-grow-1">
                                                        Chat
                                                    </Link>
                                                    <Link :href="route('user.profile', friend.id)"
                                                          class="btn btn-primary me-1 flex-grow-1">
                                                        Profile
                                                    </Link>
                                                    <Link :href="route('user.unfriend', friend.id)"
                                                          class="btn btn-danger" as="button" method="POST">Unfriend
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
        </div>
        <div>
            <teleport to="body">
                <BaseModal title="Friend Request" id="friendRequestModal">
                    <div class="card shadow-sm border-0 mb-3 overflow-auto">
                        <div v-for="friend_request in authUserFriends['friendRequests']"
                             :key="friend_request.id"
                             class="card-header text-bg-dark text-center d-flex justify-content-between align-items-center">
                            <span>{{ friend_request.username }}</span>
                            <div class="d-flex gap-3">
                                <Link :href="route('user.reject-friend-request', friend_request.id)"
                                      method="POST" as="button" class="btn btn-danger">Reject
                                </Link>
                                <Link :href="route('user.accept-friend-request', friend_request.id)"
                                      method="POST" class="btn btn-success">Accept
                                </Link>
                            </div>
                        </div>
                    </div>
                    <template #footer>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </template>
                </BaseModal>
            </teleport>

            <!-- Request Sent Modal -->
            <teleport to="body">
                <BaseModal id="request_sent" title="Request Sent">
                    <div v-for="sent in authUserFriends['requestSent']"
                         class="card shadow-sm border-0 mb-3 overflow-auto">
                        <div
                            class="card-header text-bg-dark text-center d-flex justify-content-between align-items-center">
                            <span>{{ sent.username }}</span>
                            <div class="d-flex gap-3">
                                <Link :href="route('user.abort-request-sent', sent.id)"
                                      method="POST" as="button" class="btn btn-danger">Abort
                                </Link>
                            </div>
                        </div>
                    </div>
                    <template #footer>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </template>
                </BaseModal>
            </teleport>
            <!-- Request Sent Modal -->

            <div class="modal fade" id="friend_option_{{friend.id}}" data-bs-backdrop="static"
                 tabindex="-1"
                 aria-hidden="true"
                 style="backdrop-filter: blur(5px);">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header text-bg-dark">
                            <h5 class="modal-title">Friend Details</h5>
                            <button type="button" class="btn-close btn-close-white"
                                    data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body p-0">

                        </div>
                        <div class="modal-footer">
                            <form action="{{route('user.unfriend', friend.id)}}"
                                  method="POST">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-danger">Unfriend
                                </button>
                            </form>
                            <a href="#" class="btn btn-sm btn-primary" data-bs-dismiss="modal"
                               aria-label="Close">Close</a>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Friend Option Modal-->
        </div>
    </div>
</template>

<script setup>
import {Link} from "@inertiajs/vue3";
import {route} from "ziggy-js";
import BaseModal from "@/Components/BaseModal.vue";
import * as bootstrap from 'bootstrap'

const props = defineProps(['authUserFriends']);

function friendRequestModal() {
    const myModal = new bootstrap.Modal(document.getElementById('friendRequestModal'))
    const modalToggle = document.getElementById(myModal);
    myModal.show(modalToggle);
}

function requestSendModal() {
    const myModal = new bootstrap.Modal(document.getElementById('request_sent'))
    const modalToggle = document.getElementById(myModal);
    myModal.show(modalToggle);
}

</script>
