<script setup lang="ts">
import { block, unblock } from "@/libs/utils";
import { onMounted, ref, watch } from "vue";
import * as Yup from "yup";
import axios from "@/libs/axios";
import { toast } from "vue3-toastify";
import type { User } from "@/types";
import ApiService from "@/core/services/ApiService";
import { Field, Form as VForm, ErrorMessage } from 'vee-validate';

const props = defineProps({
    selected: {
        type: String,
        default: null,
    },
});

const emit = defineEmits(["close", "refresh"]);

const user = ref<User>({
    konser: {
        title: '',
        tiket_tersedia: '',
    },
    harga_vip: '',
    harga_regular: '',
    vip: '',
    reguler: '',
    opengate: '',
    closegate: '',
    gate_a_regular: '',
    gate_b_regular: '',
    gate_c_regular: '',
    gate_d_regular: '',
    gate_e_regular: '',
});

const formRef = ref();

// Form validation schema
const formSchema = Yup.object().shape({
    // konser_title: Yup.string().required('Nama Konser wajib diisi'),
    // konser_tiket_tersedia: Yup.number().required('Total tiket wajib diisi'),
    vip: Yup.number().required('Tiket VIP wajib diisi'),
    reguler: Yup.number().required('Tiket Reguler wajib diisi'),
    harga_vip: Yup.number().required('Harga Tiket VIP wajib diisi'),
    harga_regular: Yup.number().required('Harga Tiket Reguler wajib diisi'),
    opengate: Yup.string().required('Open Gate wajib diisi'),
    closegate: Yup.string().required('Close Gate wajib diisi'),

});

function getEdit() {
    if (!props.selected) return;
    
    block(document.getElementById("form-user"));
    ApiService.get(`/tiket/edit/${props.selected}`)
        .then(({ data }) => {
            user.value = data.data;
        })
        .catch((err: any) => {
            toast.error(err.response?.data?.message || 'Terjadi kesalahan');
        })
        .finally(() => {
            unblock(document.getElementById("form-user"));
        });
}

function submit() {
    const formData = new FormData();
    formData.append("title", user.value.konser.title);
    formData.append("tiket_tersedia", user.value.konser.tiket_tersedia);
    formData.append("harga_vip", user.value.harga_vip);
    formData.append("harga_regular", user.value.harga_regular);
    formData.append("vip", user.value.vip);
    formData.append("reguler", user.value.reguler);
    formData.append("opengate", user.value.opengate);
    formData.append("closegate", user.value.closegate);
    formData.append("gate_a_capacity", user.value.gate_a_capacity);
    formData.append("gate_b_capacity", user.value.gate_b_capacity);
    formData.append("gate_c_capacity", user.value.gate_c_capacity);
    formData.append("gate_d_capacity", user.value.gate_d_capacity);
    formData.append("gate_e_capacity", user.value.gate_e_capacity);

    block(document.getElementById("form-user"));
    
    const url = props.selected
        ? `/tiket/update/${props.selected}`
        : "/tiket/store";

    axios({
        method: "post",
        url,
        data: formData,
        headers: {
            "Content-Type": "multipart/form-data",
        },
    })
        .then(() => {
            emit("close");
            emit("refresh");
            toast.success("Data berhasil disimpan");
            formRef.value?.resetForm();
        })
        .catch((err: any) => {
            if (formRef.value && err.response?.data?.errors) {
                formRef.value.setErrors(err.response.data.errors);
            }
            toast.error(err.response?.data?.message || 'Terjadi kesalahan');
        })
        .finally(() => {
            unblock(document.getElementById("form-user"));
        });
}

onMounted(() => {
    if (props.selected) {
        getEdit();
    }
});

watch(
    () => props.selected,
    (newValue) => {
        if (newValue) {
            getEdit();
        }
    }
);
</script>

