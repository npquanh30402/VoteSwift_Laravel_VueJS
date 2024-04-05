<template>
    <div class="list-group-item list-group-item-action" :class="{'active': currentUser === friend}"
         v-for="friend in friends"
         :key="friend.id" @click="changeUser(friend)">
        <div class="d-flex align-items-center justify-content-between overflow-auto">
            <div class="d-flex align-items-center">
                <img :src="friend.avatar" class="rounded-circle me-3" style="width: 50px" alt="">
                <span class="fs-5"><strong>{{ friend.username }}</strong></span>
            </div>
            <span class="badge text-bg-danger"
                  v-if="unreadMessagesCount && unreadMessagesCount[friend.id] > 0 && currentUser !== friend">{{
                    unreadMessagesCount[friend.id]
                }}</span>
        </div>
    </div>
</template>

<script setup>

import {computed, ref} from "vue";
import {useChatStore} from "@/Stores/chat.js";

const props = defineProps(['friends'])

const emit = defineEmits(['change-user'])

const ChatStore = useChatStore()
const unreadMessagesCount = computed(() => ChatStore.unreadCountsBySender)

let currentUser = ref();
const changeUser = (user) => {
    currentUser.value = user
    emit('change-user', user)
    console.log(unreadMessagesCount?.value)
}
</script>
