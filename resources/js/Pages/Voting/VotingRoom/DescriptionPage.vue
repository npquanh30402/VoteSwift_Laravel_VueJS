<template>
    <div class="card shadow-sm border-0 mb-3 overflow-auto">
        <div class="card-header text-bg-dark text-center">Room Description</div>
        <div class="card-body">
            <MdPreview :editorId="'room' + room.id" :modelValue="room.room_description"/>
        </div>
    </div>
</template>

<script setup>
import {MdPreview} from "md-editor-v3";

defineProps(['room'])

</script>