<template>
    <VForm 
        class="form card mb-10" 
        @submit="submit" 
        :validation-schema="formSchema" 
        id="form-user" 
        ref="formRef"
    >
        <div class="card-header align-items-center">
            <h2 class="mb-0">{{ selected ? "Edit" : "Tambah" }} Tiket</h2>
            <button type="button" class="btn btn-sm btn-light-danger ms-auto" @click="emit('close')">
                Batal
                <i class="la la-times-circle p-0"></i>
            </button>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="fv-row mb-7">
                        <label class="form-label fw-bold fs-6 required">
                            Nama Konser
                        </label>
                        <Field 
                            class="form-control form-control-lg form-control-solid" 
                            type="text" 
                            name="konser.title"
                            v-model="user.konser.title" 
                            placeholder="Nama Konser" 
                        />
                        <div class="fv-plugins-message-container">
                            <div class="fv-help-block">
                                <ErrorMessage name="konser.title" />
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="fv-row mb-7">
                        <label class="form-label fw-bold fs-6 required">
                            Total Jumlah Tiket 
                        </label>
                        <Field 
                            class="form-control form-control-lg form-control-solid" 
                            type="number" 
                            name="konser.tiket_tersedia"
                            v-model="user.konser.tiket_tersedia" 
                            placeholder="Total Jumlah Tiket" 
                        />
                        <div class="fv-plugins-message-container">
                            <div class="fv-help-block">
                                <ErrorMessage name="konser.tiket_tersedia" />
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="fv-row mb-7">
                        <label class="form-label fw-bold fs-6 required">
                            Tiket VIP
                        </label>
                        <Field 
                            class="form-control form-control-lg form-control-solid" 
                            type="number" 
                            name="vip"
                            v-model="user.vip" 
                            placeholder="Tiket VIP" 
                        />
                        <div class="fv-plugins-message-container">
                            <div class="fv-help-block">
                                <ErrorMessage name="vip" />
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="fv-row mb-7">
                        <label class="form-label fw-bold fs-6 required">
                            Tiket REGULER
                        </label>
                        <Field 
                            class="form-control form-control-lg form-control-solid" 
                            type="number" 
                            name="reguler"
                            v-model="user.reguler" 
                            placeholder="Tiket REGULER" 
                        />
                        <div class="fv-plugins-message-container">
                            <div class="fv-help-block">
                                <ErrorMessage name="reguler" />
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="fv-row mb-7">
                        <label class="form-label fw-bold fs-6 required">
                            Open Gate
                        </label>
                        <Field name="opengate" class="form-control form-control-lg form-control-solid" v-model="user.opengate">
                            <date-picker 
                                v-model="user.opengate" 
                                placeholder="Open Gate" 
                                :config="{
                                    enableTime: true,
                                    noCalendar: true,
                                    format: 'H:i'
                                }" 
                            />
                        </Field>
                        <div class="fv-plugins-message-container">
                            <div class="fv-help-block">
                                <ErrorMessage name="opengate" />
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="fv-row mb-7">
                        <label class="form-label fw-bold fs-6 required">
                            Close Gate
                        </label>
                        <Field name="closegate" class="form-control form-control-lg form-control-solid" v-model="user.closegate">
                            <date-picker 
                                v-model="user.closegate" 
                                placeholder="Close Gate" 
                                :config="{
                                    enableTime: true,
                                    noCalendar: true,
                                    format: 'H:i'
                                }" 
                            />
                        </Field>
                        <div class="fv-plugins-message-container">
                            <div class="fv-help-block">
                                <ErrorMessage name="closegate" />
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="fv-row mb-7">
                        <label class="form-label fw-bold fs-6 required">
                            Harga Tiket VIP
                        </label>
                        <Field 
                            class="form-control form-control-lg form-control-solid" 
                            type="number" 
                            name="harga_vip"
                            v-model="user.harga_vip" 
                            placeholder="Harga Tiket VIP" 
                        />
                        <div class="fv-plugins-message-container">
                            <div class="fv-help-block">
                                <ErrorMessage name="harga_vip" />
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="fv-row mb-7">
                        <label class="form-label fw-bold fs-6 required">
                            Harga Tiket Regular
                        </label>
                        <Field 
                            class="form-control form-control-lg form-control-solid" 
                            type="number" 
                            name="harga_regular"
                            v-model="user.harga_regular" 
                            placeholder="Harga Tiket Regular" 
                        />
                        <div class="fv-plugins-message-container">
                            <div class="fv-help-block">
                                <ErrorMessage name="harga_regular" />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <h4 class="mb-4">Regular Gate Configuration</h4>
                <div class="col-md-4">
                    <div class="fv-row mb-7">
                        <label class="form-label fw-bold fs-6">
                            Gate A Capacity
                        </label>
                        <Field 
                            class="form-control form-control-lg form-control-solid" 
                            type="number" 
                            name="gate_a_capacity"
                            v-model="user.gate_a_capacity" 
                            placeholder="Gate A Capacity" 
                        />
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="fv-row mb-7">
                        <label class="form-label fw-bold fs-6">
                            Gate B Capacity
                        </label>
                        <Field 
                            class="form-control form-control-lg form-control-solid" 
                            type="number" 
                            name="gate_b_capacity"
                            v-model="user.gate_b_capacity" 
                            placeholder="Gate B Capacity" 
                        />
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="fv-row mb-7">
                        <label class="form-label fw-bold fs-6">
                            Gate C Capacity
                        </label>
                        <Field 
                            class="form-control form-control-lg form-control-solid" 
                            type="number" 
                            name="gate_c_capacity"
                            v-model="user.gate_c_capacity" 
                            placeholder="Gate C Capacity" 
                        />
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="fv-row mb-7">
                        <label class="form-label fw-bold fs-6">
                            Gate D Capacity
                        </label>
                        <Field 
                            class="form-control form-control-lg form-control-solid" 
                            type="number" 
                            name="gate_d_capacity"
                            v-model="user.gate_d_capacity" 
                            placeholder="Gate D Capacity" 
                        />
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="fv-row mb-7">
                        <label class="form-label fw-bold fs-6">
                            Gate E Capacity
                        </label>
                        <Field 
                            class="form-control form-control-lg form-control-solid" 
                            type="number" 
                            name="gate_e_capacity"
                            v-model="user.gate_e_capacity" 
                            placeholder="Gate E Capacity" 
                        />
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