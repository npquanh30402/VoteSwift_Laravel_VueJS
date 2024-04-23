<template>
    <div>
        <CardPreview>
            <div>
                <i class="bi bi-people" style="font-size: 2em"></i>
                <i class="bi bi-chat-dots" style="font-size: 2em"></i>
                <h5 class="card-title">Friendship and Private Chat</h5>
                <p class="card-text">
                    Users can make friends within the app and have private chats
                    for secure communication.
                </p>
            </div>
            <button class="btn btn-primary mt-4" @click="visible = true">
                Find a friend now!
            </button>
        </CardPreview>
        <Dialog
            v-model:visible="visible"
            :style="{ width: '30vw' }"
            header="Search for user"
            modal
        >
            <div class="input-group mb-3">
                <span id="basic-addon1" class="input-group-text"
                    ><i class="bi bi-search"></i
                ></span>
                <input
                    v-model="search_query"
                    aria-label="Username/Email"
                    class="form-control"
                    placeholder="Username/Email"
                    type="text"
                />
            </div>
            <div>
                <template v-for="user in users" :key="user.id">
                    <div v-if="user.id !== authUser.id" class="card">
                        <div
                            class="card-body d-flex align-items-center gap-3 justify-content-between"
                        >
                            <div class="d-flex align-items-center gap-3">
                                <img
                                    :alt="user.username + '\'s avatar'"
                                    :src="user.avatar"
                                    class="rounded-circle"
                                    style="width: 50px"
                                />
                                <h5 class="card-title">{{ user.username }}</h5>
                            </div>
                            <Link
                                :href="route('user.profile', user.id)"
                                class="btn btn-primary"
                            >
                                <i class="bi bi-person-circle"></i>
                            </Link>
                        </div>
                    </div>
                </template>
            </div>
        </Dialog>
    </div>
</template>

<script setup>
import { computed, ref } from "vue";
import Dialog from "primevue/dialog";
import CardPreview from "@/Pages/Index/CardPreview.vue";
import { useUserStore } from "@/Stores/user.js";
import { watchDebounced } from "@vueuse/core";
import { Link, usePage } from "@inertiajs/vue3";
import { route } from "ziggy-js";

const authUser = computed(() => usePage().props.authUser.user);
const userStore = useUserStore();
const users = computed(() => userStore.users);
const visible = ref(false);

const search_query = ref("");

watchDebounced(
    search_query,
    () => {
        searchUsers();
    },
    { debounce: 500, maxWait: 1000 },
);

const searchUsers = async () => {
    if (search_query.value.length >= 3) {
        await userStore.searchUsers(search_query.value);
    } else {
        users.value = [];
    }
};
</script>
