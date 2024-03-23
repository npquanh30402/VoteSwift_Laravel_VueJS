<template>
    <div v-for="room in room_info" :key="room.room_id">
        <div class="hstack justify-content-between">
            <h1>Room: {{ room.room_title }}</h1>
            <Link class="btn btn-primary"
                  :href="route('vote.main', room.room_id)">Details
            </Link>
        </div>

        <div class="accordion" :id="'room_' + room.room_id">
            <div v-for="(data, index) in organizedData" :key="index">
                <div v-if="data.room_id === room.room_id" class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed" type="button"
                                :data-bs-toggle="'collapse'"
                                :data-bs-target="'#candidate_' + data.candidate_id"
                                aria-expanded="false" :aria-controls="'candidate_' + data.candidate_id">
                            Q{{ index + 1 }}: &nbsp; <span class="fw-bold fs-5">{{ data.question_title }}</span>
                        </button>
                    </h2>
                    <div :id="'candidate_' + data.candidate_id" class="accordion-collapse collapse"
                         :data-bs-parent="'#room_' + room.room_id">
                        <div class="accordion-body">
                            You picked: <span class="fw-bold">{{ data.candidate_title }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import {route} from "ziggy-js";
import {Link} from "@inertiajs/vue3";

const props = defineProps(['votingHistory', 'organizedData', 'room_info'])
</script>
