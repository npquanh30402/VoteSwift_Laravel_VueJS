<template>
    <VMenu placement="left">
        <span><i class="bi bi-info-circle fs-4"></i></span>

        <template #popper>
            <div class="card">
                <div class="card-body">
                    <div style="width: 50vw">
                        <h5 class="card-title"><strong>{{ question.question_title }}</strong></h5>
                    </div>
                    <div class="card-text">
                        <div class="overflow-auto" :style="getStyle">
                            <MdPreview :editorId="'question_' + question.id"
                                       :modelValue="question.question_description"/>
                        </div>
                    </div>
                </div>
            </div>
        </template>
    </VMenu>
</template>
<script setup>
import {MdPreview} from "md-editor-v3";
import {computed} from "vue";
import {auto} from "@popperjs/core";

const props = defineProps(['question'])

const getStyle = computed(() => {
    const descriptionLength = props.question.question_description.length;
    if (descriptionLength > 300) {
        return {
            width: '50vw',
            height: '75vh'
        };
    } else {
        return {
            width: '30vw',
            height: auto
        };
    }
});
</script>
