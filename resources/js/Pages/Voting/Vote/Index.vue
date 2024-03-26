<template>
    <button class="btn btn-info vertical-button" type="button" @click="openSidebar(bsOffcanvas)">Sidebar
    </button>
    <BaseOffcanvas id="sidebar" title="Sidebar">
        <div class="list-group">
            <a href="#" class="list-group-item list-group-item-action">Room Description</a>
            <a href="#" class="list-group-item list-group-item-action">Attachments</a>
            <a href="#" class="list-group-item list-group-item-action">Voter</a>
        </div>
    </BaseOffcanvas>

    <transition name="fade" mode="out-in">
        <component :is="tabs[currentTab]" :room="room" :questions="questions" class="tab"
                   @switch-tab="currentTab = $event"></component>
    </transition>
</template>

<script setup>
import BaseOffcanvas from "@/Components/BaseOffcanvas.vue";
import {onMounted, ref} from "vue";
import * as bootstrap from 'bootstrap'
import Welcome from "@/Pages/Voting/Vote/Welcome.vue";
import StartVoting from "@/Pages/Voting/Vote/StartVoting.vue";

const currentTab = ref('Welcome')

const tabs = {
    Welcome,
    StartVoting,
}

const props = defineProps(['questions', 'room'])

const bsOffcanvas = ref(null);

onMounted(() => {
    bsOffcanvas.value = new bootstrap.Offcanvas('#sidebar')
})

function openSidebar(modal) {
    modal.show();
}
</script>

<style scoped>
.vertical-button {
    position: fixed;
    top: 50%;
    left: 0;
    transform: rotate(-90deg);
    transform-origin: top left;
    opacity: 0.8;
}
</style>
