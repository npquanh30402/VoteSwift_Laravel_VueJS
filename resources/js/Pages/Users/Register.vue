<template>
    <h1 class="text-center mt-3">Register</h1>
    <div class="row justify-content-center my-5">
        <form class="col-md-6 shadow p-5 border rounded" @submit.prevent="register">
            <div class="mb-3">
                <label for="username" class="form-label">Username</label>
                <input type="text" class="form-control" name="username" id="username" v-model="form.username">
                <p class="m-0 small text-danger" v-if="form.errors.username">{{ form.errors.username }}</p>
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Email address</label>
                <input type="email" class="form-control" name="email" id="email" v-model="form.email">
                <p class="m-0 small text-danger" v-if="form.errors.email">{{ form.errors.email }}</p>
                <div id="emailHelp" class="form-text" v-else>We'll never share your email with anyone else.</div>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" class="form-control" name="password" id="password" v-model="form.password">
                <p class="m-0 small text-danger" v-if="form.errors.password">{{ form.errors.password }}</p>
            </div>
            <div class="mb-3">
                <label for="password_confirmation" class="form-label">Confirm Password</label>
                <input type="password" class="form-control" name="password_confirmation" id="password_confirmation"
                       v-model="form.password_confirmation">
                <p class="m-0 small text-danger" v-if="form.errors.password_confirmation">
                    {{ form.errors.password_confirmation }}</p>
            </div>
            <button type="submit" class="btn btn-primary">Register</button>
        </form>
    </div>
</template>

<script setup>
import {useForm} from "@inertiajs/vue3";
import {route} from "ziggy-js";

const form = useForm({
    username: '',
    email: '',
    password: '',
    password_confirmation: ''
})

function register() {
    if (form.password === form.password_confirmation) {
        form.post(route('register.store'), form);
    } else {
        form.errors.password_confirmation = 'Passwords do not match';
    }
}
</script>
