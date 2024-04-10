<template>
    <div class="p-5">
        <h1 class="text-center display-3">Room Description</h1>
        <p class="text-center text-muted fs-5">(You can still read this again in the sidebar)</p>
        <MdPreview :editorId="'room_' + room.id" :modelValue="room.room_description"/>

        <div class="text-center my-5" v-if="checkIfRoomOwner() && roomSettings.wait_for_voters">
            <button class="btn btn-primary btn-lg"
                    :class="isReadyToStart ? 'animate__animated animate__pulse animate__infinite infinite' : ''"
                    @click="switchTab" :disabled="!isReadyToStart">Ready to Start?
            </button>
        </div>
        <div class="text-center my-5" v-else-if="roomSettings.wait_for_voters">
            <!-- Nothing to display -->
        </div>
        <div class="text-center my-5" v-else>
            <button class="btn btn-primary btn-lg animate__ animated animate__pulse animate__infinite infinite"
                    @click="switchTab">Ready to Start?
            </button>
        </div>
    </div>
</template>
<script setup>
import {MdPreview} from "md-editor-v3";
import {computed} from "vue";
import {usePage} from "@inertiajs/vue3";

const props = defineProps(['room', 'isReadyToStart', 'roomSettings'])
const authUser = computed(() => usePage().props.authUser.user);

const emit = defineEmits(['switch-tab', 'start-voting'])

const isReadyToStart = computed(() => props.isReadyToStart);

const checkIfRoomOwner = () => {
    return authUser.value.id === props.room.user_id
}
const switchTab = () => {
    emit('switch-tab', 'StartVoting')

    if (checkIfRoomOwner() && props.roomSettings.wait_for_voters) {
        emit('start-voting')
    }
}
</script>
