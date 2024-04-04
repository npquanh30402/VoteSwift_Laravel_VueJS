<template>
    <div>
        <p v-if="question.allow_multiple_votes">User can choose <strong>multiple candidates</strong></p>
        <p v-else>User can only choose <strong>one candidate</strong></p>
        <p v-if="question.allow_skipping">User <strong>can skip</strong> voting</p>
        <p v-else>User <strong>cannot skip</strong> voting</p>
    </div>
</template>

<script setup>
defineProps(['question'])
</script>
