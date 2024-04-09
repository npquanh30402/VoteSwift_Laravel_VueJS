<template>
    <form @submit.prevent="submit">
        <div class="row gx-3">
            <div class="col-md-12">
                <div class="mb-3">
                    <label for="room_name" class="mb-2">Date/Time:</label>
                    <VueDatePicker v-model="form.date" :min-date="new Date(Date.now() - 86400000)"
                                   range
                                   multi-calendars disabled/>
                </div>
            </div>

            <div class="col-md-12">
                <div class="mb-3 row row-cols-2">
                    <div>
                        <div>
                            <span>Timezone: {{ activeTz.tz }}</span>
                            <br/>
                            <span>Offset: {{ activeTz.offset > 0 ? `+${activeTz.offset}` : activeTz.offset }}</span>
                        </div>
                        <div>
                            <input class="tz-range-slider" type="range" v-model="selectedTz" min="0" max="22"/>
                        </div>
                        <div>
                            <span>Time preview:</span>
                            <VueDatePicker v-model="form.date" :timezone="tz" range disabled/>
                        </div>
                    </div>
                    <div>
                        <VueDatePicker v-model="form.date" inline
                                       :min-date="new Date(Date.now() - 86400000)"
                                       range
                                       multi-calendars/>
                    </div>
                </div>
            </div>

            <div class="col-md-12">
                <div class="d-grid">
                    <button type="submit" class="btn btn-sm btn-success p-3">Update</button>
                </div>
            </div>
        </div>
    </form>
</template>

<script setup>
import {useForm} from "@inertiajs/vue3";
import VueDatePicker from '@vuepic/vue-datepicker';
import {computed, onMounted, ref} from "vue";
import {useVotingRoomStore} from "@/Stores/voting-room.js";
import {useToast} from "vue-toast-notification";

const props = defineProps(['room'])
const $toast = useToast();
const votingRoomStore = useVotingRoomStore()
const selectedTz = ref(11);

const timezone = ref({timezone: undefined})

const timezones = [
    {tz: 'Pacific/Midway', offset: -11},
    {tz: 'America/Adak', offset: -10},
    {tz: 'Pacific/Gambier', offset: -9},
    {tz: 'America/Los_Angeles', offset: -8},
    {tz: 'America/Denver', offset: -7},
    {tz: 'America/Chicago', offset: -6},
    {tz: 'America/New_York', offset: -5},
    {tz: 'America/Santiago', offset: -4},
    {tz: 'America/Sao_Paulo', offset: -3},
    {tz: 'America/Noronha', offset: -2},
    {tz: 'Atlantic/Cape_Verde', offset: -1},
    {tz: 'UTC', offset: 0},
    {tz: 'Europe/Brussels', offset: 1},
    {tz: 'Africa/Cairo', offset: 2},
    {tz: 'Europe/Minsk', offset: 3},
    {tz: 'Europe/Moscow', offset: 4},
    {tz: 'Asia/Tashkent', offset: 5},
    {tz: 'Asia/Dhaka', offset: 6},
    {tz: 'Asia/Novosibirsk', offset: 7},
    {tz: 'Australia/Perth', offset: 8},
    {tz: 'Asia/Tokyo', offset: 9},
    {tz: 'Australia/Hobart', offset: 10},
    {tz: 'Asia/Vladivostok', offset: 11},
];

const activeTz = computed(() => timezones[selectedTz.value]);

const tz = computed(() => {
    return {...timezone.value, timezone: activeTz.value.tz};
});

const form = useForm({
    date: [
        props.room?.start_time ? new Date(props.room.start_time) : new Date(),
        props.room?.end_time ? new Date(props.room.end_time) : new Date()
    ]
});

function updateSelectedTz() {
    let timezoneIndex;

    if (props.room && props.room.timezone !== null) {
        timezoneIndex = timezones.findIndex(tz => tz.tz === props.room.timezone);
    } else {
        const defaultTimezoneOffset = (-new Date().getTimezoneOffset()) / 60;
        timezoneIndex = timezones.findIndex(tz => tz.offset === defaultTimezoneOffset);
    }

    if (timezoneIndex !== -1) {
        selectedTz.value = timezoneIndex;
    }
}

onMounted(() => {
    updateSelectedTz();
})

const submit = () => {
    try {
        const formData = new FormData();
        formData.append('start_time', form.date[0].toJSON());
        formData.append('end_time', form.date[1].toJSON());
        formData.append('activeTz', activeTz.value.tz);

        votingRoomStore.updateRoom(props.room.id, formData)

        $toast.success('Room updated successfully');
    } catch (e) {
        $toast.error('Failed to update room');
    }
}
</script>
