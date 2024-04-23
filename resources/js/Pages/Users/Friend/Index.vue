<template>
    <div v-if="!isLoading" class="row">
        <div class="col-md-3">
            <FriendSideBar
                :authUserFriends="friends"
                @switch-tab="handleSwitchTab"
            />
        </div>
        <div class="col-md-9">
            <div class="card shadow-sm border-0 mb-3 overflow-auto">
                <div class="card-header text-bg-dark text-center">
                    {{ tabData[currentTab].name }}
                </div>
                <div class="card-body">
                    <transition mode="out-in" name="fade">
                        <component
                            :is="tabData[currentTab].component"
                            :authUserFriends="friends"
                        ></component>
                    </transition>
                </div>
            </div>
        </div>
    </div>
    <BaseLoading v-else />
</template>

<script setup>
import FriendSideBar from "@/Pages/Users/Friend/FriendSideBar.vue";
import { computed, onMounted, ref } from "vue";
import FriendList from "@/Pages/Users/Friend/FriendList.vue";
import FriendRequest from "@/Pages/Users/Friend/friendRequest.vue";
import RequestSent from "@/Pages/Users/Friend/RequestSent.vue";
import { useFriendStore } from "@/Stores/friends.js";
import { usePage } from "@inertiajs/vue3";
import BaseLoading from "@/Components/BaseLoading.vue";

const isLoading = ref(true);
const authUser = computed(() => usePage().props.authUser.user);

const friendStore = useFriendStore();
const friends = computed(() => friendStore.friends);

const currentTab = ref("FriendList");

const tabData = {
    FriendList: { component: FriendList, name: "Friend List" },
    FriendRequest: { component: FriendRequest, name: "Friend Request" },
    RequestSent: { component: RequestSent, name: "Request Sent" },
};

const handleSwitchTab = (tabName) => {
    currentTab.value = tabName;
};

onMounted(async () => {
    await friendStore.fetchFriends(authUser.value.id);

    isLoading.value = false;
});
</script>
