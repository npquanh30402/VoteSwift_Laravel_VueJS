<template>
    <transition-group name="list" tag="div">
        <div
            v-for="notification in notifications.data"
            :key="notification.id"
            :class="{
                'notification-list--unread': !notification.read_at,
            }"
            class="notification-list"
        >
            <RoomCreationType
                v-if="notification.type === 'App\\Notifications\\RoomCreation'"
                :notification="notification"
            />

            <RoomPublishType
                v-if="notification.type === 'App\\Notifications\\RoomPublish'"
                :notification="notification"
            />

            <FriendRequestSendType
                v-if="
                    notification.type ===
                    'App\\Notifications\\FriendRequestSend'
                "
                :notification="notification"
            />

            <FriendRequestAcceptedType
                v-if="
                    notification.type ===
                    'App\\Notifications\\FriendRequestAccepted'
                "
                :notification="notification"
            />

            <InvitationType
                v-if="
                    notification.type ===
                    'App\\Notifications\\InvitationNotification'
                "
                :notification="notification"
            />

            <div
                v-if="!notification.read_at"
                class="d-flex flex-column justify-content-end gap-3"
            >
                <button
                    class="btn btn-outline-primary"
                    @click="handleMarkAsRead(notification.id, currentPage)"
                >
                    Mark as read
                </button>
            </div>
        </div>
    </transition-group>

    <!--            <div class="notification-list">-->
    <!--                <div class="notification-list_content">-->
    <!--                    <div class="notification-list_img">-->
    <!--                        <img src="https://i.imgur.com/ltXdE4K.jpg" alt="user">-->
    <!--                    </div>-->
    <!--                    <div class="notification-list_detail">-->
    <!--                        <p><b>Brian Cumin</b> reacted to your post</p>-->
    <!--                        <p class="text-muted">Lorem ipsum dolor sit amet consectetur, adipisicing elit. Unde,-->
    <!--                            dolorem.</p>-->
    <!--                        <p class="text-muted"><small>10 mins ago</small></p>-->
    <!--                    </div>-->
    <!--                </div>-->
    <!--                <div class="notification-list_feature-img">-->
    <!--                    <img src="https://i.imgur.com/bpBpAlH.jpg" alt="Feature image">-->
    <!--                </div>-->
    <!--            </div>-->
</template>
<script setup>
import { computed } from "vue";
import { useToast } from "vue-toast-notification";
import { useNotificationStore } from "@/Stores/notifications.js";
import RoomCreationType from "@/Pages/Users/Notification/Type/RoomCreationType.vue";
import RoomPublishType from "@/Pages/Users/Notification/Type/RoomPublishType.vue";
import FriendRequestSendType from "@/Pages/Users/Notification/Type/FriendRequestSendType.vue";
import FriendRequestAcceptedType from "@/Pages/Users/Notification/Type/FriendRequestAcceptedType.vue";
import InvitationType from "@/Pages/Users/Notification/Type/InvitationType.vue";

const props = defineProps(["notifications", "currentPage"]);
const toast = useToast();
const notificationStore = useNotificationStore();

const notifications = computed(() => props.notifications);
const currentPage = computed(() => props.currentPage);

const handleMarkAsRead = async (id, page) => {
    await notificationStore.markAsRead(id, page);
};
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
