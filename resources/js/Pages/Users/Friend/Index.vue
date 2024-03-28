<template>
    <div class="row">
        <div class="col-md-3">
            <FriendSideBar :authUserFriends="authUserFriends" @switch-tab="handleSwitchTab"/>
        </div>
        <div class="col-md-9">
            <transition name="fade" mode="out-in">
                <component :is="tabs[currentTab]" :authUserFriends="authUserFriends"></component>
            </transition>
        </div>
    </div>
</template>
n
<script setup>
import FriendSideBar from "@/Pages/Users/Friend/FriendSideBar.vue";
import {computed, ref} from "vue";
import FriendList from "@/Pages/Users/Friend/FriendList.vue";
import FriendRequest from "@/Pages/Users/Friend/friendRequest.vue";
import RequestSent from "@/Pages/Users/Friend/RequestSent.vue";

const props = defineProps(['authUserFriends']);

const authUserFriends = computed(() => props.authUserFriends);

const currentTab = ref('FriendList')

const tabs = {
    FriendList,
    FriendRequest,
    RequestSent,
}

const handleSwitchTab = (tabName) => {
    currentTab.value = tabName;
};
</script>
