<template>
    <form>
        <div class="container card shadow p-5">
            <div class="row gx-3">
                <div class="col-md-12">
                    <div class="mb-3 form-floating">
                        <input type="text" id="room_name" name="room_name"
                               class="form-control form-control-sm"
                               placeholder="Room Name"
                               required>
                        <label for="room_name">Room Name</label>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3 row justify-content-center align-content-center text-center">
                        <div class="col-md-3 ">
                            <label for="start_time" class="align-middle">Start Time</label>
                        </div>
                        <div class="col-md-9">
                            <VueDatePicker v-model="form.start_time" id="start_time" name="start_time"></VueDatePicker>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3 row justify-content-center align-content-center text-center">
                        <div class="col-md-3 ">
                            <label for="start_time" class="align-middle">End Time</label>
                        </div>
                        <div class="col-md-9">
                            <VueDatePicker v-model="form.end_time" id="end_time" name="end_time"></VueDatePicker>
                        </div>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="mb-3 form-floating">
                        <select class="form-select" id="timezone"
                                name="timezone"
                                aria-label="Timezone" placeholder="Timezone">
                            <option v-for="(key, timezone) in timezones_with_offset" :value="timezone">{{ timezone }}:
                                GMT{{ key }}
                            </option>
                        </select>
                        <label for="Timezone">Timezone</label>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="mb-3 form-floating">
                        <!--                                        <textarea id="room_description" name="room_description"-->
                        <!--                                                  class="form-control form-control-sm"-->
                        <!--                                                  style="height: 10rem"></textarea>-->
                        <!--                        <label for="room_description">Room Description</label>-->
                        <!--                        <div v-html="renderedMarkdown"></div>-->
                        <MdEditor v-model="text" language="en-US"/>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="mb-3">
                        <div class="form-check">
                            <input type="checkbox" id="allow_multiple_votes" class="form-check-input"
                                   name="allow_multiple_votes">
                            <label for="allow_multiple_votes">Allow Multiple Votes</label>
                        </div>
                    </div>
                </div>

                <div class="col-md-12">
                    <div class="mb-3">
                        <div class="form-check">
                            <label for="public_visibility">Public Visibility</label>
                            <input type="checkbox" id="public_visibility" class="form-check-input"
                                   name="public_visibility">
                        </div>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="mb-3 form-floating">
                        <select class="form-select" id="results_visibility"
                                name="results_visibility"
                                aria-label="Results Visibility" placeholder="Results Visibility">
                            <option value="after_voting">After Voting</option>
                            <option value="restricted" selected>Restricted</option>
                        </select>
                        <label for="results_visibility">Results Visibility</label>
                    </div>
                </div>

                <div class="col-md-12">
                    <div class="mb-3 form-floating">
                        <input type="text" id="require_password" name="require_password"
                               class="form-control form-control-sm" placeholder="Password">
                        <label for="require_password">Password</label>
                    </div>
                </div>

                <div class="col-md-12">
                    <div class="mb-3">
                        <div class="form-check">
                            <label for="allow_voting">Allow Voting</label>
                            <input type="checkbox" id="allow_voting" class="form-check-input"
                                   name="allow_voting" checked>
                        </div>
                    </div>
                </div>

                <div class="col-md-12">
                    <div class="mb-3">
                        <div class="form-check">
                            <label for="allow_skipping">Allow Skipping</label>
                            <input type="checkbox" id="allow_skipping" class="form-check-input"
                                   name="allow_skipping">
                        </div>
                    </div>
                </div>

                <div class="col-md-12">
                    <div class="mb-3">
                        <div class="form-check">
                            <label for="allow_anonymous_voting">Allow Anonymous Voting</label>
                            <input type="checkbox" id="allow_anonymous_voting" class="form-check-input"
                                   name="allow_anonymous_voting">
                        </div>
                    </div>
                </div>

                <div class="col-md-8">
                    <div class="d-grid">
                        <button type="submit" class="btn btn-sm btn-success p-3">Create Room</button>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="d-grid">
                        <button type="reset" class="btn btn-sm btn-secondary p-3" aria-label="Clear">Clear
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</template>

<script setup>
import VueDatePicker from '@vuepic/vue-datepicker';
import '@vuepic/vue-datepicker/dist/main.css';
import {computed, ref} from "vue";
import {MdCatalog, MdEditor, MdPreview} from 'md-editor-v3';
import 'md-editor-v3/lib/style.css';

const id = 'preview-only';
const text = ref('# Hello Editor');
const scrollElement = document.documentElement;

defineProps(['timezones_with_offset'])

const form = ref({
    start_time: null,
    end_time: null,
    markdownContent: '',
});

const renderedMarkdown = computed(() => {
    const md = markdownit()
    return md.render(form.value.markdownContent)
})
</script>
