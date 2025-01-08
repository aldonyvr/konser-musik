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

        <form class="w-100" @submit="submit" :validation-schema="formSchema" id="form-user" ref="formRef">
            <!--begin::Input group-->
            <div class="fv-row mb-7">
                <label class="form-label fw-bold fs-6 text-white">name</label>
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
            <!--end::Input group-->

            <div class="fv-row mb-7">
                <label class="form-label fw-bold text-white fs-6">No. Telepon</label>
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

            <div class="fv-row mb-7">
                <label class="form-label fw-bold text-white fs-6">Email</label>
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
                <label class="form-label fw-bold text-white fs-6">Password</label>
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
                <label class="form-label fw-bold text-white fs-6">Password Konfirmasi</label>
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

            <!--begin::Actions-->
            <div class="d-flex flex-column w-100">
                <button
                    type="submit"
                    class="btn btn-primary w-100"
                    id="submit"
                >
                    Daftar
                </button>
            </div>
            <!--end::Actions-->
        </form>

        <div class="border-gray-300 w-100 mt-5 mb-10"></div>

        <!--begin::Link-->
        <div class="text-gray-500 fw-semobold fs-4 text-center">
            Sudah memiliki akun?

            <router-link to="/auth/sign-in" class="link-primary fw-bold">
                MASUK
            </router-link>
        </div>
        <!--end::Link-->
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
const formRef = ref();
const { data: setting } = useSetting();
const form = ref({
    name: '',
    email: '',
    phone: '',
    password: '',
    password_confirmation: ''
});

const formSchema = Yup.object().shape({
    name: Yup.string().required("name harus diisi"),
    email: Yup.string().required("Email harus diisi"),
    phone: Yup.string().required("No Telepon harus diisi"),
    password: Yup.string().required("Password harus diisi"),
    password_confirmation: Yup.string().required("Password Konfirmasi harus diisi"),
});

async function submit(e: Event) {
    e.preventDefault();

    try {
        const response = await axios.post('/auth/register', form.value, {
            headers: {
                'Content-Type': 'application/json',
            },
        });

        console.log('Response:', response.data);

        if (response.data.status) {
            console.log('Success response:', response.data);
            toast.success("Pendaftaran registrasi berhasil!");
            setTimeout(() => {
                router.push(`/sign-in`);
            }, 950);
        } else {
            toast.success("Pendaftaran registrasi berhasil!");
            setTimeout(() => {
                router.push(`/sign-in`);
            }, 950);
        }

    }
    catch (error) {
    if (error.response && error.response.data) {
        console.log('Error details:', error.response.data);
        toast.error(error.response.data.message || "Gagal menyimpan data");
    } else {
        console.error('Unexpected Error:', error);
        toast.error("Gagal menyimpan data");
    }
}

}
</script>
