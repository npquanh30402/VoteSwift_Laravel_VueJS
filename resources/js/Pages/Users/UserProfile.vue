<template>
    <section style="background-color: #eee;">
        <div class="container py-5">
            <div class="row">
                <div class="col-lg-4">
                    <div class="card mb-4">
                        <div class="card-body text-center">
                            <img :src="user.avatar"
                                 :alt="user.username + ' avatar'"
                                 class="rounded-circle img-fluid" style="width: 150px;">
                            <h5 class="my-3">{{ user.username }}</h5>
                            <!--                            <p class="text-muted mb-4">Full Stack Developer</p>-->
                            <div class="d-flex justify-content-center mb-2" v-if="user.id !== authUser.id">
                                <Link :href="route('user.send-friend-request', user.id)" method="post" as="button"
                                      class="btn btn-primary">Send
                                    Friend Request
                                </Link>
                                <Link :href="route('chat.main', user.id)"
                                      class="btn btn-outline-primary ms-1">Message
                                </Link>
                            </div>
                        </div>
                    </div>
                    <div class="card mb-4 mb-lg-0">
                        <div class="card-body p-0">

                        </div>
                    </div>
                </div>
                <div class="col-lg-8">
                    <div class="card mb-4">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-sm-3">
                                    <p class="mb-0">Full Name</p>
                                </div>
                                <div class="col-sm-9">
                                    <p class="text-muted mb-0">{{ fullName }}</p>
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-sm-3">
                                    <p class="mb-0">Age</p>
                                </div>
                                <div class="col-sm-9">
                                    <p class="text-muted mb-0">
                                        {{ Math.abs(differenceInYears(new Date(user.birth_date), new Date())) }}</p>
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-sm-3">
                                    <p class="mb-0">Gender</p>
                                </div>
                                <div class="col-sm-9">
                                    <p class="text-muted mb-0">{{ user.gender }}</p>
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-sm-3">
                                    <p class="mb-0">Country</p>
                                </div>
                                <div class="col-sm-9">
                                    <p class="text-muted mb-0">{{ user.country }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card mb-4 mb-md-0">
                                <div class="card-body">
                                    <p class="mb-4"><span class="text-primary font-italic me-1">voting</span> Room
                                        (Public)
                                    </p>
                                    <!--                                    -->
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card mb-4 mb-md-0">
                                <div class="card-body">
                                    <p class="mb-4"><span class="text-primary font-italic me-1">dummy</span> Project
                                        Status
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>

<script setup>
import {Link} from "@inertiajs/vue3";
import {usePage} from "@inertiajs/vue3";
import {computed} from "vue";
import {differenceInYears} from "date-fns";

const props = defineProps(['user', 'public_rooms'])

const authUser = computed(() => usePage().props.authUser);

const fullName = `${props.user.first_name} ${props.user.last_name}`;

</script>
