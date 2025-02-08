<script setup lang="ts">
import { block, unblock } from "@/libs/utils";
import { onMounted, ref, watch, computed } from "vue";
import axios from "@/libs/axios";
import { toast } from "vue3-toastify";
import type { Konser } from "@/types";
import ApiService from "@/core/services/ApiService";
import { useRole } from "@/services/useRole";
import * as yup from "yup";

const props = defineProps({
    selected: {
        type: String,
        default: null,
    },
});

const emit = defineEmits(["close", "refresh"]);

const user = ref<Konser>({} as Konser);
const formRef = ref()
const fileTypes = ref(["image/jpeg", "image/png", "image/jpg"]);
const image = ref<any>([]);
const existingImage = ref('');

function getEdit() {
    block(document.getElementById("form-user"));
    ApiService.get("/konser/edit", props.selected)
        .then(({ data }) => {
            user.value = data.data;
            // Handle existing image
            if (data.data.image) {
                existingImage.value = data.data.image;
                // Don't convert to File object immediately
                image.value = [{ source: data.data.image }];
            }
        })
        .catch((err: any) => {
            toast.error(err.response.data.message);
        })
        .finally(() => {
            unblock(document.getElementById("form-user"));
        });
}

const formSchema = yup.object().shape({
    title: yup.string()
        .required("Nama konser wajib diisi")
        .min(3, "Nama konser minimal 3 karakter"),
    lokasi: yup.string()
        .required("Lokasi wajib diisi")
        .min(5, "Lokasi"),
    kontak: yup.string()
        .required("Kontak wajib diisi")
        .matches(/^[0-9+()-]+$/, "Kontak hanya boleh berisi angka dan karakter +()-"),
    tiket_tersedia: yup.number()
        .required("Total tiket wajib diisi")
        .typeError("Total tiket harus berupa angka")
        .positive("Total tiket harus lebih dari 0")
        .integer("Total tiket harus berupa bilangan bulat"),
    nama_sosmed: yup.string()
        .required("Nama sosial media wajib diisi"),
    deskripsi: yup.string()
        .required("Deskripsi wajib diisi")
        .min(10, "Deskripsi minimal 10 karakter"),
});


