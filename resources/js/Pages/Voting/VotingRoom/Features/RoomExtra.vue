<template>
    <div>
        <form v-if="isReady" @submit.prevent="submit">
            <div>
                <p class="fw-bold fs-5">1. Age Requirement:</p>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-floating mb-3">
                            <input
                                id="minimumAge"
                                v-model="form.minimum_age"
                                class="form-control"
                                placeholder="minimum age"
                                required
                                type="number"
                            />
                            <label for="minimumAge">Minimum Age</label>
                            <p
                                v-if="errorMessages.minimum_age"
                                class="m-0 small text-danger"
                            >
                                {{ errorMessages.minimum_age }}
                            </p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-floating mb-3">
                            <input
                                id="maximumAge"
                                v-model="form.maximum_age"
                                class="form-control"
                                placeholder="maximum age"
                                required
                                type="number"
                            />
                            <label for="maximumAge">Maximum Age</label>
                            <p
                                v-if="errorMessages.maximum_age"
                                class="m-0 small text-danger"
                            >
                                {{ errorMessages.maximum_age }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            <div
                :class="{
                    'un-interactive':
                        errorMessages.minimum_age || errorMessages.maximum_age,
                }"
                class="float-end"
            >
                <button class="btn btn-primary" type="submit">Save</button>
            </div>
        </form>
        <BaseLoading v-else />
    </div>
</template>
<script setup>
import { computed, onMounted, reactive, ref, watch } from "vue";
import { useVotingSettingStore } from "@/Stores/voting-settings.js";
import BaseLoading from "@/Components/BaseLoading.vue";

const props = defineProps(["room"]);
const isReady = ref(false);
const votingSettingStore = useVotingSettingStore();

const roomSettings = computed(() => votingSettingStore.settings[props.room.id]);

const form = reactive({
    minimum_age: null,
    maximum_age: null,
});

watch(
    () => votingSettingStore.settings[props.room.id],
    (newValue) => {
        form.minimum_age = newValue?.minimum_age;
        form.maximum_age = newValue?.maximum_age;
    },
    { immediate: true },
);

const errorMessages = reactive({
    minimum_age: "",
    maximum_age: "",
});

const updateErrorMessage = (fieldName, value) => {
    if (value === "") {
        errorMessages[fieldName] = "Age cannot be empty.";
        return;
    }

    switch (fieldName) {
        case "minimum_age":
            if (value < 0) {
                errorMessages.minimum_age =
                    "Minimum age cannot be less than 0.";
            } else if (value > form.maximum_age) {
                errorMessages.minimum_age =
                    "Minimum age cannot be greater than maximum age.";
            } else {
                errorMessages.minimum_age = "";
            }
            break;
        case "maximum_age":
            if (value < 0) {
                errorMessages.maximum_age =
                    "Maximum age cannot be less than 0.";
            } else if (value < form.minimum_age) {
                errorMessages.maximum_age =
                    "Maximum age cannot be less than minimum age.";
            } else {
                errorMessages.maximum_age = "";
            }
            break;
    }
};

watch(
    [() => form.minimum_age, () => form.maximum_age],
    ([newMinAge, newMaxAge]) => {
        updateErrorMessage("minimum_age", newMinAge);
        updateErrorMessage("maximum_age", newMaxAge);
    },
);

const submit = async () => {
    const formData = new FormData();
    formData.append("minimum_age", form.minimum_age);
    formData.append("maximum_age", form.maximum_age);

    await votingSettingStore.updateSettings(props.room.id, formData);
};

onMounted(async () => {
    await votingSettingStore.fetchSettings(props.room.id);

    isReady.value = true;
});
</script>
