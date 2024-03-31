<template>
    <div class="card shadow-sm border-0 mb-3 overflow-auto">
        <div class="card-header text-bg-dark text-center">Publish</div>
        <div class="card-body">
            <Link :href="route('room.publish', room.id)" method="PUT" as="button" class="btn btn-primary">Publish Room
            </Link>
        </div>
    </div>
</template>

<script setup>
import {Link} from "@inertiajs/vue3";
import {route} from "ziggy-js";

defineProps(['room'])

</script>
