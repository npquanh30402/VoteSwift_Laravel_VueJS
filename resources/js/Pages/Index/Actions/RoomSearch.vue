<template>
    <div>
        <CardPreview>
            <div>
                <i class="bi bi-stopwatch" style="font-size: 2em"></i>
                <h5 class="card-title">Real-Time Voting</h5>
                <p class="card-text">
                    Cast your votes and see the results in real-time, allowing
                    you to instantly gauge public opinion.
                </p>
            </div>
            <button class="btn btn-primary mt-4" @click="visible = true">
                Start Voting
            </button>
        </CardPreview>
        <Dialog
            v-model:visible="visible"
            :style="{ width: '30vw' }"
            header="Search for room"
            modal
        >
            <div class="input-group mb-3">
                <span id="basic-addon1" class="input-group-text"
                    ><i class="bi bi-search"></i
                ></span>
                <input
                    v-model="search_query"
                    aria-label="Room ID"
                    class="form-control"
                    placeholder="Room ID"
                    type="text"
                />
                <button
                    id="basic-addon1"
                    class="input-group-text btn btn-primary"
                    @click="goToRoom"
                >
                    <i class="bi bi-send"></i>
                </button>
            </div>
        </Dialog>
    </div>
</template>

<script setup>
import { ref } from "vue";
import Dialog from "primevue/dialog";
import CardPreview from "@/Pages/Index/CardPreview.vue";
import { router } from "@inertiajs/vue3";
import { route } from "ziggy-js";

const visible = ref(false);

const search_query = ref("");

const goToRoom = () => {
    router.visit(route("vote.main", search_query.value));
};
</script>
