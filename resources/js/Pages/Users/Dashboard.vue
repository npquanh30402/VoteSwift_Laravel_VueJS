<template>
    <h1 class="display-6 text-center fw-bold">User Dashboard</h1>
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
import FriendList from "@/Pages/Users/FriendList.vue";

const props = defineProps(['rooms', 'authUserFriends']);

const currentTab = ref('RoomList')

const tabs = {
    RoomList,
    FriendList,
    UserSettings,
    MusicPlayerSettings,
}

const handleSwitchTab = (tabName) => {
    currentTab.value = tabName;
};
</script>
