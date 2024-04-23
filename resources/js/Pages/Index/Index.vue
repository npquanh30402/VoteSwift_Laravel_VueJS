<template>
    <h1 class="display-1 text-center">Welcome to VoteSwift</h1>

    <div class="container mt-5">
        <div class="row g-4 mb-5 align-items-stretch">
            <!-- Real-Time Voting -->
            <div class="col-md-4 d-flex">
                <RoomSearch />
            </div>

            <!-- Live Results -->
            <div class="col-md-4 d-flex">
                <CardPreview>
                    <div>
                        <i
                            class="bi bi-bar-chart-line"
                            style="font-size: 2em"
                        ></i>
                        <h5 class="card-title">Live Results</h5>
                        <p class="card-text">
                            Watch the results unfold as votes come in, with
                            dynamic charts and graphs showing real-time trends.
                        </p>
                    </div>
                </CardPreview>
            </div>

            <!-- Customizable Polls -->
            <div class="col-md-4 d-flex">
                <CardPreview>
                    <div>
                        <i class="bi bi-gear" style="font-size: 2em"></i>
                        <h5 class="card-title">Customizable Polls</h5>
                        <p class="card-text">
                            Create polls with customizable question types to
                            meet a variety of needs, from simple choices to
                            complex surveys.
                        </p>
                    </div>
                    <Link
                        :href="route('room.create')"
                        class="btn btn-primary mt-4"
                        >Create a Poll
                    </Link>
                </CardPreview>
            </div>

            <!-- Encrypted Data -->
            <div class="col-md-4 d-flex">
                <CardPreview>
                    <div>
                        <i
                            class="bi bi-database-fill-lock"
                            style="font-size: 2em"
                        ></i>
                        <h5 class="card-title">Encrypted Data</h5>
                        <p class="card-text">
                            Ensures all voting data is encrypted for security
                            and confidentiality.
                        </p>
                    </div>
                </CardPreview>
            </div>

            <!-- User Authentication -->
            <div class="col-md-4 d-flex">
                <CardPreview>
                    <div>
                        <i class="bi bi-shield-lock" style="font-size: 2em"></i>
                        <h5 class="card-title">User Authentication</h5>
                        <p class="card-text">
                            Ensures secure login and user authentication for
                            controlled access to the voting system.
                        </p>
                    </div>
                    <Link
                        v-if="authUser === null"
                        :href="route('register')"
                        class="btn btn-primary mt-4"
                    >
                        Register
                    </Link>
                </CardPreview>
            </div>

            <!-- Admin Dashboard -->
            <div class="col-md-4 d-flex">
                <CardPreview>
                    <div>
                        <i class="bi bi-menu-button" style="font-size: 2em"></i>
                        <h5 class="card-title">Admin Dashboard</h5>
                        <p class="card-text">
                            Provides administrators with tools to manage polls
                            and monitor results.
                        </p>
                    </div>
                    <Link
                        :href="route('dashboard.user')"
                        class="btn btn-primary mt-4"
                        >Admin
                    </Link>
                </CardPreview>
            </div>

            <!-- Friendship and Private Chat -->
            <div class="col-md-4 d-flex">
                <UserSearch />
            </div>
        </div>
    </div>
</template>
<script setup>
import CardPreview from "@/Pages/Index/CardPreview.vue";
import UserSearch from "@/Pages/Index/Actions/UserSearch.vue";
import { route } from "ziggy-js";
import { Link, usePage } from "@inertiajs/vue3";
import { computed } from "vue";
import RoomSearch from "@/Pages/Index/Actions/RoomSearch.vue";

const authUser = computed(() => usePage().props.authUser.user);
</script>