function submit() {
    const konserFormData = new FormData();
    
    // Add basic form fields
    konserFormData.append("title", user.value.title);
    konserFormData.append("jam", user.value.jam);
    konserFormData.append("tanggal", user.value.tanggal);
    konserFormData.append("lokasi", user.value.lokasi);
    konserFormData.append("tiket_tersedia", user.value.tiket_tersedia);
    konserFormData.append("deskripsi", user.value.deskripsi);
    konserFormData.append("nama_sosmed", user.value.nama_sosmed);
    konserFormData.append("kontak", user.value.kontak);

    // Handle image upload - works for both new and edit
    if (image.value && image.value.length > 0) {
        if (image.value[0] instanceof File) {
            konserFormData.append("image", image.value[0]);
        } else if (image.value[0].file) {
            konserFormData.append("image", image.value[0].file);
        }
    }

    block(document.getElementById("form-user"));

    axios({
        method: "post",
        url: props.selected
            ? `/konser/update/${props.selected}`
            : "/konser/store",
        data: konserFormData,
        headers: {
            "Content-Type": "multipart/form-data",
        },
    })
    .then((konserResponse) => {
        emit("close");
        emit("refresh");
        toast.success("Data konser dan tiket berhasil disimpan");
        formRef.value.resetForm();
    })
    .catch((err: any) => {
        formRef.value.setErrors(err.response?.data?.errors || {});
        toast.error(err.response?.data?.message || "Terjadi kesalahan");
    })
    .finally(() => {
        unblock(document.getElementById("form-user"));
    });
}



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
            <h2 class="mb-0">{{ selected ? "Edit" : "Tambah" }} KONSER</h2>
            <button type="button" class="btn btn-sm btn-light-danger ms-auto" @click="emit('close')">
                Batal
                <i class="la la-times-circle p-0"></i>
            </button>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-3">
                    <div class="fv-row mb-7">
                        <label class="form-label fw-bold fs-6 required">Nama Konser</label>
                        <Field class="form-control form-control-lg form-control-solid" type="text" name="title"
                            autocomplete="off" v-model="user.title" placeholder="Judul Konser" />
                        <div class="fv-plugins-message-container">
                            <div class="fv-help-block">
                                <ErrorMessage name="title" />
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="fv-row mb-7">
                        <label class="form-label fw-bold fs-6 required">Tanggal</label>
                        <Field name="tanggal" class="form-control form-control-lg form-control-solid" autocomplete="off">
                            <date-picker v-model="user.tanggal" placeholder="Masukan Tanggal Keberlakuan"></date-picker>
                        </Field>
                        <div class="fv-plugins-message-container">
                            <div class="fv-help-block">
                                <ErrorMessage name="tanggal" />
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="fv-row mb-7">
                        <label class="form-label fw-bold fs-6 required">Lokasi</label>
                        <Field class="form-control form-control-lg form-control-solid" type="text" name="lokasi"
                            autocomplete="off" v-model="user.lokasi" placeholder="Lokasi" />
                        <div class="fv-plugins-message-container">
                            <div class="fv-help-block">
                                <ErrorMessage name="lokasi" />
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="fv-row mb-7">
                        <label class="form-label fw-bold fs-6 required">Jam Mulai Konser</label>
                        <Field name="jam" class="form-control form-control-lg form-control-solid" autocomplete="off">
                            <date-picker v-model="user.jam" placeholder="Pilih Jam Mulai Konser" :config="{
                                enableTime: true,
                                noCalendar: true,
                                format: 'H:i'
                            }" />
                        </Field>
                        <div class="fv-plugins-message-container">
                            <div class="fv-help-block">
                                <ErrorMessage name="jam" />
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="fv-row mb-7">
                        <label class="form-label fw-bold fs-6 required">Kontak</label>
                        <Field class="form-control form-control-lg form-control-solid" type="tel" name="kontak"
                            autocomplete="off" v-model="user.kontak" placeholder="Nomor Kontak" />
                        <div class="fv-plugins-message-container">
                            <div class="fv-help-block">
                                <ErrorMessage name="kontak" />
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="fv-row mb-7">
                        <label class="form-label fw-bold fs-6 required">Total Tiket</label>
                        <Field class="form-control form-control-lg form-control-solid" type="number" name="tiket_tersedia"
                            autocomplete="off" v-model="user.tiket_tersedia" placeholder="Total Tiket" min="1" />
                        <div class="fv-plugins-message-container">
                            <div class="fv-help-block">
                                <ErrorMessage name="tiket_tersedia" />
                            </div>
                        </div>
                    </div>
                </div>


                <div class="col-md-3">
                    <div class="fv-row mb-7">
                        <label class="form-label fw-bold fs-6 required">Nama Sosmed</label>
                        <Field class="form-control form-control-lg form-control-solid" type="text" name="nama_sosmed"
                            autocomplete="off" v-model="user.nama_sosmed" placeholder="Nama Sosial Media" />
                        <div class="fv-plugins-message-container">
                            <div class="fv-help-block">
                                <ErrorMessage name="nama_sosmed" />
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-12">
                    <div class="fv-row mb-7">
                        <label class="form-label fw-bold fs-6 required">Deskripsi</label>
                        <Field as="textarea" class="form-control form-control-lg form-control-solid" name="deskripsi"
                            autocomplete="off" v-model="user.deskripsi" placeholder="Deskripsi" />
                        <div class="fv-plugins-message-container">
                            <div class="fv-help-block">
                                <ErrorMessage name="deskripsi" />
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-12">
                    <div class="fv-row mb-7">
                        <label class="form-label fw-bold fs-6">Banner Konser</label>
                        <div v-if="existingImage" class="mb-3">
                            <img :src="existingImage" 
                                 alt="Current Banner" 
                                 class="img-thumbnail" 
                                 style="max-height: 200px">
                        </div>
                        <file-upload 
                            :files="image"
                            :accepted-file-types="fileTypes"
                            :required="!existingImage"
                            v-on:updatefiles="(files) => {
                                image = files;
                                if (files && files.length > 0) {
                                    existingImage = null;
                                }
                            }"
                        ></file-upload>
                        <div class="fv-plugins-message-container">
                            <div class="fv-help-block">
                                <ErrorMessage name="foto" />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-footer d-flex">
            <button type="submit" class="btn btn-primary btn-sm ms-auto">
                Simpan
            </button>
        </div>
    </VForm>
</template>

<style scoped>
.img-thumbnail {
    border: 1px solid #dee2e6;
    border-radius: 0.25rem;
    padding: 0.25rem;
    background-color: #fff;
    max-width: 100%;
    height: auto;
}
</style>
