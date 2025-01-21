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


function getEdit() {
    block(document.getElementById("form-user"));
    ApiService.get("/banner/edit", props.selected)
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

    if (image.value.length) {
        konserFormData.append("image", image.value[0].file);
    }

    block(document.getElementById("form-user"));

    Promise.all([
        axios({
            method: "post",
            url: props.selected
                ? `/banner/update/${props.selected}`
                : "/banner/store",
            data: konserFormData,
            headers: {
                "Content-Type": "multipart/form-data",
            },
        }),
    ])
        .then(() => {
            emit("close");
            emit("refresh");
            toast.success("Data berhasil disimpan");
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
            <h2 class="mb-0">{{ selected ? "Edit" : "Tambah" }} Banner</h2>
            <button type="button" class="btn btn-sm btn-light-danger ms-auto" @click="emit('close')">
                Batal
                <i class="la la-times-circle p-0"></i>
            </button>
        </div>
        <div class="card-body">
            <div class="row">

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
                                <ErrorMessage name="image" />
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
