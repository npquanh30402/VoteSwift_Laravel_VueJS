<template>
    <div class="d-flex flex-column justify-content-center align-items-center mt-5">
        <div class="card" style="width: 20rem;">
            <img :src="image" class="card-img-top" alt="...">
            <div class="card-body">
                <h5 class="card-title">Enter the password to continue</h5>
                <form @submit.prevent="submit">
                    <div class="form-floating mb-3">
                        <input type="text" class="form-control" id="room_id" placeholder="Room ID"
                               :value="room.id" disabled>
                        <label for="room_id">Room ID</label>
                    </div>
                    <div class="form-floating">
                        <input type="password" class="form-control" id="room_password" name="room_password"
                               placeholder="Password" v-model="form.room_password" required>
                        <label for="room_password">Password</label>
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary mt-3">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>

<script setup>
import {router, useForm} from "@inertiajs/vue3";
import {route} from "ziggy-js";
import image from '../../../../public/anime-girl-peeking-behind-the-door.gif'

const props = defineProps(['room']);

const form = useForm({
    room_password: ''
})

const submit = () => {
    router.post(route('vote.password.entry', props.room.id), form);
}

</script>
