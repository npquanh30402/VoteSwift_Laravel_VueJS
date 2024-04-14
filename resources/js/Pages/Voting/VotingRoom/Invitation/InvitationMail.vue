<template>
    <div>
        <div class="mb-3">
            <label class="form-label" for="emailSubject">Subject:</label>
            <input
                id="emailSubject"
                v-model="form.mail_subject"
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
</template>
<script setup>
import { MdEditor } from "md-editor-v3";
import { route } from "ziggy-js";
import { computed, onMounted, reactive, watch } from "vue";
import { useInvitationMailStore } from "@/Stores/invitationMail.js";
import { useToast } from "vue-toast-notification";
import { useHelper } from "@/Services/helper.js";

const props = defineProps(["room"]);
const $toast = useToast();
const { sanitizeAndTrim } = useHelper();

const {
    invitationMails,
    fetchInvitationMail,
    storeInvitationMail,
    deleteInvitationMail,
} = useInvitationMailStore();

const mail = computed(() => invitationMails[props.room.id]);
const isMailExist = computed(() => mail.value !== null);

const form = reactive({
    mail_subject: mail.value?.mail_subject,
    mail_content: mail.value?.mail_content,
});

watch(mail, () => {
    form.mail_subject = mail.value?.mail_subject;
    form.mail_content = mail.value?.mail_content;
});

onMounted(() => fetchInvitationMail(props.room.id));

const handleSubmit = async () => {
    const formData = new FormData();
    formData.append("mail_subject", sanitizeAndTrim(form.mail_subject));
    formData.append("mail_content", sanitizeAndTrim(form.mail_content));

    const response = await storeInvitationMail(props.room.id, formData);

    if (response) {
        $toast.success(response.data.message);
    }
};

const handleDelete = async () => {
    const response = await deleteInvitationMail(props.room.id, mail.value?.id);

    if (response) {
        $toast.success(response.data.message);
    }
};

const onUploadImg = async (files, callback) => {
    const res = await Promise.all(
        files.map((file) => {
            return new Promise((rev, rej) => {
                const form = new FormData();
                form.append("image", file);

                axios
                    .post(route("api.image.upload"), form, {
                        headers: {
                            "Content-Type": "multipart/form-data",
                        },
                    })
                    .then((res) => rev(res))
                    .catch((error) => rej(error));
            });
        }),
    );
    callback(res.map((item) => item.data.image));
};
</script>
