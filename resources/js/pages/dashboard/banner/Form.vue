<script setup lang="ts">
import { block, unblock } from "@/libs/utils";
import { onMounted, ref, watch, computed } from "vue";
import axios from "@/libs/axios";
import { toast } from "vue3-toastify";
import type { Konser } from "@/types";
import ApiService from "@/core/services/ApiService";
import { useRole } from "@/services/useRole";

const props = defineProps({
    selected: {
        type: String,
        default: null,
    },
});

const emit = defineEmits(["close", "refresh"]);
const user = ref({});
const formRef = ref()
const fileTypes = ref(["image/jpeg", "image/png", "image/jpg"]);
const image = ref<any>([]);
const previewImage = ref('');

function getEdit() {
    block(document.getElementById("form-user"));
    ApiService.get(`/banner/edit/${props.selected}`)
        .then(({ data }) => {
            if (data.success && data.data) {
                user.value = data.data;
                previewImage.value = data.data.image;
                console.log('Current banner:', data.data);
            }
        })
        .catch((err: any) => {
            console.error('Error fetching banner:', err);
            toast.error(err.response?.data?.message || 'Gagal mengambil data banner');
        })
        .finally(() => {
            unblock(document.getElementById("form-user"));
        });
}

function submit() {
    const formData = new FormData();

    if (image.value.length > 0) {
        formData.append("image", image.value[0].file);
    }

    block(document.getElementById("form-user"));

    const url = props.selected
        ? `/banner/update/${props.selected}`
        : "/banner/store";

    axios.post(url, formData, {
        headers: {
            "Content-Type": "multipart/form-data",
        },
    })
        .then((response) => {
            if (response.data.success) {
                toast.success(props.selected ? "Banner berhasil diperbarui" : "Banner berhasil disimpan");
                emit("refresh");
                emit("close");
            } else {
                throw new Error(response.data.message);
            }
        })
        .catch((err) => {
            console.error('Error saving banner:', err);
            toast.error(err.response?.data?.message || "Gagal menyimpan banner");
        })
        .finally(() => {
            unblock(document.getElementById("form-user"));
        });
}

// Handle image upload preview
const handleImageUpload = (files: any) => {
    image.value = files;
    if (files.length > 0) {
        const reader = new FileReader();
        reader.onload = (e) => {
            previewImage.value = e.target?.result as string;
        };
        reader.readAsDataURL(files[0].file);
    }
};

onMounted(async () => {
    if (props.selected) {
        getEdit();
    }
});

watch(
    () => props.selected,
    () => {
        if (props.selected) {
            getEdit();
        }
    }
);
</script>

<template>
    <VForm class="form card mb-10" @submit="submit" :validation-schema="formSchema" id="form-user" ref="formRef">

        <div class="card-header align-items-center">
            <h2 class="mb-0">{{ selected ? "Edit" : "Tambah" }} Banner</h2>
            <button type="button" class="btn btn-sm btn-light-danger ms-auto" @click="emit('close')">
                Batal
                <i class="la la-times-circle p-0"></i>
            </button>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-12">
                    <!-- Current Banner Preview -->
                    <div v-if="previewImage" class="mb-4">
                        <label class="form-label fw-bold fs-6">Banner Saat Ini:</label>
                        <div class="banner-preview">
                            <img :src="previewImage" 
                                 alt="Current Banner" 
                                 class="img-fluid rounded shadow-sm">
                        </div>
                    </div>

                    <!-- Banner Upload -->
                    <div class="fv-row mb-7">
                        <label class="form-label fw-bold fs-6">
                            {{ selected ? 'Ganti Banner' : 'Upload Banner' }}
                        </label>
                        <file-upload
                            :files="image"
                            :accepted-file-types="fileTypes"
                            :required="!selected"
                            v-on:updatefiles="handleImageUpload"
                            class="banner-upload"
                        >
                            <template #description>
                                <p class="text-muted small">
                                    Format yang didukung: JPG, JPEG, PNG. Ukuran maksimal: 2MB
                                </p>
                            </template>
                        </file-upload>
                        <div class="fv-plugins-message-container">
                            <div class="fv-help-block">
                                <ErrorMessage name="image" />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-footer d-flex">
            <button type="submit" class="btn btn-primary btn-sm ms-auto">
                {{ selected ? 'Update Banner' : 'Simpan Banner' }}
            </button>
        </div>
    </VForm>
</template>

<style scoped>
.banner-preview {
    max-width: 100%;
    margin-bottom: 1rem;
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.banner-preview img {
    width: 100%;
    max-height: 300px;
    object-fit: contain;
    padding: 1rem;
}

.banner-upload {
    border-radius: 8px;
    padding: 1rem;
    transition: all 0.3s ease;
}

.banner-upload:hover {
    border-color: #007bff;
}

.fv-help-block {
    color: #dc3545;
    font-size: 0.875rem;
    margin-top: 0.25rem;
}
</style>
