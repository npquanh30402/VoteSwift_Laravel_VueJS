<template>
    <div>
        <h1 class="text-center mt-3">Forgot Password</h1>
        <div class="row justify-content-center my-5">
            <form
                class="col-md-6 shadow p-5 border rounded"
                @submit.prevent="submit"
            >
                <div class="mb-3">
                    <div class="input-group mb-3">
                        <input
                            v-model="form.username"
                            aria-label="Username"
                            class="form-control"
                            placeholder="Username"
                            required
                            type="text"
                        />
                        <span class="input-group-text">@</span>
                        <input
                            v-model="form.domain"
                            aria-label="Domain"
                            class="form-control"
                            placeholder="Domain"
                            required
                            type="text"
                        />
                    </div>
                </div>
                <button class="btn btn-primary float-end" type="submit">
                    Submit
                </button>
            </form>
        </div>
    </div>
</template>

<script setup>
import { useToast } from "vue-toast-notification";
import { reactive } from "vue";
import { route } from "ziggy-js";

const toast = useToast();

const form = reactive({
    username: "",
    domain: "",
});

const submit = async () => {
    try {
        const email = form.username + "@" + form.domain;

        const response = await axios.post(route("password.email"), {
            email: email,
        });

        if (response.status === 200) {
            toast.success(response.data.message);
        }
    } catch (error) {
        toast.error(error.response.data.message);
    }
};
</script>
