<template>
    <BaseModal title="Delete Question">
        <form class="row">
            <p>Do you want to delete this question?</p>
        </form>
        <template #footer>
            <button type="button" class="btn btn-danger" @click="submit" data-bs-dismiss="modal">YES</button>
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        </template>
    </BaseModal>
</template>

<script setup>
import BaseModal from "@/Components/BaseModal.vue";
import {router} from "@inertiajs/vue3";
import {route} from "ziggy-js";

const props = defineProps(['question'])
const submit = () => {
    router.delete(route('question.delete', props.question.id));
}
</script>
