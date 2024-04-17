<template>
    <div
        v-for="senderMessage in senderMessages"
        :key="generateUniqueKey"
        class="notification-list notification-list--unread"
    >
        <UserChatType :UserMessage="senderMessage.messages" />
    </div>
    <!--    <div>-->
    <!--        {{ senderMessages }}-->
    <!--    </div>-->
</template>
<script setup>
import { useHelper } from "@/Services/helper.js";
import { computed } from "vue";
import UserChatType from "@/Pages/Users/Chat/UserChatType.vue";

const props = defineProps(["messages"]);

const senderMessages = computed(() => {
    return Object.keys(props.messages).map((senderId) => ({
        senderId: parseInt(senderId), // Convert senderId to number if needed
        messages: props.messages[senderId],
    }));
});

const {
    removeSpecialCharacters,
    sanitizeAndTrim,
    extractFileName,
    truncateFileName,
    formatHour,
    formattedDate,
    truncateText,
    generateUniqueKey,
    isImage,
} = useHelper();
</script>

<style>
.list-enter-active,
.list-leave-active {
    transition: all 1s ease;
}

.list-enter-from,
.list-leave-to {
    opacity: 0;
    transform: translateX(30px);
}

.list-leave-active {
    position: absolute;
    top: -9999px;
}

.notification-list {
    display: -webkit-box;
    display: -ms-flexbox;
    display: flex;
    -webkit-box-pack: justify;
    -ms-flex-pack: justify;
    justify-content: space-between;
    padding: 20px;
    margin-bottom: 7px;
    background: #fff;
    -webkit-box-shadow: 0 3px 10px rgba(0, 0, 0, 0.06);
    box-shadow: 0 3px 10px rgba(0, 0, 0, 0.06);
}

.notification-list--unread {
    border-left: 2px solid #29b6f6;
}

.notification-list .notification-list_content {
    display: -webkit-box;
    display: -ms-flexbox;
    display: flex;
}

.notification-list .notification-list_content .notification-list_img img {
    height: 48px;
    width: 48px;
    border-radius: 50px;
    margin-right: 20px;
}

.notification-list .notification-list_content .notification-list_detail p {
    margin-bottom: 5px;
    line-height: 1.2;
}

.notification-list .notification-list_feature-img img {
    height: 48px;
    width: 48px;
    border-radius: 5px;
    margin-left: 20px;
}
</style>
