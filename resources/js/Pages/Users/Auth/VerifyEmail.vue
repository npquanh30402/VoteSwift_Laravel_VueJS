<template>
    <div class="d-flex justify-content-center align-items-center" style="min-height: 30vh;">
        <div class="text-center">
            <p class="fs-4">You need to verify your email first.</p>
            <button class="btn btn-secondary" @click="handleVerificationSend">Resend
                verification email
            </button>
        </div>
    </div>
</template>

<script setup>
import {Link, router, usePage} from "@inertiajs/vue3";
import {route} from "ziggy-js";
import {useToast} from "vue-toast-notification";

const authUser = usePage().props.authUser.user;
const $toast = useToast();

const handleVerificationSend = () => {
    router.post(route('verification.send'))
    $toast.success('Verification email has been sent');

}
</script>
