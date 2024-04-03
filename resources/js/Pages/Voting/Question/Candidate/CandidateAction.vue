<template>
    <div class="dropdown">
        <div class="dropdown-action" data-bs-toggle="dropdown" aria-expanded="false">
            <i class="bi bi-three-dots"></i>
        </div>
        <ul class="dropdown-menu">
            <li>
                <Link class="dropdown-item" :href="route('candidate.delete', candidate.id)" as="button"
                      method="DELETE">Delete
                </Link>
            </li>
            <li><a class="dropdown-item" href="#">Another action</a></li>
            <li><a class="dropdown-item" href="#">Something else here</a></li>
        </ul>
    </div>
</template>

<script setup>
import {Link} from "@inertiajs/vue3";
import {route} from "ziggy-js";

const props = defineProps(['candidate'])

</script>

<style scoped>
.dropdown-action {
    cursor: pointer;
}
</style>
