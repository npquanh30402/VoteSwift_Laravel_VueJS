<template>
    <div class="row">
        <div class="col mb-3">
            <h1 class="display-6 text-center fw-bold">Public Voting Room</h1>
        </div>

    </div>
    <div class="row row-cols-3 gy-4 mb-4">
        <div class="d-flex align-items-stretch" v-for="(room, index) in publicRooms.data">
            <div class="card text-center">
                <div class="card-header">
                    Room #{{ index + 1 }}
                </div>
                <div class="card-body">
                    <h5 class="card-title">{{ room.name }}</h5>
                    <p class="card-text text-truncate">{{ room.room_description }}</p>
                    <Link :href="route('vote.main', room.id)" class="btn btn-primary">Enter</Link>
                </div>
                <div class="card-footer text-body-secondary">
                    {{
                        formatDate(room.created_at)
                    }}
                </div>
            </div>
        </div>
    </div>
    <div class="d-flex justify-content-center my-3" v-if="publicRooms.data.length">
        <Pagination :links="publicRooms.links"/>
    </div>
</template>

<script setup>
import {Link} from "@inertiajs/vue3";
import {intlFormatDistance} from "date-fns";
import {route} from "ziggy-js";
import {VueAwesomePaginate} from "vue-awesome-paginate";
import {computed, ref} from "vue";
import Pagination from "@/Components/Pagination.vue";

const props = defineProps(['publicRooms'])

function formatDate(date) {
    return intlFormatDistance(new Date(), new Date(date), {numeric: 'always'}, {localeMatcher: 'lookup'})
}

const onClickHandler = (page) => {
    currentPage.value = page
};

const currentPage = ref(1);
const paginatedRooms = computed(() => {
    const startIndex = (currentPage.value - 1) * 9;
    const endIndex = startIndex + 9;
    return props.publicRooms.slice(startIndex, endIndex);
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
