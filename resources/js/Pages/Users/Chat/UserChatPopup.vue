<template>
    <div class="row justify-content-center">
        <div class="col-md-4">
            <div class="list-group shadow-sm small mb-3">
                <div class="list-group-item text-bg-dark">Friend lists</div>
                <UserChatList
                    :currentReceiver="currentReceiver"
                    :friends="friends"
                    @change-user="handleChangeUser"
                />
            </div>
        </div>
        <div class="col-md-8">
            <div class="card shadow-sm border-0 mb-3 overflow-auto">
                <div
                    v-if="currentReceiver === null"
                    class="card-header text-bg-dark text-center"
                >
                    Pick a friend to chat
                </div>
                <div v-else class="card-header text-bg-dark text-center">
                    {{ currentReceiver.username }}
                </div>
                <div v-if="currentReceiver">
                    <transition mode="out-in" name="fade">
                        <KeepAlive>
                            <component
                                :is="UserMessage"
                                :key="generateUniqueKey()"
                                :currentReceiver="currentReceiver"
                                @send-message="sendMessage"
                            ></component>
                        </KeepAlive>
                    </transition>
                </div>
                <div
                    :class="{
                        'un-interactive': currentReceiver === null,
                    }"
                    class="card"
                >
                    <form
                        class="card-footer text-muted d-flex justify-content-start align-items-center p-3"
                        @submit.prevent="sendMessage(newMessage)"
                    >
                        <img
                            :src="authUser.avatar"
                            alt="avatar 3"
                            style="width: 40px; height: 100%"
                        />
                        <input
                            id="exampleFormControlInput1"
                            v-model="newMessage"
                            class="form-control form-control-lg"
                            placeholder="Type message"
                            type="text"
                        />
                        <div>
                            <label for="fileInput" style="cursor: pointer">
                                <i class="bi bi-paperclip"></i>
                            </label>
                            <input
                                id="fileInput"
                                hidden
                                type="file"
                                @change="handleFileUpload"
                            />
                        </div>
                        <button
                            class="ms-3 btn btn-primary"
                            href="#"
                            type="submit"
                        >
                            <i class="bi bi-send"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { usePage } from "@inertiajs/vue3";
import { computed, onMounted, ref } from "vue";
import UserChatList from "@/Pages/Users/Chat/UserChatList.vue";
import { useHelper } from "@/Services/helper.js";
import { useUserChatStore } from "@/Stores/user-chat.js";
import UserMessage from "@/Pages/Users/Chat/UserMessage.vue";
import { useFriendStore } from "@/Stores/friends.js";

const { sanitizeAndTrim, generateUniqueKey } = useHelper();

const props = defineProps(["currentReceiver"]);
const emit = defineEmits(["change-user"]);

const friendStore = useFriendStore();
const friends = computed(() => friendStore.friends.friends);

onMounted(() => {
    friendStore.fetchFriends();
});

const { storeMessage } = useUserChatStore();

const authUser = computed(() => usePage().props.authUser.user);
const currentReceiver = ref(
    props.currentReceiver ? props.currentReceiver : null,
);
const newMessage = ref("");
const handleChangeUser = async (user) => {
    currentReceiver.value = user;
    emit("change-user", user);
};

const handleFileUpload = (event) => {
    const uploadedFile = event.target.files[0];

    sendMessage(null, uploadedFile);
};

const sendMessage = (msg, file = null) => {
    const formData = new FormData();

    if (file) {
        formData.append("file", file);
    } else {
        formData.append("message", sanitizeAndTrim(msg));
    }

    storeMessage(authUser.value.id, currentReceiver.value.id, formData);

    newMessage.value = null;
};
</script>
