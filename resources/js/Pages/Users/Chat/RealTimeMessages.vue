<template>
        <div class="container py-5">

            <div class="row d-flex justify-content-center">
                <div class="">

                    <div class="card" id="chat2">
                        <div class="card-header d-flex justify-content-between align-items-center p-3 text-bg-dark">
                            <h5 class="mb-0">Chat</h5>
                        </div>
                        <div class="card-body overflow-auto" data-mdb-perfect-scrollbar="true" style="position: relative; height: 400px">
                            <div v-for="(message, index) in databaseMessages" :key="'realtime_' + index">
                                <div class="d-flex flex-row justify-content-start" v-if="!isYou(message.sender_id)">
                                    <img :src="message.avatar" :alt="'avatar_' + message.id" class="img-fluid rounded-circle" style="width: 45px; height: 100%;">
                                    <div>
                                        <p class="small p-2 ms-3 mb-1 rounded-3" style="background-color: #f5f6f7;">{{ message.message }}</p>
                                        <p class="small ms-3 mb-3 rounded-3 text-muted">{{ formatHour(message.send_date) }}</p>
                                    </div>
                                </div>
                                <div class="d-flex flex-row justify-content-end mb-4 pt-1" v-else>
                                    <div>
                                        <p class="small p-2 me-3 mb-1 text-white rounded-3 bg-primary">{{ message.message }}</p>
                                        <p class="small me-3 mb-3 rounded-3 text-muted d-flex justify-content-end">{{ formatHour(message.send_date) }}</p>
                                    </div>
                                    <img :src="message.avatar" :alt="'avatar_' + message.id" class="img-fluid rounded-circle" style="width: 45px; height: 100%;">
                                </div>
                            </div>
                            <div v-for="(message, index) in messages" :key="'realtime_' + index">
                                <div class="d-flex flex-row justify-content-start" v-if="!isYou(message.user.id)">
                                    <img :src="message.user.avatar" :alt="'avatar_' + message.user.id" class="img-fluid rounded-circle" style="width: 45px; height: 100%;">
                                    <div>
                                        <p class="small p-2 ms-3 mb-1 rounded-3" style="background-color: #f5f6f7;">{{ message.message }}</p>
                                        <p class="small ms-3 mb-3 rounded-3 text-muted">{{ formatHour(message.messageObj.created_at) }}</p>
                                    </div>
                                </div>
                                <div class="d-flex flex-row justify-content-end mb-4 pt-1" v-else>
                                    <div>
                                        <p class="small p-2 me-3 mb-1 text-white rounded-3 bg-primary">{{ message.message }}</p>
                                        <p class="small me-3 mb-3 rounded-3 text-muted d-flex justify-content-end">{{ formatHour(message.messageObj.created_at) }}</p>
                                    </div>
                                    <img :src="message.user.avatar" :alt="'avatar_' + message.user.id" class="img-fluid rounded-circle" style="width: 45px; height: 100%;">
                                </div>
                            </div>

<!--                            <div class="divider d-flex align-items-center mb-4">-->
<!--                                <p class="text-center mx-3 mb-0" style="color: #a2aab7;">Today</p>-->
<!--                            </div>-->

                        </div>
                        <form @submit.prevent="sendMessage" class="card-footer text-muted d-flex justify-content-start align-items-center p-3">
                            <img :src="authUser.avatar"
                                 alt="avatar 3" style="width: 40px; height: 100%;">
                            <input type="text" class="form-control form-control-lg" id="exampleFormControlInput1"
                                   placeholder="Type message" v-model="newMessage">
                            <a class="ms-1 text-muted" href="#"><i class="bi bi-paperclip"></i></a>
                            <a class="ms-3 text-muted" href="#"><i class="bi bi-emoji-smile"></i></a>
                            <button type="submit" class="ms-3 btn btn-primary" href="#"><i class="bi bi-send"></i></button>
                        </form>
                    </div>

                </div>
            </div>

        </div>
</template>

<script setup>
import {computed, ref} from 'vue';
import {usePage} from "@inertiajs/vue3";
import date from 'date-and-time';

const props = defineProps(['messages', 'new-message', 'databaseMessages'])

const authUser = computed(() => usePage().props.authUser);

const messages = ref(props['messages']);
const newMessage = ref(props["new-message"]);

const emit = defineEmits(['send-message'])

const isYou = (msgUserId) => {
    return msgUserId === authUser.value.id
}

const sendMessage = () => {
    emit('send-message', newMessage.value);
    newMessage.value = '';
};

function formatHour(dt) {
    const fdt = new Date(dt);
    return date.format(fdt, 'HH:mm');
}

</script>

<style scoped>
#chat2 .form-control {
    border-color: transparent;
}

#chat2 .form-control:focus {
    border-color: transparent;
    box-shadow: inset 0px 0px 0px 1px transparent;
}

.divider:after,
.divider:before {
    content: "";
    flex: 1;
    height: 1px;
    background: #eee;
}
</style>
