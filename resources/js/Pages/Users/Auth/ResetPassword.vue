<template>
    <div>
        <h1 class="text-center mt-3">Reset Password</h1>
        <div class="row justify-content-center my-5">
            <form
                class="col-md-6 shadow p-5 border rounded"
                @submit.prevent="reset"
            >
                <div class="mb-3">
                    <label class="form-label" for="email">Email address</label>
                    <input
                        id="email"
                        v-model="form.email"
                        class="form-control"
                        disabled
                        type="email"
                    />
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
                        id="password_confirmation"
                        v-model="form.password_confirmation"
                        class="form-control"
                        type="password"
                    />
                </div>
                <div>
                    <input :value="form.token" name="token" type="hidden" />
                </div>
                <button class="btn btn-primary float-end" type="submit">
                    Reset
                </button>
            </form>
        </div>
    </div>
</template>

<script setup>
import { useForm } from "@inertiajs/vue3";
import { route } from "ziggy-js";

const props = defineProps(["token", "email"]);

const form = useForm({
    email: props.email,
    password: null,
    password_confirmation: null,
    token: props.token,
});

function reset() {
    form.post(route("password.update"));
}
</script>
