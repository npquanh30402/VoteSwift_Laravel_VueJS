<template>
    <div class="form-floating">
        <textarea
            v-model="content"
            class="form-control"
            placeholder="Content"
            style="height: 300px"
            @input="handleUpdateContent"
        >
        </textarea>
        <label for="floatingTextarea2">Write a note</label>
    </div>
</template>

<script setup>
import { ref } from "vue";

const props = defineProps(["tab"]);
const emit = defineEmits(["update-content"]);
const content = ref(props.tab.content);

const handleUpdateContent = () => {
    emit("update-content", props.tab.id, content.value);
};
</script>
