<template>
    <div>
        <ul class="nav nav-pills nav-fill hstack">
            <li
                v-for="tab in tabArray"
                :key="tab.componentName"
                class="nav-item"
                @click="switchTab(tab.componentName)"
            >
                <button
                    :class="{ active: currentTab === tab.componentName }"
                    class="nav-link"
                >
                    {{ tab.name }}
                </button>
            </li>
        </ul>
    </div>
</template>

<script setup>
import { computed, ref } from "vue";

const props = defineProps(["tabs"]);
const emit = defineEmits(["switch-tab"]);

const tabArray = computed(() => {
    return Object.values(props.tabs).map((tab) => ({
        component: tab.component,
        name: tab.name,
        componentName: tab.componentName,
    }));
});

const currentTab = ref(tabArray.value[0].componentName);

const switchTab = (tabName) => {
    currentTab.value = tabName;
    emit("switch-tab", tabName);
};
</script>
