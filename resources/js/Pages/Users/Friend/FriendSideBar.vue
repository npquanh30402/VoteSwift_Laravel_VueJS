<template>
    <div class="list-group shadow-sm small">
        <div class="list-group-item text-bg-dark">Friends Option</div>
        <button
            @click="switchTab('FriendList')"
            class="list-group-item list-group-item-action"
        >
            Friend List
        </button>
        <button
            @click="switchTab('FriendRequest')"
            class="list-group-item list-group-item-action d-flex justify-content-between"
        >
            Friend Requests
            <span
                v-if="authUserFriends['friendRequests'].length > 0"
                class="badge text-bg-danger ms-auto"
            >
                {{ authUserFriends.friendRequests.length }}
            </span>
        </button>
        <button
            @click="switchTab('RequestSent')"
            class="list-group-item list-group-item-action d-flex justify-content-between"
        >
            Request Sent
            <span
                class="badge text-bg-danger ms-auto"
                v-if="authUserFriends.requestSent.length > 0"
            >
                {{ authUserFriends.requestSent.length }}
            </span>
        </button>
    </div>
</template>

<script setup>
defineProps(["authUserFriends"]);

const emit = defineEmits(["switch-tab"]);

const switchTab = (tabName) => {
    emit("switch-tab", tabName);
};
</script>
