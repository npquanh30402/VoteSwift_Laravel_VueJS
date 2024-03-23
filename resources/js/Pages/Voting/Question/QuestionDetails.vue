<template>
    <BaseModal title="Question Details">
        <div class="row">
            <div class="col-md-8">
                <div class="mb-3">
                    <label for="question_title" class="form-label">Question Title</label>
                    <input type="text" class="form-control" v-model="data.question_title"
                           placeholder="Enter Question Title" disabled>
                </div>
                <div class="mb-3">
                    <label for="question_description" class="form-label">Question Description</label>
                    <textarea v-model="data.question_description"
                              class="form-control"
                              style="height: 10rem" disabled></textarea>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group mb-4 text-center">
                    <img :src="data.question_image"
                         class="img-fluid"
                         style="cursor: pointer"
                         alt="Image" @click="showSingle"/>
                    <teleport to="body">
                        <vue-easy-lightbox
                            :visible="visibleRef"
                            :imgs="imgsRef"
                            :index="indexRef"
                            @hide="onHide"
                        ></vue-easy-lightbox>
                    </teleport>
                </div>
            </div>
        </div>
    </BaseModal>
</template>

<script setup>
import BaseModal from "@/Components/BaseModal.vue";
import {reactive, ref, watch} from "vue";
import VueEasyLightbox from "vue-easy-lightbox";

const props = defineProps(['question'])

const visibleRef = ref(false)
const indexRef = ref(0)
const imgsRef = ref([])

const onShow = () => {
    visibleRef.value = true
}

const showSingle = (e) => {
    imgsRef.value = e.target.src
    onShow()
}

const onHide = () => {
    visibleRef.value = false
}

const data = reactive({
    question_title: props.question?.question_title,
    question_description: props.question?.question_description,
    question_image: props.question?.question_image,
});

watch(() => props.question, (newQuestion) => {
    data.question_title = newQuestion?.question_title;
    data.question_description = newQuestion?.question_description;
    data.question_image = newQuestion?.question_image;
    console.log(data.question_image)
}, {
    deep: true,
    immediate: true,
});
</script>
