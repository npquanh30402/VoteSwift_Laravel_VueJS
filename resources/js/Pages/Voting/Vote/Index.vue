<template>
    <button class="btn btn-primary" type="button" @click="openSidebar(bsOffcanvas)">Enable body scrolling
    </button>
    <BaseOffcanvas id="sidebar"></BaseOffcanvas>
    <div class="p-5">
        <h1 class="text-center display-3">Room Description</h1>
        <p class="text-center text-muted fs-5">(You can still read this again in the sidebar)</p>
        <MdPreview :editorId="'room_' + room.id" :modelValue="room.room_description"/>
    </div>

    <div class="text-center my-5">
        <button class="btn btn-primary btn-lg animate__animated animate__pulse animate__infinite 	infinite">Ready to
            Start?
        </button>
    </div>
</template>

<script setup>
import {MdPreview} from "md-editor-v3";
import BaseOffcanvas from "@/Components/BaseOffcanvas.vue";
import {onMounted, ref} from "vue";
import * as bootstrap from 'bootstrap'

const props = defineProps(['questions', 'room'])

const bsOffcanvas = ref(null);

onMounted(() => {
    bsOffcanvas.value = new bootstrap.Offcanvas('#sidebar')
})

function openSidebar(modal) {
    modal.show();
}
</script>
