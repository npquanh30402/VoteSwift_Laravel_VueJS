<template>
    <div class="p-5">
        <h1 class="text-center display-3">Room Description</h1>
        <p class="text-center text-muted fs-5">(You can still read this again in the sidebar)</p>
        <MdPreview :editorId="'room_' + room.id" :modelValue="room.room_description"/>

        <div class="text-center my-5">
            <button class="btn btn-primary btn-lg animate__animated animate__pulse animate__infinite 	infinite"
                    @click="switchTab">Ready to
                Start?
            </button>
        </div>
    </div>
</template>
<script setup>
import {MdPreview} from "md-editor-v3";

defineProps(['room'])

const emit = defineEmits(['switch-tab'])

const switchTab = () => {
    emit('switch-tab', 'StartVoting')
}

</script>
