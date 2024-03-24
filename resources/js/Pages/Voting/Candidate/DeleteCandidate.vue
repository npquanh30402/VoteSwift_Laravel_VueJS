<template>
    <BaseModal title="Delete Candidate">
        <form class="row">
            <p>Do you want to delete this candidate?</p>
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

const props = defineProps(['candidate'])
const submit = () => {
    router.delete(route('candidate.delete', props.candidate.id));
}
</script>
