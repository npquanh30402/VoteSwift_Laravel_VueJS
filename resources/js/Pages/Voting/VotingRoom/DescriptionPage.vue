<template>
    <div class="row">
        <div class="col">
            <h1 class="display-6 text-center fw-bold">Dashboard: {{ room.room_name }}</h1>
        </div>
    </div>
    <div class="my-3">
        <div class="row justify-content-center">
            <div class="col-md-2">
                <BallotSidebar :room="room"></BallotSidebar>
            </div>
            <div class="col-md-9">
                <div class="card shadow-sm border-0 mb-3 overflow-auto">
                    <div class="card-header text-bg-dark text-center">Room Description</div>
                    <div class="card-body">
                        <MdPreview :editorId="'room' + room.id" :modelValue="room.room_description" />
                    </div>
                </div>
            </div>
        </div>
    </div>
    <teleport to="body">
        <vue-easy-lightbox
            :visible="visibleRef"
            :imgs="imgsRef"
            :index="indexRef"
            @hide="onHide"
        ></vue-easy-lightbox>
    </teleport>

</template>

<script setup>
import BallotSidebar from "@/Pages/Voting/BallotSidebar.vue";
import {MdPreview} from "md-editor-v3";

defineProps(['room'])


</script>
