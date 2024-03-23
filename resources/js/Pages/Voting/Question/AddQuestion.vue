<template>
    <BaseModal title="Create a Question">
        <form @submit.prevent="submit" class="row">
            <div class="col-md-8">
                <div class="mb-3">
                    <label for="question_title" class="form-label">Question Title</label>
                    <input type="text" class="form-control" v-model="form.question_title"
                           placeholder="Enter Question Title">
                </div>
                <div class="mb-3">
                    <label for="question_description" class="form-label">Question Description</label>
                    <textarea v-model="form.question_description"
                              class="form-control"
                              style="height: 10rem"></textarea>
                </div>
                <div class="text-end">
                    <button type="submit" class="btn btn-primary">Add</button>
                </div>
            </div>
            <div class="col-md-4 vstack">
                <div class="form-group">
                    <label class="form-label" for="question_image">Image:</label>
                    <input type="file" class="form-control" id="question_image" name="question_image"
                           @change="handleFileChange">
                    <p class="m-0 small text-danger"></p>
                </div>
                <!--                <div class="form-group">-->
                <!--                    <img src=""-->
                <!--                         class="img-fluid"-->
                <!--                         style="width: 10rem;"-->
                <!--                         alt="Avatar"/>-->
                <!--                </div>-->
            </div>
        </form>
    </BaseModal>
</template>

<script setup>
import BaseModal from "@/Components/BaseModal.vue";
import {router, useForm} from "@inertiajs/vue3";
import {route} from "ziggy-js";

const props = defineProps(['room'])

const form = useForm({
    question_title: '',
    question_description: '',
    question_image: null,
});

function handleFileChange(event) {
    form.question_image = event.target.files[0];
}

const submit = () => {
    router.post(route('question.store', props.room.id), {
        ...form,
    });
}
</script>
