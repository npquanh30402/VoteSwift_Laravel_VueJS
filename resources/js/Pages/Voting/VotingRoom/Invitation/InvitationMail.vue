<template>
    <div v-if="!isLoading">
        <div class="mb-3">
            <label class="form-label" for="emailSubject">Subject:</label>
            <input
                id="emailSubject"
                v-model="form.mail_subject"
                :class="{ 'un-interactive': isPublish }"
                class="form-control"
                placeholder="Enter your voting topic here..."
                type="email"
            />
        </div>
        <div class="mb-3">
            <label class="form-label" for="emailContent">Content:</label>
            <MdEditor
                v-model="form.mail_content"
                language="en-US"
                @onUploadImg="onUploadImg"
            />
            <div
                v-if="!isPublish"
                :class="
                    isMailExist
                        ? 'justify-content-between'
                        : 'justify-content-end'
                "
                class="d-flex"
            >
                <div v-if="isMailExist" class="d-flex align-items-center gap-3">
                    <button class="btn btn-danger mt-3" @click="handleDelete">
                        Delete
                    </button>
                    <VTooltip class="mt-3">
                        <i class="bi bi-info-circle"></i>

                        <template #popper>
                            <p>The system will use default mail template</p>
                        </template>
                    </VTooltip>
                </div>
                <button class="btn btn-success mt-3" @click="handleSubmit">
                    Save
                </button>
            </div>
        </div>
    </div>
    <BaseLoading v-else />
</template>
<script setup>
import { MdEditor } from "md-editor-v3";
import { computed, onMounted, reactive, ref, watch } from "vue";
import { useInvitationMailStore } from "@/Stores/invitationMail.js";
import { useHelper } from "@/Services/helper.js";
import { useEtcStore } from "@/Stores/etc.js";
import BaseLoading from "@/Components/BaseLoading.vue";

const isLoading = ref(true);
const props = defineProps(["room"]);
const helper = useHelper();
const sanitizeAndTrim = helper.sanitizeAndTrim;

const isPublish = computed(() => props.room.is_published === 1);

const invitationMailStore = useInvitationMailStore();

const mail = computed(() => invitationMailStore.invitationMails[props.room.id]);
const isMailExist = computed(() => mail.value !== null);

const form = reactive({
    mail_subject: mail.value?.mail_subject,
    mail_content: mail.value?.mail_content,
});

watch(mail, () => {
    form.mail_subject = mail.value?.mail_subject;
    form.mail_content = mail.value?.mail_content;
});

onMounted(async () => {
    await invitationMailStore.fetchInvitationMail(props.room.id);

    isLoading.value = false;
});

const handleSubmit = async () => {
    const formData = new FormData();
    formData.append("mail_subject", sanitizeAndTrim(form.mail_subject));
    formData.append("mail_content", sanitizeAndTrim(form.mail_content));

    await invitationMailStore.storeInvitationMail(props.room.id, formData);
};

const handleDelete = async () => {
    await invitationMailStore.deleteInvitationMail(
        props.room.id,
        mail.value?.id,
    );
};

const etcStore = useEtcStore();
const onUploadImg = etcStore.onUploadImg;
</script>
