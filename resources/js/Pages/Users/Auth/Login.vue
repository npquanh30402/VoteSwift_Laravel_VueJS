<template>
    <div>
        <h1 class="text-center mt-3">Login</h1>
        <div class="row justify-content-center my-5">
            <form
                class="col-md-6 shadow p-5 border rounded"
                @submit.prevent="login"
            >
                <div class="mb-3">
                    <label class="form-label" for="username">Username</label>
                    <input
                        id="username"
                        v-model="form.username"
                        class="form-control"
                        name="username"
                        type="text"
                    />
                    <p
                        v-if="form.errors.username"
                        class="m-0 small text-danger"
                    >
                        {{ form.errors.username }}
                    </p>
                </div>
                <div class="mb-3">
                    <label class="form-label" for="password">Password</label>
                    <input
                        id="password"
                        v-model="form.password"
                        class="form-control"
                        name="password"
                        type="password"
                    />
                    <p
                        v-if="form.errors.password"
                        class="m-0 small text-danger"
                    >
                        {{ form.errors.password }}
                    </p>
                </div>
                <div class="d-flex justify-content-between">
                    <div class="mb-3 form-check">
                        <input
                            id="remember_me"
                            v-model="form.remember_me"
                            class="form-check-input"
                            name="remember_me"
                            type="checkbox"
                        />
                        <label class="form-check-label" for="remember_me"
                            >Remember me</label
                        >
                    </div>
                    <Link :href="route('password.request')">
                        <span>Forgot password?</span>
                    </Link>
                </div>
                <button class="btn btn-primary" type="submit">Login</button>
            </form>
        </div>
    </div>
</template>

<script setup>
import { useForm } from "@inertiajs/vue3";
import { route } from "ziggy-js";
import { useToast } from "vue-toast-notification";
import { Link } from "@inertiajs/vue3";

const $toast = useToast();
const form = useForm({
    username: "",
    email: "",
    password: "",
    remember_me: false,
});

function login() {
    form.post(
        route("login.store"),
        {
            onSuccess: () => {
                $toast.success("Login successfully");
            },
            onError: (errors) => {
                $toast.error("Login failed");
            },
        },
        form,
    );
}
</script>
