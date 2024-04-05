<template>
    <div class="list-group-item list-group-item-action" :class="{'active': currentUser === friend}"
         v-for="friend in friends"
         :key="friend.id" @click="changeUser(friend)">
        <div class="d-flex align-items-center">
            <img :src="friend.avatar" class="rounded-circle me-3" style="width: 50px" alt="">
            <span class="fs-5"><strong>{{ friend.username }}</strong></span>
        </div>
    </div>
</template>

<script setup>

import {ref} from "vue";

const props = defineProps(['friends'])

const emit = defineEmits(['change-user'])
let currentUser = ref();
const changeUser = (user) => {
    currentUser.value = user
    emit('change-user', user)
}
</script>
