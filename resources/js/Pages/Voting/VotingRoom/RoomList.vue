<template>
    <div>
        <div class="card shadow-sm border-0 mb-3 overflow-auto">
            <div class="card-header text-bg-dark text-center">Room List</div>
            <table class="table table-sm small table-bordered table align-middle mb-0">
                <!-- Table header -->
                <tr class="table-secondary">
                    <th>ID</th>
                    <th>Room Name</th>
                    <th>Start Time</th>
                    <th>End Time</th>
                    <th>Status</th>
                    <th>Details</th>
                </tr>
                <!-- Table rows -->
                <tr v-for="room in paginatedRooms" :key="room.id">
                    <td>{{ room.id }}</td>
                    <td>{{ room.room_name }}</td>
                    <td>{{ formattedDate(room?.start_time) }}
                    </td>
                    <td>{{ formattedDate(room?.end_time) }}</td>
                    <td>{{ room.is_published === 1 ? 'Published' : 'Draft' }}</td>
                    <td>
                        <div class="d-grid">
                            <Link :href="route('room.dashboard', room.id)" class="btn btn-sm btn-secondary">Details
                            </Link>
                        </div>
                    </td>
                </tr>
            </table>
        </div>
        <div class="d-flex justify-content-end">
            <vue-awesome-paginate
                :total-items="rooms.length"
                :items-per-page="5"
                :max-pages-shown="5"
                v-model="currentPage"
                :on-click="onClickHandler"
                v-if="rooms.length"
            />
        </div>
    </div>
</template>

<script setup>
import {Link} from "@inertiajs/vue3";
import {route} from "ziggy-js";
import {computed, ref} from "vue";
import {VueAwesomePaginate} from "vue-awesome-paginate";
import useFormattedDate from "@/Composables/useFormattedDate.js";

const props = defineProps(["rooms"]);

const onClickHandler = (page) => {
    currentPage.value = page
};

const formattedDate = useFormattedDate();

const currentPage = ref(1);
const paginatedRooms = computed(() => {
    const startIndex = (currentPage.value - 1) * 5;
    const endIndex = startIndex + 5;
    return props.rooms.slice(startIndex, endIndex);
});
</script>
