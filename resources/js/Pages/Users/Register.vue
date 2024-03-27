<template>
    <h1 class="text-center mt-3">Register</h1>
    <div class="row justify-content-center my-5">
        <form class="col-md-6 shadow p-5 border rounded" @submit.prevent="register">
            <div class="mb-3">
                <label for="username" class="form-label">Username</label>
                <input type="text" class="form-control" id="username" v-model="form.username">
                <p class="m-0 small text-danger" v-if="form.errors.username">{{ form.errors.username }}</p>
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Email address</label>
                <input type="email" class="form-control" id="email" v-model="form.email">
                <p class="m-0 small text-danger" v-if="form.errors.email">{{ form.errors.email }}</p>
                <div id="emailHelp" class="form-text" v-else>We'll never share your email with anyone else.</div>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" class="form-control" id="password" v-model="form.password">
                <p class="m-0 small text-danger" v-if="form.errors.password">{{ form.errors.password }}</p>
            </div>
            <div class="mb-3">
                <label for="password_confirmation" class="form-label">Confirm Password</label>
                <input type="password" class="form-control" id="password_confirmation"
                       v-model="form.password_confirmation">
            </div>
            <button type="submit" class="btn btn-primary">Register</button>
        </form>
    </div>
</template>

<script setup>
import {useForm} from "@inertiajs/vue3";
import {route} from "ziggy-js";

const form = useForm({
    username: null,
    email: null,
    password: null,
    password_confirmation: null
})

function register() {
    form.post(route('register.store'), form);
}
</script>
