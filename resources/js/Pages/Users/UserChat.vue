<template>
    <div>
        <div class="row">
            <div class="col">
                <h1 class="display-6 text-center fw-bold">Chat</h1>
            </div>
        </div>
        <div class="my-3 mx-5">
            <UserChatPopup />
        </div>
    </div>
</template>

<script setup>
import UserChatPopup from "@/Pages/Users/Chat/UserChatPopup.vue";
</script>
