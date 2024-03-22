<template>
    <div class="my-3">
        <div class="row justify-content-center mb-3">
            <div class="col-md-3">
                <a @click="back" class="btn btn-secondary small">Back</a>
            </div>
            <div class="col-md-7 gap-3 align-items-center"></div>
        </div>
        <div class="row justify-content-center">
            <div class="col-md-2">
                <BallotSidebar :room="room"></BallotSidebar>
            </div>
            <div class="col-md-9">
                <div class="card shadow-sm border-0 mb-3 overflow-auto">
                    <div class="card-header text-bg-dark text-center">Add Question</div>
                    <div class="card-body">
                        <a :href="111" class="btn btn-primary">Add</a>
                        <div class="mt-3 d-flex flex-column gap-3">
                            <div v-for="question in questions" class="card">
                                <div class="card-header fw-bold d-flex align-items-center gap-2">
                                    {{question.question_title}}
                                    <a href=""
                                       class="text-success">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                             fill="currentColor" class="bi bi-arrow-up-right-square"
                                             viewBox="0 0 16 16">
                                            <path fill-rule="evenodd"
                                                  d="M15 2a1 1 0 0 0-1-1H2a1 1 0 0 0-1 1v12a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1zM0 2a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2zm5.854 8.803a.5.5 0 1 1-.708-.707L9.243 6H6.475a.5.5 0 1 1 0-1h3.975a.5.5 0 0 1 .5.5v3.975a.5.5 0 1 1-1 0V6.707z"/>
                                        </svg>
                                    </a>
                                </div>
                                <div class="card-body d-flex flex-column">
                                    <p class="card-text text-truncate">{{question.question_description}}</p>
                                    <div class="d-flex justify-content-between">
                                        <a href="#" class="btn btn-primary">Add Candidates</a>
                                        <a href="#" class="btn btn-secondary">See more...</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import {Link, router, usePage} from "@inertiajs/vue3";
import BallotSidebar from "@/Pages/Voting/BallotSidebar.vue";

let urlPrev = usePage().props.urlPrev;

const back = () => {
    if (urlPrev !== 'empty') {
        router.visit(urlPrev)
    }
}

defineProps(['room', 'questions'])
</script>
