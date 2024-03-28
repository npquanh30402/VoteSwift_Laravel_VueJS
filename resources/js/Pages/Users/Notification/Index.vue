<template>
    <div class="my-3 mx-5">
        <h3 class="m-b-50 heading-line">Notifications <i class="bi bi-bell-fill"></i></h3>

        <div v-if="notifications.data.length" class="notification-ui_dd-content">
            <div v-for="notification in notifications.data" :key="notification.id"
                 class="notification-list" :class="{'notification-list--unread': !notification.read_at}">
                <div v-if="notification.type === 'App\\Notifications\\RoomCreation'" class="notification-list_content">
                    <div class="notification-list_detail">
                        <p>You have created a new <b>Voting Room</b></p>
                        <p class="text-muted">Click
                            <Link :href="route('room.dashboard', notification.data.room_id)">here</Link>
                            to visit its dashboard.
                        </p>
                        <p class="text-muted"><small>{{ formattedDate(notification.created_at) }}</small></p>
                    </div>

                    <!--                    <div class="notification-list_img">-->
                    <!--                        <img src="https://i.imgur.com/zYxDCQT.jpg" alt="user">-->
                    <!--                    </div>-->
                    <!--                    <div class="notification-list_detail">-->
                    <!--                        <p><b>John Doe</b> reacted to your post</p>-->
                    <!--                        <p class="text-muted">Lorem ipsum dolor sit amet consectetur, adipisicing elit. Unde,-->
                    <!--                            dolorem.</p>-->
                    <!--                        <p class="text-muted"><small>10 mins ago</small></p>-->
                    <!--                    </div>-->
                </div>
                <div v-if="!notification.read_at" class="d-flex flex-column justify-content-end gap-3">
                    <Link :href="route('notification.read', notification.id)" method="PUT" as="button"
                          class="btn btn-outline-primary">Mark
                        as
                        read
                    </Link>
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
        </div>

        <div v-if="notifications.data.length">
            <Pagination :links="notifications.links"/>
        </div>
    </div>
</template>

<script setup>
import {Link} from "@inertiajs/vue3";
import {route} from "ziggy-js";
import {formatDistanceToNow} from "date-fns/formatDistanceToNow";
import {computed} from "vue";
import Pagination from "@/Components/Pagination.vue";

defineProps(['notifications'])

const formattedDate = computed(() => (date) => formatDistanceToNow(new Date(date), {
    includeSeconds: true,
    addSuffix: true
}))

</script>

<style scoped>
body {
    font-family: "Roboto", sans-serif;
    background: #EFF1F3;
    min-height: 100vh;
    position: relative;
}

.m-b-50 {
    margin-bottom: 50px;
}

.dark-link {
    color: #333;
}

.heading-line {
    position: relative;
    padding-bottom: 5px;
}

.heading-line:after {
    content: "";
    height: 4px;
    width: 75px;
    background-color: #29B6F6;
    position: absolute;
    bottom: 0;
    left: 0;
}

.notification-ui_dd-content {
    margin-bottom: 30px;
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
    border-left: 2px solid #29B6F6;
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
