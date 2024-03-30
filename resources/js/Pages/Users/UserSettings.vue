<template>
    <div>
        <form class="d-flex flex-column gap-3" @submit.prevent="updateSettings">
            <div class="vstack gap-4">
                <div class="row">
                    <div class="form-group col-md-6 d-flex flex-column">
                        <label class="form-label" for="username">Username:</label>
                        <input type="text" class="form-control" id="username" v-model="form.username"
                               disabled>
                    </div>
                    <div class="form-group col-md-6 d-flex flex-column">
                        <label class="form-label" for="email">Email:</label>
                        <input type="email" class="form-control" id="email" v-model="form.email" disabled>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-md-6 d-flex flex-column">
                        <label class="form-label" for="first_name">First Name:</label>
                        <input type="text" class="form-control" id="first_name" v-model="form.first_name"
                               placeholder="Nothing...">
                        <p class="m-0 small text-danger" v-if="errors.first_name">{{ errors.first_name }}</p>
                    </div>
                    <div class="form-group col-md-6 d-flex flex-column">
                        <label class="form-label" for="last_name">Last Name:</label>
                        <input type="text" class="form-control" id="last_name" v-model="form.last_name"
                               placeholder="Nothing...">
                        <p class="m-0 small text-danger" v-if="errors.last_name">{{ errors.last_name }}</p>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-md-6 d-flex flex-column">
                        <label class="form-label" for="birth_date">Birth Date <strong>(Age: {{
                                userAge
                            }})</strong>:</label>
                        <VueDatePicker placeholder="Nothing..." v-model="form.birth_date" :enable-time-picker="false"/>
                        <p class="m-0 small text-danger" v-if="errors.birth_date">{{ errors.birth_date }}</p>
                    </div>
                    <div class="form-group col-md-6 d-flex flex-column">
                        <label class="form-label" for="gender">Gender:</label>
                        <select class="form-select" id="gender" v-model="form.gender">
                            <option selected disabled>Open to choose</option>
                            <option value="male">Male</option>
                            <option value="female">Female</option>
                            <option value="other">Other</option>
                        </select>
                        <p class="m-0 small text-danger" v-if="errors.gender">{{ errors.gender }}</p>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-md-4 d-flex flex-column">
                        <label class="form-label" for="country">Country:</label>
                        <select class="form-select" id="country" v-model="form.country">
                            <option selected disabled>Open to choose</option>
                            <option v-for="country in countries">{{ country }}</option>
                            s
                        </select>
                        <p class="m-0 small text-danger" v-if="errors.country">{{ errors.country }}</p>
                    </div>
                    <div class="form-group col-md-4 d-flex flex-column">
                        <label class="form-label" for="city">City:</label>
                        <input type="text" class="form-control" id="city" v-model="form.city" placeholder="Nothing...">
                        <p class="m-0 small text-danger" v-if="errors.city">{{ errors.city }}</p>
                    </div>
                    <div class="form-group col-md-4 d-flex flex-column">
                        <label class="form-label" for="zip_code">Zip Code:</label>
                        <input type="text" class="form-control" id="zip_code" v-model="form.zip_code"
                               placeholder="Nothing...">
                        <p class="m-0 small text-danger" v-if="errors.zip_code">{{ errors.zip_code }}</p>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-md-4 d-flex flex-column">
                        <label class="form-label" for="phone">Phone:</label>
                        <input type="text" class="form-control" id="phone" v-model="form.phone"
                               placeholder="Nothing...">
                        <p class="m-0 small text-danger" v-if="errors.phone">{{ errors.phone }}</p>
                    </div>
                    <div class="form-group col-md-8 d-flex flex-column">
                        <label class="form-label" for="address">Address:</label>
                        <input type="text" class="form-control" id="address" v-model="form.address"
                               placeholder="Nothing...">
                        <p class="m-0 small text-danger" v-if="errors.address">{{ errors.address }}</p>
                    </div>
                </div>
                <div class="row align-items-center">
                    <div class="form-group col-md-4 d-flex justify-content-center">
                        <img :src="authUser.user.avatar"
                             class="rounded-circle img-fluid"
                             style="width: 10rem;"
                             alt="Avatar"/>
                    </div>
                    <div class="form-group col-md-8 d-flex flex-column">
                        <label class="form-label" for="avatar">Avatar:</label>
                        <input type="file" class="form-control" id="avatar" @change="handleFileChange"
                               placeholder="Nothing...">
                        <p class="m-0 small text-danger" v-if="errors.avatar">{{ errors.avatar }}</p>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary w-25 ms-auto me-3">Save</button>
            </div>
        </form>
    </div>
</template>
<script setup>
import {router, useForm, usePage} from "@inertiajs/vue3";
import {route} from "ziggy-js";
import {computed, ref} from "vue";
import VueDatePicker from '@vuepic/vue-datepicker';
import '@vuepic/vue-datepicker/dist/main.css'
import axios from 'axios';
import {differenceInYears} from "date-fns";

const authUser = computed(() => usePage().props.authUser);

const countries = ref([]);

axios.get('https://restcountries.com/v3.1/all')
    .then(function (response) {
        response.data
            .forEach(country => {
                countries.value.push(country.name.common);
            });
    });

const userAge = computed(() => Math.abs(differenceInYears(new Date(form.birth_date), new Date())))

const form = useForm({
    username: authUser.value.user.username,
    email: authUser.value.user.email,
    first_name: authUser.value.user.first_name,
    last_name: authUser.value.user.last_name,

    country: authUser.value.user.country,
    city: authUser.value.user.city,
    zip_code: authUser.value.user.zip_code,

    birth_date: authUser.value.user.birth_date ? new Date(authUser.value.user.birth_date) : null,
    gender: authUser.value.user.gender,

    phone: authUser.value.user.phone,
    address: authUser.value.user.address,
    avatar: null
})

const errors = computed(() => usePage().props.errors);

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
