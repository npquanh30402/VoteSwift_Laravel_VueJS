<template>
    <div>
        <h5 class="card-title">Room Name: {{ room.room_name }}</h5>
        <h6 class="card-subtitle mb-2 text-muted">Room ID: {{ room.id }}</h6>
        <p class="card-text">
            Room Date (Your Locale): {{ new Date(room?.start_time).toLocaleString() }}
            - {{ new Date(room?.end_time).toLocaleString() }}
        </p>
        <p class="card-text">
            Room Date (UTC): {{ room.start_time }}
            - {{ room.end_time }}
        </p>
        <!--            <p class="card-text">-->
        <!--                Room Hours: {{ room.start_time }}-->
        <!--                - {{ room.end_time }}-->
        <!--            </p>-->
        <p class="card-text">
            Room Timezone: {{ room.timezone }}
            ({{ gmtOffset(room.timezone) }})
        </p>
        <p class="card-text">
            Room Link:
            <code>
                <a :href="getRoomVoteLink(room.id)" target="_blank">{{
                        getRoomVoteLink(room.id)
                    }}
                </a>
            </code>
        </p>
        <p class="card-text">
            Result Link:
            <code>
                <a :href="getRoomResultLink(room.id)" target="_blank">{{
                        getRoomResultLink(room.id)
                    }}
                </a>
            </code>
        </p>
    </div>
</template>
<script setup>
import {route} from "ziggy-js";

import 'md-editor-v3/lib/preview.css';

defineProps(["room"]);

const gmtOffset = (timezone) => {
    const now = new Date(Date.now())
    const timezoneOffset = now.offset / 60;
    const offsetSign = timezoneOffset >= 0 ? "+" : "-";
    const offsetHours = Math.abs(Math.floor(timezoneOffset));
    const offsetMinutes = Math.abs(Math.round((timezoneOffset % 1) * 60));

    return `GMT${offsetSign}${offsetHours
        .toString()
        .padStart(2, "0")}:${offsetMinutes.toString().padStart(2, "0")}`;
};

function getTimezoneOffset(timezone) {
    const now = new Date();
    const formatOptions = {timeZone: timezone, timeZoneName: 'shortOffset'};
    const formatted = new Intl.DateTimeFormat('en-US', formatOptions).format(now);
    return formatted.split('GMT')[1];
}

function getRoomVoteLink(roomId) {
    // Replace 'your-vote-route' with your actual route logic or method
    return route('vote.main', roomId);
}

function getRoomResultLink(roomId) {
    // Replace 'your-result-route' with your actual route logic or method
    return route('vote.result', roomId);
}
</script>
