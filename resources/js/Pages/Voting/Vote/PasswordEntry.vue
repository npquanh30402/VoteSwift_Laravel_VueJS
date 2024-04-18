<template>
    <div
        class="d-flex flex-column justify-content-center align-items-center mt-5"
    >
        <div class="card" style="width: 20rem">
            <img :src="image" alt="..." class="card-img-top" />
            <div class="card-body">
                <h5 class="card-title">Enter the password to continue</h5>
                <form @submit.prevent="submit">
                    <div class="form-floating mb-3">
                        <input
                            id="room_id"
                            :value="room.id"
                            class="form-control"
                            disabled
                            placeholder="Room ID"
                            type="text"
                        />
                        <label for="room_id">Room ID</label>
                    </div>
                    <div class="form-floating">
                        <input
                            id="room_password"
                            v-model="form.room_password"
                            class="form-control"
                            name="room_password"
                            placeholder="Password"
                            required
                            type="password"
                        />
                        <label for="room_password">Password</label>
                    </div>

                    <div>
                        <input :value="form.token" name="token" type="hidden" />
                    </div>
                    <div class="d-grid">
                        <button class="btn btn-primary mt-3" type="submit">
                            Submit
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>

<script setup>
import { router, useForm } from "@inertiajs/vue3";
import { route } from "ziggy-js";
import image from "../../../../../public/anime-girl-peeking-behind-the-door.gif";

const props = defineProps(["room", "token"]);

const form = useForm({
    room_password: "",
    token: props.token,
});

const submit = () => {
    router.post(route("vote.password.entry", props.room.id), form);
};
</script>
