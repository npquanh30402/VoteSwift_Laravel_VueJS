<template>
    <div v-if="!isLoading">
        <div class="card">
            <DataTable
                v-model:filters="filters"
                :globalFilterFields="['id', 'room_name', 'displayStatus']"
                :rows="5"
                :rowsPerPageOptions="[5, 10, 20, 50]"
                :value="rooms"
                dataKey="id"
                filterDisplay="row"
                paginator
                removableSort
                tableStyle="min-width: 50rem"
            >
                <template #header>
                    <div class="d-flex justify-content-end">
                        <div class="d-flex align-items-center gap-3">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"
                                        ><i class="bi bi-search"></i
                                    ></span>
                                </div>
                                <input
                                    v-model="filters['global'].value"
                                    class="form-control"
                                    placeholder="Keyword Search"
                                />
                            </div>
                        </div>
                    </div>
                </template>
                <template #empty> No data found.</template>
                <Column field="id" header="ID" sortable></Column>

                <Column filterField="room_name" header="Room Name">
                    <template #body="{ data }">
                        {{ data.room_name }}
                    </template>
                    <template #filter="{ filterModel, filterCallback }">
                        <input
                            v-model="filterModel.value"
                            class="form-control"
                            placeholder="Search by room name"
                            type="text"
                            @input="filterCallback()"
                        />
                    </template>
                </Column>

                <Column field="start_time" header="Start Time" sortable>
                    <template #body="slotProps">
                        {{ formatDate(slotProps.data.start_time) }}
                    </template>
                </Column>
                <Column field="end_time" header="End Time" sortable>
                    <template #body="slotProps">
                        {{ formatDate(slotProps.data.end_time) }}
                    </template>
                </Column>
                <Column field="displayStatus" header="Status" sortable>
                    <template #body="slotProps">
                        <span
                            :class="
                                slotProps.data.displayStatus === 'Published'
                                    ? 'text-bg-success'
                                    : 'text-bg-warning'
                            "
                            class="badge"
                            >{{ slotProps.data.displayStatus }}</span
                        >
                    </template>
                </Column>
                <Column header="Action">
                    <template #body="slotProps">
                        <div class="d-grid">
                            <Link
                                :href="
                                    route('room.dashboard', slotProps.data.id)
                                "
                                class="btn btn-sm btn-secondary"
                                >Details
                            </Link>
                        </div>
                    </template>
                </Column>
            </DataTable>
        </div>
    </div>
    <BaseLoading v-else />
</template>

<script setup>
import { Link } from "@inertiajs/vue3";
import { route } from "ziggy-js";
import { computed, onMounted, ref } from "vue";
import "vue-awesome-paginate/dist/style.css";
import DataTable from "primevue/datatable";
import Column from "primevue/column";
import { useHelper } from "@/Services/helper.js";
import { useVotingRoomStore } from "@/Stores/voting-room.js";
import BaseLoading from "@/Components/BaseLoading.vue";
import { FilterMatchMode } from "primevue/api";

const roomStore = useVotingRoomStore();
const helper = useHelper();
const isLoading = ref(true);

const rooms = computed(() => {
    return roomStore.rooms.map((item) => {
        return {
            ...item,
            displayStatus: item.is_published === 1 ? "Published" : "Draft",
        };
    });
});

const formatDate = helper.formatDate;

const filters = ref({
    global: { value: null, matchMode: FilterMatchMode.CONTAINS },
    room_name: { value: null, matchMode: FilterMatchMode.CONTAINS },
});

// const vueRouter = useRouter();
//
// function goToDashboard(roomId) {
//     vueRouter.push({ name: "room-dashboard", params: { roomId: roomId } });
// }

onMounted(async () => {
    await roomStore.fetchRooms();

    isLoading.value = false;
});
</script>
