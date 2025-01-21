<template>
    <div class="w-100">
        <div class="text-center mb-10">
            <router-link to="/">
                <img
                    :src="setting?.logo"
                    :alt="setting?.app"
                    class="w-200px mb-4"
                />
            </router-link>
            <h2 class="">
                Daftar Akun <span class="text-primary">{{ setting?.app }}</span>
            </h2>
        </div>

        <!-- Step 1: Initial Registration (Basic Info) -->
        <form v-if="currentStep === 1" class="w-100" @submit.prevent="initializeRegistration">
            <div class="fv-row mb-7">
                <label class="form-label fw-bold fs-6 ">Nama</label>
                <Field 
                    class="form-control form-control-lg form-control-solid" 
                    type="text" 
                    name="name" 
                    autocomplete="off"
                    v-model="form.name" 
                />
                <div class="fv-plugins-message-container">
                    <div class="fv-help-block">
                        <ErrorMessage name="name" />
                    </div>
                </div>
            </div>

            <div class="fv-row mb-7">
                <label class="form-label fw-bold  fs-6">Email</label>
                <Field 
                    class="form-control form-control-lg form-control-solid" 
                    type="email" 
                    name="email" 
                    autocomplete="off"
                    v-model="form.email" 
                />
                <div class="fv-plugins-message-container">
                    <div class="fv-help-block">
                        <ErrorMessage name="email" />
                    </div>
                </div>
            </div>

            <div class="fv-row mb-7">
                <label class="form-label fw-bold  fs-6">No. Telepon</label>
                <Field 
                    class="form-control form-control-lg form-control-solid" 
                    type="tel" 
                    name="phone" 
                    autocomplete="off"
                    v-model="form.phone" 
                />
                <div class="fv-plugins-message-container">
                    <div class="fv-help-block">
                        <ErrorMessage name="phone" />
                    </div>
                </div>
            </div>

            <div class="d-flex flex-column w-100">
                <button
                    type="submit"
                    class="btn btn-primary w-100"
                    :disabled="loading"
                >
                    {{ loading ? 'Selanjutnya' : 'Selanjutnya' }}
                </button>
            </div>
        </form>

        <!-- Step 2: OTP Verification -->
        <form v-if="currentStep === 2" class="w-100 mb-20 mt-20" @submit.prevent="verifyOTP">
            <div class="fv-row mb-13">
                <label class="form-label fw-bold  fs-6">Kode OTP</label>
                <Field 
                    class="form-control form-control-lg form-control-solid" 
                    type="text" 
                    name="otp" 
                    autocomplete="off"
                    v-model="form.otp"
                    maxlength="6"
                />
                <div class="fv-plugins-message-container">
                    <div class="fv-help-block">
                        <ErrorMessage name="otp" />
                    </div>
                </div>
            </div>

            <div class="d-flex flex-column w-100">
                <button
                    type="submit"
                    class="btn btn-primary w-100 mb-3"
                    :disabled="loading"
                >
                    {{ loading ? 'Memverifikasi...' : 'Verifikasi OTP' }}
                </button>
                <button
                    type="button"
                    class="btn btn-secondary w-100"
                    @click="currentStep = 1"
                    :disabled="loading"
                >
                    Kembali
                </button>
            </div>
        </form>

        <!-- Step 3: Set Password -->
        <form v-if="currentStep === 3" class="w-100" @submit.prevent="completeRegistration">
            <div class="fv-row mb-7">
                <label class="form-label fw-bold  fs-6">Password</label>
                <Field 
                    class="form-control form-control-lg form-control-solid" 
                    type="password" 
                    name="password" 
                    autocomplete="off"
                    v-model="form.password" 
                />
                <div class="fv-plugins-message-container">
                    <div class="fv-help-block">
                        <ErrorMessage name="password" />
                    </div>
                </div>
            </div>

            <div class="fv-row mb-7">
                <label class="form-label fw-bold  fs-6">Konfirmasi Password</label>
                <Field 
                    class="form-control form-control-lg form-control-solid" 
                    type="password" 
                    name="password_confirmation" 
                    autocomplete="off"
                    v-model="form.password_confirmation" 
                />
                <div class="fv-plugins-message-container">
                    <div class="fv-help-block">
                        <ErrorMessage name="password_confirmation" />
                    </div>
                </div>
            </div>

            <div class="d-flex flex-column w-100">
                <button
                    type="submit"
                    class="btn btn-primary w-100 mb-3"
                    :disabled="loading"
                >
                    {{ loading ? 'Mendaftar...' : 'Selesaikan Pendaftaran' }}
                </button>
                <button
                    type="button"
                    class="btn btn-secondary w-100"
                    @click="currentStep = 2"
                    :disabled="loading"
                >
                    Kembali
                </button>
            </div>
        </form>

        <div class="border-gray-300 w-100 mt-5 mb-10"></div>

        <div class="text-gray-500 fw-semobold fs-4 text-center">
            Sudah memiliki akun?
            <router-link to="/sign-in" class="link-primary fw-bold">
                MASUK
            </router-link>
        </div>
    </div>
</template>

<script setup lang="ts">
import { ref } from 'vue';
import { Field, ErrorMessage } from 'vee-validate';
import * as Yup from 'yup';
import { useSetting } from '@/services';
import axios from '@/libs/axios';
import { toast } from 'vue3-toastify';
import { useRouter } from 'vue-router';

const router = useRouter();
const { data: setting } = useSetting();
const currentStep = ref(1);
const loading = ref(false);

const form = ref({
    name: '',
    email: '',
    phone: '',
    password: '',
    password_confirmation: '',
    otp: ''
});

const formSchema = {
    name: Yup.string().required("Nama harus diisi"),
    email: Yup.string().email("Email tidak valid").required("Email harus diisi"),
    phone: Yup.string().min(10, "No Telepon minimal 10 digit").required("No Telepon harus diisi"),
    password: Yup.string().min(8, "Password minimal 8 karakter").required("Password harus diisi"),
    password_confirmation: Yup.string().oneOf([Yup.ref('password')], "Password tidak sama").required("Konfirmasi password harus diisi"),
    otp: Yup.string().length(6, "Kode OTP harus 6 digit").required("Kode OTP harus diisi")
};

async function initializeRegistration() {
    loading.value = true;
    try {
        const response = await axios.post('/auth/initialize-registration', {
            name: form.value.name,
            email: form.value.email,
            phone: form.value.phone
        });

        if (response.data.status) {
            toast.success(response.data.message);
            currentStep.value = 2;
        }
    } catch (error) {
        toast.error(error.response?.data?.message || "Gagal memulai registrasi");
    }
    loading.value = false;
}

async function verifyOTP() {
    loading.value = true;
    try {
        const response = await axios.post('/auth/verify-registration-otp', {
            email: form.value.email,
            otp: form.value.otp
        });

        if (response.data.status) {
            toast.success(response.data.message);
            currentStep.value = 3;
        }
    } catch (error) {
        toast.error(error.response?.data?.message || "Verifikasi OTP gagal");
    }
    loading.value = false;
}

async function completeRegistration() {
    loading.value = true;
    try {
        const response = await axios.post('/auth/complete-registration', {
            email: form.value.email,
            password: form.value.password,
            password_confirmation: form.value.password_confirmation
        });

        if (response.data.status) {
            toast.success("Pendaftaran berhasil!");
            setTimeout(() => {
                router.push('/sign-in');
            }, 1000);
        }
    } catch (error) {
        toast.error(error.response?.data?.message || "Gagal menyelesaikan registrasi");
    }
    loading.value = false;
}
</script>