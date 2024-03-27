<template>
    <section style="background-color: #eee;">
        <div class="container py-5">
            <div class="row">
                <div class="col">
                    <nav aria-label="breadcrumb" class="bg-light rounded-3 p-3 mb-4">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a>Home</a></li>
                            <li class="breadcrumb-item"><a href="#">User</a></li>
                            <li class="breadcrumb-item active" aria-current="page">User Profile</li>
                        </ol>
                    </nav>
                </div>
            </div>

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
                                    <p class="mb-0">Email</p>
                                </div>
                                <div class="col-sm-9">
                                    <p class="text-muted mb-0">{{ user.email }}</p>
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-sm-3">
                                    <p class="mb-0">Phone</p>
                                </div>
                                <div class="col-sm-9">
                                    <p class="text-muted mb-0">{{ user.phone }}</p>
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-sm-3">
                                    <p class="mb-0">Address</p>
                                </div>
                                <div class="col-sm-9">
                                    <p class="text-muted mb-0">{{ user.address }}</p>
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
                                    <div v-for="room in public_rooms" :key="room.id"
                                         class="card shadow-sm border-0 mb-3 overflow-auto"
                                         style="background-color: lightblue">
                                        <div
                                            class="card-header text-center d-flex justify-content-between align-items-center">
                                            <div>
                                                <span class="btn btn-sm btn-primary">{{ room.id }}</span>
                                                <span class="btn btn-sm btn-success">{{ room.room_name }}</span>
                                            </div>
                                            <div class="d-flex gap-3">
                                                <a href="{{route('vote.main', room.id)}}"
                                                   class="btn btn-sm btn-warning">Join</a>
                                            </div>
                                        </div>
                                    </div>
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

const props = defineProps(['user', 'public_rooms'])

const authUser = computed(() => usePage().props.authUser);

const fullName = `${props.user.first_name} ${props.user.last_name}`;

</script>
