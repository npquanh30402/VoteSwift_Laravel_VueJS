<template>
    <div class="card shadow-sm border-0 mb-3 overflow-auto">
        <div class="card-header text-bg-dark text-center">Voters</div>
        <div class="card-body">
            <div class="d-flex flex-column gap-3">
                <div class="hstack gap-3 align-items-center justify-content-between">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" role="switch" id="SpecificVoterSwitch"
                               @change="toggleInvitation" :checked="onlyInvitation">
                        <label class="form-check-label" for="musicPlayerSwitch">Only Voters I invite Can Join and
                            Vote</label>
                    </div>
                    <div class="form-check form-switch" :class="[onlyInvitation ? '' : 'un-interactive']">
                        <input class="form-check-input" type="checkbox" role="switch" id="waitVoterSwitch"
                               @change="toggleWaitForVoters" :checked="waitForVoters">
                        <label class="form-check-label" for="waitVoterSwitch">Wait Until Voters Joined to Start
                            Voting</label>
                    </div>
                </div>
                <div class="row g-3" :class="[onlyInvitation ? '' : 'un-interactive']">
                    <div class="col-md-4 vstack justify-content-between gap-3">
                        <form @submit.prevent="searchUsers"
                              class="card border border-3 border-success-subtle p-3 shadow-sm form-group d-flex flex-column">
                            <label class="form-label" for="uploadMusic">Search User:</label>
                            <input type="text" id="search" v-model="search_query" class="form-control">
                            <div class="ms-auto mt-3">
                                <button type="submit" class="btn btn-primary">Search</button>
                            </div>
                        </form>
                        <div
                            class="card border border-3 border-success-subtle p-3 shadow-sm form-group d-flex flex-column flex-grow-1 overflow-auto">
                            <div>
                                <div
                                    class="border border-1 border-black rounded p-2 mb-2 hstack justify-content-between"
                                    v-for="user in users"
                                    :key="user.id">
                                    <a :href="route('user.profile', user.id)" target="_blank"
                                       class="d-flex justify-content-between align-items-center gap-2"
                                       style="width: 4rem">
                                        <img :src="user.avatar" class="img-fluid rounded" alt="avatar">
                                        <div class="vstack text-center">
                                            <span class="text-bg-info p-1 rounded-top fs-6">{{ user.id }}</span>
                                            <span class="text-bg-secondary p-1 rounded-bottom fs-6">{{
                                                    user.username
                                                }}</span>
                                        </div>
                                    </a>
                                    <button type="button" class="btn btn-success rounded" @click="addToInvite(user)">
                                        <i class="bi bi-plus-circle text-white"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-8">
                        <div
                            class="card border border-3 border-success-subtle p-3 shadow-sm form-group d-flex flex-column"
                            style="height: 50vh">
                            <div class="d-flex justify-content-between">
                                <span>User Selected:</span>
                            </div>
                            <div class="card-body overflow-auto vstack justify-content-between">
                                <div
                                    class="list-group vstack align-items-center">
                                    <div
                                        class="border border-1 border-black rounded p-2 mb-2 hstack justify-content-between"
                                        v-for="user in userInvitationList"
                                        :key="user.id">
                                        <a :href="route('user.profile', user.id)" target="_blank"
                                           class="d-flex justify-content-between align-items-center gap-2"
                                           style="width: 4rem">
                                            <img :src="user.avatar" class="img-fluid rounded" alt="avatar">
                                            <div class="vstack text-center">
                                                <span class="text-bg-info p-1 rounded-top fs-6">{{ user.id }}</span>
                                                <span class="text-bg-secondary p-1 rounded-bottom fs-6">{{
                                                        user.username
                                                    }}</span>
                                            </div>
                                        </a>
                                        <button type="button" class="btn btn-danger rounded"
                                                @click="removeFromInvite(user.id)">
                                            <i class="bi bi-dash-circle text-white"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="ms-auto">
                                    <button type="button" class="btn btn-primary" @click="submit">Save</button>
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
import axios from 'axios';
import {route} from "ziggy-js";
import {router, usePage} from "@inertiajs/vue3";
import {computed, ref} from "vue";

const props = defineProps(['room', 'room_settings'])

const authUser = computed(() => usePage().props.authUser.user);

const onlyInvitation = ref(props.room_settings?.invitation_only === 1);
const waitForVoters = ref(props.room_settings?.wait_for_voters === 1);

const toggleInvitation = () => {
    onlyInvitation.value = !onlyInvitation.value
    router.put(route('room.settings.invitation.update', props.room.id), {
        invitation_only: onlyInvitation.value
    })
}

const toggleWaitForVoters = () => {
    waitForVoters.value = !waitForVoters.value
    router.put(route('room.settings.waitForVoters.update', props.room.id), {
        wait_for_voters: waitForVoters.value
    })
}

const search_query = ref('');
const users = ref([]);

const searchUsers = async (specificUserId) => {
    if (search_query.value.length >= 3) {
        try {
            const response = await axios.get(route('user.search'), {
                params: {
                    query: search_query.value
                }
            });

            users.value = response.data.filter(user => {
                return !userInvitationList.value.some(invitedUser => invitedUser.id === user.id) && user.id !== authUser.value.id;
            });
        } catch (error) {
            console.error(error);
        }
    } else {
        users.value = [];
    }
};

let userInvitationList = ref([]);

axios.get(route('invitation.get', props.room.id))
    .then(function (response) {
        response.data.invitedUsers.forEach(invitation => {
            userInvitationList.value.push(invitation);
        });
    });

const addToInvite = (userToAdd) => {
    const userExistsInInvitationListIndex = userInvitationList.value.findIndex(user => user.id === userToAdd.id);

    if (userExistsInInvitationListIndex === -1) {
        userInvitationList.value.push(userToAdd);

        const userIndexInUsers = users.value.findIndex(user => user.id === userToAdd.id);
        if (userIndexInUsers !== -1) {
            users.value.splice(userIndexInUsers, 1);
        }
    }
};

const removeFromInvite = (userIdToRemove) => {
    const index = userInvitationList.value.findIndex(user => user.id === userIdToRemove);

    if (index !== -1) {
        userInvitationList.value.splice(index, 1);
    }
};

const submit = () => {
    router.post(route('invitation.store', props.room.id), {
        user_ids: userInvitationList.value
    })
}

</script>
