<template>
    <div class="row">
        <div class="col">
            <h1 class="display-6 text-center fw-bold">Chat</h1>
        </div>
    </div>
    <div class="my-3">
        <div class="row justify-content-center">
            <div class="col-md-2">
                <div class="list-group shadow-sm small mb-3">
                    <div class="list-group-item text-bg-dark">Friend lists</div>
                    <UserChatList :friends="friends" @change-user="handleChangeUser"/>
                </div>
            </div>
            <div class="col-md-8">
                <div class="card shadow-sm border-0 mb-3 overflow-auto">
                    <div class="card-header text-bg-dark text-center"
                         v-if="currentRecipient === null">Pick a friend to
                        chat
                    </div>
                    <div class="card-header text-bg-dark text-center" v-else>Chat with {{
                            currentRecipient.username
                        }}
                    </div>
                    <div v-if="currentRecipient">
                        <transition name="fade" mode="out-in">
                            <component :is="UserMessage" :key="currentRecipient?.id" :messages="filteredMessages"
                                       :currentRecipient="currentRecipient"
                                       @send-message="sendMessage"></component>
                        </transition>
                    </div>
                    <div class="card">
                        <form @submit.prevent="sendMessage(newMessage)"
                              class="card-footer text-muted d-flex justify-content-start align-items-center p-3">
                            <img :src="authUser.user.avatar"
                                 alt="avatar 3" style="width: 40px; height: 100%;">
                            <input type="text" class="form-control form-control-lg" id="exampleFormControlInput1"
                                   placeholder="Type message" v-model="newMessage">
                            <div>
                                <label for="fileInput" style="cursor:pointer;">
                                    <i class="bi bi-paperclip"></i>
                                </label>
                                <input id="fileInput" type="file" @change="handleFileUpload" hidden/>
                            </div>
                            <a class="ms-3 text-muted" href="#"><i class="bi bi-emoji-smile"></i></a>
                            <button type="submit" class="ms-3 btn btn-primary" href="#"><i class="bi bi-send"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import {usePage} from "@inertiajs/vue3";
import {computed, onMounted, ref, watch} from "vue";
import {route} from "ziggy-js";
import UserChatList from "@/Pages/Users/Chat/UserChatList.vue";
import UserMessage from "@/Pages/Users/Chat/UserMessage.vue";
import {useChatStore} from "@/Stores/chat.js";

const props = defineProps(['friends']);

const authUser = computed(() => usePage().props.authUser);
const messages = ref([]);
const newMessage = ref();
const currentRecipient = ref(null)

const ChatStore = useChatStore()

onMounted(async () => {
    await ChatStore.fetchUnreadAll()
})

const handleChangeUser = async (user) => {
    currentRecipient.value = user;
    messages.value = [];
};

const handleFileUpload = (event) => {
    const uploadedFile = event.target.files[0];

    sendMessage(null, uploadedFile);
};

const sendMessage = (msg, file = null) => {
    const formData = new FormData();
    formData.append('message', msg);

    // const data = {
    //     id: null,
    //     sender_id: authUser.value.user.id,
    //     avatar: authUser.value.user.avatar,
    //     file: file ? URL.createObjectURL(file) : null,
    //     message: msg,
    //     send_date: new Date().toISOString(),
    //     sender: authUser.value.user.username,
    // };
    //
    // console.log(data)

    if (file) {
        formData.append('file', file);
    }

    window.axios.post(route('api.user.chat.store', currentRecipient.value.id), formData, {
        headers: {
            'Content-Type': 'multipart/form-data'
        }
    });

    // console.log(formData)
    //
    // ChatStore.storeMessage(currentRecipient.value.id, data);

    newMessage.value = null;
};

const filteredMessages = computed(() => {
    return messages.value.filter(message => {
        return (message.messageObj.sender_id === authUser.value.user.id && message.messageObj.receiver_id === currentRecipient.value.id) ||
            (message.messageObj.sender_id === currentRecipient.value.id && message.messageObj.receiver_id === authUser.value.user.id);
    });
});

const handleReceivedMessage = (e) => {
    messages.value.push({user: e.user, messageObj: e.messageObj, message: e.plainMessage});
};

const setupEchoListeners = () => {
    if (authUser.value && authUser.value.user) {
        Echo.private(`chat.${authUser.value.user.id}`).listen("MessageSent", handleReceivedMessage);
    }
};

watch(currentRecipient, (newRecipient, oldRecipient) => {
    if (newRecipient !== null) {
        if (oldRecipient !== null) {
            Echo.leave(`chat.${oldRecipient.id}`);
        }
        Echo.private(`chat.${newRecipient.id}`).listen("MessageSent", handleReceivedMessage);
    }
});

onMounted(() => {
    setupEchoListeners()
})
</script>
