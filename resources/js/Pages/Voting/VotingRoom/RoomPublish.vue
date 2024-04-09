<template>
    <Link :href="route('room.publish', room.id)" method="PUT" as="button" class="btn btn-primary">Publish Room
    </Link>
</template>

<script setup>
import {Link} from "@inertiajs/vue3";
import {route} from "ziggy-js";

defineProps(['room'])

</script>
