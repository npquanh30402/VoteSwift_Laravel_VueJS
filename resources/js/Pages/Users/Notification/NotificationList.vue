<template>
    <div
        v-for="notification in notifications.data"
        :key="notification.id"
        class="notification-list"
        :class="{
            'notification-list--unread': !notification.read_at,
        }"
    >
        <div
            v-if="notification.type === 'App\\Notifications\\RoomCreation'"
            class="notification-list_content"
        >
            <div class="notification-list_detail">
                <p>You have created a new <b>Voting Room</b></p>
                <p class="text-muted">
                    Click
                    <Link
                        :href="
                            route('room.dashboard', notification.data.room_id)
                        "
                        >here
                    </Link>
                    to visit its dashboard.
                </p>
                <p class="text-muted">
                    <small>{{ formattedDate(notification.created_at) }}</small>
                </p>
            </div>
        </div>

        <div
            v-if="notification.type === 'App\\Notifications\\RoomPublish'"
            class="notification-list_content"
        >
            <div class="notification-list_detail">
                <p>You have published a great <b>Voting Room</b></p>
                <p class="text-muted">
                    Click
                    <Link
                        :href="
                            route('room.dashboard', notification.data.room_id)
                        "
                        >here
                    </Link>
                    to visit its dashboard.
                </p>
                <p class="text-muted">
                    <small>{{ formattedDate(notification.created_at) }}</small>
                </p>
            </div>
        </div>

        <div
            v-if="notification.type === 'App\\Notifications\\FriendRequestSend'"
            class="notification-list_content"
        >
            <Link
                :href="route('user.profile', notification.data.sender_id)"
                class="notification-list_img"
            >
                <img :src="notification.data.sender_avatar" alt="user" />
            </Link>
            <div class="notification-list_detail">
                <p>
                    <b>{{ notification.data.sender_username }}</b>
                    have sent you a friend request.
                </p>
                <p class="text-muted">
                    Click
                    <Link
                        class="link-success"
                        :href="
                            route(
                                'user.accept-friend-request',
                                notification.data.sender_id,
                            )
                        "
                        method="POST"
                        >here
                    </Link>
                    to accept.
                </p>
                <p class="text-muted">
                    Or
                    <Link
                        class="link-danger"
                        :href="
                            route(
                                'user.reject-friend-request',
                                notification.data.sender_id,
                            )
                        "
                        method="POST"
                        >here
                    </Link>
                    to reject.
                </p>
                <p class="text-muted">
                    <small>{{ formattedDate(notification.created_at) }}</small>
                </p>
            </div>
        </div>

        <div
            v-if="
                notification.type ===
                'App\\Notifications\\InvitationNotification'
            "
            class="notification-list_content"
        >
            <Link
                :href="route('user.profile', notification.data.sender_id)"
                class="notification-list_img"
            >
                <img :src="notification.data.sender_avatar" alt="user" />
            </Link>
            <div class="notification-list_detail">
                <p>
                    <b>{{ notification.data.sender_username }}</b>
                    have sent you an invitation link to his voting room. Please
                    check your email for more details.
                </p>
                <p class="text-muted">
                    <small>{{ formattedDate(notification.created_at) }}</small>
                </p>
            </div>
        </div>

        <div
            v-if="
                notification.type ===
                'App\\Notifications\\FriendRequestAccepted'
            "
            class="notification-list_content"
        >
            <Link
                :href="route('user.profile', notification.data.recipient_id)"
                class="notification-list_img"
            >
                <img :src="notification.data.recipient_avatar" alt="user" />
            </Link>
            <div class="notification-list_detail">
                <p>
                    <b>{{ notification.data.recipient_username }}</b>
                    have accepted your friend request.
                </p>
                <p class="text-muted">
                    <small>{{ formattedDate(notification.created_at) }}</small>
                </p>
            </div>
        </div>

        <div
            v-if="!notification.read_at"
            class="d-flex flex-column justify-content-end gap-3"
        >
            <button
                @click="handleMarkAsRead(notification.id, currentPage)"
                class="btn btn-outline-primary"
            >
                Mark as read
            </button>
        </div>
    </div>
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
import { route } from "ziggy-js";
import { Link } from "@inertiajs/vue3";
import { computed } from "vue";
import { useHelper } from "@/Services/helper.js";
import { useToast } from "vue-toast-notification";
import { useNotificationStore } from "@/Stores/notifications.js";

const props = defineProps(["notifications", "currentPage"]);
const toast = useToast();
const notificationStore = useNotificationStore();

const helper = useHelper();
const formattedDate = helper.formattedDate;

const notifications = computed(() => props.notifications);
const currentPage = computed(() => props.currentPage);

const handleMarkAsRead = async (id, page) => {
    const message = await notificationStore.markAsRead(id, page);

    if (message) {
        toast.success(message);
    }
};
</script>

<style scoped>
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
