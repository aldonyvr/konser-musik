<script setup lang="ts">
import { block, unblock } from "@/libs/utils";
import { onMounted, ref, watch } from "vue";
import * as Yup from "yup";
import axios from "@/libs/axios";
import { toast } from "vue3-toastify";
import type { User } from "@/types";
import ApiService from "@/core/services/ApiService";
import { Field, Form as VForm, ErrorMessage } from 'vee-validate'; 
import DatePicker from '@/components/DatePicker.vue'; 

const props = defineProps({
    selected: {
        type: String,
        default: null,
    },
});

const emit = defineEmits(["close", "refresh"]);
const formRef = ref();

// Initialize form data
const formData = ref({
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
    gate_a_capacity: '',
    gate_b_capacity: '',
    gate_c_capacity: '',
    gate_d_capacity: '',
    gate_e_capacity: '',
});

// Form validation schema
const formSchema = Yup.object().shape({
    vip: Yup.number().required('Tiket VIP wajib diisi'),
    reguler: Yup.number().required('Tiket Reguler wajib diisi'),
    harga_vip: Yup.number().required('Harga Tiket VIP wajib diisi'),
    harga_regular: Yup.number().required('Harga Tiket Reguler wajib diisi'),
});

function getEdit() {
    if (!props.selected) return;
    
    block(document.getElementById("form-user"));
    ApiService.get(`/tiket/edit/${props.selected}`)
        .then(({ data }) => {
            if (data.success && data.data) {
                formData.value = {
                    ...data.data,
                    konser: data.data.konser || {
                        title: '',
                        tiket_tersedia: ''
                    }
                };
                console.log('Loaded ticket data:', formData.value);
            }
        })
        .catch((err: any) => {
            console.error('Error loading ticket:', err);
            toast.error(err.response?.data?.message || 'Terjadi kesalahan');
        })
        .finally(() => {
            unblock(document.getElementById("form-user"));
        });
}

