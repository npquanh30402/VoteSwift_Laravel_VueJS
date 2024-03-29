<template>
    <div class="row">
        <div class="col">
            <h1 class="display-6 text-center fw-bold">Private Chat</h1>
        </div>
    </div>
    <div class="my-3">
        <div class="row justify-content-center">
            <div class="col-md-2">
                <div class="list-group shadow-sm small mb-3">
                    <div class="list-group-item text-bg-dark">Friend lists</div>
                    <Link :href="route('chat.main', friend.id)" v-for="friend in friends"
                          class="list-group-item list-group-item-action">{{ friend.username }}
                    </Link>
                </div>
            </div>
            <div class="col-md-8">
                <div class="card shadow-sm border-0 mb-3 overflow-auto">
                    <div class="card-header text-bg-dark text-center">Chat with {{ user.username }}</div>
                    <div class="container">
                        <div class="row justify-content-center">
                            <div class="card-body">
                                <div class="row p-2">
                                    <div class="col-md-10">
                                        <RealTimeMessages :databaseMessages="decryptedMessages" :messages="messages"
                                                          :new-message="newMessage"
                                                          @send-message="sendMessage"></RealTimeMessages>
                                    </div>
                                    <div class="col-md-2">
                                        <p><strong>Currently Online</strong></p>
                                        <ul id="online-users"
                                            class="list-unstyled overflow-auto text-info" style="height: 45vh">
                                            <li v-for="user in onlineUsers" :key="user.id">
                                                {{ user.name }}
                                            </li>
                                        </ul>
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
import {Link, router} from "@inertiajs/vue3";
import {computed, ref} from "vue";
import {usePage} from "@inertiajs/vue3";
import {route} from "ziggy-js";
import RealTimeMessages from "@/Pages/Users/Chat/RealTimeMessages.vue";

const props = defineProps(['user', 'friends', 'decryptedMessages']);

// Data
const recipientId = props.user.id;
const authUser = computed(() => usePage().props.authUser);
const onlineUsers = ref([]);
const messages = ref([]);
const newMessage = ref('');

// Real-time updates with Echo
const handleHere = (users) => {
    onlineUsers.value = users.map(user => user);
};

const handleJoining = (user) => {
    onlineUsers.value.push(user);
};

const handleLeaving = (user) => {
    onlineUsers.value = onlineUsers.value.filter((u) => u.id !== user.id);
};

Echo.join('chat')
    .here(handleHere)
    .joining(handleJoining)
    .leaving(handleLeaving);

const sendMessage = (msg, file = null) => {
    const formData = new FormData();
    formData.append('message', msg);

    if (file) {
        formData.append('file', file);
    }

    window.axios.post(route('chat.message', recipientId), formData, {
        headers: {
            'Content-Type': 'multipart/form-data'
        }
    });
};

// Real-time message listening
const handleReceivedMessage = (e) => {
    messages.value.push({user: e.user, messageObj: e.messageObj, message: e.plainMessage});
    // console.log({user: e.user, messageObj: e.messageObj, message: e.plainMessage});
};

const chatAuthChannel = 'chat.' + authUser.value.user.id;
const chatRecipientChannel = 'chat.' + recipientId;

Echo.private(chatAuthChannel)
    .listen('MessageSent', handleReceivedMessage);

Echo.private(chatRecipientChannel)
    .listen('MessageSent', handleReceivedMessage);
</script>
