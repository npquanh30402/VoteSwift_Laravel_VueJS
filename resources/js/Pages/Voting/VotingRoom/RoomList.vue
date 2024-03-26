<template>
    <div class="card shadow-sm border-0 mb-3 overflow-auto">
        <div class="card-header text-bg-dark text-center">Room List</div>
        <table class="table table-sm small table-bordered table align-middle mb-0">
            <!-- Table header -->
            <tr class="table-secondary">
                <th>ID</th>
                <th>Room Name</th>
                <th>Start Time</th>
                <th>End Time</th>
                <th>Timezone</th>
                <th>Details</th>
                <th>Delete</th>
            </tr>
            <!-- Table rows -->
            <tr v-for="room in paginatedRooms" :key="room.id">
                <td>{{ room.id }}</td>
                <td>{{ room.room_name }}</td>
                <td>{{ room.start_time }}</td>
                <td>{{ room.end_time }}</td>
                <td>{{ room.timezone }} ({{ getGmtOffset(room.timezone) }})</td>
                <td>
                    <div class="d-grid">
                        <Link :href="route('room.dashboard', room.id)" class="btn btn-sm btn-primary">Details</Link>
                    </div>
                </td>
                <td>
                    <div class="d-grid">
                        <button @click="showDeleteModal(room.id)" class="btn btn-sm btn-danger">Delete</button>
                    </div>
                </td>
            </tr>
        </table>
        <teleport to="body">
            <BaseModal title="Confirm Delete" id="deleteModal">
                Do you want to delete this room?
                <template #footer>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">No</button>
                    <button type="button" class="btn btn-danger" @click="deleteRoom">Yes</button>
                </template>
            </BaseModal>
        </teleport>
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
</template>

<script setup>
import {Link, router} from "@inertiajs/vue3";
import {DateTime} from "luxon";
import {route} from "ziggy-js";
import {computed, onMounted, ref} from "vue";
import * as bootstrap from 'bootstrap'
import BaseModal from "@/Components/BaseModal.vue";
import {VueAwesomePaginate} from "vue-awesome-paginate";

const props = defineProps(["rooms"]);

const onClickHandler = (page) => {
    currentPage.value = page
};

const currentPage = ref(1);
const paginatedRooms = computed(() => {
    const startIndex = (currentPage.value - 1) * 5;
    const endIndex = startIndex + 5;
    return props.rooms.slice(startIndex, endIndex);
});

const getGmtOffset = (timezone) => {
    const now = DateTime.now().setZone(timezone);
    const timezoneOffset = now.offset / 60;
    const offsetSign = timezoneOffset >= 0 ? "+" : "-";
    const offsetHours = Math.abs(Math.floor(timezoneOffset)).toString().padStart(2, "0");
    const offsetMinutes = Math.abs(Math.round((timezoneOffset % 1) * 60)).toString().padStart(2, "0");

    return `GMT${offsetSign}${offsetHours}:${offsetMinutes}`;
};

let modalRoomId = ref(-1);
let deleteModal;

onMounted(() => {
    deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
});

const showDeleteModal = (roomId) => {
    deleteModal.show();
    modalRoomId.value = roomId;
};

const deleteRoom = () => {
    router.delete(route("room.delete", modalRoomId.value));
    deleteModal.hide();
};
</script>
