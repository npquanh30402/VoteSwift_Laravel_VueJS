<template>
    <div class="list-group shadow-sm small mb-3">
        <div class="list-group-item text-bg-dark">Actions</div>
        <button
            v-for="tab in tabDataArray"
            :key="tab.componentName"
            :class="[
                'list-group-item',
                'list-group-item-action',
                {
                    active: currentTab === tab.componentName,
                    'opacity-50 fw-bold text-bg-danger':
                        tab.componentName === 'DeleteRoom',
                    'opacity-50 fw-bold text-bg-success':
                        tab.componentName === 'RoomPublish',
                    'opacity-50 fw-bold text-bg-warning':
                        tab.componentName === 'RoomDuplication',
                    'opacity-100':
                        currentTab === tab.componentName &&
                        [
                            'DeleteRoom',
                            'RoomPublish',
                            'RoomDuplication',
                        ].includes(tab.componentName),
                },
            ]"
            @click="switchTab(tab.componentName)"
        >
            <i :class="tab.icon" class="bi me-2"></i>
            {{ tab.name }}
        </button>
    </div>
</template>

<script setup>
import { computed, ref } from "vue";

const props = defineProps(["tabData", "room"]);
const emit = defineEmits(["switch-tab"]);

const tabDataArray = computed(() => {
    return Object.values(props.tabData).map((tab) => ({
        component: tab.component,
        name: tab.name,
        icon: tab.icon,
        componentName: tab.componentName,
    }));
});

const currentTab = ref(tabDataArray.value[0].componentName);

const switchTab = (tabName) => {
    currentTab.value = tabName;
    emit("switch-tab", tabName);
};
</script>
