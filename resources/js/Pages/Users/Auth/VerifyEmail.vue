<template>
    <div>
        <p>You need to verify your email first.</p>
        <Link :href="route('verification.send')" method="post" as="button" class="btn btn-secondary">Resend verification
            email
        </Link>
    </div>
</template>

<script setup>
import {Link} from "@inertiajs/vue3";
import {route} from "ziggy-js";
</script>
