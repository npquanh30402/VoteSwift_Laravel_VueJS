<template>
    <div>
        <div v-if="!showNote" class="popup">
            <button
                class="btn btn-primary fs-5 position-relative"
                @click="toggleNote"
            >
                <i class="bi bi-journal-text"></i>
            </button>
        </div>
        <div>
            <transition name="fade">
                <div
                    v-if="showNote"
                    ref="el"
                    :style="style"
                    class="card shadow"
                    style="position: fixed; z-index: 1"
                >
                    <div class="card-header d-flex justify-content-between">
                        <div
                            class="d-flex justify-content-center align-items-center gap-3"
                        >
                            <span>Note</span>
                            <VTooltip>
                                <span><i class="bi bi-info-circle"></i></span>
                                <template #popper>
                                    <span
                                        >The note will be saved in your local
                                        storage automatically</span
                                    >
                                </template>
                            </VTooltip>
                        </div>
                        <span style="cursor: pointer" @click="toggleNote"
                            ><i class="bi bi-x-lg"></i
                        ></span>
                    </div>
                    <div class="card-body">
                        <div
                            class="mb-3"
                            style="min-height: 20vh; min-width: 30vw"
                        >
                            <NoteSidebar
                                :tabs="tabs"
                                @add-tab="addTab"
                                @switch-tab="handleSwitchTab"
                                @delete-tab="deleteTab"
                            />
                            <component
                                :is="
                                    tabs.find((tab) => tab.id === currentTab)
                                        ?.component
                                "
                                :key="currentTab"
                                :tab="tabs.find((tab) => tab.id === currentTab)"
                                @update-content="updateContent"
                            ></component>
                        </div>
                    </div>
                </div>
            </transition>
        </div>
    </div>
</template>

<script setup>
import { computed, markRaw, onMounted, onUnmounted, ref } from "vue";
import { usePage } from "@inertiajs/vue3";
import { useDraggable } from "@vueuse/core";
import NoteContent from "@/Pages/Voting/Vote/Note/NoteContent.vue";
import NoteSidebar from "@/Pages/Voting/Vote/Note/NoteSidebar.vue";

const props = defineProps(["room", "roomSettings"]);

const tabs = ref([
    {
        id: 1,
        component: markRaw(NoteContent),
        name: "Tab 1",
        content: "",
    },
]);

const loadTabs = () => {
    const storedTabs = JSON.parse(localStorage.getItem("tabs"));
    if (storedTabs.length > 0) {
        tabs.value = storedTabs.map((tab) => {
            return {
                ...tab,
                component: NoteContent,
            };
        });
    }
};

const saveTabs = () => {
    localStorage.setItem("tabs", JSON.stringify(tabs.value));
};

const deleteTab = (tabId) => {
    tabs.value = tabs.value.filter((tab) => tab.id !== tabId);

    if (currentTab.value === tabId) {
        currentTab.value = tabs.value.length > 0 ? tabs.value[0].id : null;
    }

    if (tabs.value.length === 0) {
        addTab();
    }
};

onMounted(() => {
    window.addEventListener("beforeunload", saveTabs);
    loadTabs();
});

onUnmounted(() => {
    saveTabs();
});

let currentTab = ref(tabs.value[0].id);

const addTab = () => {
    const newTabIndex = tabs.value.length + 1;
    const newTab = {
        id: newTabIndex,
        component: NoteContent,
        name: `Tab ${newTabIndex}`,
        content: ``,
    };
    tabs.value.push(newTab);
};

const updateContent = (tabId, content) => {
    tabs.value = tabs.value.map((tab) => {
        if (tab.id === tabId) {
            tab.content = content;
        }
        return tab;
    });
};

const handleSwitchTab = (tabId) => {
    currentTab.value = tabId;
};

const el = ref();
const { x, y, style } = useDraggable(el, {
    initialValue: { x: 80, y: 240 },
});

const showNote = ref(false);
const authUser = computed(() => usePage().props.authUser.user);

function toggleNote() {
    showNote.value = !showNote.value;
}
</script>

<style scoped>
.popup {
    position: fixed;
    bottom: 20vh;
    left: 3vw;
}
</style>
