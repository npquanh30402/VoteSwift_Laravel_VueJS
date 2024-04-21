import { createRouter, createWebHistory } from "vue-router";
import Dashboard from "@/Pages/Voting/VotingRoom/Dashboard.vue";

const router = createRouter({
    history: createWebHistory(import.meta.env.BASE_URL),
    routes: [
        {
            path: "/voting-test/room/:roomId/dashboard",
            name: "room-dashboard",
            component: Dashboard,
            props: true,
        },
    ],
});

export default router;
