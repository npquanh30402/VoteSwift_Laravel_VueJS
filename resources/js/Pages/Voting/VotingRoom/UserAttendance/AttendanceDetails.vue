<template>
    <div v-if="!isLoading">
        <div class="card">
            <DataTable
                v-model:expandedRows="expandedRows"
                v-model:filters="filters"
                :globalFilterFields="['username', 'email']"
                :rows="10"
                :rowsPerPageOptions="[5, 10, 20, 50]"
                :value="Object.values(attendances)"
                dataKey="id"
                paginator
                removableSort
                tableStyle="min-width: 60rem"
                @rowCollapse="onRowCollapse"
                @rowExpand="onRowExpand"
            >
                <template #header>
                    <div class="d-flex justify-content-between">
                        <div>
                            <Button
                                label="Expand All"
                                text
                                @click="expandAll"
                            />
                            <Button
                                label="Collapse All"
                                text
                                @click="collapseAll"
                            />
                        </div>
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
                                <i
                                    class="bi bi-arrow-clockwise"
                                    style="cursor: pointer"
                                    @click="refreshData"
                                ></i>
                            </div>
                        </div>
                    </div>
                </template>
                <template #empty> No data found.</template>
                <Column expander style="width: 5rem" />
                <Column field="id" header="Id" sortable></Column>
                <Column field="username" header="Username">
                    <template #body="slotProps">
                        <img
                            :src="slotProps.data.avatar"
                            alt=""
                            class="rounded-circle me-3"
                            width="32"
                        />
                        <span>{{ slotProps.data.username }}</span>
                    </template>
                </Column>

                <Column field="fullName" header="Full Name">
                    <template #body="slotProps">
                        <span
                            >{{ slotProps.data.first_name }}
                            {{ slotProps.data.last_name }}</span
                        >
                    </template>
                </Column>

                <Column field="email" header="Email">
                    <template #body="slotProps">
                        <span>{{ slotProps.data.email }}</span>
                    </template>
                </Column>

                <template #expansion="slotProps">
                    <div class="p-3">
                        <DataTable :value="slotProps.data.joins" removableSort>
                            <Column header="#" headerStyle="width:3rem">
                                <template #body="slotProps" sortable>
                                    {{ slotProps.index + 1 }}
                                </template>
                            </Column>
                            <Column
                                field="join_time"
                                header="Join Time"
                                sortable
                            >
                                <template #body="slotProps">
                                    <span>{{
                                        formatDate(slotProps.data.join_time)
                                    }}</span>
                                </template>
                            </Column>
                            <Column
                                field="leave_time"
                                header="Leave Time"
                                sortable
                            >
                                <template #body="slotProps">
                                    <span>{{
                                        formatDate(slotProps.data.leave_time)
                                    }}</span>
                                </template>
                            </Column>
                        </DataTable>
                    </div>
                </template>
            </DataTable>
        </div>
    </div>
    <BaseLoading v-else />
</template>

<script setup>
import { computed, onMounted, ref } from "vue";
import { useVoteStore } from "@/Stores/vote.js";
import DataTable from "primevue/datatable";
import Column from "primevue/column";
import Button from "primevue/button";
import BaseLoading from "@/Components/BaseLoading.vue";
import { useHelper } from "@/Services/helper.js";
import { FilterMatchMode } from "primevue/api";
import { useVotingAttendanceStore } from "@/Stores/voting-attendance.js";

const isLoading = ref(true);

const props = defineProps(["room"]);
const attendanceStore = useVotingAttendanceStore();
const helper = useHelper();
const formatDate = helper.formatDate;

const attendances = computed(() => attendanceStore.attendances[props.room.id]);

const filters = ref({
    global: { value: null, matchMode: FilterMatchMode.CONTAINS },
});

const expandedRows = ref({});

const onRowExpand = (event) => {};
const onRowCollapse = (event) => {};
const expandAll = () => {
    expandedRows.value = attendances.value.reduce(
        (acc, p) => (acc[p.id] = true) && acc,
        {},
    );
};
const collapseAll = () => {
    expandedRows.value = null;
};

const refreshData = async () => {
    isLoading.value = true;

    await attendanceStore.fetchAttendances(props.room.id, true);

    isLoading.value = false;
};

onMounted(async () => {
    await attendanceStore.fetchAttendances(props.room.id);

    isLoading.value = false;
});
</script>
