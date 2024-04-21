<template>
    <form v-if="!isLoading" @submit.prevent="submit">
        <div class="row gx-3">
            <div class="col-md-12">
                <div class="mb-3">
                    <label class="mb-2" for="room_name">Date/Time:</label>
                    <VueDatePicker
                        v-model="form.date"
                        :min-date="new Date(Date.now() - 86400000)"
                        disabled
                        multi-calendars
                        range
                    />
                    <p v-if="errorMessage" class="m-0 small text-danger">
                        {{ errorMessage }}
                    </p>
                </div>
            </div>

            <div class="col-md-12">
                <div class="mb-3 row row-cols-2">
                    <div>
                        <div>
                            <span>Timezone: {{ activeTz.tz }}</span>
                            <br />
                            <span
                                >Offset:
                                {{
                                    activeTz.offset > 0
                                        ? `+${activeTz.offset}`
                                        : activeTz.offset
                                }}</span
                            >
                        </div>
                        <div>
                            <input
                                v-model="selectedTz"
                                class="tz-range-slider"
                                max="22"
                                min="0"
                                type="range"
                            />
                        </div>
                        <div>
                            <span>Time preview:</span>
                            <VueDatePicker
                                v-model="form.date"
                                :timezone="tz"
                                disabled
                                range
                            />
                        </div>
                    </div>
                    <div>
                        <VueDatePicker
                            v-model="form.date"
                            :class="{ 'un-interactive': isPublish }"
                            :min-date="new Date(Date.now() - 86400000)"
                            inline
                            multi-calendars
                            range
                        />
                    </div>
                </div>
            </div>

            <div v-if="!isPublish" class="col-md-12">
                <div class="d-grid">
                    <button
                        :class="{ disabled: errorMessage }"
                        class="btn btn-sm btn-success p-3"
                        type="submit"
                    >
                        Update
                    </button>
                </div>
            </div>
        </div>
    </form>
    <BaseLoading v-else />
</template>

<script setup>
import { useForm } from "@inertiajs/vue3";
import VueDatePicker from "@vuepic/vue-datepicker";
import { computed, onMounted, ref, watch } from "vue";
import { useVotingRoomStore } from "@/Stores/voting-room.js";
import BaseLoading from "@/Components/BaseLoading.vue";
import { useHelper } from "@/Services/helper.js";

const isLoading = ref(true);
const props = defineProps(["room"]);
const helper = useHelper();
const votingRoomStore = useVotingRoomStore();
const selectedTz = ref(11);

// const room = computed(() => {
//     for (const room of votingRoomStore.rooms) {
//         if (room.id === props.room.id) {
//             return room;
//         }
//     }
// });
const room = computed(() => props.room);
const isPublish = computed(() => props.room.is_published === 1);
const timezone = ref({ timezone: undefined });

const timezones = [
    { tz: "Pacific/Midway", offset: -11 },
    { tz: "America/Adak", offset: -10 },
    { tz: "Pacific/Gambier", offset: -9 },
    { tz: "America/Los_Angeles", offset: -8 },
    { tz: "America/Denver", offset: -7 },
    { tz: "America/Chicago", offset: -6 },
    { tz: "America/New_York", offset: -5 },
    { tz: "America/Santiago", offset: -4 },
    { tz: "America/Sao_Paulo", offset: -3 },
    { tz: "America/Noronha", offset: -2 },
    { tz: "Atlantic/Cape_Verde", offset: -1 },
    { tz: "UTC", offset: 0 },
    { tz: "Europe/Brussels", offset: 1 },
    { tz: "Africa/Cairo", offset: 2 },
    { tz: "Europe/Minsk", offset: 3 },
    { tz: "Europe/Moscow", offset: 4 },
    { tz: "Asia/Tashkent", offset: 5 },
    { tz: "Asia/Dhaka", offset: 6 },
    { tz: "Asia/Novosibirsk", offset: 7 },
    { tz: "Australia/Perth", offset: 8 },
    { tz: "Asia/Tokyo", offset: 9 },
    { tz: "Australia/Hobart", offset: 10 },
    { tz: "Asia/Vladivostok", offset: 11 },
];

const activeTz = computed(() => timezones[selectedTz.value]);

const tz = computed(() => {
    return { ...timezone.value, timezone: activeTz.value.tz };
});

const dbTimeZone = computed(() => room.value.timezone);

const form = useForm({
    date: [
        room.value.start_time
            ? helper.convertToLocal(
                  room.value.start_time,
                  helper.getUserTimeZone(),
              )
            : new Date(),
        room.value.end_time
            ? helper.convertToLocal(
                  room.value.end_time,
                  helper.getUserTimeZone(),
              )
            : new Date(),
    ],
});

const errorMessage = ref(null);

watch(
    () => form.date,
    () => {
        const startTime = form.date[0];
        const endTime = form.date[1];
        const currentTime = new Date(Date.now());

        switch (true) {
            case startTime > endTime:
                errorMessage.value = "End time must be greater than start time";
                break;
            case startTime < currentTime:
                errorMessage.value =
                    "Start time must be greater than current time";
                break;
            case endTime < currentTime:
                errorMessage.value =
                    "End time must be greater than current time";
                break;
            default:
                errorMessage.value = null;
                break;
        }
    },
);

function updateSelectedTz() {
    let timezoneIndex;

    if (room.value && dbTimeZone.value !== null) {
        timezoneIndex = timezones.findIndex((tz) => tz.tz === dbTimeZone.value);
    } else {
        const defaultTimezoneOffset = -new Date().getTimezoneOffset() / 60;
        timezoneIndex = timezones.findIndex(
            (tz) => tz.offset === defaultTimezoneOffset,
        );
    }

    if (timezoneIndex !== -1) {
        selectedTz.value = timezoneIndex;
    }
}

onMounted(async () => {
    // await votingRoomStore.fetchRooms();

    updateSelectedTz();

    isLoading.value = false;
});

const submit = async () => {
    const startTimeUtc = helper.convertToUtc(form.date[0]);
    const endTimeUtc = helper.convertToUtc(form.date[1]);

    const formData = new FormData();
    formData.append("start_time", startTimeUtc);
    formData.append("end_time", endTimeUtc);
    formData.append("activeTz", activeTz.value.tz);

    await votingRoomStore.updateRoom(props.room.id, formData);
};
</script>
