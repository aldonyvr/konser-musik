<script setup lang="ts">
import { block, unblock } from "@/libs/utils";
import { onMounted, ref, watch, computed } from "vue";
import * as Yup from "yup";
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

const user = ref<Konser>({} as Konser);
const formRef = ref()
const fileTypes = ref(["image/jpeg", "image/png", "image/jpg"]);
const image = ref<any>([]);


function getEdit() {
    block(document.getElementById("form-user"));
    ApiService.get("/konser/edit", props.selected)
        .then(({ data }) => {
            user.value = data.data;
        })
        .catch((err: any) => {
            toast.error(err.response.data.message);
        })
        .finally(() => {
            unblock(document.getElementById("form-user"));
        });
}



function submit() {
    const konserFormData = new FormData();
    konserFormData.append("title", user.value.title);
    konserFormData.append("jam", user.value.jam);
    konserFormData.append("tanggal", user.value.tanggal);
    konserFormData.append("lokasi", user.value.lokasi);
    konserFormData.append("harga", user.value.harga);
    konserFormData.append("tiket_tersedia", user.value.tiket_tersedia);
    konserFormData.append("deskripsi", user.value.deskripsi);
    konserFormData.append("nama_sosmed", user.value.nama_sosmed);

    if (image.value.length) {
        konserFormData.append("image", image.value[0].file);
    }

    const tiketFormData = new FormData();
    tiketFormData.append("vip", user.value.vip);
    tiketFormData.append("reguler", user.value.reguler);

    block(document.getElementById("form-user"));

    Promise.all([
        axios({
            method: "post",
            url: props.selected
                ? `/konser/update/${props.selected}`
                : "/konser/store",
            data: konserFormData,
            headers: {
                "Content-Type": "multipart/form-data",
            },
        }),
        axios.post("/tiket/store", tiketFormData),
    ])
        .then(([konserResponse, tiketResponse]) => {
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
                    <!--begin::Input group-->
                    <div class="fv-row mb-7">
                        <label class="form-label fw-bold fs-6 required">
                            Nama Konser
                        </label>
                        <Field class="form-control form-control-lg form-control-solid" type="text" name="title"
                            autocomplete="off" v-model="user.title" placeholder="Judul Konser" />
                        <div class="fv-plugins-message-container">
                            <div class="fv-help-block">
                                <ErrorMessage name="title" />
                            </div>
                        </div>
                    </div>
                    <!--end::Input group-->
                </div>

                <div class="col-md-3">
                    <!--begin::Input group-->
                    <div class="fv-row mb-7">
                        <label class="form-label fw-bold fs-6 required">
                            Tanggal
                        </label>
                        <Field name="tanggal" class="form-control form-control-lg form-control-solid"
                            autocomplete="off">
                            <date-picker v-model="user.tanggal" placeholder="Masukan Tanggal Keberlakuan"></date-picker>
                        </Field>
                        <div class="fv-plugins-message-container">
                            <div class="fv-help-block">
                                <ErrorMessage name="tanggal" />
                            </div>
                        </div>
                    </div>
                    <!--end::Input group-->
                </div>

                <div class="col-md-3">
                    <!--begin::Input group-->
                    <div class="fv-row mb-7">
                        <label class="form-label fw-bold fs-6 required">
                            Lokasi
                        </label>
                        <Field class="form-control form-control-lg form-control-solid" type="text" name="lokasi"
                            autocomplete="off" v-model="user.lokasi" placeholder="Lokasi" />
                        <div class="fv-plugins-message-container">
                            <div class="fv-help-block">
                                <ErrorMessage name="lokasi" />
                            </div>
                        </div>
                    </div>
                    <!--end::Input group-->
                </div>

                <div class="col-md-3">
                    <!--begin::Input group-->
                    <div class="fv-row mb-7">
                        <label class="form-label fw-bold fs-6">
                            Jam Mulai Konser
                        </label>
                        <Field name="jam" class="form-control form-control-lg form-control-solid"
                            autocomplete="off">
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
                    <!--end::Input group-->
                </div>

                
                <div class="col-md-3">
                    <!--begin::Input group-->
                    <div class="fv-row mb-7">
                        <label class="form-label fw-bold fs-6 required">
                            Kontak
                        </label>
                        <Field class="form-control form-control-lg form-control-solid" type="text" name="kontak"
                            autocomplete="off" v-model="user.kontak" placeholder="" />
                        <div class="fv-plugins-message-container">
                            <div class="fv-help-block">
                                <ErrorMessage name="kontak" />
                            </div>
                        </div>
                    </div>
                    <!--end::Input group-->
                </div>
                <div class="col-md-3">
                    <!--begin::Input group-->
                    <div class="fv-row mb-7">
                        <label class="form-label fw-bold fs-6 required">
                            Total Tiket
                        </label>
                        <Field class="form-control form-control-lg form-control-solid" type="text" name="tiket_tersedia"
                            autocomplete="off" v-model="user.tiket_tersedia" placeholder="" />
                        <div class="fv-plugins-message-container">
                            <div class="fv-help-block">
                                <ErrorMessage name="tiket_tersedia" />
                            </div>
                        </div>
                    </div>
                    <!--end::Input group-->
                </div>
                <div class="col-md-3">
                    <!--begin::Input group-->
                    <div class="fv-row mb-7">
                        <label class="form-label fw-bold fs-6 required">
                            Harga
                        </label>
                        <Field class="form-control form-control-lg form-control-solid" type="text" name="harga"
                            autocomplete="off" v-model="user.harga"/>
                        <div class="fv-plugins-message-container">
                            <div class="fv-help-block">
                                <ErrorMessage name="harga" />
                            </div>
                        </div>
                    </div>
                    <!--end::Input group-->
                </div>
                <div class="col-md-3">
                    <!--begin::Input group-->
                    <div class="fv-row mb-7">
                        <label class="form-label fw-bold fs-6 required">
                            Nama Sosmed
                        </label>
                        <Field class="form-control form-control-lg form-control-solid" type="text" name="nama_sosmed"
                            autocomplete="off" v-model="user.nama_sosmed" placeholder="Nama Sosial Media" />
                        <div class="fv-plugins-message-container">
                            <div class="fv-help-block">
                                <ErrorMessage name="nama_sosmed" />
                            </div>
                        </div>
                    </div>
                    <!--end::Input group-->
                </div>

                <div class="col-md-3">
                    <!--begin::Input group-->
                    <div class="fv-row mb-7">
                        <label class="form-label fw-bold fs-6 required">
                            Tiket VIP
                        </label>
                        <Field class="form-control form-control-lg form-control-solid" type="text" name="vip"
                            autocomplete="off" v-model="user.vip" placeholder="" />
                        <div class="fv-plugins-message-container">
                            <div class="fv-help-block">
                                <ErrorMessage name="vip" />
                            </div>
                        </div>
                    </div>
                    <!--end::Input group-->
                </div>
                <div class="col-md-3">
                    <!--begin::Input group-->
                    <div class="fv-row mb-7">
                        <label class="form-label fw-bold fs-6 required">
                            Tiket REGULER
                        </label>
                        <Field class="form-control form-control-lg form-control-solid" type="text" name="reguler"
                            autocomplete="off" v-model="user.reguler" placeholder="" />
                        <div class="fv-plugins-message-container">
                            <div class="fv-help-block">
                                <ErrorMessage name="reguler" />
                            </div>
                        </div>
                    </div>
                    <!--end::Input group-->
                </div>
                <div class="col-md-12">
                    <!--begin::Input group-->
                    <div class="fv-row mb-7">
                        <label class="form-label fw-bold fs-6 required">
                            Deskripsi
                        </label>
                        <textarea class="form-control form-control-lg form-control-solid" type="text" name="deskripsi"
                            autocomplete="off" v-model="user.deskripsi" placeholder="Deskripsi" />
                        <div class="fv-plugins-message-container">
                            <div class="fv-help-block">
                                <ErrorMessage name="deskripsi" />
                            </div>
                        </div>
                    </div>
                    <!--end::Input group-->
                </div>

                <div class="col-md-12">
                    <!--begin::Input group-->
                    <div class="fv-row mb-7">
                        <label class="form-label fw-bold fs-6"> Banner Konser</label>
                        <!--begin::Input-->
                        <file-upload
                            :files="image"
                            :accepted-file-types="fileTypes"
                            required
                            v-on:updatefiles="(file) => (image = file)"
                        ></file-upload>
                        <!--end::Input-->
                        <div class="fv-plugins-message-container">
                            <div class="fv-help-block">
                                <ErrorMessage name="foto" />
                            </div>
                        </div>
                    </div>
                    <!--end::Input group-->
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
