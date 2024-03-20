<template>
    <h1 class="display-6 text-center fw-bold">User Dashboard</h1>
    <div class="my-3">
        <div class="row justify-content-center">
            <div class="col-md-2">
                <UserSidebar></UserSidebar>
            </div>
            <div class="col-md-8">
                <RoomList :rooms="rooms"></RoomList>
            </div>
        </div>
    </div>
</template>

<script setup>

import RoomList from "@/Pages/Users/RoomList.vue";
import UserSidebar from "@/Pages/Users/UserSidebar.vue";

defineProps(['rooms']);
</script>
