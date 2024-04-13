<template>
    <div>
        <ul class="nav nav-pills nav-fill">
            <li class="nav-item">
                <button class="nav-link" :class="{'active': currentTab === 'CandidateList'}"
                        @click="switchTab('CandidateList')">Candidates
                </button>
            </li>
            <li class="nav-item">
                <button class="nav-link" :class="{'active': currentTab === 'QuestionRule'}"
                        @click="switchTab('QuestionRule')">Rules
                </button>
            </li>
        </ul>
    </div>
</template>

<script setup>
import {ref} from "vue";

const emit = defineEmits(['switch-tab'])
const currentTab = ref('CandidateList');

const switchTab = (tabName) => {
    currentTab.value = tabName;
    emit('switch-tab', tabName)
}
</script>
