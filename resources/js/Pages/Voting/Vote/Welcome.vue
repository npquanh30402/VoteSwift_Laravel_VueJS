<template>
    <div class="p-5">
        <h1 class="text-center display-3">Room Description</h1>
        <p class="text-center text-muted fs-5">(You can still read this again in the sidebar)</p>
        <MdPreview :editorId="'room_' + room.id" :modelValue="room.room_description"/>

        <div class="text-center my-5">
            <button class="btn btn-primary btn-lg animate__animated animate__pulse animate__infinite 	infinite"
                    @click="switchTab" :disabled="!isReadyToStart">Ready to
                Start?
            </button>
        </div>
    </div>
</template>
<script setup>
import {MdPreview} from "md-editor-v3";
import {ref, watch} from "vue";

const props = defineProps(['room', 'isReadyToStart'])

const emit = defineEmits(['switch-tab'])

const isReadyToStart = ref(props.isReadyToStart);

watch(() => props.isReadyToStart, (newValue, oldValue) => {
    isReadyToStart.value = newValue
});

const switchTab = () => {
    emit('switch-tab', 'StartVoting')
}

</script>
