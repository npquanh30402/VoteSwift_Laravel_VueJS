<template>
    <div>
        <div class="row">
            <div class="col">
                <h1 class="display-6 text-center fw-bold">Voting History</h1>
            </div>
        </div>
        <div class="my-3">
            <div class="row justify-content-center">
                <div class="col-md-2">
                    <UserSidebar></UserSidebar>
                </div>
                <div class="col-md-8">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <div class="vstack gap-4">
                                <VotingHistoryList :room_info="room_info"
                                                   :organizedData="organizedData"></VotingHistoryList>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import UserSidebar from "@/Pages/Users/UserSidebar.vue";
import {Link} from "@inertiajs/vue3";
import {route} from "ziggy-js";
import VotingHistoryList from "@/Pages/Users/VotingHistoryList.vue";

const props = defineProps(['votingHistory', 'organizedData', 'room_info'])
</script>
