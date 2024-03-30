<template>
    <h1 class="display-6 text-center fw-bold">User Dashboard
        <i class="bi bi-arrow-clockwise icon" @click="router.reload()" style="cursor: pointer"></i>
    </h1>
    <div class="my-3">
        <div class="row justify-content-center">
            <div class="col-md-2">
                <UserSidebar @switch-tab="handleSwitchTab"></UserSidebar>
            </div>
            <div class="col-md-8">
                <transition name="fade" mode="out-in">
                    <component :is="tabs[currentTab]" :rooms="rooms" :authUserFriends="authUserFriends"></component>
                </transition>
            </div>
        </div>
    </div>
</template>

<script setup>
import UserSidebar from "@/Pages/Users/UserSidebar.vue";
import {ref} from "vue";
import RoomList from "@/Pages/Voting/VotingRoom/RoomList.vue";
import UserSettings from "@/Pages/Users/UserSettings.vue";
import MusicPlayerSettings from "@/Pages/Users/MusicPlayerSettings.vue";
import Friend from "@/Pages/Users/Friend/Index.vue";
import {router} from "@inertiajs/vue3";
import UserCalendar from "@/Pages/Users/UserCalendar.vue";

const props = defineProps(['rooms', 'authUserFriends']);

const currentTab = ref('RoomList')

const tabs = {
    RoomList,
    Friend,
    UserSettings,
    MusicPlayerSettings,
    UserCalendar,
}

const handleSwitchTab = (tabName) => {
    currentTab.value = tabName;
};
</script>
