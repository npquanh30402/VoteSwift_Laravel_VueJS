<template>
    <div class="card shadow-sm border-0 mb-3 overflow-auto">
        <div class="card-header text-bg-dark text-center">Delete Room</div>
        <div class="card-body text-center">
            <p class="display-5">Are you sure you want to delete this room?</p>
            <p class="text-muted">This cannot be undone!</p>
            <Link :href="route('room.delete', room.id)" method="DELETE" as="button" class="btn btn-danger">DELETE
            </Link>
        </div>
    </div>
</template>

<script setup>
import {route} from "ziggy-js";
import {Link} from "@inertiajs/vue3";

defineProps(['room'])

</script>
