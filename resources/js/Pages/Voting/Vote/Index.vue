<template>
    <button class="btn btn-info vertical-button" type="button" @click="openSidebar(bsOffcanvas)">Sidebar
    </button>
    <BaseOffcanvas id="sidebar"></BaseOffcanvas>

    <transition name="fade" mode="out-in">
        <component :is="tabs[currentTab]" :room="room" :questions="questions" class="tab"></component>
    </transition>

    <div class="text-center my-5">
        <button class="btn btn-primary btn-lg animate__animated animate__pulse animate__infinite 	infinite"
                @click="currentTab = 'StartVoting'">Ready to
            Start?
        </button>
    </div>
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

console.log(props.questions)

// const scrollToTop = () => {
//     window.scrollTo({top: 0, behavior: 'smooth'});
// };

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