function submit() {
    // Validate if the required fields are filled
    if (!formData.value.opengate || !formData.value.closegate) {
        toast.error('Open Gate dan Close Gate harus diisi');
        return;
    }

    const submitData = new FormData();
    
    // Add ticket data
    Object.entries(formData.value).forEach(([key, value]) => {
        if (key !== 'konser' && value !== null && value !== undefined) {
            submitData.append(key, value.toString());
        }
    });

    block(document.getElementById("form-user"));
    
    // Fix the URL to match the route
    const url = props.selected
        ? `/tiket/update/${props.selected}`
        : "/tiket/store";

    console.log('Submitting to:', url, submitData); // Add logging

    axios.post(url, submitData)
        .then((response) => {
            console.log('Response:', response.data); // Add logging
            if (response.data.success) {
                toast.success("Data berhasil disimpan");
                emit("refresh");
                emit("close");
            } else {
                throw new Error(response.data.message);
            }
        })
        .catch((err) => {
            console.error('Error saving ticket:', err);
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
            <!-- Basic Information -->
            <div class="row mb-6">
                <div class="col-md-6">
                    <div class="fv-row mb-7">
                        <label class="form-label fw-bold fs-6">Nama Konser</label>
                        <input 
                            type="text" 
                            class="form-control form-control-solid" 
                            :value="formData.konser?.title"
                            disabled
                        />
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="fv-row mb-7">
                        <label class="form-label fw-bold fs-6">Total Tiket</label>
                        <input 
                            type="text" 
                            class="form-control form-control-solid" 
                            :value="formData.konser?.tiket_tersedia"
                            disabled
                        />
                    </div>
                </div>
            </div>

            <!-- Ticket Types -->
            <div class="row mb-6">
                <div class="col-md-6">
                    <div class="fv-row mb-7">
                        <label class="form-label fw-bold fs-6 required">Tiket VIP</label>
                        <Field 
                            type="number" 
                            class="form-control form-control-lg"
                            name="vip"
                            v-model="formData.vip"
                        />
                        <div class="fv-plugins-message-container">
                            <ErrorMessage name="vip" class="fv-help-block" />
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="fv-row mb-7">
                        <label class="form-label fw-bold fs-6 required">Tiket Regular</label>
                        <Field 
                            type="number" 
                            class="form-control form-control-lg"
                            name="reguler"
                            v-model="formData.reguler"
                        />
                        <div class="fv-plugins-message-container">
                            <ErrorMessage name="reguler" class="fv-help-block" />
                        </div>
                    </div>
                </div>
            </div>

            <!-- Gate Times -->
            <div class="row mb-6">
                <div class="col-md-6">
                    <div class="fv-row mb-7">
                        <label class="form-label fw-bold fs-6 required">Open Gate</label>
                        <Field name="opengate">
                            <DatePicker
                                v-model="formData.opengate"
                                :config="{
                                    enableTime: true,
                                    noCalendar: true,
                                    dateFormat: 'H:i',
                                }"
                                class="form-control"
                            />
                        </Field>
                        <div class="fv-plugins-message-container">
                            <ErrorMessage name="opengate" class="fv-help-block" />
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="fv-row mb-7">
                        <label class="form-label fw-bold fs-6 required">Close Gate</label>
                        <Field name="closegate">
                            <DatePicker
                                v-model="formData.closegate"
                                :config="{
                                    enableTime: true,
                                    noCalendar: true,
                                    dateFormat: 'H:i',
                                }"
                                class="form-control"
                            />
                        </Field>
                        <div class="fv-plugins-message-container">
                            <ErrorMessage name="closegate" class="fv-help-block" />
                        </div>
                    </div>
                </div>
            </div>

            <!-- Prices -->
            <div class="row mb-6">
                <div class="col-md-6">
                    <div class="fv-row mb-7">
                        <label class="form-label fw-bold fs-6 required">Harga VIP</label>
                        <Field 
                            type="number" 
                            class="form-control form-control-lg"
                            name="harga_vip"
                            v-model="formData.harga_vip"
                        />
                        <div class="fv-plugins-message-container">
                            <ErrorMessage name="harga_vip" class="fv-help-block" />
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="fv-row mb-7">
                        <label class="form-label fw-bold fs-6 required">Harga Regular</label>
                        <Field 
                            type="number" 
                            class="form-control form-control-lg"
                            name="harga_regular"
                            v-model="formData.harga_regular"
                        />
                        <div class="fv-plugins-message-container">
                            <ErrorMessage name="harga_regular" class="fv-help-block" />
                        </div>
                    </div>
                </div>
            </div>

            <!-- Gate Capacities -->
            <h4 class="mb-4">Gate Capacities</h4>
            <div class="row">
                <div class="col-md-4">
                    <div class="fv-row mb-7">
                        <label class="form-label fw-bold fs-6">Gate A</label>
                        <Field 
                            type="number" 
                            class="form-control form-control-lg"
                            name="gate_a_capacity"
                            v-model="formData.gate_a_capacity"
                        />
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="fv-row mb-7">
                        <label class="form-label fw-bold fs-6">Gate B</label>
                        <Field 
                            type="number" 
                            class="form-control form-control-lg"
                            name="gate_b_capacity"
                            v-model="formData.gate_b_capacity"
                        />
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="fv-row mb-7">
                        <label class="form-label fw-bold fs-6">Gate C</label>
                        <Field 
                            type="number" 
                            class="form-control form-control-lg"
                            name="gate_c_capacity"
                            v-model="formData.gate_c_capacity"
                        />
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="fv-row mb-7">
                        <label class="form-label fw-bold fs-6">Gate D</label>
                        <Field 
                            type="number" 
                            class="form-control form-control-lg"
                            name="gate_d_capacity"
                            v-model="formData.gate_d_capacity"
                        />
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="fv-row mb-7">
                        <label class="form-label fw-bold fs-6">Gate E</label>
                        <Field 
                            type="number" 
                            class="form-control form-control-lg"
                            name="gate_e_capacity"
                            v-model="formData.gate_e_capacity"
                        />
                    </div>
                </div>
            </div>
        </div>

        <div class="card-footer d-flex justify-content-end">
            <button type="submit" class="btn btn-primary">
                {{ selected ? 'Update' : 'Simpan' }}
            </button>
        </div>
    </VForm>
</template>

<style scoped>
.fv-help-block {
    color: #dc3545;
    font-size: 0.875rem;
    margin-top: 0.25rem;
}

.form-control-solid {
    border-color: #f5f8fa;
    color: #5e6278;
    transition: color 0.2s ease, background-color 0.2s ease;
}

.form-control-solid:disabled {
}
</style>