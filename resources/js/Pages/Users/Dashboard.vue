<template>
    <h1 class="display-6 text-center fw-bold">User Dashboard</h1>
    <div class="my-3">
        <div class="row justify-content-center">
            <div class="col-md-2">
                <UserSidebar></UserSidebar>
            </div>
            <div class="col-md-8">
                <RoomList :rooms="paginatedRooms"></RoomList>
                <div class="d-flex justify-content-end">
                    <vue-awesome-paginate
                        :total-items="rooms.length"
                        :items-per-page="5"
                        :max-pages-shown="5"
                        v-model="currentPage"
                        :on-click="onClickHandler"
                    />
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>

import RoomList from "@/Pages/Users/RoomList.vue";
import UserSidebar from "@/Pages/Users/UserSidebar.vue";
import {VueAwesomePaginate} from "vue-awesome-paginate";
import {computed, ref} from "vue";

const props = defineProps(['rooms']);

const onClickHandler = (page) => {
    currentPage.value = page
};

const currentPage = ref(1);
const paginatedRooms = computed(() => {
    const startIndex = (currentPage.value - 1) * 5;
    const endIndex = startIndex + 5;
    return props.rooms.slice(startIndex, endIndex);
});
</script>

<style>
.pagination-container {
    display: flex;
    column-gap: 10px;
}

.paginate-buttons {
    height: 40px;
    width: 40px;
    border-radius: 20px;
    cursor: pointer;
    background-color: rgb(242, 242, 242);
    border: 1px solid rgb(217, 217, 217);
    color: black;
}

.paginate-buttons:hover {
    background-color: #d8d8d8;
}

.active-page {
    background-color: #3498db;
    border: 1px solid #3498db;
    color: white;
}

.active-page:hover {
    background-color: #2988c8;
}
</style>
