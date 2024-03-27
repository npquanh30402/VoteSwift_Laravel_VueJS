<template>
    <div class="card shadow-sm border-0 mb-3">
        <div class="card-header text-bg-dark text-center">Change Time</div>
        <form @submit.prevent="submit" class="card-body">
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
    </div>
</template>

<script setup>
import {router, useForm} from "@inertiajs/vue3";
import {route} from "ziggy-js";
import VueDatePicker from '@vuepic/vue-datepicker';
import {computed, onMounted, ref} from "vue";

const props = defineProps(['room'])

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
    const timezoneIndex = timezones.findIndex(tz => tz.tz === props.room?.timezone);

    if (timezoneIndex !== -1) {
        selectedTz.value = timezoneIndex;
    }
}

onMounted(() => {
    updateSelectedTz();
})

const submit = () => {
    router.post(route('room.update', props.room.id), {
        _method: 'put',
        ...form,
        activeTz: activeTz.value
    })
}
</script>
