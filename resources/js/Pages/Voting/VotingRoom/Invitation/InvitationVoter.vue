<template>
    <div class="row g-3">
        <div
            class="col-md-4 vstack justify-content-between gap-3"
            style="height: 50vh"
        >
            <div
                class="card border border-3 border-success-subtle p-3 shadow-sm form-group d-flex flex-column"
            >
                <label class="form-label" for="uploadMusic">Search User:</label>
                <input
                    id="search"
                    v-model="search_query"
                    class="form-control"
                    type="text"
                />
            </div>
            <div
                class="card border border-3 border-success-subtle p-3 shadow-sm form-group d-flex flex-column flex-grow-1 overflow-auto"
            >
                <div>
                    <div
                        v-for="user in users"
                        :key="user.id"
                        class="border border-1 border-black rounded p-2 mb-2 hstack justify-content-between"
                    >
                        <a
                            :href="route('user.profile', user.id)"
                            class="d-flex justify-content-between align-items-center gap-2"
                            style="width: 4rem"
                            target="_blank"
                        >
                            <img
                                :src="user.avatar"
                                alt="avatar"
                                class="img-fluid rounded"
                            />
                            <div class="vstack text-center">
                                <span
                                    class="text-bg-info p-1 rounded-top fs-6"
                                    >{{ user.id }}</span
                                >
                                <span
                                    class="text-bg-secondary p-1 rounded-bottom fs-6"
                                    >{{ user.username }}</span
                                >
                            </div>
                        </a>
                        <button
                            class="btn btn-success rounded"
                            type="button"
                            @click="addToInvite(user)"
                        >
                            <i class="bi bi-plus-circle text-white"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-8">
            <div
                class="card border border-3 border-success-subtle p-3 shadow-sm form-group d-flex flex-column"
                style="height: 50vh"
            >
                <div class="d-flex justify-content-between">
                    <span>User Selected:</span>
                </div>
                <div
                    class="card-body overflow-auto vstack justify-content-between"
                >
                    <div class="list-group vstack align-items-center">
                        <div
                            v-for="user in userInvitationList"
                            :key="user.id"
                            class="border border-1 border-black rounded p-2 mb-2 hstack justify-content-between"
                        >
                            <a
                                :href="route('user.profile', user.id)"
                                class="d-flex justify-content-between align-items-center gap-2"
                                style="width: 4rem"
                                target="_blank"
                            >
                                <img
                                    :src="user.avatar"
                                    alt="avatar"
                                    class="img-fluid rounded"
                                />
                                <div class="vstack text-center">
                                    <span
                                        class="text-bg-info p-1 rounded-top fs-6"
                                        >{{ user.id }}</span
                                    >
                                    <span
                                        class="text-bg-secondary p-1 rounded-bottom fs-6"
                                        >{{ user.username }}</span
                                    >
                                </div>
                            </a>
                            <button
                                class="btn btn-danger rounded"
                                type="button"
                                @click="removeFromInvite(user.id)"
                            >
                                <i class="bi bi-dash-circle text-white"></i>
                            </button>
                        </div>
                    </div>
                    <div class="ms-auto">
                        <button
                            class="btn btn-primary"
                            type="button"
                            @click="submit"
                        >
                            Save
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import axios from "axios";
import { route } from "ziggy-js";
import { usePage } from "@inertiajs/vue3";
import { computed, onMounted, ref } from "vue";
import { useInvitationStore } from "@/Stores/invitations.js";
import { useToast } from "vue-toast-notification";
import { watchDebounced } from "@vueuse/core";

const props = defineProps(["room"]);
const $toast = useToast();

const authUser = computed(() => usePage().props.authUser.user);
const invitationStore = useInvitationStore();
const userInvitationList = computed(
    () => invitationStore.invitations[props.room.id],
);

const search_query = ref("");
const users = ref([]);

watchDebounced(
    search_query,
    () => {
        searchUsers();
    },
    { debounce: 500, maxWait: 1000 },
);

onMounted(() => {
    invitationStore.fetchInvitations(props.room.id);
});

const searchUsers = async () => {
    if (search_query.value.length >= 3) {
        try {
            const response = await axios.get(route("user.search"), {
                params: {
                    query: search_query.value,
                },
            });

            users.value = response.data.filter((user) => {
                return (
                    !userInvitationList.value.some(
                        (invitedUser) => invitedUser.id === user.id,
                    ) && user.id !== authUser.value.id
                );
            });
        } catch (error) {
            console.error(error);
        }
    } else {
        users.value = [];
    }
};

const addToInvite = (userToAdd) => {
    const userExistsInInvitationListIndex = userInvitationList.value.findIndex(
        (user) => user.id === userToAdd.id,
    );

    if (userExistsInInvitationListIndex === -1) {
        userInvitationList.value.push(userToAdd);

        const userIndexInUsers = users.value.findIndex(
            (user) => user.id === userToAdd.id,
        );
        if (userIndexInUsers !== -1) {
            users.value.splice(userIndexInUsers, 1);
        }
    }
};

const removeFromInvite = (userIdToRemove) => {
    const index = userInvitationList.value.findIndex(
        (user) => user.id === userIdToRemove,
    );

    if (index !== -1) {
        userInvitationList.value.splice(index, 1);
    }
};

const submit = () => {
    const data = {
        user_ids: userInvitationList.value,
    };

    invitationStore.storeInvitations(props.room.id, data);

    $toast.success("Saved successfully");
};
</script>