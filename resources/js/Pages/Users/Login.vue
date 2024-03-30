<template>
    <h1 class="text-center mt-3">Login</h1>
    <div class="row justify-content-center my-5">
        <form class="col-md-6 shadow p-5 border rounded" @submit.prevent="login">
            <div class="mb-3">
                <label for="username" class="form-label">Username</label>
                <input type="text" class="form-control" name="username" id="username" v-model="form.username">
                <p class="m-0 small text-danger" v-if="form.errors.username">{{ form.errors.username }}</p>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" class="form-control" name="password" id="password"
                       v-model="form.password">
                <p class="m-0 small text-danger" v-if="form.errors.password">{{ form.errors.password }}</p>
            </div>
            <div class="mb-3 form-check">
                <input type="checkbox" class="form-check-input" name="remember_me" id="remember_me"
                       v-model="form.remember_me">
                <label class="form-check-label" for="remember_me">Remember me</label>
            </div>
            <button type="submit" class="btn btn-primary">Login</button>
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
    remember_me: false
})

function login() {
    form.post(route('login.store'), form);
}
</script>
