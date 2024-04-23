import { defineStore } from "pinia";
import { route } from "ziggy-js";
import { useToast } from "vue-toast-notification";

const toast = useToast();

export const useEtcStore = defineStore("etc", () => {
    const onUploadImg = async (files, callback) => {
        try {
            const res = await Promise.all(
                files.map(async (file) => {
                    const form = new FormData();
                    form.append("image", file);

                    return await axios.post(route("api.images.store"), form, {
                        headers: {
                            "Content-Type": "multipart/form-data",
                        },
                    });
                }),
            );

            const imageUrls = res.map((item) => item.data.image);
            callback(imageUrls);
        } catch (error) {
            toast.error(error.response.data.message);
            callback(null, error);
        }
    };

    return {
        onUploadImg,
    };
});
