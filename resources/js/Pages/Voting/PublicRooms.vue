<template>
    <div class="row">
        <div class="col mb-3">
            <h1 class="display-6 text-center fw-bold">Public Voting Room</h1>
        </div>

    </div>
    <div class="row row-cols-3 gy-4 mb-4">
        <div class="d-flex align-items-stretch" v-for="(room, index) in public_rooms.data">
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
    <div class="d-flex justify-content-center my-3" v-if="public_rooms.data.length">
        <Pagination :links="public_rooms.links"/>
    </div>
</template>

<script setup>
import {Link} from "@inertiajs/vue3";
import {intlFormatDistance} from "date-fns";
import {route} from "ziggy-js";
import Pagination from "@/Components/Pagination.vue";

const props = defineProps(['public_rooms'])

function formatDate(date) {
    return intlFormatDistance(new Date(), new Date(date), {numeric: 'always'}, {localeMatcher: 'lookup'})
}
</script>
