<template>
    <div style="width: 30vw">
        <ul
            class="nav nav-tabs hstack overflow-auto"
            style="max-width: 100%; flex-wrap: nowrap"
        >
            <li v-for="tab in tabs" :key="tab.id" class="nav-item">
                <button
                    :class="{ active: currentTab === tab.id }"
                    aria-current="page"
                    class="nav-link d-flex"
                    style="white-space: nowrap"
                    @click="switchTab(tab.id)"
                >
                    <span>{{ tab.name }}</span>
                    <span
                        ><i
                            class="bi bi-x-lg text-danger ms-2"
                            @click="deleteTab(tab.id)"
                        ></i
                    ></span>
                </button>
            </li>
            <li class="nav-item">
                <button class="nav-link" @click="addTab">
                    <i class="bi bi-plus text-success"></i>
                </button>
            </li>
        </ul>
    </div>
</template>

<script setup>
import { computed, ref } from "vue";

const props = defineProps(["tabs"]);
const emit = defineEmits(["switch-tab", "add-tab", "delete-tab"]);
const currentTab = ref(props.tabs[0].id);

const tabs = computed(() => {
    return props.tabs;
});

const switchTab = (tabId) => {
    currentTab.value = tabId;
    emit("switch-tab", tabId);
};

const addTab = () => {
    emit("add-tab");
};

const deleteTab = (tabId) => {
    emit("delete-tab", tabId);
};
</script>
