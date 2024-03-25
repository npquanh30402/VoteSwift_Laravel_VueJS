<template>
    <div class="p-5">
        <h1 class="text-center display-3">Room Description</h1>
        <p class="text-center text-muted fs-5">(You can still read this again in the sidebar)</p>
        <MdPreview :editorId="'room_' + room.id" :modelValue="room.room_description"/>
    </div>
</template>
<script setup>
import {MdPreview} from "md-editor-v3";

defineProps(['room'])
</script>
