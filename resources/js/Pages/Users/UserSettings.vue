<template>
    <h1 class="display-6 text-center fw-bold">User Settings
        <Link :href="route('user.profile', authUser.id)"
              class="btn btn-sm btn-primary">To Profile
            Page
        </Link>
    </h1>
    <div class="my-3">
        <div class="row justify-content-center">
            <div class="col-md-2">
                <UserSidebar :selectedTab="`settings`"></UserSidebar>
            </div>
            <div class="col-md-8">
                <form class="d-flex flex-column gap-3" @submit.prevent="updateSettings">
                    <div class="row g-4">
                        <div class="form-group col-md-6 d-flex flex-column">
                            <label class="form-label" for="username">Username:</label>
                            <input type="text" class="form-control" id="username" v-model="form.username"
                                   disabled>
                        </div>
                        <div class="form-group col-md-6 d-flex flex-column">
                            <label class="form-label" for="email">Email:</label>
                            <input type="email" class="form-control" id="email" v-model="form.email" disabled>
                        </div>
                        <div class="form-group col-md-6 d-flex flex-column">
                            <label class="form-label" for="first_name">First Name:</label>
                            <input type="text" class="form-control" id="first_name" name="first_name"
                                   v-model="form.first_name">
                            <p class="m-0 small text-danger"></p>
                        </div>
                        <div class="form-group col-md-6 d-flex flex-column">
                            <label class="form-label" for="last_name">Last Name:</label>
                            <input type="text" class="form-control" id="last_name" name="last_name"
                                   v-model="form.last_name">
                            <p class="m-0 small text-danger"></p>
                        </div>
                        <div class="form-group col-md-4 d-flex flex-column">
                            <label class="form-label" for="phone">Phone:</label>
                            <input type="text" class="form-control" id="phone" name="phone" v-model="form.phone">
                            <p class="m-0 small text-danger"></p>
                        </div>
                        <div class="form-group col-md-8 d-flex flex-column">
                            <label class="form-label" for="address">Address:</label>
                            <input type="text" class="form-control" id="address" name="address" v-model="form.address">
                            <p class="m-0 small text-danger"></p>
                        </div>
                        <div class="d-flex align-items-center">
                            <div class="form-group col-md-4 d-flex justify-content-center">
                                <img :src="authUser.avatar"
                                     class="rounded-circle img-fluid"
                                     style="width: 10rem;"
                                     alt="Avatar"/>
                            </div>
                            <div class="form-group col-md-8 d-flex flex-column">
                                <label class="form-label" for="avatar">Avatar:</label>
                                <input type="file" class="form-control" id="avatar" name="avatar"
                                       @change="handleFileChange">
                                <p class="m-0 small text-danger"></p>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary w-25 ms-auto me-3">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>
<script setup>
import UserSidebar from "@/Pages/Users/UserSidebar.vue";
import {router, useForm, usePage} from "@inertiajs/vue3";
import {route} from "ziggy-js";
import {Link} from "@inertiajs/vue3";

const authUser = usePage().props.authUser;

const form = useForm({
    username: authUser.username,
    email: authUser.email,
    first_name: authUser.first_name,
    last_name: authUser.last_name,
    phone: authUser.phone,
    address: authUser.address,
    avatar: null
})

function handleFileChange(event) {
    form.avatar = event.target.files[0];
}

function updateSettings() {
    router.post(route('user.settings.update'), {
        _method: 'put',
        ...form,
        avatar: form.avatar,
    })
}
</script>
