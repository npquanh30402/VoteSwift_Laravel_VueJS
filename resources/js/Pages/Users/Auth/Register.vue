<template>
    <div>
        <h1 class="text-center mt-3">Register</h1>
        <div class="row justify-content-center my-5">
            <form
                class="col-md-6 shadow p-5 border rounded"
                @submit.prevent="register"
            >
                <div class="mb-3">
                    <label class="form-label" for="username">Username</label>
                    <input
                        id="username"
                        v-model="form.username"
                        class="form-control"
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
                    <label class="form-label" for="email">Email address</label>
                    <input
                        id="email"
                        v-model="form.email"
                        class="form-control"
                        type="email"
                    />
                    <p v-if="form.errors.email" class="m-0 small text-danger">
                        {{ form.errors.email }}
                    </p>
                    <div v-else id="emailHelp" class="form-text">
                        We'll never share your email with anyone else.
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label" for="password">Password</label>
                    <input
                        id="password"
                        v-model="form.password"
                        class="form-control"
                        type="password"
                    />
                    <p
                        v-if="form.errors.password"
                        class="m-0 small text-danger"
                    >
                        {{ form.errors.password }}
                    </p>
                </div>
                <div class="mb-3">
                    <label class="form-label" for="password_confirmation"
                        >Confirm Password</label
                    >
                    <input
                        class="form-control"
                        type="password"
                        id="password_confirmation"
                        v-model="form.password_confirmation"
                    />
                </div>
                <button type="submit" class="btn btn-primary">Register</button>
            </form>
        </div>
    </div>
</template>

<script setup>
import { useForm } from "@inertiajs/vue3";
import { route } from "ziggy-js";
import { useToast } from "vue-toast-notification";

const $toast = useToast();
const form = useForm({
    username: null,
    email: null,
    password: null,
    password_confirmation: null,
});

function register() {
    form.post(
        route("register.store"),
        {
            onSuccess: () => {
                $toast.success("Registration successfully");
            },
            onError: (errors) => {
                $toast.error(errors.register);
            },
        },
        form,
    );
}
</script>
