<template>
    <h1 class="display-6 text-center fw-bold">User Settings</h1>
    <div class="my-3">
        <div class="row justify-content-center">
            <div class="col-md-2">
                <UserSidebar :selectedTab="`settings`"></UserSidebar>
            </div>
            <div class="col-md-8">
                <form class="d-flex flex-column gap-3" enctype="multipart/form-data">
                    <div class="row g-4">
                        <div class="form-group col-md-6 d-flex flex-column">
                            <label class="form-label" for="username">Username:</label>
                            <input type="text" class="form-control" id="username" :value="user.username"
                                   disabled>
                        </div>
                        <div class="form-group col-md-6 d-flex flex-column">
                            <label class="form-label" for="email">Email:</label>
                            <input type="email" class="form-control" id="email"
                                   value="" disabled>
                        </div>
                        <div class="form-group col-md-6 d-flex flex-column">
                            <label class="form-label" for="first_name">First Name:</label>
                            <input type="text" class="form-control" id="first_name" name="first_name"
                                   value="">
                            <p class="m-0 small text-danger"></p>
                        </div>
                        <div class="form-group col-md-6 d-flex flex-column">
                            <label class="form-label" for="last_name">Last Name:</label>
                            <input type="text" class="form-control" id="last_name" name="last_name"
                                   value="">
                            <p class="m-0 small text-danger"></p>
                        </div>
                        <div class="form-group col-md-4 d-flex flex-column">
                            <label class="form-label" for="phone">Phone:</label>
                            <input type="text" class="form-control" id="phone" name="phone"
                                   value="">
                            <p class="m-0 small text-danger"></p>
                        </div>
                        <div class="form-group col-md-8 d-flex flex-column">
                            <label class="form-label" for="address">Address:</label>
                            <input type="text" class="form-control" id="address" name="address"
                                   value="">
                            <p class="m-0 small text-danger"></p>
                        </div>
                        <div class="d-flex align-items-center">
                            <div class="form-group col-md-4 d-flex justify-content-center">
                                <img src=""
                                     class="rounded-circle img-fluid"
                                     style="width: 10rem;"
                                     alt="Avatar"/>
                            </div>
                            <div class="form-group col-md-8 d-flex flex-column">
                                <label class="form-label" for="avatar">Avatar:</label>
                                <input type="file" class="form-control" id="avatar" name="avatar"
                                       value="">
                                <p class="m-0 small text-danger"></p>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary w-25 ms-auto me-3">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!--    <div class="row">-->
    <!--        <div class="col">-->
    <!--            <h1 class="display-6 text-center fw-bold">User Dashboard <a href="{{route('user.profile', $user->id)}}"-->
    <!--                                                                        class="btn btn-sm btn-primary">To Profile-->
    <!--                Page</a></h1>-->
    <!--        </div>-->
    <!--    </div>-->
</template>
<script setup>
import UserSidebar from "@/Pages/Users/UserSidebar.vue";
import {usePage} from "@inertiajs/vue3";
import {computed} from "vue";

const page = usePage()

const user = computed(() => page.props.user)
</script>
