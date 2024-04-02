<template>
    <div class="card shadow-sm border-0 mb-3 overflow-auto">
        <div class="card-header text-bg-dark text-center">Password</div>
        <form @submit.prevent="submit" class="card-body">
            <div class="row gx-3">
                <div class="d-flex flex-column gap-3">
                    <div class="hstack gap-3 align-items-center">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" role="switch" id="passwordSwitch"
                                   @change="togglePassword" :checked="isPasswordEnable">
                            <label class="form-check-label" for="passwordSwitch">Enable Password</label>
                        </div>
                    </div>
                    <div class="row g-3" :class="[isPasswordEnable ? '' : 'un-interactive']">
                        <div class="col-md-12">
                            <form @submit.prevent="submit"
                                  class="card border border-3 border-success-subtle p-3 shadow-sm form-group d-flex flex-column">
                                <label class="form-label" for="require_password">Password:</label>
                                <input type="password" id="require_password" name="require_password"
                                       class="form-control form-control-sm"
                                       v-model="form.require_password">
                                <p class="m-0 small text-danger"></p>
                                <div class="ms-auto mt-3">
                                    <button type="submit" class="btn btn-primary">Save</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</template>

<script setup>
import {router, useForm} from "@inertiajs/vue3";
import {route} from "ziggy-js";
import {ref} from "vue";

const props = defineProps(['room', 'room_settings'])

const isPasswordEnable = ref(props.room_settings?.password !== null);

const form = useForm({
    require_password: props.room_settings?.password,
});

const togglePassword = () => {
    isPasswordEnable.value = !isPasswordEnable.value

    if (isPasswordEnable.value === false) {
        router.post(route('room.settings.password.update', props.room.id), {
            _method: 'put',
            ...form,
            require_password: null,
        })
    }
}

const submit = () => {
    router.post(route('room.update', props.room.id), {
        _method: 'put',
        ...form
    })
}
</script>
