<template>
    <div>
        <p v-if="question.allow_multiple_votes">User can choose mutiple votes</p>
        <p v-else>User can only choose one vote</p>
        <p v-if="question.allow_skipping">User can skip voting</p>
        <p v-else>User cannot skip voting</p>
    </div>
</template>

<script setup>
defineProps(['question'])
</script>
