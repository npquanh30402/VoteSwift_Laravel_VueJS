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
            <tr v-for="room in rooms" :key="room.id">
                <td>{{ room.id }}</td>
                <td>{{ room.room_name }}</td>
                <td>{{ room.start_time }}</td>
                <td>{{ room.end_time }}</td>
                <td>{{ room.timezone }} ({{ gmtOffset(room.timezone) }})</td>
                <td>
                    <div class="d-grid">
                        <!--                        <Link :href="" class="btn btn-sm btn-primary">Details</Link>-->
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
    </div>
</template>

<script setup>
import {Link, router} from "@inertiajs/vue3";
import {DateTime} from "luxon";
import {route} from "ziggy-js";
import {onMounted, ref} from "vue";
import * as bootstrap from 'bootstrap'
import BaseModal from "@/Components/BaseModal.vue";

defineProps(["rooms"]);

const gmtOffset = (timezone) => {
    const now = DateTime.now().setZone(timezone);
    const timezoneOffset = now.offset / 60;
    const offsetSign = timezoneOffset >= 0 ? "+" : "-";
    const offsetHours = Math.abs(Math.floor(timezoneOffset));
    const offsetMinutes = Math.abs(Math.round((timezoneOffset % 1) * 60));

    return `GMT${offsetSign}${offsetHours
        .toString()
        .padStart(2, "0")}:${offsetMinutes.toString().padStart(2, "0")}`;
};

let modalRoomId = ref(-1);
let modal;

onMounted(() => {
    modal = new bootstrap.Modal(document.getElementById('deleteModal'));
});

const showDeleteModal = (roomId) => {
    modal.show();

    modalRoomId = roomId;
};

const deleteRoom = () => {
    router.delete(route("room.delete", modalRoomId));
    modal.hide();
};
</script>
