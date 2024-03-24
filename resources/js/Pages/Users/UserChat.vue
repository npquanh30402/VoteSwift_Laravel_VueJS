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
                                        <!--                                        <div class="vstack">-->
                                        <!--                                            <div class="col-md-12 border rounded-lg p-3">-->
                                        <!--                                                <ul id="messages" class="list-unstyled overflow-auto" style="height: 45vh">-->
                                        <!--                                                    <li v-for="message in decryptedMessages" :key="'decrypted_' + message.id" class="vstack list-group-item mb-2">-->
                                        <!--                                                        <div>-->
                                        <!--                                                            <img :src="message.avatar" alt="Avatar" class="img-fluid rounded-circle me-2" style="width: 30px; height: auto;">-->
                                        <!--                                                            <strong>{{ message.sender }}</strong>: {{ message.message }}-->
                                        <!--                                                        </div>-->
                                        <!--                                                        <div class="d-block ms-auto">-->
                                        <!--                                                            <span class="text-muted small"></span>-->
                                        <!--                                                        </div>-->
                                        <!--                                                        <div v-if="message.file">-->
                                        <!--                                                            <br>-->
                                        <!--                                                            <img v-if="isImage(message.file)" :src="message.file" class="img-fluid" style="width: 200px; height: auto;" alt="image">-->
                                        <!--                                                            <a v-else :href="message.file" class="btn btn-sm btn-primary w-50" download>Download: {{ getFileName(message.file) }}</a>-->
                                        <!--                                                        </div>-->
                                        <!--                                                    </li>-->
                                        <!--                                                    <li v-for="(message, index) in messages" :key="'realtime_' + index" class="vstack list-group-item mb-2">-->
                                        <!--                                                        <div>-->
                                        <!--                                                            <strong>{{ message.id }}</strong>: {{ message.encrypted_content }}-->
                                        <!--                                                        </div>-->
                                        <!--                                                        <div class="d-block ms-auto">-->
                                        <!--                                                        </div>-->
                                        <!--                                                    </li>-->
                                        <!--                                                </ul>-->
                                        <!--                                            </div>-->
                                        <!--                                            <div>-->
                                        <!--                                                <form @submit.prevent="sendMessage" class="row py-3">-->
                                        <!--                                                    <div class="col-md-6">-->
                                        <!--                                                        <input id="message" name="message" class="form-control" type="text" v-model="newMessage" placeholder="Type your message...">-->
                                        <!--                                                    </div>-->
                                        <!--                                                    <div class="col-md-4">-->
                                        <!--                                                    </div>-->
                                        <!--                                                    <div class="col-md-2 d-grid">-->
                                        <!--                                                        <button id="send" @click="sendMessage" class="btn btn-primary">Send</button>-->
                                        <!--                                                    </div>-->
                                        <!--                                                </form>-->
                                        <!--                                            </div>-->
                                        <!--                                        </div>-->
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

    console.log(formData, msg, file);

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

const chatAuthChannel = 'chat.' + authUser.value.id;
const chatRecipientChannel = 'chat.' + recipientId;

Echo.private(chatAuthChannel)
    .listen('MessageSent', handleReceivedMessage);

Echo.private(chatRecipientChannel)
    .listen('MessageSent', handleReceivedMessage);
</script>
